<?php
// ---------------------------------------------------------------
// Cloudflare Worker Usage Monitor (PHP runtime, single account)
// Credentials load từ biến môi trường (xem .env.example).
// Lịch gửi định kỳ được xử lý bởi GitHub Actions cron,
// KHÔNG phải sleep() trong PHP (được loại bỏ vì treo web request).
// ---------------------------------------------------------------
$api_email  = getenv('API_EMAIL')  ?: 'your_cloudflare_email@example.com';
$api_key    = getenv('API_KEY')    ?: 'your_cloudflare_api_key_here';
$account_id = getenv('ACCOUNT_ID') ?: 'your_cloudflare_account_id_here';
$bot_token  = getenv('BOT_TOKEN')  ?: 'your_telegram_bot_token_here';
$chat_id    = getenv('CHAT_ID')    ?: 'your_telegram_chat_id_here';
$daily_limit = (int)(getenv('DAILY_LIMIT') ?: 100000);

// Hàm che ký tự (hiển thị read-only trên UI)
function mask_string(string $string, int $visible_chars = 3, string $mask_char = '*'): string
{
    if (strlen($string) <= $visible_chars) return $string;
    $visible_part = substr($string, 0, $visible_chars);
    $masked_part = str_repeat($mask_char, strlen($string) - $visible_chars);
    return $visible_part . $masked_part;
}

// Gửi tin nhắn Telegram (realtime)
function sendTelegramMessage(string $bot_token, string $chat_id, string $message): bool
{
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $data = ['chat_id' => $chat_id, 'text' => $message];
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 10,
            'ignore_errors' => true,
        ],
    ];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    return $result !== false;
}

// Defaults cho biz state (giúp static analyzer / intelephense tracing đúng flow)
$requests = 0;
$usage_percentage = 0.0;
$date = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));

// Kiểm tra nếu form đã được submit
// Security: credentials KHÔNG lấy từ $_POST (tránh CSRF + leak qua view-source).
// Chúng được nạp từ env ở đầu file.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Chuẩn bị biến filter với thời gian thực theo múi giờ Asia/Ho_Chi_Minh
    $date = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    $start_date = $date->format('Y-m-d\T00:00:00.000\Z');
    $end_date = $date->format('Y-m-d\TH:i:s.000\Z');

    $filter = [
        "datetime_geq" => $start_date,
        "datetime_leq" => $end_date
    ];

    $overviewFilter = [
        "datetime_geq" => $start_date,
        "datetime_leq" => $end_date
    ];

    $durableObjectFilter = [
        "datetimeHour_geq" => $start_date,
        "datetimeHour_leq" => $end_date
    ];

    // Truy vấn GraphQL để lấy số lượng truy vấn
    $query = '
    query getBillingMetrics($accountTag: String!, $filter: AccountWorkersInvocationsAdaptiveFilter_InputObject, $overviewFilter: AccountWorkersInvocationsAdaptiveFilter_InputObject) {
      viewer {
        accounts(filter: {accountTag: $accountTag}) {
          workersInvocationsAdaptive(limit: 10000, filter: $filter) {
            sum {
              duration
              requests
              subrequests
              responseBodySize
              errors
            }
            quantiles {
              cpuTimeP50
            }
            dimensions {
              usageModel
            }
          }
          workersOverviewRequestsAdaptiveGroups(limit: 1000, filter: $overviewFilter) {
            sum {
              cpuTimeUs
            }
            dimensions {
              usageModel
            }
          }
          durableObjectsInvocationsAdaptiveGroups(limit: 10000, filter: $durableObjectFilter) {
            sum {
              requests
            }
          }
          durableObjectsPeriodicGroups(limit: 10000, filter: $durableObjectFilter) {
            sum {
              activeTime
            }
          }
        }
      }
    }';

    // Thiết lập cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'query' => $query,
        'variables' => [
            'accountTag' => $account_id,
            'filter' => $filter,
            'overviewFilter' => $overviewFilter,
            'durableObjectFilter' => $durableObjectFilter
        ]
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Auth-Email: ' . $api_email,
        'X-Auth-Key: ' . $api_key
    ]);

    // Thực hiện yêu cầu
    $response = curl_exec($ch);

    // Kiểm tra lỗi cURL
    if (curl_errno($ch)) {
        echo 'Lỗi cURL: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    // Giải mã phản hồi JSON
    $data = json_decode($response, true);

    // Kiểm tra lỗi
    if (isset($data['errors'])) {
        echo 'Lỗi: ' . $data['errors'][0]['message'];
        exit;
    }

    // Trích xuất số lượng truy vấn
    $requests = isset($data['data']['viewer']['accounts'][0]['workersInvocationsAdaptive'][0]['sum']['requests']) ? $data['data']['viewer']['accounts'][0]['workersInvocationsAdaptive'][0]['sum']['requests'] : 0;
    $usage_percentage = ($requests / $daily_limit) * 100;

    // Gửi tin nhắn qua Telegram (realtime - "Gửi Ngay")
    // Lưu ý: lịch định kỳ (09:00/12:00/...) hãy cấu hình qua GitHub Actions cron,
    // xem .github/workflows/cloudflare-usage.yml
    if (isset($_POST['send_telegram'])) {
        $message  = "⏰ Dữ liệu truy vấn lúc: " . $date->format('d-m-Y H:i:s') . "\n";
        $message .= "🚦 Số lượng truy vấn: $requests / $daily_limit\n";
        $message .= "🧮 Phần trăm sử dụng: " . number_format($usage_percentage, 2) . "%";

        if (sendTelegramMessage($bot_token, $chat_id, $message)) {
            $telegram_message_success = "Tin nhắn đã được gửi tới Telegram.";
        } else {
            $telegram_message_error = "Gửi Telegram thất bại. Kiểm tra BOT_TOKEN và CHAT_ID.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloudflare API Usage</title>
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/minty/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #dedede;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-section h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .usage-details {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Cloudflare API Usage</h1>
        <form method="POST" action="">
            <div class="form-section">
                <h2>Truy Vấn</h2>
                <p class="text-muted small">Credentials được nạp từ biến môi trường (xem <code>.env.example</code>). UI chỉ hiển thị ở dạng đã che.</p>
                <div class="form-group">
                    <label>API Email</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($api_email)); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>API Key</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($api_key)); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Account ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($account_id)); ?>" readonly>
                </div>
                <button type="submit" class="btn btn-primary" name="query">Truy Vấn</button>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['send_telegram'])): ?>
                    <div class="card mt-4 usage-details">
                        <div class="card-body">
                            <h5 class="card-title">Usage Details</h5>
                            <p class="card-text">
                                <strong>Dữ liệu truy vấn lúc ngày giờ hiện tại:</strong> <?php echo $date->format('d-m-Y H:i:s'); ?><br>
                                <strong>Số lượng truy vấn:</strong> <?php echo $requests; ?> / <?php echo $daily_limit; ?><br>
                                <strong>Phần trăm sử dụng:</strong> <?php echo number_format($usage_percentage, 2); ?>%
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-section">
                <h2>Send Telegram</h2>
                <div class="form-group">
                    <label>Bot Token</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($bot_token)); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Chat ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($chat_id)); ?>" readonly>
                </div>
                <p class="text-muted small">Để gửi theo lịch định kỳ (hàng ngày), cấu hình <code>.github/workflows/cloudflare-usage.yml</code> thay vì gọi trong PHP.</p>
                <button type="submit" class="btn btn-info" name="send_telegram">Gửi Telegram ngay</button>
            </div>

            <?php if (isset($telegram_message_error)): ?>
                <div class="alert alert-danger mt-4">
                    <?php echo $telegram_message_error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($telegram_message_success)): ?>
                <div class="alert alert-success mt-4">
                    <?php echo $telegram_message_success; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>