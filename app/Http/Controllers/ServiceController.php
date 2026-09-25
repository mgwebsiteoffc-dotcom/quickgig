<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Public service detail + order form.
     * Accepts either the numeric id or the slug.
     */
    public function show(Request $request, string $service)
    {
        $model = Service::with(['creator.portfolio'])
            ->where('slug', $service)
            ->orWhere('id', $service)
            ->firstOrFail();

        abort_unless($model->is_active, 404);

        $related = Service::with('creator')
            ->active()
            ->where('id', '!=', $model->id)
            ->when($model->category, fn ($q) => $q->where('category', $model->category))
            ->limit(4)
            ->get();

        $companies   = Company::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $currentCompany = $request->session()->get('company_id', $companies->first()->id ?? null);

        return view('services.show', [
            'service'        => $model,
            'creator'        => $model->creator,
            'related'        => $related,
            'companies'      => $companies,
            'currentCompany' => $currentCompany,
        ]);
    }
}
