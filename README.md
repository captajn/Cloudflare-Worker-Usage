# Cloudflare Worker Usage

[English](#english) | [Tiếng Việt](#tiếng-việt)

## English

### Description

PHP/Json code allows you to query request volume/day from Cloudflare. By default, Cloudflare will give us 100k requests/day for all existing workers

### Create API Token, Setup Environment Variables

1. Access [Cloudflare API Tokens](https://dash.cloudflare.com/profile/api-tokens) to get Global API Key
2. Access [Cloudflare Dashboard](https://dash.cloudflare.com/) you will see ACCOUNT_ID on url browser
3. Access [Cloudflare Dashboard](https://dash.cloudflare.com/), select Worker & Pages, select Create Application > Create Worker (fill in your desired name) then press Deploy, you will get URL Worker to access later
4. On name Worker you created, click Settings - Variable - Environment Variables, create 5 variables named API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID and fill in the Value column. Remember click button Encrypt
5. Variable và Encrypt
![Setup Variable và Encrypt](images/encrypt-variable.png)
6. Copy the cloudflare.json file and paste to your Cloudflare Worker
7. Open the browser with your worker url and try

### With JSON

1. Copy the cloudflare.json file and paste to your Cloudflare Worker
2. Open the browser with Cloudflare's automatic worker url once you've set the name and Deloy is done

### With PHP

To use this project, follow these steps:

1. Install php 8.1+curl+Nginx on your vps if you don't have it yet. Hosting often support PHP, not need to install
2. [ACCOUNT_ID, API_KEY read Create API Token, Setup Environment Variables](#create-api-token-setup-environment-variables)
4. Modify the cloudflare.php information in the lines $api_email,$api_key,$account_id,$bot_token,$chat_id
5. Open your browser and access the file as ip/cloudflare.php or domain/cloudflare.phpp

### With Json

1. Visit https://dash.cloudflare.com/ select Worker & Pages, select Create Application > Create Worker (fill in your desired name) then press Deploy
2. Copy the entire json file and paste to your Cloudflare Worker
3. Access the Worker you created, go to Settings > Variable names, create 5 variables named API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID and fill in the Value column. Remember click button Encrypt
4. Open the browser with Cloudflare's automatic worker url once you've set the name and Deloy is done

## Tiếng Việt

### Mô Tả

Code PHP/Json cho phép bạn truy vấn dung lượng request/ngày từ Cloudflare. Mặc định Cloudflare sẽ cho chúng ta 100k request/day đối với toàn bộ workers đang có.

### Cài Đặt

Để cài đặt bạn cần có vps/hosting hỗ trợ PHP/Json:
1. Tải về tập tin cloudflare.php hoặc cloudflare.json dựa theo ngôn ngữ lập trình bạn muốn.
2. Tải tập tin lên máy chủ/vps của bạn.
3. Sau đó truy cập.

### Đối với file PHP

Để sử dụng dự án này, hãy làm theo các bước sau:
1. Cài đặt php trên vps/hosting của bạn nếu chưa có.
2. Truy cập https://dash.cloudflare.com/profile/api-tokens để lấy Global API Key
3. Truy cập https://dash.cloudflare.com/account_id/workers-and-pages bạn sẽ thấy accountid là dãy số chữ trước /workers-and-pages
4. Sửa đổi các thông tin cần thiết trong các dòng $api_email,$api_key,$account_id,$bot_token,$chat_id
5. Mở trình duyệt của bạn và truy cập file theo dạng ip/cloudflare.php.

### Đối với file json

1. Truy cập https://dash.cloudflare.com/ chọn Worker & Pages, chọn Create Application > Create Worker(điền tên tuỳ thích) sau đó ấn Deploy
2. Sao chép toàn bộ tập tin json và dán vào worker bạn vừa tạo
3. Truy cập Worker bạn đã tạo, vào mục Setting > Variable name tạo 5 biến tên API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID và điền vào giá trị cột Value
4. Mở trình duyệt bằng url worker tự động của Cloudflare khi bạn đặt tên và Deloy xong

## Ảnh cách tạo Variable và Encrypt (Bảo Mật)

![A descriptive alt text](images/encrypt-variable.png)
