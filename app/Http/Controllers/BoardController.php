<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Service;
use App\Models\Task;
use App\Services\Ai\TaskParser;
use Illuminate\Http\Request;

/**
 * The business task board — queue work, we assign a specialist, you approve.
 *
 * Every mutation answers JSON so the board updates in place; the same routes
 * still work without JavaScript by falling back to a redirect.
 */
class BoardController extends Controller
{
    public function index(Request $request)
    {
        $company = $this->company($request);

        $tasks = Task::with(['creator', 'order'])
            ->where('company_id', $company->id)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.board', [
            'company'  => $company,
            'columns'  => Task::STATUSES,
            'tasks'    => $tasks->groupBy('status'),
            'counts'   => [
                'open'     => $tasks->whereNotIn('status', ['done'])->count(),
                'overdue'  => $tasks->filter->isOverdue()->count(),
                'done'     => $tasks->where('status', 'done')->count(),
                'thisWeek' => $tasks->where('delivered_at', '!=', null)
                    ->filter(fn ($t) => $t->delivered_at?->gt(now()->subWeek()))->count(),
            ],
            'categories' => Service::CATEGORIES,
            'priorities' => Task::PRIORITIES,
            'seo'        => ['title' => 'Task board — Quick GIGS'],
        ]);
    }

    /** Create a task — plain fields, or one sentence parsed by the task engine. */
    public function store(Request $request, TaskParser $parser)
    {
        $company = $this->company($request);

        $data = $request->validate([
            'prompt'   => ['nullable', 'string', 'max:600'],
            'title'    => ['nullable', 'required_without:prompt', 'string', 'max:120'],
            'brief'    => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:40'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'due_on'   => ['nullable', 'date'],
            'link'     => ['nullable', 'url', 'max:300'],
        ]);

        $parsed = [];
        if (! empty($data['prompt'])) {
            $parsed = $parser->parse($data['prompt'], ['client' => $company->name]);
        }

        $task = Task::create([
            'company_id' => $company->id,
            'created_by' => $request->user()?->id,
            'title'      => $data['title'] ?? $parsed['title'] ?? 'New task',
            'brief'      => $data['brief'] ?? $parsed['description'] ?? null,
            'category'   => $data['category'] ?? $this->guessCategory(($data['title'] ?? '') . ' ' . ($parsed['title'] ?? '')),
            'priority'   => $data['priority'] ?? 'normal',
            'status'     => 'queued',
            'start_on'   => $parsed['start_date'] ?? now()->toDateString(),
            'due_on'     => $data['due_on'] ?? $parsed['end_date'] ?? now()->addDays(2)->toDateString(),
            'links'      => array_values(array_filter([$data['link'] ?? null])),
            'sort_order' => (int) Task::where('company_id', $company->id)->max('sort_order') + 1,
        ]);

        // Auto-assign straight away, like the live pipeline does.
        $this->autoAssign($task);

        return $this->respond($request, $task, 'Task queued — matching a specialist now.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        $data = $request->validate(['status' => ['required', 'in:' . implode(',', array_keys(Task::STATUSES))]]);

        $task->status = $data['status'];

        if ($data['status'] === 'done') {
            $task->delivered_at = now();
        }

        if (in_array($data['status'], ['assigned', 'production'], true) && ! $task->creator_id) {
            $this->autoAssign($task, save: false, setStatus: false);
        }

        $task->save();

        return $this->respond($request, $task, 'Moved to ' . $task->statusLabel() . '.');
    }

    public function updatePriority(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        $data = $request->validate(['priority' => ['required', 'in:low,normal,high,urgent']]);
        $task->update($data);

        return $this->respond($request, $task, 'Priority set to ' . $task->priorityLabel() . '.');
    }

    /** Turn a board task into a real escrow-backed order. */
    public function convert(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        if ($task->order_id) {
            return $this->respond($request, $task, 'This task already has an order.');
        }

        $service = Service::where('is_active', true)
            ->where('category', $task->category)
            ->orderByDesc('sold_count')
            ->first()
            ?? Service::where('is_active', true)->orderByDesc('sold_count')->first();

        if (! $service) {
            return $this->respond($request, $task, 'No matching gig in the catalogue yet.');
        }

        $subtotal = (int) $service->price;

        $order = \App\Models\Order::create([
            'company_id'    => $task->company_id,
            'creator_id'    => $task->creator_id ?: $service->creator_id,
            'service_id'    => $service->id,
            'brief'         => $task->brief ?: $task->title,
            'turnaround'    => $task->priority === 'urgent' ? 'Express · 3 hours' : 'Standard · 24 hours',
            'subtotal'      => $subtotal,
            'fee'           => (int) round($subtotal * 0.10),
            'discount'      => 0,
            'total'         => $subtotal,
            'status'        => 'working',
            'escrow_status' => 'held',
            'progress'      => 25,
            'due_at'        => $task->due_on ?? now()->addDay(),
        ]);

        $task->update(['order_id' => $order->id, 'status' => 'production', 'service_id' => $service->id]);
        $task->company->increment('credits_used');

        return $this->respond($request, $task->fresh(['creator', 'order']), 'Order ' . $order->uid . ' created — ₹' . number_format($subtotal) . ' held in escrow.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->delete();

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'message' => 'Task removed.'])
            : back()->with('toast', 'Task removed.');
    }

    /* ───────────────────────── helpers ───────────────────────── */

    /** Pick the best available creator. Only the create flow flips the status. */
    private function autoAssign(Task $task, bool $save = true, bool $setStatus = true): void
    {
        $creator = Creator::where('is_verified', true)
            ->where('is_available', true)
            ->orderByDesc('rating')
            ->orderByDesc('orders_count')
            ->first();

        if (! $creator) return;

        $task->creator_id = $creator->id;

        if ($setStatus) $task->status = 'assigned';
        if ($save) $task->save();
    }

    private function guessCategory(string $text): string
    {
        $t = mb_strtolower($text);

        return match (true) {
            str_contains($t, 'thumbnail')                            => 'Thumbnail',
            str_contains($t, 'ugc') || str_contains($t, 'unboxing')  => 'UGC Video',
            str_contains($t, ' ai') || str_contains($t, 'ai ')       => 'AI Video',
            str_contains($t, 'brand') || str_contains($t, 'logo')    => 'Bundle',
            default                                                  => 'Reel',
        };
    }

    private function respond(Request $request, Task $task, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['task' => $task->fresh(['creator', 'order'])->toBoardArray(), 'message' => $message]);
        }

        return back()->with('toast', $message);
    }

    private function authorizeTask(Request $request, Task $task): void
    {
        $user = $request->user();
        $ok = $user && ($user->isAdmin() || $user->company_id === $task->company_id || $request->session()->get('company_id') === $task->company_id);

        abort_unless($ok, 403, 'That task belongs to another workspace.');
    }

    private function company(Request $request): Company
    {
        $user = $request->user();

        if ($user?->company_id && ($c = Company::find($user->company_id))) return $c;
        if (($id = $request->session()->get('company_id')) && ($c = Company::find($id))) return $c;

        $company = Company::create([
            'name'        => $user ? $user->name . "'s workspace" : 'My workspace',
            'person_name' => $user->name ?? 'Owner',
            'email'       => $user->email ?? null,
            'is_active'   => true,
        ]);

        $user?->update(['company_id' => $company->id]);
        $request->session()->put('company_id', $company->id);

        return $company;
    }
}
