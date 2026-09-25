<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Resolves *who the request is acting as* from the authenticated user — never from
 * an unauthenticated session value.
 *
 * Staff (super_admin/admin/manager/support/finance) may act on behalf of any
 * creator/company for support purposes; everyone else is locked to the record
 * their user row is bound to.
 */
trait ResolvesActor
{
    protected function currentCreator(Request $request): Creator
    {
        $user = $request->user();

        abort_unless($user, 403);

        if ($user->isAdmin()) {
            $id = $request->session()->get('creator_id') ?? $request->query('creator_id');

            abort_if(blank($id), 404, 'Pick a creator first.');

            return Creator::findOrFail($id);
        }

        abort_unless($user->creator_id, 403, 'Your account is not linked to a creator profile.');

        return Creator::findOrFail($user->creator_id);
    }

    protected function currentCompany(Request $request): Company
    {
        $user = $request->user();

        abort_unless($user, 403);

        if ($user->isAdmin()) {
            $id = $request->session()->get('company_id') ?? $request->query('company_id');

            abort_if(blank($id), 404, 'Pick a company first.');

            return Company::findOrFail($id);
        }

        abort_unless($user->company_id, 403, 'Your account is not linked to a business profile.');

        return Company::findOrFail($user->company_id);
    }

    /** Companies this user is allowed to switch between. */
    protected function selectableCompanies(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return Company::query()->whereRaw('1 = 0')->get();
        }

        if ($user->isAdmin()) {
            return Company::where('is_active', true)->orderBy('name')->get();
        }

        return Company::where('id', $user->company_id)->get();
    }

    /** True when the user is the buyer, the assigned creator, or staff. */
    protected function canSeeOrder(Request $request, Order $order): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        return $user->isAdmin()
            || ($user->company_id && $user->company_id === $order->company_id)
            || ($user->creator_id && $user->creator_id === $order->creator_id);
    }

    protected function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($this->canSeeOrder($request, $order), 403, 'This order does not belong to you.');
    }
}
