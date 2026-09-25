# Build status — what is done, what is pending

Last reviewed: 25 September 2026.

## Working end to end

| Area | State |
|---|---|
| Public site | Landing, marketplace + gig pages, how it works, AI engine, brief builder, pricing, compare, for business, for talent, enterprise, about, contact, FAQ, insights + articles, sitemap, robots |
| Accounts | Register (business / talent), login, logout, role-aware redirects, seeded demo logins |
| Marketplace | Search, category filters, sort, 24-hour filter, pagination, MRP + % off, gig detail with speed lanes and live fee split |
| Orders | Create → escrow held → advance pipeline → approve → release, inline (no reloads), messages, creator delivery |
| Business panel | Dashboard, task board (create from one line, move, re-prioritise, convert to escrow order, delete), workspace settings, subscription credits |
| Talent panel | Studio, assigned gigs, deliver, availability toggle, profile with admin-managed skill picker, portfolio CRUD |
| Admin | Dashboard, orders, freelancers, companies, services, leads, skills, blogs, FAQs, users & roles, payouts, **settings (payments + AI keys)** |
| AI layer | Brief writer, explainable matching, QA gate, revision translator, task parser, repurposing — deterministic by default, LLM when a key is set |
| Payments | Razorpay checkout, signature verification, webhooks, refund call, demo-escrow fallback |
| SEO | Per-page meta, Organization / FAQPage / BlogPosting / Breadcrumb JSON-LD, sitemap |
| Dev tooling | `scripts/dev/` — offline bootstrap, contrast audit, Alpine audit, brief-flow check, skill-picker check, GIF generator, static router |

## Recently closed

* **Payout ledger** — `payouts` table with a real queue. Approving an order raises exactly one payout
  (idempotent), held for `platform.escrow_hours`, addressed to the freelancer's UPI. `/admin/payouts`
  filters by open/ready/paid/failed, shows held vs ready vs paid-this-month vs lifetime fees, and each row
  can be paid (RazorpayX when the funding account is set, otherwise a manual UTR), held, or retried after a
  failure. The freelancer studio shows the same ledger.
* **Transactional email** — order placed (buyer + freelancer), delivery submitted (buyer), approval and
  payout released (freelancer), dispatched through `App\Support\Notifier`, which swallows mail failures so a
  checkout can never fail because of SMTP.
* **Automated tests** — 22 PHPUnit tests, 73 assertions, covering the money flow, fee settings, payout
  idempotency and hold window, authorisation boundaries, Razorpay signatures and webhooks, settings
  encryption, provider selection, the brief engine, match ranking and the skill library.
  Run with `php artisan test` (or `vendor/bin/phpunit`).

* **Payout reconciliation** — `RazorpayGateway::fetchPayout()` + a state map (`processed` → paid,
  `reversed|cancelled|rejected|failed` → failed, everything else stays processing). `App\Jobs\ReconcilePayout`
  re-checks a payout with a widening backoff (5 min → 4 h, ten rounds) until it settles, writes the UTR or the
  failure reason back to the ledger and emails the freelancer on success. `php artisan payouts:reconcile`
  (`--sync`, `--dry-run`) sweeps everything still in flight; scheduled every 30 minutes.
* **Scheduler + worker** — `routes/console.php` now drives everything from the single
  `* * * * * php artisan schedule:run` cron: payout reconciliation, a `queue:work --stop-when-empty --max-time=55`
  pass every minute (no supervisor needed on shared hosting), `subscriptions:roll` at 00:10 to zero used credits
  and advance `renews_on`, `skills:recount` at 02:00, and weekly failed-job pruning.
* **Auth hardening (partial)** — working password reset (`/forgot-password`, `/reset-password/{token}`, two new
  pages in the existing site style, identical response whether or not the address exists), per-email+IP login
  throttling (5 tries / 5 min) plus route throttles on login, register, reset, the public brief builder and the
  contact form, and an `audit_logs` table recording every state-changing admin request (actor, role, route,
  scrubbed payload, status, IP) via the `audit` middleware. Secrets are masked before they are stored.
* **Test coverage** — three new suites: payout reconciliation (7), task board + marketplace filters (16),
  brief-builder endpoints + admin CRUD + audit trail (15).

## Pending — ordered by how much it matters

### 1. Payout execution against a real bank
Reconciliation is built and covered by tests against faked RazorpayX responses, but nothing has yet been run
against live RazorpayX credentials with real money, and there is no partial-payout or bulk-transfer handling.

### 2. File delivery
Deliveries are a URL in a text field. Real uploads (S3 or local disk), virus scanning, expiring links and
version history are not built. The QA gate therefore scores a description of the file, not the file.

### 3. Test coverage gaps
The board, marketplace, brief-builder and admin CRUD suites are written but **have never been executed** — no
PHP runtime was available in the environment they were authored in. Run `php artisan test` and expect small
fixture corrections. Still untested beyond that: the order messaging thread and the creator studio.

### 4. Queue + scheduler in production
Scheduled and documented, but not yet verified on the Hostinger box: the cron entry has to be added in hPanel
and `payouts.log` watched for a cycle. SLA-breach checks and digest emails are still not written.

### 5. Auth hardening
Remaining: email verification on signup, 2FA for admin accounts, and a UI for reading the new `audit_logs`
table (rows are written, nothing displays them yet).

### 6. Multi-currency and tax
Everything is INR and GST is mentioned but not calculated. Invoice PDFs are not generated.

### 7. Real-time
Order tracking and chat poll on page load only. Broadcasting (Reverb/Pusher) would make the live pipeline
genuinely live.

### 8. Content operations
Blog editor works, but no scheduled publishing, no image optimisation pipeline, and the sitemap is generated
per request rather than cached.

### 9. Accessibility and i18n
Keyboard traps in a few Alpine menus have not been audited, focus states are default, and all copy is
hard-coded English.

## Configuration reference

Everything below can be set in **`/admin/settings`** (super admin only) and falls back to `.env`:

| Setting | Purpose |
|---|---|
| `platform.fee_percent` | Platform commission, used by orders and board conversions |
| `platform.escrow_hours` | Auto-approve window |
| `payments.razorpay.key_id` / `key_secret` / `webhook_secret` | Live checkout; empty = demo escrow |
| `ai.default_provider` | `auto`, `openrouter`, `openai`, `gemini`, or `none` |
| `ai.openrouter.key` / `ai.openai.key` / `ai.gemini.key` + `.model` | Model access; empty = deterministic engines |

Secrets are encrypted with the app key before storage and only ever shown masked.
Razorpay webhook endpoint: `POST /webhooks/razorpay` — subscribe to `payment.captured`,
`payment.failed` and `refund.processed`.
