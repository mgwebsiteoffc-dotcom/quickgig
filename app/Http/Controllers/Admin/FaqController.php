<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $faqs = Faq::when($q, fn($qq)=> $qq->where('question','like',"%$q%"))
            ->orderBy('sort_order')->orderBy('id')
            ->paginate(20)->withQueryString();
        return view('admin.faqs.index', compact('faqs','q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'=>'required|string|max:200',
            'answer'=>'required|string|max:2000',
            'category'=>'required|string|max:40',
            'sort_order'=>'nullable|integer|min:0|max:999',
            'is_published'=>'nullable|boolean',
            'is_featured'=>'nullable|boolean',
            'slug'=>'nullable|string|max:160|unique:faqs,slug',
        ]);
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['question']);
        $data['is_published'] = $request->boolean('is_published', true);
        $data['is_featured'] = $request->boolean('is_featured');
        Faq::create($data);
        return back()->with('toast','FAQ added ✓ — JSON-LD auto-updates on landing');
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'=>'required|string|max:200',
            'answer'=>'required|string|max:2000',
            'category'=>'required|string|max:40',
            'sort_order'=>'nullable|integer|min:0|max:999',
            'is_published'=>'nullable|boolean',
            'is_featured'=>'nullable|boolean',
            'slug'=>'required|string|max:160|unique:faqs,slug,'.$faq->id,
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        $faq->update($data);
        return back()->with('toast','FAQ updated ✓');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('toast','FAQ deleted');
    }
}
