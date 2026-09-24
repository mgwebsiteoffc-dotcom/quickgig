# Quick GIGS

**Hire a verified pro in minutes, not weeks.**

Quick GIGS is a fast, escrow-protected gig marketplace built on Laravel 11: post a brief or buy a
fixed-price gig, get matched to a verified creator automatically, track production live, and release
payment only when you approve.

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
| Landing | `/` | Hero with a live order pipeline, how it works, **interactive demo simulation**, marketplace preview, creators, pricing (pay-per-gig / retainer toggle), testimonials, FAQ, CTA. |
| Marketplace | `/marketplace` | Real search, category filters, sort, 24-hour filter, pagination, "available now" rail. |
| Gig detail | `/gigs/{id}` | Speed lanes (express / standard / relaxed) with live price + fee split, brief form, creator card, related gigs. |
| Sign up | `/register` | One flow, two account types (business or creator). Creates the user **and** the company/creator profile, then logs you in. |
| Log in | `/login` | Role-aware redirect, one-tap demo account fill. |
| Business dashboard | `/business` | Spend/escrow stats, gig list with progress, recommendations, creators online. |
| Order tracking | `/orders/{uid}` | Five-stage pipeline, **Advance demo pipeline** button, approve-and-release escrow, messages. |
| Creator studio | `/creator` | Availability toggle, assigned gigs, deliver flow, earnings, profile-strength meter. |
| Profiles | `/business/profile`, `/creator/profile`, `/creators/{id}` | Editable and persisted, with portfolio CRUD for creators. |
| Insights | `/blog`, `/blog/{slug}` | Article + FAQ JSON-LD, categories, search. |
| Admin | `/admin` | Orders, creators, companies, **leads**, services, blogs, FAQs, users & roles, payouts, settings. |
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

### Money flow

* Gig price is set per service; speed lanes multiply it (`express ×1.6`, `standard ×1`, `relaxed ×0.85`).
* Platform fee is a flat **10%** — creators keep 90%.
* Funds are marked `held` in escrow on order, and `released` when the buyer approves.

---

## Design system

* Dark, high-contrast UI with an aurora/grid backdrop, glass surfaces and a violet → cyan accent ramp.
* Type: **Space Grotesk** for display, **Inter** for body — semibold headings instead of heavy black weights.
* Shared shell: `resources/views/layouts/site.blade.php` with `partials/nav` (four links + one CTA) and `partials/footer`.

---

## Deployment notes

* Requires PHP 8.2+, any MySQL/MariaDB or SQLite database.
* `php artisan migrate --force && php artisan db:seed --force` on first deploy (seeding is optional in production).
* Point the web root at `public/`. An `.htaccess` is included for Apache-style hosts.
* Queue worker (cron every minute is fine):
  `* * * * * cd /path/to/app && php artisan queue:work --stop-when-empty >> /dev/null 2>&1`
* File uploads go to `storage/app/public` (run `storage:link`), with a `public/uploads` fallback when
  symlinks are not available.
