# QuickContent — Build Status & Audit

_Audit date: 2026-09-25 · Branch: `arena/01a0d7f6-quickgig` · Base commit: `9122ffc`_

> **Note on history:** work described in an earlier session (commit `e297021`) is **not present** in this
> checkout or on `origin` — the tree contains only the base `9122ffc Setup Laravel project` commit.
> This file was rebuilt from a fresh audit of the code that is actually on disk. No code was changed
> in this pass.

---

## 1. Blockers — the app cannot run as-is

| # | Issue | Evidence | Impact |
|---|-------|----------|--------|
| B1 | **Missing controller class `App\Http\Controllers\ServiceController`** | `routes/web.php` registers `GET /services/{service}` → `ServiceController@show`; the file does not exist | Fatal `BindingResolutionException` on every public service page. Also no `resources/views/services/` directory. |
| B2 | **Missing view `creator.order`** | `CreatorController` returns `view('creator.order')`; `resources/views/creator/order.blade.php` absent | `GET /creator/orders/{order}` throws `InvalidArgumentException: View [creator.order] not found` |
| B3 | **No seeders / factories** | `database/seeders` and `database/factories` do not exist | Nobody can log in. The login page advertises `admin@quickcontent.in / Admin@12345` etc., but no such users are ever created → the entire `/admin` area is unreachable. |
| B4 | **No `vendor/`, no PHP toolchain in the sandbox** | `php: command not found`, no `composer install` run | Cannot boot, migrate, or run any automated verification locally. |
| B5 | **No `settings` table** | Migration list has no `settings`; `Admin\SettingController::update()` is `// TODO: Setting::updateOrCreate(...)` | Saving settings silently does nothing — returns a success toast on a no-op. |

**Tables that do exist:** `companies`, `users`, `password_reset_tokens`, `sessions`, `jobs`,
`job_batches`, `failed_jobs`, `cache`, `cache_locks`, `creators`, `services`, `orders`,
`portfolio_items`, `faqs`, `blog_categories`, `blogs`.

---

## 2. Screens still backed by hardcoded mock data

These controllers build in-memory `collect([...])` arrays and never touch Eloquent. All of them ship
fake Indian names, `@quickcontent.in` emails, `i.pravatar.cc` avatars and pre-baked ₹ amounts.

| Controller | LOC | DB calls | What's faked |
|---|---|---|---|
| `Admin\DashboardController` | 38 | 0 | Stat cards (1,247 orders / ₹8.4L), recent orders, payout queue. Comment in code: _"In production replace with Eloquent counts"_ |
| `Admin\CompanyController` | 22 | 0 | 4 hardcoded companies + in-memory search |
| `Admin\PayoutController` | 31 | 0 | 4 payout rows; `markPaid()` / `hold()` just `return back()->with('toast', ...)` — no state change, no RazorpayX call |
| `Admin\SettingController` | 35 | 0 | Settings array literal; update is a TODO |
| `Admin\OrderController` | 72 | 1 | `allOrders()` returns 6 fixture rows (QC-1824…QC-1829); index/show/filters all read the fixture |
| `Admin\UserController` | 58 | 1 | 5 fixture users with `@quickcontent.in` emails |
| `Admin\CreatorController` | 117 | 9 | Partially real; still falls back to a 4-row fixture list |

**Already wired to the DB (working):** `BusinessController` (209 LOC), `CreatorController` (185),
`OnboardingController` (185), `OrderController` (149), `Admin\ServiceController` (152),
`Admin\BlogController` (139), `LandingController`, `BlogController`, `SitemapController`,
`Admin\FaqController`.

---

## 3. Feature gaps (the "still open" list, re-verified)

1. **Payments — RazorpayX.** No client, service class, job, webhook route or signature verification
   exists anywhere in `app/`. The only references are the strings `RazorpayX`/`rzp_live_xxx` inside
   toast messages and the settings fixture. `.env.example` declares `RAZORPAY_KEY`,
   `RAZORPAY_SECRET`, `RAZORPAY_WEBHOOK_SECRET` — nothing reads them.
   _Also needed:_ escrow release on approval, payout reconciliation polling, idempotency keys,
   a `payouts` / `transactions` table (neither exists).
2. **File uploads for deliveries.** Only blog images upload (`admin/blogs/upload` →
   `storage/app/public/blogs`). `creator.deliver` takes no file. No size/MIME validation, no
   virus/extension allowlist, no signed download URLs, no `storage:link` documented.
3. **Test coverage.** There is **no `tests/` directory at all** and no `phpunit.xml`, despite
   `phpunit/phpunit ^11` being in `require-dev`. Zero tests for the board or admin screens.
4. **Queue worker & scheduler.** `jobs`/`failed_jobs` migrations exist and `QUEUE_CONNECTION=database`
   is set, but there are **no Job classes**, `routes/console.php` is 4 lines (empty), and there is no
   scheduler entry or Hostinger cron documentation.
5. **Auth hardening.** Login/logout only. Missing: registration, email verification (`users` has no
   verification flow wired), password reset (table exists, no controller/route/mail), 2FA,
   `throttle` middleware on `POST /login`, password policy, session fixation beyond `regenerate()`.
   Login view also ships **live demo credentials in plaintext** — must be removed before production.
6. **Tax & invoices.** Nothing: no GST calculation, no invoice model/PDF, no HSN/SAC codes, no
   TDS 194-O handling for creator payouts, no invoice numbering series.
7. **Real-time.** `BROADCAST_DRIVER=log`. Order chat (`orders/{order}/message`) is POST + full page
   reload; no polling, SSE, or websockets. Shared hosting rules out Redis/Reverb — SSE or short
   polling is the realistic path.
8. **i18n.** No `lang/` directory, no `__()` calls; all copy is hardcoded English + `₹` in Blade.
   Hindi is the obvious first locale for the target market.

---

## 4. Smaller findings

- `config/` directory does **not** exist. Laravel 11 tolerates this, but it means no
  `config/services.php` to hold Razorpay keys and no way to `php artisan config:cache` custom values.
- `bootstrap/app.php` has an empty `withExceptions()` — no custom 404/500 pages for a production app.
- `storage/framework/cache/` is missing (only `sessions/` and `views/` exist) while `CACHE_STORE=file`.
- `.gitignore` is 3 lines and has no trailing newline; missing `/storage/*.key`, `/public/storage`,
  `.env.backup`, `/public/uploads/*`, `.phpunit.result.cache`.
- `.env.example` sets `APP_ENV=production` / `APP_DEBUG=false` as the default copy target — fine for
  Hostinger, hostile for local dev. Ship a `.env.example.local` or document the override.
- Hardcoded `support@quickcontent.in` / `creators@quickcontent.in` / `+91 98765 43210` in
  `onboarding/business`, `onboarding/creator`, `admin/layout`, `Admin\SettingController`.
- `GET /creator/{id}` is a closure in `routes/web.php` doing `Creator::findOrFail()` — blocks
  `route:cache` cleanliness and belongs in the controller.
- Two health endpoints: `/health` (custom JSON) and `/up` (framework, via `withRouting(health:)`).
- Public disk is `FILESYSTEM_DISK=public` with a `public/uploads` directory committed — confirm
  whether uploads go through `storage:link` or straight into `public/`.

### Verified clean ✅
- **No broken `route()` names.** Every route helper used across all 39 Blade files resolves to a
  defined named route.
- **No missing admin views.** All 16 `view('admin.*')` targets exist.
- All 9 models have matching migrations.

---

## 5. Suggested order of work

1. B1–B3 (missing controller, missing view, seeders) — without these the app doesn't demo.
2. Replace the 4 fully-mocked admin controllers with Eloquent queries; drop `@quickcontent.in`.
3. `phpunit.xml` + feature tests covering every admin route (smoke: 200/302/403 by role).
4. Auth hardening + remove demo credentials from the login page.
5. `settings` table & real Setting model.
6. RazorpayX service + webhook + reconciliation job, then queue worker/cron docs.
7. Tax/invoices → delivery uploads → real-time → i18n.
