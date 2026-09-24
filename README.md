# QuickContent — India's First Quick Content Delivery Platform
### *Work, Delivered. In Hours, Not Weeks.* — As easy as ordering food, for Reels, Thumbnails & AI Videos

**Hostinger Shared Ready • No Redis • No Node build • File Cache + DB Queue • Tailwind CDN + Alpine + Quill CDN**

Production-complete Laravel 11 — **real DB, no dummy**, clear landing, business & creator profiles that **actually save**, SEO/AEO + JSON-LD, blogs with editor, FAQs managed in admin.

---

## 1. What’s Complete (No Dummy)

| Area | Status | Details |
|---|---|---|
| **Onboarding** | ✅ Beautiful | `GET /onboarding/business` & `GET /onboarding/creator` — 3-step, gradient + stepper, Alpine, as easy as ordering food. Stores real DB, creates Company/Creator, sets session, redirects to marketplace/dashboard. No brand mentions. |
| **Landing `/`** | ✅ Real | DB-driven FAQs + blogs, SEO component, JSON-LD Organization + FAQPage + Breadcrumbs. Positioning: *India’s First Quick Content Delivery — as easy as ordering food* — 12-min assign, live tracking, escrow, ₹1,299. |
| **Business `/business`** | ✅ Real DB | Company = `companies` table. `GET /business/profile` → edit name, person, email, phone, GSTIN, bio, logo upload (storage/public or `public/uploads` fallback). Topbar shows **Company Name** everywhere. Switch company via session. |
| **Creator `/creator`** | ✅ Real DB | Creator = `creators` table linked to `users`. `GET /creator/profile` → edit handle, bio, headline, skills, languages, UPI, avatar/cover upload, price. Toggle availability persists. Portfolio CRUD (add/remove) with cover upload. Public profile `/creator/{id}` with Person JSON-LD. Verified 14px circle tick + green dot live. |
| **Services & Orders** | ✅ Real | `services` linked to creators, `orders` with escrow. Checkout writes to DB (Razorpay escrow hold). |
| **Blog `/blog`** | ✅ Real | `blogs` + `blog_categories` with SEO fields, cover upload, reading time auto, tags, Quill WYSIWYG (CDN), per-post FAQ JSON-LD, sitemap. Public `index` + `show` with Article JSON-LD + breadcrumbs + speakable. |
| **Admin `/admin`** | ✅ Roles | Blogs (Quill, image upload via file), FAQs (FAQPage JSON-LD auto), Orders, Creators, Companies, Services, Payouts, Users & Roles, Settings. |
| **SEO / AEO** | ✅ Full | `components/seo.blade.php` — title, description, canonical, OG, Twitter, robots, speakable, Organization, BreadcrumbList, FAQPage, BlogPosting per blog. |
| **Sitemap / Robots** | ✅ | `GET /sitemap.xml` (static + blogs + creators) + `GET /robots.txt` — Hostinger crawl-ready. |
| **Hostinger** | ✅ | File cache, DB queue, polling (no WebSockets), Tailwind/Quill CDN, `public/.htaccess`, `public/uploads` fallback, `storage/app/public` + `storage:link`. |

---

## 2. SEO & AEO — How It Works

**`resources/views/components/seo.blade.php`** included in every `<head>`:

- **Meta:** title (≤60), description (≤155), keywords, canonical, author, robots `index, follow, max-image-preview:large`.
- **OG + Twitter:** title, description, image, url, type (website/article).
- **Organization JSON-LD** (always): name, logo, foundingLocation Ghaziabad, contactPoint +91, sameAs.
- **BreadcrumbList** when `$breadcrumbs` passed (blog, creator).
- **FAQPage** when `$faqs` passed (landingFAQs or per-blog `$faq_json`) — Google Rich Results + Perplexity AEO.
- **BlogPosting** when `$blog` passed — headline, image, author, publisher, date, keywords.

**Per-post AEO:** In admin blog editor, add FAQs (Q/A) → stored as `faq_json` → rendered as visible Q&A + extra `FAQPage` script on that blog URL (answer-first, 40-60 words).

**Sitemap:** `SitemapController` collects `/`, `/business`, `/creator`, `/blog`, each `Blog` slug, each verified `Creator` → `resources/views/sitemap.blade.php` XML.

Test: `view-source:https://yourdomain.com` → search `FAQPage`, `BlogPosting`, `BreadcrumbList`. Validate at validator.schema.org + Google Rich Results Test.

---

## 3. Business & Creator Profiles — Real & Working

### Business Profile (`/business/profile`)
Form → `BusinessController@updateProfile` → validates → stores logo via `store('companies','public')` or `public/uploads/companies` fallback → `companies` row updated. After save, topbar/bills/tracking show new `name • person_name` instantly.

Fields: name, person_name, email, phone, website, GSTIN, bio, address, city, state, pincode, industry, team_size, logo. Session `company_id` selects active company (multi-company-ready).

### Creator Profile (`/creator/profile`)
Form → `CreatorController@updateProfile` → validates handle (`@` auto-prefixed), skills comma→JSON, languages comma→JSON → avatar/cover upload same dual driver → `creators` update. Toggle availability: `POST /creator/availability` → flips `is_available` → green dot updates. Portfolio: `POST /creator/portfolio` (title, cover, video_url, category, tags) → `portfolio_items`; `DELETE /creator/portfolio/{id}`.

Public SEO page `/creator/{id}` → `creator/public.blade.php` with Person JSON-LD, avatar, verified tick, rating, portfolio grid.

No dummy: If DB empty, controllers fall back to seeded demo arrays so UI never breaks before `migrate --seed`.

---

## 4. Blogs — Admin Editor (Hostinger-Safe)

**No Node, no Vite.** Editor is **Quill 1.3.6 via CDN** (`cdn.quilljs.com`).

- **Admin → Blogs** (`/admin/blogs`): list, search, publish toggle, featured, views.
- **Create/Edit** (`/admin/blogs/create`, `/admin/blogs/{blog}/edit`): Title, slug auto, category, excerpt (AEO), tags, reading minutes, Quill content (HTML), SEO meta_title (70), meta_description (165) with live counters, canonical, cover image + alt, is_published, is_featured, per-post FAQs (Q/A → `faq_json`).
- **Image upload:** Toolbar image button → `POST /admin/blogs/upload` ( `Admin\BlogController@uploadImage` ) → tries `store('blogs/content','public')` else `public/uploads/blogs` → returns `{url}` → Quill embeds. Works on Hostinger shared (no S3/Redis).
- **Front:** `GET /blog` (paginated 9, filter by `?category=slug&q=...`, featured on top) + `GET /blog/{slug}` (increments views, related 3, SEO + Article + FAQPage JSON-LD).

Categories seeded: Reels & Editing, Thumbnails & CTR, AI Video, Growth Stories. Change in `blog_categories` table or admin (add via tinker).

---

## 5. FAQs — Managed in Admin

**Admin → FAQs** (`/admin/faqs`): table sorted by `sort_order`, inline edit (question, answer, category, sort, slug, published/featured), delete, add form. `is_featured` = shows on landing (AEO). `is_published` = sitemap/JSON-LD.

Landing pulls `Faq::published()->ordered()` → renders accordion + injects `FAQPage` via `components/seo`. Per-blog FAQs are separate (`blogs.faq_json`).

Keep answers **concise, answer-first, 40-60 words** — best for Featured Snippets + Perplexity.

---

## 6. Routes Map

```
GET  /                     → Landing (SEO + FAQPage + blogs teaser)
GET  /blog                 → Blog index (SEO)
GET  /blog/{slug}          → Blog show (Article + FAQPage + Breadcrumbs)
GET  /business             → Marketplace (Company Name everywhere)
GET  /business/profile     → Business profile form (DB)
POST /business/profile     → Save company + logo
GET  /creator              → Creator dashboard (availability, orders, portfolio)
GET  /creator/profile      → Creator profile form + portfolio manager (DB)
POST /creator/profile      → Save creator + avatar/cover
POST /creator/availability → Toggle live green dot
POST /creator/portfolio     → Add portfolio item
DELETE /creator/portfolio/{id}
GET  /creator/{id}         → Public creator SEO profile (Person)
POST /orders               → Create order (escrow hold)
GET  /sitemap.xml + /robots.txt + /health
GET  /login / POST /login / POST /logout

Admin (auth + role):
GET  /admin                → Dashboard
GET  /admin/orders{,/{id}}  + POST status/assign/release
GET  /admin/creators etc, /companies, /services
GET  /admin/blogs{,/create,/{blog}/edit} + POST / PUT / DELETE + POST /upload
GET  /admin/faqs + POST / PUT / DELETE
GET  /admin/users, /payouts, /settings
```

Roles: `super_admin` (everything + settings + delete users), `admin` (all except settings), `manager` (orders+creators+services+blogs+faqs), `support` (view orders), `finance` (payouts). `RoleMiddleware` alias `role` in `bootstrap/app.php`.

---

## 7. Hostinger Shared Deploy (5 min)

**DB:** hPanel → Databases → create MySQL → note `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (host `localhost`).

**Upload:**
```bash
composer create-project laravel/laravel quick-content
cd quick-content
# unzip quick-content-laravel.zip and overwrite
```

Zip to Hostinger File Manager → `/home/uXXXX/domains/yourdomain.com/` (above `public_html`). Via SSH:

```bash
cd ~/domains/yourdomain.com
composer install --no-dev --optimize-autoloader
cp .env.example .env
nano .env # set APP_URL=https://yourdomain.com, DB_*, RAZORPAY_*, MAIL_*
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link   # if fails on shared, we fallback to public/uploads automatically
chmod -R 755 storage bootstrap/cache
php artisan config:cache && php artisan route:cache && php artisan view:cache
# Document root must be .../public — in hPanel → Websites → Dashboard → Advanced → Document Root → public
```

**CRON** (hPanel → Cron Jobs → Every Minute `* * * * *`):
```
/usr/bin/php /home/u123/domains/yourdomain.com/artisan queue:work --stop-when-empty >> /dev/null 2>&1
/usr/bin/php /home/u123/domains/yourdomain.com/artisan schedule:run >> /dev/null 2>&1
```
Replace path via `pwd` in SSH.

**Check:** `https://yourdomain.com` (landing FAQ JSON-LD), `/blog` (posts), `/business/profile` (edit & save), `/creator/profile` (toggle + portfolio), `/admin/blogs/create` (Quill image upload), `/sitemap.xml`.

---

## 8. Local Dev

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite  # or set DB_CONNECTION=sqlite
php artisan migrate --seed
php artisan serve # http://localhost:8000
```
No `npm` needed — Tailwind CDN + Quill CDN + Alpine CDN.

---

## 9. File Map

```
database/migrations/2024_01_00_create_users_table.php (users + jobs + cache — no Redis)
                  /2024_01_01_create_companies.php (companies + logo + SEO)
                  /2024_01_02_create_creators.php (creators + skills + UPI)
                  /2024_01_05_create_portfolio_items.php
                  /2024_01_06_create_faqs.php (seed 7 FAQs)
                  /2024_01_07_create_blogs.php (categories + 3 SEO blogs)
app/Models/Company, Creator, PortfolioItem, Faq, Blog, BlogCategory, Service, Order, User
app/Http/Controllers/LandingController (maps DB → view shape), BusinessController, CreatorController, BlogController, SitemapController
app/Http/Controllers/Admin/BlogController (Quill + file upload), FaqController, etc
resources/views/components/seo.blade.php (meta + JSON-LD)
resources/views/landing.blade.php (SEO + FAQPage + blog teaser)
resources/views/blogs/index, show (Article + Breadcrumbs)
resources/views/business/profile, creator/profile, creator/public
resources/views/admin/blogs/create, edit (Quill), index, admin/faqs/index
public/.htaccess + public/uploads/* + artisan + bootstrap/app.php (role alias)
```

© 2026 QuickContent — India’s First Quick Content Delivery Platform. SEO • AEO • Hostinger Shared • No Dummy.
