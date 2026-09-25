# Quick GIGS

**Hire a verified pro in minutes, not weeks.**

Quick GIGS is a fast, escrow-protected freelance marketplace built on Laravel 11: post a brief or buy
a fixed-price gig, get matched to a verified freelancer automatically, track production live, and
release payment only when you approve.

Categories span video and editing, design and brand, content and copy, web and app development,
AI and automation, marketing, UGC and voice over.

No build step required — Tailwind, Alpine and the editor load from CDN, cache and sessions use
file/database drivers, and the queue runs on the database connection.

---

## Quick start

```bash
cp .env.example .env

# zero-config local database
touch database/database.sqlite
# then set in .env:
#   DB_CONNECTION=sqlite
#   DB_DATABASE=/absolute/path/to/database/database.sqlite

composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

> Running through PHP's built-in server? Use the dev router so static files are not swallowed by
> Laravel: `php -S 0.0.0.0:8080 -t public scripts/dev/router.php`

Open <http://localhost:8000>.

### Demo accounts (created by the seeder)

| Role     | Email                    | Password       | Lands on      |
|----------|--------------------------|----------------|---------------|
| Business | `business@quickgigs.in`  | `Business@123` | `/business`   |
| Creator  | `creator@quickgigs.in`   | `Creator@123`  | `/creator`    |
| Admin    | `admin@quickgigs.in`     | `Admin@12345`  | `/admin`      |

Extra staff logins: `manager@quickgigs.in` / `Manager@123`, `support@quickgigs.in` / `Support@123`,
`finance@quickgigs.in` / `Finance@123`.

---

## What's in the box

| Area | Route | Notes |
|---|---|---|
| Landing | `/` | Hero that **generates a real brief in under a second** in front of the visitor (local composer, timed on screen), isometric artwork, how it works, **interactive demo simulation**, marketplace preview, creators, pricing (pay-per-gig / retainer toggle), testimonials, FAQ, CTA. |
| Marketplace | `/marketplace` | Real search, category filters, sort, 24-hour filter, pagination, "available now" rail. |
| Gig detail | `/gigs/{id}` | Speed lanes (express / standard / relaxed) with live price + fee split, brief form, creator card, related gigs. |
| Sign up | `/register` | One flow, two account types (business or creator). Creates the user **and** the company/creator profile, then logs you in. |
| Log in | `/login` | Role-aware redirect, one-tap demo account fill. |
| Business dashboard | `/business` | Spend/escrow stats, task-board snapshot, natural-language task capture, gig list, recommendations. |
| **Task board** | `/business/board` | Five-column board (queued → assigned → production → review → delivered). Create tasks from one sentence, auto-assignment, priorities, due dates, monthly credits, and one-click conversion into an escrow-backed order. Every action is inline JSON — no reloads. |
| For business | `/for-business` | B2B landing: live board demo, hiring-vs-subscription calculator, category grid, workspace features, plan tiers (Starter/Growth/Scale) and a demo request form. |
| Order tracking | `/orders/{uid}` | Five-stage pipeline, **Advance demo pipeline** button, approve-and-release escrow, messages. |
| Creator studio | `/creator` | Availability toggle, assigned gigs, deliver flow, earnings, profile-strength meter. |
| Profiles | `/business/profile`, `/creator/profile`, `/creators/{id}` | Editable and persisted, with portfolio CRUD for creators. |
| Insights | `/blog`, `/blog/{slug}` | Article + FAQ JSON-LD, categories, search, and a Quick answers accordion on both the index and every post. |
| FAQ | `/faq` | Every published answer grouped by category, with FAQPage structured data. |
| Admin | `/admin` | Orders, freelancers, companies, leads, **skills**, services, blogs, FAQs, users & roles, payouts, settings. |
| SEO | `/sitemap.xml`, `/robots.txt` | Organization, FAQPage, BreadcrumbList and BlogPosting JSON-LD via `components/seo.blade.php`. |

### Differentiators (and where they live in the code)

| USP | Page | Implementation |
|---|---|---|
| **Brief engine** — one line becomes hooks, a timed beat sheet, deliverables, spec and QA gate | `/brief-builder` | `app/Services/BriefComposer.php` — deterministic, template/rule based, no API key. `compose()` is the single extension point if you want to hand the draft to an LLM. |
| **Explainable matching** — score out of 100 with five weighted, published factors | `/ai-engine` | `app/Services/MatchEngine.php` — skill fit, availability, reliability, rating, budget fit. Re-weighted live on the page. |
| **Automated QA gate** — six checks before a delivery reaches the buyer | `/ai-engine#m02` | Gate definition lives with the brief (`BriefComposer::qaGate()`); the page runs an interactive simulation of it. |
| **Revision translator** — vague feedback becomes timestamped editor notes | `/ai-engine#m03` | Rule table in the page's Alpine component; mirrors what the order chat sends to creators. |
| **Auto-repurpose** — one master forks into the formats you publish | `/how-it-works` | Stage 07 of the pipeline defined in `PageController::pipeline()`. |
| **Escrow with SLA credit** — no commitment fee, refundable until approval | `/pricing` | `OrderController` holds `escrow_status` and releases only on approve. |

Positioning versus quick-commerce gig apps, managed agencies and bidding marketplaces is documented in
`PageController::comparison()` and rendered on `/compare` — including three cases where we tell people
to hire someone else.

### Marketing & tool pages

`/how-it-works` · `/ai-engine` · `/brief-builder` · `/pricing` · `/compare` · `/for-creators` ·
`/enterprise` (lead form) · `/about` · `/contact` (lead form). Enquiries are stored in the `leads`
table and reviewed at `/admin/leads`.

### Skill library

Skills are curated by admin at `/admin/skills` (add, bulk add, rename, re-file under a discipline,
hide or delete, with live usage counts). Freelancers pick from that list with a searchable
multi-select — `resources/views/partials/skill-picker.blade.php` — on sign-up (max 8) and in their
profile (max 12). Selections are stored on `creators.skills` as a JSON array of names, so the match
engine and every existing view keep working. Hiding a skill removes it from the picker but leaves it
on profiles that already use it.

### Business plans

`Company::TIERS` defines the subscription tiers used by the panel and `/for-business`:

| Tier | Price | Task credits | Seats |
|---|---|---|---|
| Pay as you go | — | per gig | 2 |
| Starter | ₹24,999 / mo | 8 | 3 |
| Growth | ₹54,999 / mo | 20 | 8 |
| Scale | ₹99,999 / mo | 45 | 20 |

Credits are consumed when a board task is converted into an order; the remaining balance shows in
the board header and on the dashboard.

### Money flow

* Gig price is set per service; speed lanes multiply it (`express ×1.6`, `standard ×1`, `relaxed ×0.85`).
* Platform fee is a flat **10%** — creators keep 90%.
* Funds are marked `held` in escrow on order, and `released` when the buyer approves.

---

## Configuration from the admin console

Super admins set everything at **`/admin/settings`** — no redeploy needed. Secrets are encrypted with the
app key before they hit the database and are only ever shown masked (`sk-or-••••••••3f2a`), with a
**Test connection** button per provider.

| Group | Keys |
|---|---|
| Platform | fee %, escrow hold hours, support email/phone, maintenance banner |
| Payments | Razorpay key id, key secret, webhook secret |
| AI | preferred provider (auto / OpenRouter / OpenAI / Gemini / off) plus a key and model for each |

Values fall back to `.env`, so an env-only deployment keeps working unchanged.

## Payments

Razorpay is wired end to end: ordering creates a gateway order, the gig page opens Checkout, the callback
signature is verified server-side before anything is marked paid, and `POST /webhooks/razorpay` accepts
signed `payment.captured` / `payment.failed` / `refund.processed` events (CSRF-exempt, signature-checked).

**With no keys configured the platform runs in demo mode** — orders are marked held so the whole flow stays
demonstrable. Note that payouts to freelancers are not automated yet; approval releases escrow and records a
payout reference. See `docs/status.md`.

## AI (optional)

Every AI feature has a deterministic engine behind it. Without a key the product works exactly as
before — it just stops calling a model. With a key, the model writes and the deterministic result
becomes the fallback and the validator.

```env
OPENROUTER_API_KEY=sk-or-...            # empty = deterministic mode
OPENROUTER_MODEL="nvidia/nemotron-3.5-lightning:free"
OPENROUTER_REASONING=true               # ask for reasoning and replay it on follow-ups
OPENROUTER_TEMPERATURE=0.4
OPENROUTER_MAX_TOKENS=1600
OPENROUTER_TIMEOUT=45
OPENROUTER_RETRIES=2                    # transport errors, 429 and 5xx only
OPENROUTER_LOG=false
```

| Feature | With a key | Without a key |
|---|---|---|
| `/brief-builder` | Model rewrites title, summary, hooks, beats and "do not" — validated field by field, everything else (pricing, QA gate, confidence) stays rule-based | Full brief from `BriefComposer` |
| Brief **Refine** box | Second call replays the assistant turn *including* `reasoning_details`, so the model continues its earlier reasoning | Box is hidden |
| Dashboard **Describe a task** | Model returns `{title, start_date, end_date, description, client}` | Regex + Carbon parser returns the same shape |
| `POST /tasks/parse` (JSON) | Same, as an API | Same, as an API |

### Code map

| File | Role |
|---|---|
| `app/Services/Ai/OpenRouterClient.php` | HTTP client. Returns `reasoning_details` untouched, exposes `assistantTurn()` for replaying it, `extractJson()` for fenced/prose-wrapped JSON. Every failure becomes `AiUnavailable`. |
| `app/Services/Ai/BriefWriter.php` | Model-written briefs on top of `BriefComposer`, with per-field validation and merge. |
| `app/Services/Ai/TaskParser.php` | Sentence → `{title, start_date, end_date, description, client}`, model-first with a Carbon/regex fallback. Flags past or inverted dates instead of silently rewriting them. |
| `app/Services/Ai/AiManager.php` | Picks the active provider (admin setting → first configured → none) and exposes one `chat()` for every service. |
| `app/Services/Ai/Providers/*` | OpenRouter, OpenAI and Gemini clients behind one interface. |
| `app/Services/Payments/RazorpayGateway.php` | Order creation, signature verification, webhooks, refunds, credential ping. |
| `scripts/openrouter_reasoning_example.py` | The same reasoning round-trip in ~60 lines of Python. |

### Verify it

```bash
php artisan ai:ping                       # model, latency, tokens, whether reasoning came back
php artisan ai:task "create task to create mobile app, delivery date is 29 aug 2026"
php artisan ai:task "..." --refine="Are you sure about the year?"   # reasoning continues
php artisan ai:task "..." --json          # just the JSON object
```

```bash
curl -X POST https://your-app.test/tasks/parse \
  -H "Accept: application/json" -H "X-CSRF-TOKEN: ..." \
  --data-urlencode 'prompt=5 instagram reels for client Nova Foods in 2 weeks'
# {"task":{"title":"5 instagram reels","start_date":"…","end_date":"…","description":"…","client":"Nova Foods"},
#  "meta":{"source":"rules","model":null,"error":null,"suggestion":"https://…/gigs/1"}}
```

Failure behaviour is tested against a mock provider: a 401 falls back on the first attempt, a 503 is
retried then falls back, and a non-JSON completion falls back with the reason recorded — the user
always gets a brief.

---

## Design system

* **Black, white and one mint accent** (`#00C48C`). White is the default surface, near-black
  (`#0A0A0B`) carries type and the few deliberately dark bands, mint marks the AI moments, links and
  success states. Amber and rose appear only as warning/danger status.
* Light by default; `.band-dark` opts a section into black (hero demo, delivered strip, newsletter,
  final CTA, footer) and re-tones glass, muted text, borders, inputs and buttons inside it.
* **Isometric line illustrations** in `public/img/iso-*.png` (hero, brief, match, deliver, board) —
  monochrome with a single mint accent, matching the flat-vector look of the rest of the UI.
* **`public/img/how-it-works.gif`** — a five-frame animated walkthrough of the pipeline, generated
  from the same isometric art (`scripts/dev/make-gif.py`). Re-run it if the steps change.
* Semantic text/surface helpers instead of theme-specific opacity classes: `.text-body`,
  `.text-faint`, `.bg-tint`, `.border-line`.
* Catalogue cards follow quick-commerce conventions: image-led, MRP strike-through, “% off” badge,
  delivery chip and rating — colour comes from the thumbnails, not the chrome.
* Type: **Space Grotesk** for display, **Inter** for body — semibold headings instead of heavy black weights.
* Shared shell: `resources/views/layouts/site.blade.php` with `partials/nav` (four links + one CTA) and `partials/footer`.

---

## Competitor audit

`docs/competitor-audit.md` captures the September 2026 review of Unjob.ai (incl. business.unjob.ai),
Elyvato and the wider field (Fiverr, Upwork, Superside, Design Pickle, Awesomic, Contra, Toptal) —
their models, design language, weaknesses, and the gaps Quick GIGS targets.

## Deployment notes

* Requires PHP 8.2+, any MySQL/MariaDB or SQLite database.
* `php artisan migrate --force && php artisan db:seed --force` on first deploy (seeding is optional in production).
* Point the web root at `public/`. An `.htaccess` is included for Apache-style hosts.
* Queue worker (cron every minute is fine):
  `* * * * * cd /path/to/app && php artisan queue:work --stop-when-empty >> /dev/null 2>&1`
* File uploads go to `storage/app/public` (run `storage:link`), with a `public/uploads` fallback when
  symlinks are not available.
