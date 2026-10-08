from reportlab.lib.pagesizes import landscape, A4
from reportlab.pdfgen import canvas
from reportlab.lib import colors
from reportlab.lib.units import inch
from reportlab.pdfbase.pdfmetrics import stringWidth
OUT='Quick_GIGS_Business_Plan_GTM_Deck.pdf'
W,H=landscape(A4); ink=colors.HexColor('#101014'); mint=colors.HexColor('#00A878'); muted=colors.HexColor('#5F6368'); light=colors.HexColor('#EAF8F3'); lav=colors.HexColor('#F4F0FF')
slides=[
('BUSINESS PLAN + GO-TO-MARKET','Quick GIGS',['The fastest, most transparent way for Indian businesses to get creative work made.','Business model • 12-month GTM • Operating plan','Vinayak Tower, Vibhuti Khand, Lucknow 226028']),
('01 / THE OPPORTUNITY','Creative work is needed weekly—but buying it is still fragmented.',['Slow hiring, vague briefs and unclear pricing create revision loops.','Freelancers lose time chasing leads and face unclear scope or payouts.','Agencies need overflow capacity without adding fixed headcount.','Wedge: speed + trust—not another directory.']),
('02 / PRODUCT','One workflow from rough idea to approved work.',['Describe: AI structures the request.','Match: verified specialists scored transparently.','Order: service amount + GST; escrow protected.','Deliver: progress, feedback and quality checks.','Approve: release payout after acceptance.']),
('03 / BEACHHEAD','Start narrow: recurring content buyers with an urgent weekly need.',['D2C brands: launch reels, paid-social creatives and UGC packs.','Agencies: white-label overflow and specialist pods.','Regional SMBs: starter gigs and social presence.','Creator-led businesses: podcast clips and repurposing.']),
('04 / POSITIONING','A managed marketplace—not a list of profiles.',['For Indian businesses that need creative work shipped this week, Quick GIGS turns a rough request into a verified match and accountable delivery.','Speed: brief to shortlist in minutes.','Trust: verification, escrow and clear GST.','Fairness: zero creator platform fee and 100% of service subtotal.']),
('05 / BUSINESS MODEL','Transparent pricing now; monetisation expands with trust.',['One-off gigs: Starter, Pro and Studio packages.','Monthly plans: seats, allowance, SLA and one invoice.','Managed pods: dedicated specialists and reporting.','Future options: customer-side fee, add-ons and priority matching.','Guardrail: no creator deduction and no hidden customer fee.']),
('06 / GTM THESIS','Concierge first. Proof second. Scale third.',['Weeks 1–2: instrument CRM, events, QA and creator rubric.','Weeks 3–8: founder-led matching and concierge pilot.','Months 3–4: case studies and delivered-work proof.','Months 5–8: referrals, partnerships, SEO/AEO.','Months 9–12: pods and controlled paid acquisition.']),
('07 / CHANNEL PLAN','Five channels, each with a distinct job.',['Founder outbound: D2C founders, agencies and marketing heads.','Referrals: buyers and creators after positive outcomes.','Partnerships: coworking spaces, accelerators and consultants.','SEO/AEO: reels, UGC, pricing, GST and escrow pages.','Proof content: delivered work and transparent maths.','Paid tests: only validated segments with CAC stop rules.']),
('08 / FUNNEL','Measure approved, repeatable work—not vanity signups.',['Lead → qualified: target ≥50%.','Qualified → first order: 15–30% concierge benchmark.','Median time to match: under 15 minutes.','On-time delivery: ≥90%.','Buyer rating: ≥4.6 / 5.','Second order within 90 days: ≥35% by month 12.']),
('09 / SUPPLY STRATEGY','Demand and supply grow together around real packages.',['Recruit through LinkedIn, Instagram, referrals, creator communities and agencies.','Verify proof of work, category fit, availability, rate and turnaround.','Activate with a complete profile and first accepted brief.','Retain through clear scope, fair revisions, repeat demand and 100% subtotal payout.']),
('10 / 90-DAY PLAN','The first 90 days prove the loop.',['Weeks 1–2: choose three packages and instrument the funnel.','Weeks 3–4: recruit 30–40 specialists and contact 100 businesses.','Weeks 5–8: deliver manually, interview buyers and publish proof.','Weeks 9–10: improve onboarding and pricing pages.','Weeks 11–12: test one partner and one paid-intent campaign.','Pilot exit: 30 paid orders, ≥80% on-time, 10 repeat opportunities and 5 case studies.']),
('11 / ECONOMICS','Scale when contribution and repeat are visible.',['Illustrative average service subtotal: ₹2,499.','GST shown separately; confirm treatment with a qualified adviser.','Creator receives ₹2,499—100% of service subtotal.','Keep payouts, GST, refunds, processing, support and contribution separate.','Scale paid channels only when CAC payback is within three months.']),
('12 / RISKS','Trust is the moat—and the operating risk.',['Cold start → concierge matching, human callback and proof.','Quality variance → narrow categories, QA checklist and pause weak supply.','Scope creep → structured brief, deliverables and revision policy.','Paid CAC → pilot first and strict stop rules.','Tax/compliance → qualified Indian tax adviser reviews GST, TDS and contracts.']),
('13 / THE ASK','Build the loop before buying the scale.',['Run a focused Lucknow/NCR-led concierge pilot serving India remotely.','Keep the customer promise simple and every number visible.','Protect the creator policy: zero platform fee and 100% service-subtotal payout.','Scale only after repeat orders, on-time delivery and creator activation are healthy.'])]
c=canvas.Canvas(OUT,pagesize=(W,H))
for idx,(kicker,title,items) in enumerate(slides):
    c.setFillColor(ink); c.rect(0,0,W,H,fill=1,stroke=0); c.setFillColor(mint); c.rect(0,H-7,W,7,fill=1,stroke=0)
    c.setFillColor(mint); c.setFont('Helvetica-Bold',10); c.drawString(42,H-45,kicker)
    c.setFillColor(colors.white); c.setFont('Helvetica-Bold',28); c.drawString(42,H-88,title)
    y=H-145
    for i,item in enumerate(items):
        c.setFillColor(light if i%2==0 else lav); c.roundRect(55,y-10,W-110,38,8,fill=1,stroke=0)
        c.setFillColor(ink); c.setFont('Helvetica',13); c.drawString(75,y+4,item[:125]); y-=52
    c.setFillColor(colors.HexColor('#B8C4C0')); c.setFont('Helvetica',8); c.drawString(42,22,'QUICK GIGS  •  CONFIDENTIAL  •  Vinayak Tower, Vibhuti Khand, Lucknow 226028')
    c.drawRightString(W-42,22,str(idx+1)); c.showPage()
c.save(); print(OUT)
