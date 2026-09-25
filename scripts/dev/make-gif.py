#!/usr/bin/env python3
"""Rebuild public/img/how-it-works.gif from the isometric art. Requires Pillow."""
from PIL import Image, ImageDraw, ImageFont
import os

W, H = 760, 428
INK, MINT, GREY, LINE, PAPER = (10,10,11), (0,196,140), (113,113,122), (228,228,231), (255,255,255)
F = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"
FB = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
f_step  = ImageFont.truetype(FB, 17)
f_small = ImageFont.truetype(F, 13)
f_tiny  = ImageFont.truetype(F, 12)
f_mono  = ImageFont.truetype("/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf", 14)
f_head  = ImageFont.truetype(FB, 24)

steps = [
    ("Describe it", "one line, no form",            "iso-brief.png",   "0.6s"),
    ("Brief written", "hooks, beats, spec, QA gate", "iso-brief.png",   "1.0s"),
    ("Freelancer matched", "scored and explained",   "iso-match.png",   "4 min"),
    ("In production", "tracked live, chat open",     "iso-board.png",   "3 hrs"),
    ("Delivered + escrow released", "you approve, they get paid", "iso-deliver.png", "done"),
]

art = {}
for _, _, img, _ in steps:
    if img not in art:
        im = Image.open(f"public/img/{img}").convert("RGBA")
        im.thumbnail((360, 210), Image.LANCZOS)
        art[img] = im

def rr(d, box, r, fill=None, outline=None, w=1):
    d.rounded_rectangle(box, radius=r, fill=fill, outline=outline, width=w)

def frame(i, fade=1.0):
    title, sub, img, timing = steps[i]
    c = Image.new("RGB", (W, H), PAPER)
    d = ImageDraw.Draw(c)

    # header
    d.text((48, 38), "How Quick GIGS works", font=f_head, fill=INK)
    d.text((48, 72), "Describe the work. We do the rest.", font=f_small, fill=GREY)
    rr(d, (W-176, 38, W-48, 70), 16, fill=(232,250,243))
    d.text((W-160, 46), "quickgigs.in", font=f_tiny, fill=(0,130,95))

    # step rail
    y = 116
    n = len(steps)
    gap = (W - 96) / n
    for k in range(n):
        x = 48 + gap * k
        done, active = k < i, k == i
        col = MINT if (done or active) else LINE
        if k < n - 1:
            d.line([(x + 17, y + 13), (x + gap - 6, y + 13)], fill=MINT if done else LINE, width=3)
        d.ellipse((x, y, x + 26, y + 26), fill=MINT if (done or active) else PAPER, outline=col, width=3)
        if done:
            d.line([(x + 7, y + 13), (x + 11, y + 18), (x + 19, y + 8)], fill=PAPER, width=3)
        elif active:
            d.ellipse((x + 9, y + 9, x + 17, y + 17), fill=PAPER)
        label = steps[k][0].split(" +")[0]
        d.text((x - 6, y + 36), label if len(label) < 17 else label[:16] + "…",
               font=f_tiny, fill=INK if (done or active) else (161,161,170))

    # art card
    rr(d, (40, 168, 420, 386), 18, fill=(250,250,250), outline=LINE, w=1)
    a = art[img]
    c.paste(a, (40 + (380 - a.width)//2, 168 + (218 - a.height)//2), a)

    # copy panel
    x0 = 452
    d.text((x0, 206), f"STEP {i+1} OF {n}", font=f_tiny, fill=MINT)
    d.text((x0, 230), title, font=f_step, fill=INK)
    d.text((x0, 256), sub, font=f_small, fill=GREY)

    rr(d, (x0, 288, W - 48, 332), 12, fill=(250,250,250), outline=LINE, w=1)
    d.text((x0 + 14, 303), "elapsed", font=f_tiny, fill=GREY)
    d.text((W - 64 - d.textlength(timing, font=f_mono), 302), timing, font=f_mono, fill=(0,130,95))

    bullets = ["Escrow held until you approve", "Two free revisions", "Six automated quality checks"]
    for bi, b in enumerate(bullets):
        by = 356 + bi * 26
        d.ellipse((x0, by + 5, x0 + 7, by + 12), fill=MINT)
        d.text((x0 + 18, by), b, font=f_tiny, fill=(63,63,70))

    # progress
    rr(d, (48, 458, W - 48, 466), 4, fill=(240,240,242))
    frac = (i + fade) / n
    rr(d, (48, 458, 48 + int((W - 96) * min(1, frac)), 466), 4, fill=MINT)
    return c

frames, durations = [], []
for i in range(len(steps)):
    frames.append(frame(i)); durations.append(1500)

out = "public/img/how-it-works.gif"
frames = [f.convert("P", palette=Image.ADAPTIVE, colors=32) for f in frames]
frames[0].save(out, save_all=True, append_images=frames[1:], duration=durations,
               loop=0, optimize=True, disposal=2)
print(out, round(os.path.getsize(out)/1024), "KB,", len(frames), "frames")
