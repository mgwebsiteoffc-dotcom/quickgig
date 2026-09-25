<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * The skill library. Admin owns this list; freelancers pick from it.
 * Selections are stored on creators.skills (JSON array of names) so the
 * matching engine and every existing view keep working unchanged.
 */
class Skill extends Model
{
    protected $fillable = ['name', 'slug', 'discipline', 'is_active', 'sort_order', 'usage_count'];

    protected $casts = ['is_active' => 'boolean'];

    public const DISCIPLINES = [
        'Video', 'Design', 'Writing', 'Development', 'Voice', 'Marketing', 'UGC & Creators', 'General',
    ];

    protected static function booted(): void
    {
        static::saving(function (Skill $skill) {
            $skill->slug = Str::slug($skill->name);
        });
    }

    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('discipline')->orderBy('sort_order')->orderBy('name'); }

    /** ['Video' => ['Reels', 'Colour grading', …], …] for the picker. */
    public static function grouped(): array
    {
        return static::active()->ordered()->get()
            ->groupBy('discipline')
            ->map(fn ($g) => $g->pluck('name')->values()->all())
            ->all();
    }

    /** Flat list of allowed names, used for validation. */
    public static function allowedNames(): array
    {
        return static::active()->pluck('name')->all();
    }

    /** Keep the counters roughly honest so admin can see what is actually used. */
    public static function recount(): void
    {
        $counts = [];
        foreach (Creator::pluck('skills') as $list) {
            foreach ((array) $list as $name) {
                $counts[$name] = ($counts[$name] ?? 0) + 1;
            }
        }

        foreach (static::all() as $skill) {
            $skill->updateQuietly(['usage_count' => $counts[$skill->name] ?? 0]);
        }
    }
}
