# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Professional bilingual (Vi/En) README with badges, architecture diagram, and Quick Start.
- `LICENSE` (MIT), `CONTRIBUTING.md`, `SECURITY.md`, `CHANGELOG.md`, `.gitignore`.
- `.env.example` template for secrets.
- `wrangler.toml.example` for Wrangler CLI-based Workers deployment.
- `PITCH.md` — project pitch for funding applications.

### Fixed
- **PHP (critical)**: removed broken `scheduleTelegramMessages()` which called `sleep($delay)` on web requests — would block PHP-FPM worker up to 24h and cause hard timeout. Scheduling now officially delegated to GitHub Actions cron.
- **PHP (security)**: stopped echoing raw `API_KEY`, `BOT_TOKEN`, `CHAT_ID` back into hidden form inputs (viewable via page source). Credentials now only masked in display, never sent to the browser.
- **GitHub Actions**: quoted all shell variable expansions (`"$API_EMAIL"`, `"$API_KEY"`, `"$ACCOUNT_ID"`) to prevent word-splitting and injection.

### Changed
- Default credential placeholders changed from `xxxxxxx` to descriptive `your_cloudflare_api_key_here` style in templates.

## [1.0.0] — 2024-XX-XX

### Added
- Initial release with 3 runtimes: Cloudflare Workers (JS), PHP, GitHub Actions.
- Single-account + multi-account variants for each runtime.
- Telegram bot notification (immediate + scheduled via Actions cron).
- Bilingual UI (Vietnamese / English) on PHP and Workers HTML output.
- 1-click "Deploy to Cloudflare Workers" button.
