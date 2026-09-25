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

## Pending — ordered by how much it matters

### 1. Money movement beyond capture
Escrow capture is real; **payouts are not**. Approving an order marks the escrow released and records a
`payout_reference`, but no money leaves the platform account. Needs RazorpayX (or bank transfer ops) plus
a payout ledger, retry handling and reconciliation. `/admin/payouts` is currently a view over orders, not a
real payout queue.

### 2. Notifications
No email or WhatsApp yet. Nothing tells a freelancer they were matched, a buyer that a delivery landed, or
finance that a payout is due. Mail is configured (`MAIL_MAILER=log`) but no Mailables or notifications exist.
Highest-value additions: order placed, gig assigned, delivered for review, approval + payout, SLA breach.

### 3. File delivery
Deliveries are a URL in a text field. Real uploads (S3 or local disk), virus scanning, expiring links and
version history are not built. The QA gate therefore scores a description of the file, not the file.

### 4. Automated tests
There is no PHPUnit suite — `phpunit` is in `require-dev` but no tests exist. The JS/contrast/flow auditors in
`scripts/dev/` cover regressions we actually hit, but controller and service unit tests are missing.

### 5. Queue + scheduler in production
`QUEUE_CONNECTION=database` with no worker documented beyond a cron line, and no scheduled jobs (SLA checks,
credit resets on renewal date, digest emails, skill usage recount).

### 6. Auth hardening
No email verification, password reset is a dead link, no 2FA for admins, no rate limiting on login or the
public brief builder, no audit log of admin actions.

### 7. Multi-currency and tax
Everything is INR and GST is mentioned but not calculated. Invoice PDFs are not generated.

### 8. Real-time
Order tracking and chat poll on page load only. Broadcasting (Reverb/Pusher) would make the live pipeline
genuinely live.

### 9. Content operations
Blog editor works, but no scheduled publishing, no image optimisation pipeline, and the sitemap is generated
per request rather than cached.

### 10. Accessibility and i18n
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
