<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\Ai\TaskParser;
use Illuminate\Http\Request;

/**
 * "Create task to build a mobile app, delivery date is 29 Aug 2026"
 *   → { title, start_date, end_date, description, client }
 *
 * Works as a JSON endpoint (Accept: application/json) or as a form post that
 * renders the parsed task on the dashboard.
 */
class TaskController extends Controller
{
    public function parse(Request $request, TaskParser $parser)
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'min:6', 'max:600'],
        ]);

        $company = $request->user()?->company;

        $task = $parser->parse($data['prompt'], [
            'client' => $company->name ?? null,
        ]);

        $task['prompt'] = $data['prompt'];
        $task['gig_id'] = $this->suggestGig($task)?->id;

        if ($request->expectsJson()) {
            return response()->json([
                'task' => $this->publicFields($task),
                'meta' => [
                    'source'     => $task['source'],
                    'model'      => $task['model'],
                    'error'      => $task['error'],
                    'suggestion' => $task['gig_id'] ? route('gigs.show', $task['gig_id']) : null,
                ],
            ]);
        }

        $request->session()->put('task.state', $task);
        $request->session()->put('brief.draft', $task['description']);

        return back()->with('toast', $task['source'] === 'ai'
            ? 'Task parsed by ' . $task['model'] . '.'
            : 'Task parsed. Add OPENROUTER_API_KEY to have a model do it.');
    }

    /** Second pass on the same conversation — reasoning continues where it left off. */
    public function refine(Request $request, TaskParser $parser)
    {
        $data = $request->validate([
            'instruction' => ['required', 'string', 'min:3', 'max:300'],
        ]);

        $state = $request->session()->get('task.state');

        if (! $state) {
            return back();
        }

        $task = $parser->refine($state, $data['instruction']);
        $task['prompt'] = $state['prompt'] ?? '';
        $task['gig_id'] = $this->suggestGig($task)?->id;

        $request->session()->put('task.state', $task);
        $request->session()->put('brief.draft', $task['description']);

        if ($request->expectsJson()) {
            return response()->json([
                'task' => $this->publicFields($task),
                'meta' => [
                    'source' => $task['source'],
                    'model'  => $task['model'],
                    'error'  => $task['refine_error'] ?? null,
                ],
            ]);
        }

        return back()->with('toast', $task['refine_error'] ?? 'Task updated.');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('task.state');

        return $request->expectsJson() ? response()->json(['ok' => true]) : back();
    }

    /** @return array<string,mixed> */
    private function publicFields(array $task): array
    {
        return [
            'title'       => $task['title'],
            'start_date'  => $task['start_date'],
            'end_date'    => $task['end_date'],
            'description' => $task['description'],
            'client'      => $task['client'],
            'warnings'    => $task['warnings'] ?? [],
            'gig_url'     => ! empty($task['gig_id']) ? route('gigs.show', $task['gig_id']) : null,
            'brief_url'   => route('brief-builder') . '?idea=' . urlencode($task['title']),
        ];
    }

    private function suggestGig(array $task): ?Service
    {
        $haystack = mb_strtolower($task['title'] . ' ' . $task['description']);

        $category = match (true) {
            str_contains($haystack, 'thumbnail')                                        => 'Thumbnail',
            str_contains($haystack, 'ugc') || str_contains($haystack, 'unboxing')       => 'UGC Video',
            str_contains($haystack, 'blog') || str_contains($haystack, 'copy')
                || str_contains($haystack, 'article') || str_contains($haystack, 'seo') => 'Writing',
            str_contains($haystack, 'website') || str_contains($haystack, 'landing page')
                || str_contains($haystack, 'shopify') || str_contains($haystack, 'app') => 'Development',
            str_contains($haystack, 'voice') || str_contains($haystack, 'narration')    => 'Voice Over',
            str_contains($haystack, 'ads') || str_contains($haystack, 'campaign')       => 'Marketing',
            str_contains($haystack, 'logo') || str_contains($haystack, 'ui ')
                || str_contains($haystack, 'figma')                                     => 'Design',
            str_contains($haystack, 'ai ')  || str_contains($haystack, 'ai-')           => 'AI Video',
            str_contains($haystack, 'brand') || str_contains($haystack, 'kit')          => 'Bundle',
            default                                                                     => 'Reel',
        };

        return Service::where('is_active', true)->where('category', $category)->orderByDesc('sold_count')->first()
            ?? Service::where('is_active', true)->orderByDesc('sold_count')->first();
    }
}
