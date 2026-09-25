# QuickContent — Build Status

_Last updated: 2026-09-25 · Branch: `arena/01a0d7f6-quickgig`_

> ⚠️ **Not executed.** There is no PHP runtime in this workspace (`php: command not found`,
> no `vendor/`), so nothing below has been run. Everything was written against the existing
> models/migrations/views and checked statically. **First thing to do locally:**
> `composer install && php artisan migrate:fresh --seed && ./vendor/bin/phpunit`

---

## ✅ Done in this pass

### Blockers fixed
| Was broken | Fix |
|---|---|
| `/services/{service}` → missing `ServiceController` (fatal) | `app/Http/Controllers/ServiceController.php` + `resources/views/services/show.blade.php` (detail page, creator card, order form, related services) |
| `/creator/orders/{id}` → missing `creator.order` view | `resources/views/creator/order.blade.php` (brief, progress, past deliveries, upload form) |
| No seed data | `database/seeders/DatabaseSeeder.php` — 3 companies, 4 creators + portfolio, 4 services, 8 orders across all statuses, payouts, FAQs, a blog post. (Admin logins were already inserted by the install migration.) |
| No `settings` table; save was a TODO | `settings` table + `App\Models\Setting` (cached, `putMany`), `SettingController` now reads/writes it |
| No `config/` directory | `config/services.php` with `razorpay` + `razorpayx` blocks |

### Mock data removed
All five hardcoded admin controllers now query Eloquent:
- **Dashboard** — real order counts, escrow sums, creator availability, recent orders, grouped payout queue
- **Companies** — paginated, `withCount('orders')` + `withSum` spend, real search
- **Orders** — paginated/filtered from `orders`, real timeline from timestamps, live creator dropdown, real brief
- **Users** — real `users` table, `store()` actually creates (was a commented-out line), self-demotion and last-super-admin guards
- **Payouts** — real `payouts` table, `markPaid()` hits RazorpayX, `hold()` re-holds
- Views de-mocked too: no more `i.pravatar.cc` avatars, no `@quickcontent.in`, no `3 ready` / `Pending (1)` literals

### RazorpayX + reconciliation
- `app/Services/RazorpayXService.php` — payout create (UPI/VPA), fetch, status mapping, HMAC webhook verification, idempotency header, auto-simulate when unconfigured
- `app/Jobs/ReconcilePayoutJob.php` — self-requeuing poll with exponential backoff, 12-attempt budget
- `app/Http/Controllers/WebhookController.php` + `POST /webhooks/razorpayx` (CSRF-exempt, signature-verified)
- `payouts` table with `reference` (idempotency key), `payout_id`, `utr`, `failure_reason`, `poll_attempts`
- Escrow release creates exactly one payout per order (`firstOrCreate` in a transaction), net of the creator fee

### Queue worker & scheduler
- `php artisan payouts:release` (`app/Console/Commands/ReleasePayoutHolds.php`, `--dry-run` supported)
- `routes/console.php` now schedules: `payouts:release` hourly, `queue:work --stop-when-empty` every minute, `queue:prune-failed` daily — all driven by **one** Hostinger cron (`schedule:run`)

### File uploads for deliveries
- `order_deliveries` table + `App\Models\OrderDelivery` (signed temp URL, human file size)
- `CreatorController::deliver()` stores the file (50 MB cap, extension allowlist: mp4/mov/webm/zip/png/jpg/pdf/srt), records name/mime/size, falls back to `public/uploads` if the disk isn't linked
- Upload UI in the new creator order view; past deliveries listed

### Auth hardening
- Per-email+IP login throttle (5 attempts / 5 min) **plus** route `throttle:10,1`
- Password reset end-to-end: `/forgot-password`, `/reset-password/{token}`, uses Laravel's broker, strong-password rule, enumeration-safe response, throttled
- **Demo credentials removed** from the login page (and the prefilled `Admin@12345` password field)

### Tests — 38 assertions across 4 feature files
`phpunit.xml` (SQLite in-memory), `tests/TestCase.php` with `admin()` / `makeOrder()` helpers:
- `AdminScreensTest` — all 10 admin screens render; RBAC (support blocked, manager allowed, finance-only escrow, super-admin-only settings); escrow release idempotency + 10% fee maths; user CRUD guards; settings persistence
- `BoardScreensTest` — public pages, service page + 404, creator order screen, delivery upload happy path, `.php` upload rejection, missing-file validation, order placement
- `AuthTest` — no leaked demo creds, sign-in, disabled account, brute-force lockout, reset request/enumeration/actual reset/weak password
- `PayoutReconciliationTest` — idempotency keys, faked RazorpayX create (asserts paise + idempotency header), failure path, reconcile job, release command, webhook signature reject/accept

### Housekeeping
- `.gitignore` expanded (storage, uploads, `.phpunit.cache`, `auth.json`, …) + `.gitkeep`s; `storage/framework/cache/data` created
- `composer.json` gained `autoload-dev` for `Tests\`
- `.env.example` gained the `RAZORPAYX_*` block

---

## 🔜 Still open

1. **Run it.** Nothing here has been executed — expect to fix a typo or two on first `phpunit` run.
2. **Tax & invoices** — GST calculation, invoice numbering series, PDF, HSN/SAC, TDS 194-O on payouts. Not started.
3. **Real-time** — order chat is still a POST + reload; the admin order page's chat panel is still static markup. Shared hosting rules out Reverb/Redis, so SSE or 15s polling.
4. **i18n** — no `lang/` directory, no `__()`; Hindi would be the first locale.
5. **2FA** — rate limits and reset are in; TOTP enrolment/challenge is not.
6. **Email verification** — `users.email_verified_at` exists but no flow is wired.
7. **Live RazorpayX run** — the client, webhook and reconciliation exist but have only ever been exercised against `Http::fake()`. Needs real test-mode keys and one end-to-end payout.
8. **`OrderController` (public) fallbacks** — still returns dummy `UNJ-####` orders when the service lookup fails; `BusinessController`/`CreatorController` still have `fallbackCreators()`-style demo arrays for a fresh install. Now that seeding exists, these can go.
9. **Custom error pages** — `withExceptions()` in `bootstrap/app.php` is still empty.
