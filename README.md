# Cloudflare Worker Usage

[Tiếng Việt](#tiếng-việt) | [English](#english)

## Tiếng Việt

### Mô Tả
Code PHP/Json cho phép bạn truy vấn dung lượng request/ngày từ Cloudflare. Mặc định Cloudflare sẽ cho chúng ta 100k request/day đối với toàn bộ workers đang có.

### Tạo API Token, tạo các biến Environment Variables
1. Truy cập [Cloudflare API Tokens](https://dash.cloudflare.com/profile/api-tokens) để lấy Global API Key
2. Chú ý thanh trình duyệt [Cloudflare Dashboard](https://dash.cloudflare.com/) bạn sẽ thấy ACCOUNT_ID
3. Vào [Cloudflare Dashboard](https://dash.cloudflare.com/), chọn Worker & Pages, click Create Application > Create Worker(đặt tên tuỳ ý), sau đó ấn Deploy, url worker sẽ tự động tạo để truy cập
4. Chọn Worker đã tạo, vào mục Setting > Variable name tạo 5 biến tên API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID và điền các giá trị vào. Nhớ click nút Encrypt(bảo mật)
5. Nếu dùng file json nhiều account thì tạo thêm 3 biến tên API_EMAIL2, API_KEY2, ACCOUNT_ID2
6. Hình minh hoạ
![Tạo biến và bảo mật](images/multi-encrypt-variable.png)

### Đối với file PHP
1. Cài đặt php 8.1+curl+Nginx trên VPS nếu bạn chưa có. Hosting linux thường hỗ trợ sẵn php+webserver nên không cần cài.
2. Dùng file cloudflare.php nếu bạn chỉ dùng 1 account Cloudflare, dùng file multi-account.php nếu muốn truy vấn nhiều account
3. Thay đổi các thông tin $api_email,$api_key,$account_id,$bot_token,$chat_id trong file
4. Mở trình duyệt của bạn và truy cập file theo dạng ip/cloudflare.php hoặc tên miền abc.com/cloudflare.php

### Đối với file json
1. Dùng file cloudflare.json nếu bạn chỉ dùng 1 account Cloudflare, dùng file multi-account.json nếu muốn truy vấn nhiều account
2. Copy toàn bộ nội dung file .json và dán vào worker của bạn
3. Mở trình duyệt bằng url worker của bạn và tận hưởng
[[Tạo Workers](https://deploy.workers.cloudflare.com/button)

### Chạy với Github Action
1. Fork repo này vào tài khoản của bạn
2. Vào repository của bạn Settings - Secrets and variables - Action > Repository secrets
3. Tạo 5 Repository secrets: API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID
4. Tạo thêm 3 cái Repository secrets: API_EMAIL2, API_KEY2, ACCOUNT_ID2 nếu dùng Multi Cloudflare API Usage
5. Hình minh hoạ
![Tạo biến và bảo mật](images/multi-action.png)
6. Chạy Github Action và tận hưởng

## English

### Description
PHP/Json code allows you to query request volume/day from Cloudflare. By default, Cloudflare will give us 100k requests/day for all existing workers

### Create API Token, Setup Environment Variables
1. Access [Cloudflare API Tokens](https://dash.cloudflare.com/profile/api-tokens) to get Global API Key
2. Access [Cloudflare Dashboard](https://dash.cloudflare.com/) you will see ACCOUNT_ID on url browser
3. Access [Cloudflare Dashboard](https://dash.cloudflare.com/), select Worker & Pages, select Create Application > Create Worker (fill in your desired name) then press Deploy, you will get URL Worker to access later
4. On name Worker you created, click Settings - Variable - Environment Variables, create 5 variables named API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID and fill in the Value column. Remember click button Encrypt
5. If you use multi-account.json, remember create more 3 Environment Variables: API_EMAIL2, API_KEY2, ACCOUNT_ID2
6. Variable và Encrypt
![Setup Variable và Encrypt](images/multi-encrypt-variable.png)

### With JSON
1. Use cloudflare.json if you only use 1 account Cloudflare, use file multi-account.json if you want to use with many accounts cloudflare
2. Copy the file .json and paste to your Cloudflare Worker
[![Deploy to Cloudflare Workers](https://deploy.workers.cloudflare.com/button)]
3. Open the browser with Cloudflare's automatic worker url once you've set the name and Deloy is done

### With PHP
1. Install php 8.1+curl+Nginx on your vps if you don't have it yet. Hosting often support PHP, not need to install
2. [ACCOUNT_ID, API_KEY read Create API Token, Setup Environment Variables](#create-api-token-setup-environment-variables)
3. Use cloudflare.php if you only use 1 account Cloudflare, use file multi-account.php if you want to use with many accounts cloudflare
4. Edit information in the lines $api_email,$api_key,$account_id,$bot_token,$chat_id
5. Open your browser and access the file as ip/cloudflare.php or domain/cloudflare.php

### With Github Action
1. Fork this repo to your account
2. Enter your repository github - Settings - Secrets and variables - Action > Repository secrets
3. Create 5 Repository secrets: API_EMAIL, API_KEY, ACCOUNT_ID, BOT_TOKEN, CHAT_ID
4. If you run action Multi Cloudflare API Usage, remember create more 3 Repository secrets: API_EMAIL2, API_KEY2, ACCOUNT_ID2
5. Example
![Tạo biến và bảo mật](images/multi-action.png)
5. Run Github Action > Cloudflare API Usage with 1 account Cloudflare, use Multi Cloudflare API Usage with 2 accounts
