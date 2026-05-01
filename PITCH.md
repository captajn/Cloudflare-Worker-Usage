# Project Pitch — Cloudflare Worker Usage Monitor

> **One-liner:** Open-source, zero-cost quota monitor cho Cloudflare Workers + cảnh báo Telegram, chạy trên 3 runtime (Workers / PHP / GitHub Actions) — giúp dev Việt Nam tránh sập service khi chạm quota free 100k req/ngày.

**Repo:** https://github.com/captajn/Cloudflare-Worker-Usage
**License:** MIT · **Status:** Active · **Maintainer:** [@captajn](https://github.com/captajn)

---

## 🇻🇳 Tiếng Việt — Pitch cho đơn xin tài trợ Claude Code

### Vấn đề (The Problem)
Cloudflare Workers free tier cho **100,000 request/ngày**, nhưng Cloudflare **không có cơ chế cảnh báo khi gần chạm quota**. Hậu quả:
- Dev đang học / startup nhỏ bị **sập service bất ngờ** → mất user, mất uy tín.
- Phải log-in dashboard check thủ công → không scale khi nhiều account.
- Muốn paid plan thì tốn $5+/tháng/domain — rào cản với học sinh, sinh viên, indie dev ở Việt Nam.

### Giải pháp (The Solution)
Dự án open-source này giải quyết gap đó bằng cách:
1. Truy vấn **Cloudflare GraphQL Analytics API** để lấy số request thực/ngày.
2. Tính % sử dụng vs quota 100k.
3. Đẩy thông báo qua **Telegram Bot** — realtime hoặc cron định kỳ (12h/18h/22h VN).
4. Hỗ trợ **multi-account** trong cùng một thông báo.
5. Cho phép deploy trên **3 runtime** tuỳ hạ tầng user đã có:
   - Cloudflare Workers (JS, serverless, 0đ).
   - PHP 8.1+ (VPS / shared hosting bình dân).
   - GitHub Actions (cron, 0đ, không cần server).

### Đối tượng người dùng
- **Dev Việt Nam** đang học về serverless / edge computing.
- **Indie hacker** chạy side-project trên CF Workers free.
- **Freelancer** quản lý nhiều client CF account cùng lúc.
- **Startup giai đoạn đầu** chưa đủ ngân sách paid plan.

### Tại sao mình cần Claude Code
Mình là dev đang trong giai đoạn học tập & xây dựng portfolio. Claude Code sẽ giúp mình:

1. **Refactor + hoàn thiện codebase** — hiện tại có 3 runtime viết 3 lần cùng 1 GraphQL query, vi phạm DRY. Cần tách shared logic + thêm test.
2. **Mở rộng roadmap đã public**:
   - Dashboard gộp nhiều account (cần học Cloudflare D1 + SvelteKit).
   - Alert threshold động (không chỉ report mà còn trigger webhook khi >80%).
   - Mở rộng sang Workers KV, R2, D1 usage — không chỉ request count.
   - Docker image self-host dễ hơn.
   - Unit test + CI test thật sự (hiện chỉ có Actions chạy cron, chưa có test).
3. **Học best practice** khi pair với Claude — improve code review skill, security mindset, system design. Các fix bảo mật trong bản v1.1 (tránh credential leak, shell quoting, bỏ sleep() treo request) là kết quả của audit Claude-assisted đầu tiên — chứng minh giá trị thực.
4. **Tạo tutorial tiếng Việt** cho community — dự án đã song ngữ Vi/En, mình muốn viết series "Build Cloudflare Workers monitor from scratch with Claude" để chia sẻ.

### Milestone cam kết (3 tháng)
| Tháng | Deliverable |
|---|---|
| **1** | Refactor shared logic (tách `lib/cloudflare-query.ts`), unit test cho % calc + GraphQL parser (coverage ≥70%). |
| **2** | Alert threshold feature + Discord/Slack webhook, Docker image trên GHCR, blog post "How I built this with Claude Code". |
| **3** | Dashboard gộp account (CF D1 + SvelteKit/Remix), mở rộng sang KV/R2 metrics, tutorial series Vi/En. |

### Tiến độ hiện tại (hard evidence)
- ✅ **v1.0** shipped với 3 runtime đầy đủ chức năng.
- ✅ **v1.1 polish** (bản này): thêm LICENSE MIT, CONTRIBUTING, SECURITY policy, CHANGELOG, `.env.example`, `wrangler.toml.example`, README chuyên nghiệp song ngữ có architecture diagram.
- ✅ **Security hardening v1.1**: fix 3 lỗ hổng (PHP `sleep()` treo request, PHP credential leak qua hidden input, Actions shell quoting). Chi tiết trong [CHANGELOG.md](./CHANGELOG.md).
- ✅ **1-click deploy button** cho Cloudflare Workers.
- ✅ **Docs song ngữ Vi/En** — lowered onboarding barrier cho dev VN.

---

## 🇺🇸 English — Funding Application Pitch

### Problem
Cloudflare Workers' free tier allows **100,000 requests/day**, but Cloudflare **does not notify users when approaching the quota**. This causes:
- Learning devs and small startups **silently hit quota and lose traffic** without warning.
- Manual dashboard checks don't scale across multiple accounts.
- The paid plan starts at $5+/month, which is a real barrier for students and indie devs in emerging markets (Vietnam, SEA).

### Solution
An open-source quota monitor that:
1. Queries Cloudflare's GraphQL Analytics API for real daily request counts.
2. Computes usage percentage vs the 100k/day quota.
3. Pushes alerts to Telegram — realtime or cron-scheduled.
4. Supports multi-account notifications in one message.
5. Deploys on 3 runtimes to match whatever infra the user already has:
   - Cloudflare Workers (JS, serverless, free).
   - PHP 8.1+ (VPS / shared hosting — still widely used in Vietnam).
   - GitHub Actions (cron-based, no server required).

### Why I need Claude Code
I'm an emerging developer building a portfolio of practical open-source tools. Claude Code will help me:

1. **Refactor the codebase** — the 3 runtimes currently duplicate the same GraphQL query. I want to extract a shared TypeScript library with proper unit tests (target coverage ≥70%).
2. **Ship the roadmap**: unified dashboard (Cloudflare D1 + SvelteKit), threshold-based alerts, Discord/Slack webhooks, Docker image, extended metrics (KV / R2 / D1).
3. **Learn security best practices** — the v1.1 audit (removed `sleep()` DoS, credential-leak hidden inputs, shell-injection in workflows) was Claude-assisted, proving the productivity boost.
4. **Produce Vietnamese-language tutorials** for the local dev community — the project is already bilingual; I plan a "Building with Claude Code" series to help others.

### 3-month milestones
| Month | Deliverable |
|---|---|
| **1** | Shared library refactor, unit tests ≥70% coverage |
| **2** | Threshold alerts + Discord/Slack webhooks, Docker image on GHCR, write-up blog post |
| **3** | Unified account dashboard (D1 + SvelteKit), KV/R2 metric expansion, bilingual tutorial series |

### Current traction (v1.1 shipped)
- MIT licensed, bilingual README with architecture diagram.
- 3 production-ready runtimes (Workers, PHP, Actions) with single + multi-account variants.
- 3 security fixes + standard OSS files (CONTRIBUTING, SECURITY, CHANGELOG, `.env.example`, `wrangler.toml.example`).
- 1-click deploy button for Cloudflare Workers.

---

## 📊 Quick stats

| Metric | Value |
|---|---|
| Lines of code | ~1,500 (JS + PHP + YAML + docs) |
| Supported runtimes | 3 (Workers, PHP, GitHub Actions) |
| External APIs integrated | 2 (Cloudflare GraphQL, Telegram Bot) |
| Deployment cost | $0 (Workers + Actions free tier) |
| Languages supported in UI/docs | 2 (Vietnamese, English) |
| License | MIT |

## 🔗 Links
- **Repository:** https://github.com/captajn/Cloudflare-Worker-Usage
- **Live demo (1-click deploy):** [Deploy to Cloudflare Workers](https://deploy.workers.cloudflare.com/?url=https://github.com/captajn/Cloudflare-Worker-Usage)
- **Changelog:** [./CHANGELOG.md](./CHANGELOG.md)
- **Security policy:** [./SECURITY.md](./SECURITY.md)
- **Contributing guide:** [./CONTRIBUTING.md](./CONTRIBUTING.md)

---

*Prepared for funding application · Vietnamese emerging developer program · 2024*
