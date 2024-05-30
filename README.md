# Cloudflare Worker Usage - Telegram

<details>
<summary>🇬🇧 English</summary>

## Description

PHP/Json code allows you to query request volume/day from Cloudflare. By default, Cloudflare will give us 100k requests/day for all existing workers

## Installation

To install you need a vps/hosting that supports PHP/Json:
1. Download the cloudflare.php or cloudflare.json file based on the programming language you want.
2. Upload files to your server/vps.
3. Then access.

## With PHP


To use this project, follow these steps:
1. Install php on your vps/hosting if you don't have it yet.
2. Modify the necessary information in the lines $api_email,$api_key,$account_id,$bot_token,$chat_id
3. Open your browser and access the file as ip/cloudflare.php or domain/cloudflare.php

## With Json

1. Visit https://dash.cloudflare.com/ select Worker & Pages, select Create Application > Create Worker (fill in your desired name) then press Deploy
2. Copy the entire json file and edit your const information apiEmail,apiKey,accountId,botToken,chatId
3. Open the browser with Cloudflare's automatic worker url once you've set the name and Deloy is done

</details>

<details>
<summary>🇻🇳 Tiếng Việt</summary>

## Mô Tả

Code PHP/Json cho phép bạn truy vấn dung lượng request/ngày từ Cloudflare. Mặc định Cloudflare sẽ cho chúng ta 100k request/day đối với toàn bộ workers đang có.

## Cài Đặt

Để cài đặt bạn cần có vps/hosting hỗ trợ PHP/Json:
1. Tải về tập tin cloudflare.php hoặc cloudflare.json dựa theo ngôn ngữ lập trình bạn muốn.
2. Tải tập tin lên máy chủ/vps của bạn.
3. Sau đó truy cập.

## Đối với file PHP

Để sử dụng dự án này, hãy làm theo các bước sau:
1. Cài đặt php trên vps/hosting của bạn nếu chưa có.
2. Sửa đổi các thông tin cần thiết trong các dòng $api_email,$api_key,$account_id,$bot_token,$chat_id
3. Mở trình duyệt của bạn và truy cập file theo dạng ip/cloudflare.php.

## Đối với file json

1. Truy cập https://dash.cloudflare.com/ chọn Worker & Pages, chọn Create Application > Create Worker(điền tên tuỳ thích) sau đó ấn Deploy
2. Sao chép toàn bộ tập tin json và sửa lại các thông tin const apiEmail,apiKey,accountId,botToken,chatId của bạn
3. Mở trình duyệt bằng url worker tự động của Cloudflare khi bạn đặt tên và Deloy xong

</details>
