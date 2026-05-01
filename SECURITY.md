# Security Policy

## 🔒 Reporting a Vulnerability

Nếu bạn phát hiện lỗ hổng bảo mật trong dự án này, **vui lòng KHÔNG mở issue public**.

### Cách báo cáo

Gửi email trực tiếp tới maintainer qua GitHub:
- Liên hệ [@captajn](https://github.com/captajn) qua GitHub issues (chọn private disclosure nếu có) hoặc mention trong PR draft.
- Cung cấp:
  - Mô tả lỗ hổng (loại: XSS, SSRF, credential leak, RCE, v.v.).
  - Các bước reproduce.
  - Impact ước tính (ai bị ảnh hưởng, mức độ nghiêm trọng).
  - Fix đề xuất nếu có.

### Timeline cam kết

| Bước | Thời gian |
|---|---|
| Xác nhận đã nhận báo cáo | ≤ 72h |
| Đánh giá sơ bộ | ≤ 7 ngày |
| Patch + release | ≤ 30 ngày (tuỳ độ phức tạp) |
| Public disclosure | Sau khi có bản vá |

## 🛡 Best practices khi deploy

Dù dự án đã được audit cơ bản, **trách nhiệm bảo mật credential là của người deploy**:

1. **KHÔNG** commit `API_KEY`, `BOT_TOKEN`, `API_EMAIL` vào Git.
2. **LUÔN** dùng Encrypted Variables trên Cloudflare Workers và GitHub Secrets trên Actions.
3. **KHÔNG** expose file PHP public nếu không cần — đặt sau basic auth hoặc hạn chế IP.
4. **Rotate** API Key định kỳ (3-6 tháng) hoặc ngay khi nghi ngờ lộ.
5. **Dùng Cloudflare API Token** (scoped) thay vì Global API Key nếu có thể — quyền tối thiểu an toàn hơn.
6. **Giám sát** Telegram bot — nếu bot gửi tin lạ, revoke `BOT_TOKEN` qua [@BotFather](https://t.me/BotFather).

## 📜 Known considerations

- **PHP runtime** (`Php/*.php`) nên được triển khai sau HTTPS và có IP allowlist. File hiển thị masked credential trong UI, không expose plaintext.
- **GitHub Actions** chạy trên runner public của GitHub — log được xoá sau 90 ngày nhưng tránh `echo` raw credential trong step script.
- **Cloudflare Workers** chạy trong V8 isolate — secrets được encrypt at rest nhưng có thể log ra console.log nếu code bug. Không log `API_KEY` hay `BOT_TOKEN`.

## 🙏 Thanks

Security researchers báo cáo responsibly sẽ được credit trong `CHANGELOG.md` (nếu đồng ý).
