"""
OpenRouter reasoning round-trip — the pattern Quick GIGS uses in
app/Services/Ai/OpenRouterClient.php, in ~60 lines of Python.

Key idea: the assistant turn from call #1 is replayed *unmodified* (including
`reasoning_details`) in call #2, so the model continues its earlier reasoning
instead of starting from scratch.

Run:
    export OPENROUTER_API_KEY=sk-or-...
    python scripts/openrouter_reasoning_example.py
"""

from __future__ import annotations

import json
import os
import sys

import requests

API_KEY = os.environ.get("OPENROUTER_API_KEY")
if not API_KEY:
    sys.exit("OPENROUTER_API_KEY is not set.")

MODEL = os.environ.get("OPENROUTER_MODEL", "nvidia/nemotron-3.5-lightning:free")
URL = "https://openrouter.ai/api/v1/chat/completions"
HEADERS = {
    "Authorization": f"Bearer {API_KEY}",
    "Content-Type": "application/json",
    # Optional attribution headers OpenRouter recommends.
    "HTTP-Referer": os.environ.get("OPENROUTER_REFERER", "https://quickgigs.in"),
    "X-Title": os.environ.get("OPENROUTER_TITLE", "Quick GIGS"),
}

SYSTEM = (
    "You convert a work request into one JSON object for a project tracker. "
    'Return JSON only: {"title": string, "start_date": "YYYY-MM-DD", '
    '"end_date": "YYYY-MM-DD", "description": string, "client": string or null}. '
    "If no start date is given use today. end_date must not be before start_date. "
    "Never invent a client name."
)


def chat(messages: list[dict], *, json_mode: bool = True) -> dict:
    """One completion. Returns the assistant message dict, reasoning_details included."""
    payload = {
        "model": MODEL,
        "messages": messages,
        "reasoning": {"enabled": True},
        "temperature": 0.2,
    }
    if json_mode:
        payload["response_format"] = {"type": "json_object"}

    response = requests.post(URL, headers=HEADERS, data=json.dumps(payload), timeout=60)

    # Surface the API's error body instead of a bare "HTTP 400".
    if not response.ok:
        sys.exit(f"HTTP {response.status_code}: {response.text[:500]}")

    return response.json()["choices"][0]["message"]


def extract_json(content: str) -> dict | None:
    """Tolerate ```json fences and stray prose around the object."""
    text = content.strip()
    if text.startswith("```"):
        text = text.split("```")[1].removeprefix("json").strip()
    try:
        return json.loads(text)
    except json.JSONDecodeError:
        start, end = text.find("{"), text.rfind("}")
        if start != -1 and end > start:
            try:
                return json.loads(text[start : end + 1])
            except json.JSONDecodeError:
                return None
    return None


# ── call 1 ────────────────────────────────────────────────────────────────────
first_request = "create task to create mobile app, delivery date is 29 aug 2026"

messages = [
    {"role": "system", "content": SYSTEM},
    {"role": "user", "content": first_request},
]

assistant = chat(messages)
print("── first pass ──")
print(json.dumps(extract_json(assistant.get("content", "")), indent=2))

# ── call 2 — replay the assistant turn so reasoning continues ─────────────────
# NOTE: the original user turn must stay in the list, in order. Dropping it (or
# reordering the turns) is what usually breaks reasoning continuity.
messages += [
    {
        "role": "assistant",
        "content": assistant.get("content"),
        "reasoning_details": assistant.get("reasoning_details"),  # pass back unmodified
    },
    {"role": "user", "content": "Are you sure about the year? Think carefully and return the corrected JSON only."},
]

second = chat(messages)
print("\n── second pass ──")
print(json.dumps(extract_json(second.get("content", "")), indent=2))
print("\nreasoning carried over:", bool(second.get("reasoning_details")))
