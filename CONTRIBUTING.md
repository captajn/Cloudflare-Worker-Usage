# Contributing to Cloudflare Worker Usage Monitor

Cảm ơn bạn muốn đóng góp! 🎉 / Thanks for wanting to contribute!

## 🐞 Báo bug / Reporting bugs

1. Search [existing issues](https://github.com/captajn/Cloudflare-Worker-Usage/issues) để chắc chắn chưa ai report.
2. Mở issue mới với thông tin:
   - Runtime đang dùng (Workers / PHP / Actions).
   - Log lỗi cụ thể (xoá credential trước khi paste!).
   - Bước reproduce.
   - OS / PHP version / Node version nếu liên quan.

## 💡 Đề xuất feature / Feature requests

Mở issue với prefix `[Feature]` và mô tả:
- Vấn đề bạn đang gặp.
- Giải pháp bạn đề xuất.
- Alternative nếu có.

## 🔧 Pull Request

1. **Fork** repo và tạo branch từ `main`:
   ```bash
   git checkout -b feat/ten-tinh-nang
   ```
2. **Commit** theo [Conventional Commits](https://www.conventionalcommits.org/):
   - `feat:` tính năng mới
   - `fix:` sửa bug
   - `docs:` thay đổi docs
   - `refactor:` refactor không đổi hành vi
   - `chore:` việc vặt (deps, config)
3. **Test** thủ công trên ít nhất 1 runtime trước khi PR.
4. **Không commit credential thật** — dùng placeholder (`xxxxxxx`, `your_api_key_here`).
5. **Cập nhật** `CHANGELOG.md` mục `[Unreleased]` nếu thay đổi ảnh hưởng user.
6. Mở PR, link issue nếu có (`Closes #123`).

## 📐 Code style

- **JS (Workers)**: 2 spaces, single quote, trailing comma.
- **PHP**: PSR-12, 4 spaces.
- **YAML (Actions)**: 2 spaces, quote mọi shell expansion chứa biến người dùng.
- **Bash trong Actions**: luôn quote `"$VAR"` để tránh word-splitting.

## 🔒 Security

Tìm thấy lỗ hổng? **Đừng mở issue public** — xem [SECURITY.md](./SECURITY.md).

## 📜 License

Khi contribute, bạn đồng ý code của mình được phát hành dưới [MIT License](./LICENSE).
