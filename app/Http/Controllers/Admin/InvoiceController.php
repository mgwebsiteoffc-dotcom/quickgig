<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['company', 'service', 'creator'])->latest();
        if ($request->filled('status')) {
            $query->where('payment_status', $request->string('status'));
        }
        if ($request->filled('q')) {
            $term = '%' . $request->string('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('uid', 'like', $term)
                    ->orWhereHas('company', fn ($company) => $company->where('name', 'like', $term))
                    ->orWhereHas('service', fn ($service) => $service->where('title', 'like', $term));
            });
        }

        $invoices = $query->paginate(20)->withQueryString();
        $summary = [
            'paid' => Order::where('payment_status', 'paid')->sum('total'),
            'held' => Order::where('escrow_status', 'held')->sum('total'),
            'released' => Order::where('escrow_status', 'released')->sum('total'),
        ];

        return view('admin.invoices.index', compact('invoices', 'summary'));
    }
}
