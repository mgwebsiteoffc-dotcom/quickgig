from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE
from pptx.enum.dml import MSO_THEME_COLOR

OUT='Quick_GIGS_Business_Plan_GTM_Deck.pptx'
prs=Presentation(); prs.slide_width=Inches(13.333); prs.slide_height=Inches(7.5)
blank=prs.slide_layouts[6]
INK=RGBColor(16,16,20); MUTED=RGBColor(95,99,104); MINT=RGBColor(0,168,120); LIGHT=RGBColor(234,248,243); LAV=RGBColor(244,240,255); WHITE=RGBColor(255,255,255); LINE=RGBColor(218,226,222); BLUE=RGBColor(42,134,230)

def rect(slide,x,y,w,h,fill,rad=False,line=None):
    s=slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE if rad else MSO_SHAPE.RECTANGLE, Inches(x), Inches(y), Inches(w), Inches(h))
    s.fill.solid(); s.fill.fore_color.rgb=fill; s.line.color.rgb=(line or fill); return s
def text(slide,x,y,w,h,txt,size=18,color=INK,bold=False,align=PP_ALIGN.LEFT,font='Aptos',val=MSO_ANCHOR.TOP):
    box=slide.shapes.add_textbox(Inches(x), Inches(y), Inches(w), Inches(h)); tf=box.text_frame; tf.clear(); tf.word_wrap=True; tf.vertical_anchor=val
    p=tf.paragraphs[0]; p.alignment=align; p.space_after=Pt(0); p.line_spacing=1.05
    r=p.add_run(); r.text=txt; r.font.name=font; r.font.size=Pt(size); r.font.bold=bold; r.font.color.rgb=color
    return box
def bullets(slide,x,y,w,h,items,size=16,color=INK):
    box=slide.shapes.add_textbox(Inches(x), Inches(y), Inches(w), Inches(h)); tf=box.text_frame; tf.clear(); tf.word_wrap=True
    for i,item in enumerate(items):
        p=tf.paragraphs[0] if i==0 else tf.add_paragraph(); p.text=item; p.level=0; p.font.name='Aptos'; p.font.size=Pt(size); p.font.color.rgb=color; p.space_after=Pt(9); p.line_spacing=1.08
    return box
def base(kicker,title,sub=''):
    s=prs.slides.add_slide(blank); rect(s,0,0,13.333,7.5,WHITE); rect(s,0,0,13.333,.08,MINT)
    text(s,.65,.38,11.8,.25,kicker.upper(),9,MINT,True)
    text(s,.65,.72,12,0.65,title,28,INK,True,font='Aptos Display')
    if sub: text(s,.65,1.42,11.8,.42,sub,11,MUTED)
    text(s,.65,7.15,8,.18,'QUICK GIGS  •  BUSINESS PLAN + GTM  •  CONFIDENTIAL',7,MUTED,True)
    text(s,12.15,7.12,.5,.2,str(len(prs.slides)),8,MUTED,True,PP_ALIGN.RIGHT)
    return s
def card(slide,x,y,w,h,title,body,fill=LIGHT,accent=MINT):
    rect(slide,x,y,w,h,fill,True,fill); rect(slide,x,y,.07,h,accent)
    text(slide,x+.22,y+.2,w-.4,.3,title,13,INK,True)
    text(slide,x+.22,y+.62,w-.4,h-.75,body,11,MUTED)
def pill(slide,x,y,w,label,fill=LIGHT,color=MINT):
    rect(slide,x,y,w,.32,fill,True,fill); text(slide,x,y+.03,w,.2,label,8,color,True,PP_ALIGN.CENTER)

# cover
s=prs.slides.add_slide(blank); rect(s,0,0,13.333,7.5,INK); rect(s,0,0,13.333,.1,MINT)
text(s,.8,1.05,8,.35,'BUSINESS PLAN + GO-TO-MARKET',11,MINT,True)
text(s,.8,1.55,8.7,1.1,'Quick GIGS',44,WHITE,True,font='Aptos Display')
text(s,.85,2.85,7.6,.8,'The fastest, most transparent way for Indian businesses to get creative work made.',22,WHITE,True)
text(s,.85,4.15,6.9,.65,'Business model • 12-month GTM • Operating plan',14,RGBColor(205,213,210))
card(s,8.65,1.5,3.6,3.2,'THE PROMISE','Describe the work.\nGet a verified specialist, a clear price and accountable delivery.',RGBColor(28,42,39),MINT)
text(s,.85,6.55,7,.3,'Vinayak Tower, Vibhuti Khand, Lucknow 226028  •  September 2026',9,RGBColor(180,190,187))

# 2
s=base('01 / The opportunity','Creative work is needed weekly—but buying it is still fragmented.','Quick GIGS combines an AI-assisted brief, verified supply, escrow and delivery accountability.')
card(s,.7,2.1,3.7,2.55,'BUSINESS PAIN','Slow hiring\nVague briefs\nUnclear pricing\nRevision loops\nNo reliable overflow capacity',LIGHT)
card(s,4.82,2.1,3.7,2.55,'FREELANCER PAIN','Time wasted on chasing leads\nUnclear scope\nPayout anxiety\nLow-quality briefs\nNo repeatable demand',LAV,BLUE)
card(s,8.94,2.1,3.7,2.55,'WHY NOW','Short-form content cycles\nCreator economy growth\nSMBs need speed\nAgencies need overflow\nAI lowers coordination cost',RGBColor(255,247,228),RGBColor(230,160,0))
text(s,.75,5.35,11.7,.55,'The wedge: speed + trust. Not another directory—an accountable path from rough request to approved delivery.',20,INK,True,PP_ALIGN.CENTER)

# 3
s=base('02 / Product','One workflow from rough idea to approved work.','The product should feel simple to the buyer and fair to the creator.')
steps=[('01','Describe','AI structures the request'),('02','Match','Verified specialists, scored transparently'),('03','Order','Service amount + GST; escrow protected'),('04','Deliver','Progress, feedback and QA'),('05','Approve','Release payout after acceptance')]
for i,(n,t,b) in enumerate(steps):
    x=.7+i*2.5; rect(s,x,2.3,2.1,2.05,LIGHT if i%2==0 else LAV,True); text(s,x+.18,2.52,.5,.3,n,10,MINT,True); text(s,x+.18,2.93,1.75,.35,t,15,INK,True); text(s,x+.18,3.42,1.7,.58,b,10,MUTED)
    if i<4: text(s,x+2.15,3.0,.3,.3,'→',20,MINT,True,PP_ALIGN.CENTER)
text(s,.8,5.2,11.7,.7,'Customer-facing transaction breakdown: service subtotal + GST only. Creator receives 100% of the service subtotal.',18,INK,True,PP_ALIGN.CENTER)

# 4
s=base('03 / Beachhead','Start narrow: recurring content buyers with an urgent weekly need.','Win one job type repeatedly before expanding the marketplace.')
card(s,.7,2.0,2.85,3.15,'D2C BRANDS','Launch reels\nPaid-social creatives\nUGC packs\nThumbnails',LIGHT)
card(s,3.8,2.0,2.85,3.15,'AGENCIES','White-label overflow\nSpecialist pods\nFast turnaround\nConsolidated billing',LAV,BLUE)
card(s,6.9,2.0,2.85,3.15,'REGIONAL SMBs','Social presence\nStarter gigs\nLocal-language needs\nLow-risk first order',RGBColor(255,247,228),RGBColor(230,160,0))
card(s,10.0,2.0,2.6,3.15,'CREATOR-LED','Podcast clips\nRepurposing\nUGC content\nRecurring packs',LIGHT)
text(s,.8,5.75,11.8,.5,'Initial geography: Lucknow + NCR-led supply and demand, serving businesses across India remotely.',15,MUTED,True,PP_ALIGN.CENTER)

# 5
s=base('04 / Positioning','A managed marketplace—not a list of profiles.','Position Quick GIGS around outcomes, transparency and speed.')
text(s,.9,2.05,11.5,.8,'For Indian businesses that need creative work shipped this week, Quick GIGS turns a rough request into a verified match and accountable delivery.',22,INK,True,PP_ALIGN.CENTER)
card(s,1.1,3.5,3.4,1.8,'SPEED','Brief to shortlist in minutes\nFast, packaged services\nRush lanes when available',LIGHT)
card(s,4.95,3.5,3.4,1.8,'TRUST','Verified profiles\nEscrow protection\nClear GST and scope',LAV,BLUE)
card(s,8.8,3.5,3.4,1.8,'FAIRNESS','Zero creator platform fee\n100% of service subtotal\nVisible payout expectation',RGBColor(255,247,228),RGBColor(230,160,0))

# 6
s=base('05 / Business model','Transparent pricing now; monetisation expands with trust.','Do not compromise the creator promise to force early take-rate.')
card(s,.75,2.0,2.8,2.7,'ONE-OFF GIGS','Starter, Pro and Studio packages\n\nCustomer pays: subtotal + GST\nCreator receives: 100% subtotal',LIGHT)
card(s,3.8,2.0,2.8,2.7,'MONTHLY PLANS','Team seats\nDefined allowance\nSLA and reporting\nOne consolidated invoice',LAV,BLUE)
card(s,6.85,2.0,2.8,2.7,'MANAGED PODS','Dedicated specialists\nAccount coordination\nQuality reporting\nWhite-label delivery',RGBColor(255,247,228),RGBColor(230,160,0))
card(s,9.9,2.0,2.7,2.7,'FUTURE OPTIONS','Configurable customer-side fee\nAdd-on packs\nPriority matching\nOnly after repeat is proven',LIGHT)
text(s,.8,5.4,11.8,.6,'Guardrail: no creator deduction and no customer-facing platform-fee language until intentionally activated.',17,INK,True,PP_ALIGN.CENTER)

# 7
s=base('06 / Go-to-market thesis','Concierge first. Proof second. Scale third.','Every phase earns the right to invest in the next.')
phases=[('0','Instrument','Weeks 1–2','Events, CRM, QA rubric'),('1','Pilot','Weeks 3–8','Founder-led matching'),('2','Proof','Months 3–4','Case studies + delivered work'),('3','Repeat','Months 5–8','Partners, SEO, referrals'),('4','Scale','Months 9–12','Pods + controlled paid')]
for i,(n,t,tm,b) in enumerate(phases):
    x=.65+i*2.5; rect(s,x,2.35,2.1,2.25,LIGHT if i in [0,2,4] else LAV,True); text(s,x+.18,2.58,.5,.3,n,11,MINT,True); text(s,x+.18,2.98,1.75,.34,t,15,INK,True); text(s,x+.18,3.45,1.75,.25,tm,9,MUTED,True); text(s,x+.18,3.84,1.7,.5,b,10,MUTED)
    if i<4: text(s,x+2.14,3.2,.3,.3,'→',20,MINT,True,PP_ALIGN.CENTER)
text(s,.8,5.45,11.7,.55,'Scale only after repeat orders, on-time delivery and creator activation are healthy.',18,INK,True,PP_ALIGN.CENTER)

# 8
s=base('07 / Channel plan','Five channels, each with a distinct job.','Avoid broad awareness spend before the economics are visible.')
rows=[('Founder outbound','D2C founders, agencies','Brief teardown + shortlist','Qualified meeting → order'),('Referrals','Buyers + creators','Credit / priority match','Referral order share'),('Partnerships','Coworking, accelerators, consultants','Member starter pack','Orders per partner'),('SEO / AEO','High-intent searchers','Pricing, UGC, reels and GST pages','Qualified organic leads'),('Proof content','Founders + marketers','Delivered work and transparent maths','Qualified traffic'),('Paid tests','Validated segments only','Outcome-led search/social','CAC + payback')]
for i,(a,b,c,d) in enumerate(rows):
    y=1.98+i*.7; fill=LIGHT if i%2==0 else RGBColor(248,250,249); rect(s,.7,y,11.95,.56,fill,False,LINE); text(s,.9,y+.14,2.1,.2,a,10,INK,True); text(s,3.05,y+.14,2.7,.2,b,9,MUTED); text(s,5.85,y+.14,3.4,.2,c,9,MUTED); text(s,9.4,y+.14,2.9,.2,d,9,MINT,True)
text(s,.8,6.45,11.7,.3,'Operating rhythm: 50 targeted accounts + 10 personalised demos + 2 proof assets per week in pilot.',12,INK,True,PP_ALIGN.CENTER)

# 9
s=base('08 / Funnel','Measure approved, repeatable work—not vanity signups.','The north-star is approved work delivered on time with a positive rating and a fair payout.')
metrics=[('Lead → qualified','≥50%','Clear use case + budget'),('Qualified → order','15–30%','Concierge benchmark'),('Match time','<15 min','Median shortlist time'),('On-time','≥90%','Delivery reliability'),('Rating','≥4.6/5','Buyer trust'),('Repeat in 90d','≥35%','Lifecycle health')]
for i,(a,b,c) in enumerate(metrics):
    x=.7+(i%3)*4.1; y=2+(i//3)*2.1; rect(s,x,y,3.65,1.55,LIGHT if i%2==0 else LAV,True); text(s,x+.2,y+.2,2.9,.25,a,11,MUTED,True); text(s,x+.2,y+.58,3,.4,b,23,MINT,True); text(s,x+.2,y+1.08,3,.25,c,9,MUTED)

# 10
s=base('09 / Supply strategy','Demand and supply grow together around real packages.','Recruit for proof of work and route first jobs quickly.')
card(s,.75,2.0,3.7,3.15,'RECRUIT','LinkedIn, Instagram, referrals, creator communities and agencies.\n\nPrioritise portfolio proof, response time and category fit.',LIGHT)
card(s,4.82,2.0,3.7,3.15,'ACTIVATE','Profile approved\nAvailability set\nRate and turnaround clear\nFirst brief accepted\nFirst delivery approved',LAV,BLUE)
card(s,8.89,2.0,3.7,3.15,'RETAIN','100% subtotal payout\nClear scope\nFair revision policy\nRepeat demand\nAgency/team profiles',RGBColor(255,247,228),RGBColor(230,160,0))

# 11
s=base('10 / 90-day plan','The first 90 days are about proving the loop.','Do the hard operational work before automating scale.')
table_data=[('Weeks 1–2','Instrument funnel; choose 3 packages; define QA and verification rubrics.'),('Weeks 3–4','Recruit 30–40 specialists; contact 100 target businesses; run demos.'),('Weeks 5–8','Deliver manually; interview buyers; publish proof; ask for referrals.'),('Weeks 9–10','Improve onboarding, matching explanations and pricing pages.'),('Weeks 11–12','Test one partner and one paid-intent campaign; review economics.')]
for i,(a,b) in enumerate(table_data):
    y=2+i*.75; rect(s,.8,y,1.55,.55,INK,True); text(s,.8,y+.16,1.55,.2,a,9,WHITE,True,PP_ALIGN.CENTER); rect(s,2.55,y,9.9,.55,LIGHT if i%2==0 else LAV,True); text(s,2.78,y+.13,9.35,.28,b,10,INK)
text(s,.8,6.2,11.7,.4,'Pilot exit: 30 paid orders • ≥80% on-time • 10 repeat opportunities • 5 case studies.',16,MINT,True,PP_ALIGN.CENTER)

# 12
s=base('11 / Economics','Scale when contribution and repeat are visible.','Illustrative assumptions for planning—not audited forecasts.')
card(s,.9,2.0,2.7,2.7,'AOV','₹2,499\nAverage service subtotal\n\nReplace with blended live data.',LIGHT)
card(s,3.9,2.0,2.7,2.7,'GST','18% / ₹450\nShown separately\n\nConfirm treatment with tax adviser.',LAV,BLUE)
card(s,6.9,2.0,2.7,2.7,'CREATOR','₹2,499\n100% of subtotal\n\nNo creator platform fee.',RGBColor(255,247,228),RGBColor(230,160,0))
card(s,9.9,2.0,2.7,2.7,'SCALE RULE','CAC payback\nwithin 3 months\n\nOnly scale winning channels.',LIGHT)
text(s,.9,5.45,11.5,.55,'Keep payouts, GST, refunds, payment processing, support and contribution separate in the ledger.',16,INK,True,PP_ALIGN.CENTER)

# 13
s=base('12 / Risks','Trust is the moat—and the operating risk.','Make quality and fairness measurable from day one.')
risks=[('Cold start','Concierge matching + human callback + proof'),('Quality variance','Narrow categories + QA checklist + pause weak supply'),('Scope creep','Structured brief + deliverables + revision policy'),('Paid CAC','Pilot first + strict stop rules'),('Tax / compliance','Qualified Indian tax adviser reviews GST, TDS and contracts')]
for i,(a,b) in enumerate(risks):
    y=1.95+i*.82; rect(s,.8,y,.15,.58,MINT,True); text(s,1.15,y+.05,2.2,.3,a,12,INK,True); text(s,3.4,y+.05,8.9,.35,b,11,MUTED)

# 14
s=base('13 / The ask','Build the loop before buying the scale.','The next decision is a focused concierge pilot, not a broad marketplace launch.')
text(s,.9,2.0,11.5,.8,'What success looks like',22,INK,True)
card(s,.9,3.0,3.55,1.9,'CUSTOMERS','A clear, repeatable use case\nFast shortlist\nApproved work',LIGHT)
card(s,4.9,3.0,3.55,1.9,'CREATORS','Fair scope\n100% subtotal payout\nRepeat demand',LAV,BLUE)
card(s,8.9,3.0,3.55,1.9,'BUSINESS','Measurable channel\nHealthy repeat\nContribution visibility',RGBColor(255,247,228),RGBColor(230,160,0))
text(s,.9,5.75,11.5,.5,'Recommended starting point: Lucknow/NCR-led supply and demand, serving businesses across India remotely.',16,MINT,True,PP_ALIGN.CENTER)
text(s,.9,6.45,11.5,.25,'Vinayak Tower, Vibhuti Khand, Lucknow 226028',9,MUTED,False,PP_ALIGN.CENTER)

prs.save(OUT); print(OUT)
