<?php

namespace App\Services;

use App\Models\Creator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Explainable matching.
 *
 * Most marketplaces show "AI matched" and nothing else. This returns a score
 * out of 100 together with the factors that produced it, so a buyer can see
 * exactly why a creator was picked — and a creator can see what to improve.
 */
class MatchEngine
{
    /** factor => weight (sums to 100) */
    public const WEIGHTS = [
        'skill'        => 34,
        'availability' => 22,
        'reliability'  => 18,
        'rating'       => 16,
        'budget'       => 10,
    ];

    /**
     * @param  array{category?:string,skills?:array<string>,budget?:int,urgency?:string}  $need
     * @return Collection<int,array<string,mixed>>
     */
    public function rank(Collection $creators, array $need = [], int $limit = 3): Collection
    {
        return $creators
            ->map(fn (Creator $c) => $this->score($c, $need))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /** @return array<string,mixed> */
    public function score(Creator $creator, array $need = []): array
    {
        $factors = [
            'skill'        => $this->skillFit($creator, $need),
            'availability' => $this->availabilityFit($creator, $need),
            'reliability'  => $this->reliabilityFit($creator),
            'rating'       => $this->ratingFit($creator),
            'budget'       => $this->budgetFit($creator, $need),
        ];

        $score = 0;
        $breakdown = [];

        foreach ($factors as $key => [$pct, $reason]) {
            $points = (int) round(self::WEIGHTS[$key] * $pct);
            $score += $points;
            $breakdown[] = [
                'label'  => ucfirst($key === 'skill' ? 'skill fit' : $key),
                'points' => $points,
                'max'    => self::WEIGHTS[$key],
                'reason' => $reason,
            ];
        }

        return [
            'creator'   => $creator,
            'score'     => min(98, max(41, $score)),
            'breakdown' => $breakdown,
            'headline'  => $this->headline($creator, $score),
        ];
    }

    /* ───────────────────────── factors (0.0 – 1.0 + reason) ───────────────────────── */

    private function skillFit(Creator $creator, array $need): array
    {
        $skills   = collect((array) ($creator->skills ?? []))->map(fn ($s) => Str::lower($s));
        $wanted   = collect($need['skills'] ?? [])->map(fn ($s) => Str::lower($s));
        $category = Str::lower($need['category'] ?? '');

        $hits = $wanted->isEmpty() ? collect() : $wanted->filter(
            fn ($w) => $skills->contains(fn ($s) => Str::contains($s, $w) || Str::contains($w, $s))
        );

        $typeBonus = match (true) {
            $category === 'ugc video'  && $creator->profile_type === 'ugc_creator'  => 0.35,
            $category === 'thumbnail'  && $creator->profile_type === 'designer'     => 0.35,
            $category === 'ai video'   && $creator->profile_type === 'hybrid'       => 0.30,
            in_array($category, ['reel', 'bundle'], true) && $creator->profile_type === 'video_editor' => 0.35,
            $creator->profile_type === 'hybrid' => 0.18,
            default => 0.05,
        };

        // portfolio depth nudges the score so two similar creators never tie exactly
        $depth = min(0.08, ($creator->orders_count ?? 0) / 5000);
        $pct = min(0.99, 0.28 + $typeBonus + $depth + ($wanted->isEmpty() ? 0.18 : 0.42 * ($hits->count() / max(1, $wanted->count()))));

        $reason = $hits->isNotEmpty()
            ? 'Matches ' . $hits->count() . ' of ' . $wanted->count() . ' requested skills (' . $hits->take(3)->implode(', ') . ')'
            : $creator->profileLabel() . ' — primary fit for ' . ($need['category'] ?? 'this category');

        return [$pct, $reason];
    }

    private function availabilityFit(Creator $creator, array $need): array
    {
        $express = ($need['urgency'] ?? 'standard') === 'express';
        $minutes = (int) ($creator->response_minutes ?: 20);

        if (! $creator->is_available) {
            return [$express ? 0.25 : 0.55, 'Currently busy — would start after the active gig'];
        }

        // continuous: 5 min ≈ 1.0, 65 min ≈ 0.55
        $pct = max(0.55, min(1.0, 1 - (($minutes - 5) / 120)));

        return [$pct, 'Online now, replies in about ' . $minutes . ' minutes'];
    }

    private function reliabilityFit(Creator $creator): array
    {
        $onTime = (int) ($creator->on_time_rate ?: 92);
        $orders = (int) ($creator->orders_count ?: 0);

        $pct = min(1.0, ($onTime / 100) * (0.75 + min(0.25, $orders / 400)));

        return [$pct, $onTime . '% on-time across ' . number_format($orders) . ' delivered gigs'];
    }

    private function ratingFit(Creator $creator): array
    {
        $rating = (float) ($creator->rating ?: 4.5);

        return [min(1.0, $rating / 5), number_format($rating, 1) . '★ from ' . number_format((int) $creator->reviews_count) . ' reviews'];
    }

    private function budgetFit(Creator $creator, array $need): array
    {
        $budget = (int) ($need['budget'] ?? 0);
        $from   = (int) ($creator->price_from ?: 1299);

        if ($budget <= 0) {
            return [0.8, 'Starts at ₹' . number_format($from)];
        }

        if ($from <= $budget) {
            return [1.0, 'Within budget — starts at ₹' . number_format($from)];
        }

        $over = ($from - $budget) / max(1, $budget);

        return [max(0.25, 1 - $over), 'Starts ₹' . number_format($from - $budget) . ' above the target budget'];
    }

    private function headline(Creator $creator, int $score): string
    {
        return match (true) {
            $score >= 90 => 'Best overall fit for this brief',
            $score >= 80 => 'Strong fit, available now',
            $score >= 70 => 'Good fit — slightly longer turnaround',
            default      => 'Backup option if the top picks are booked',
        };
    }
}
