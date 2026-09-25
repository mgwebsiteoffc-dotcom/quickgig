# Competitor audit — on-demand creative/freelance marketplaces (India), Sep 2026

Researched directly from the live sites. Used to set Quick GIGS' positioning, palette and page structure.

---

## 1. Unjob.ai — "quick commerce for digital services"

**Model.** Catalogue of fixed-price service templates you add to a cart, like a grocery app.
Pricing in USD ($15–$119), each card showing delivery time, MRP strike-through and "% OFF on MRP".
Categories: video editing, design, AI services, content creation. Creator partners shown with
Instagram follower counts.

**B2B (business.unjob.ai).** A different pitch: subscription instead of hiring.
"Create a task → AI assigns the best expert → task delivered." Compares ₹1,80,000/month of salaries
against ₹29,999/month. Product proof is a **screenshot of a kanban task board** (status, assignee,
priority flag, due date, links, comment count, "Chat with …"). CTA is a Calendly demo booking.
Stats: 200,000+ freelancers, 50K+ tasks, "Top 1%".

**Design.** Black wordmark with a **mint-green `.ai`** — white surfaces, black type, one green accent.
Colour comes from thumbnails, not chrome. Dense product grids, minimal decoration.

**Weaknesses.** You still write the brief. Matching is a black box ("AI assigns"). No QA before
delivery. Board shown as an image, not a live product. Prepaid checkout, no escrow language.

---

## 2. Elyvato — "managed-freelancers marketplace"

**Model.** Managed service: pick a service → team contacts you → **pay "commitment money"** → work
starts. "AI finds. You hire. All in 30 minutes." Instant-hire pages per *role* (graphic designer,
DoP, data entry, content writer, UGC creator, VFX artist, voice over, moderation, CSE).
Claims: 1M+ videos, 10B+ views, 99%+ quality delivery, 48-hour turnaround, 100+ business clients
(Times Internet, NDTV, MX Player, ShareChat, Dow, DuPont).

**Design.** Enterprise-trust styling: client logo walls, big stat counters, role photography,
WhatsApp CTA. Photo-led rather than illustration-led.

**Weaknesses.** Commitment fee before any output. Human coordination in the loop ("our team will
contact you") kills the speed claim. Talent is anonymous until later. No self-serve checkout.

---

## 3. Wider field

| Player | Model | What we take | What we avoid |
|---|---|---|---|
| Fiverr | Gig catalogue, ~20% take rate | Fixed-price cards, sold counts, ratings | Bidding noise, opaque quality |
| Upwork | Bids + connects | Escrow familiarity | Pay-to-apply, 50-proposal inboxes |
| Superside / Design Pickle / Kimp | Creative subscription, unlimited queue | Task board, monthly plans, one invoice | "Unlimited" that queues forever; no talent transparency |
| Awesomic / Draftss | AI-ish matching to one designer, day rates | Fast single-assignee matching | Matching shown as magic, no score |
| Contra | Commission-free freelancer profiles | Freelancer-positive economics | Thin buyer tooling |
| Toptal | Vetted elite network, high touch | Verification rigour | Weeks to start, enterprise pricing |

**Category convention:** everyone says *freelancers / experts / specialists* — "creator" reads as
influencer-only and narrows the platform to video. Quick GIGS uses freelancer everywhere.

---

## What Quick GIGS does differently (and where it shows in the product)

| Gap in the market | Our answer | Where |
|---|---|---|
| You write the brief | Brief written from one line, in ~1 second, free, no signup | Hero demo, `/brief-builder` |
| "AI matched" with no reasoning | Score out of 100 with five published factors, re-weightable | `/ai-engine` |
| Nothing checks the work | Six automated QA checks before delivery reaches the buyer | `/ai-engine#m02` |
| Commitment fees / prepaid carts | Escrow, refundable until approval, SLA credit if late | `/pricing` |
| Board shown as a screenshot | A real board in the panel — inline create, move, convert to order | `/business/board` |
| Video-only catalogue | Video, design, copy, code, voice, marketing — 11 categories | `/marketplace` |
| Vague revisions | Two free rounds, feedback translated into timestamped notes | `/ai-engine#m03` |

**Design direction taken from the audit:** unjob's restraint (white, black, one mint accent, product
grids that let thumbnails carry colour) + isometric black-and-white illustration to carry the
"2027 AI-first" feel without rainbow gradients. Elyvato's trust furniture (logo wall, stat counters)
kept, its commitment-fee model explicitly rejected.
