<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $companies = Company::query()
            ->withCount('orders')
            ->withSum('orders as orders_spent', 'total')
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('person_name', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $rows = collect($companies->items())->map(fn (Company $c) => [
            'id'       => $c->id,
            'company'  => $c->name,
            'person'   => $c->person_name ?: '—',
            'email'    => $c->email ?: '—',
            'phone'    => $c->phone ?: '—',
            'plan'     => $c->plan ?: 'Starter',
            'orders'   => (int) $c->orders_count,
            'spent'    => '₹'.number_format((int) ($c->orders_spent ?? 0)),
            'joined'   => $c->created_at?->format('Y-m-d') ?? '—',
            'initials' => $c->initials ?: strtoupper(substr($c->name, 0, 2)),
        ]);

        return view('admin.companies.index', [
            'companies' => $rows,
            'paginator' => $companies,
            'q'         => $q,
        ]);
    }
}
