<?php
// Khởi tạo các biến với giá trị mặc định
$api_email = 'xxxxxxx';
$api_key = 'xxxxxxx';
$account_id = 'xxxxxxx';
$bot_token = 'xxxxxxx';
$chat_id = 'xxxxxxx';
$daily_limit = 100000; // Giới hạn hàng ngày cố định
$send_times = ['09:00', '12:00', '18:00']; // Thời gian gửi trong ngày (giờ phút)

// Hàm để che bớt ký tự của các trường
function mask_string($string, $visible_chars = 3, $mask_char = '*') {
    $visible_part = substr($string, 0, $visible_chars);
    $masked_part = str_repeat($mask_char, strlen($string) - $visible_chars);
    return $visible_part . $masked_part;
}

// Hàm gửi tin nhắn Telegram
function sendTelegramMessage($bot_token, $chat_id, $message) {
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        // Xử lý lỗi nếu cần
    }
}

// Hàm hẹn giờ gửi tin nhắn
function scheduleTelegramMessages($send_times, $bot_token, $chat_id, $message) {
    foreach ($send_times as $time) {
        $time_parts = explode(':', $time);
        $hour = (int)$time_parts[0];
        $minute = (int)$time_parts[1];

        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $scheduled_time = new DateTime('today ' . $time, new DateTimeZone('Asia/Ho_Chi_Minh'));

        if ($now > $scheduled_time) {
            // Nếu thời gian đã qua, đặt lịch cho ngày mai
            $scheduled_time->modify('+1 day');
        }

        $delay = $scheduled_time->getTimestamp() - $now->getTimestamp();

        // Đặt lịch gửi tin nhắn
        sleep($delay);
        sendTelegramMessage($bot_token, $chat_id, $message);
    }
}

// Kiểm tra nếu form đã được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api_email = $_POST['api_email'];
    $api_key = $_POST['api_key'];
    $account_id = $_POST['account_id'];
    $bot_token = $_POST['bot_token'];
    $chat_id = $_POST['chat_id'];

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

    // Gửi tin nhắn qua Telegram bot nếu chọn Send Telegram
    if (isset($_POST['send_telegram'])) {
        if (empty($_POST['send_times'])) {
            $telegram_message_error = "Vui lòng chọn khung giờ để gửi truy vấn.";
        } else {
            $selected_time = $_POST['send_times'];
            $message = "Dữ liệu truy vấn lúc: " . $date->format('d-m-Y H:i:s') . "\n";
            $message .= "Số lượng truy vấn: $requests / $daily_limit\nPhần trăm sử dụng: " . number_format($usage_percentage, 2) . "%";
            
            if ($selected_time === 'now') {
                sendTelegramMessage($bot_token, $chat_id, $message);
                $telegram_message_success = "Tin nhắn đã được gửi ngay tới Telegram.";
            } else {
                scheduleTelegramMessages([$selected_time], $bot_token, $chat_id, $message);
                $telegram_message_success = "Lịch gửi truy vấn đã được thiết lập tới Telegram theo khung giờ bạn đã chọn.";
            }
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
                <div class="form-group">
                    <label for="api_email">API Email</label>
                    <input type="email" class="form-control" id="api_email" name="api_email" required value="<?php echo htmlspecialchars($api_email); ?>">
                </div>
                <div class="form-group">
                    <label for="api_key">API Key</label>
                    <input type="text" class="form-control" id="api_key" name="api_key" required value="<?php echo htmlspecialchars(mask_string($api_key)); ?>">
                </div>
                <div class="form-group">
                    <label for="account_id">Account ID</label>
                    <input type="text" class="form-control" id="account_id" name="account_id" required value="<?php echo htmlspecialchars(mask_string($account_id)); ?>">
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
                    <label for="bot_token">Bot Token</label>
                    <input type="text" class="form-control" id="bot_token" name="bot_token" required value="<?php echo htmlspecialchars(mask_string($bot_token)); ?>">
                </div>
                <div class="form-group">
                    <label for="chat_id">Chat ID</label>
                    <input type="text" class="form-control" id="chat_id" name="chat_id" required value="<?php echo htmlspecialchars(mask_string($chat_id)); ?>">
                </div>
                <button type="button" class="btn btn-info" id="send_telegram_btn">Send Telegram</button>
            </div>

            <!-- Hidden inputs to store the real values -->
            <input type="hidden" name="api_key" value="<?php echo htmlspecialchars($api_key); ?>">
            <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($account_id); ?>">
            <input type="hidden" name="bot_token" value="<?php echo htmlspecialchars($bot_token); ?>">
            <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chat_id); ?>">

            <!-- Hiển thị khung giờ chọn nếu button send_telegram được chọn -->
            <div id="send_times_container" style="display: none;" class="form-section">
                <h5>Chọn thời gian để gửi tin nhắn:</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="send_times" value="now" id="time_now" checked>
                    <label class="form-check-label" for="time_now">
                        Gửi Ngay
                    </label>
                </div>
                <?php foreach ($send_times as $time): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="send_times" value="<?php echo $time; ?>" id="time_<?php echo $time; ?>">
                        <label class="form-check-label" for="time_<?php echo $time; ?>">
                            <?php echo $time; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-info mt-3" name="send_telegram">Xác nhận</button>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq8KG4Qd2D7z8gl5Q5cj6u+c9/oxK5y5z4KN2X2F3z7a5x5y5f" crossorigin="anonymous"></script>
    <script>
        // Hiển thị khung giờ chọn nếu button send_telegram được chọn
        document.getElementById('send_telegram_btn').addEventListener('click', function () {
            document.getElementById('send_times_container').style.display = 'block';
        });
    </script>
</body>
</html>