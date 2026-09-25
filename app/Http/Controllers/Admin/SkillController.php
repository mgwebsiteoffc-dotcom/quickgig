<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        Skill::recount();

        $discipline = (string) $request->query('discipline', '');

        $skills = Skill::query()
            ->when($discipline !== '', fn ($q) => $q->where('discipline', $discipline))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->query('q') . '%'))
            ->ordered()
            ->get();

        return view('admin.skills.index', [
            'skills'      => $skills->groupBy('discipline'),
            'total'       => Skill::count(),
            'active'      => Skill::active()->count(),
            'disciplines' => Skill::DISCIPLINES,
            'filters'     => ['discipline' => $discipline, 'q' => $request->query('q', '')],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:60', Rule::unique('skills', 'name')],
            'discipline' => ['required', Rule::in(Skill::DISCIPLINES)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        Skill::create($data + ['is_active' => true]);

        return back()->with('toast', 'Skill added — freelancers can pick it immediately.');
    }

    /** Bulk add: one skill per line, for seeding a new discipline quickly. */
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'names'      => ['required', 'string', 'max:2000'],
            'discipline' => ['required', Rule::in(Skill::DISCIPLINES)],
        ]);

        $added = 0;
        foreach (preg_split('/[\r\n,]+/', $data['names']) as $name) {
            $name = trim($name);
            if ($name === '' || mb_strlen($name) > 60) continue;
            if (Skill::whereRaw('lower(name) = ?', [mb_strtolower($name)])->exists()) continue;

            Skill::create(['name' => $name, 'discipline' => $data['discipline'], 'is_active' => true]);
            $added++;
        }

        return back()->with('toast', $added ? "Added {$added} skills." : 'Nothing new to add.');
    }

    public function update(Request $request, Skill $skill)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:60', Rule::unique('skills', 'name')->ignore($skill->id)],
            'discipline' => ['required', Rule::in(Skill::DISCIPLINES)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $skill->update($data);

        return back()->with('toast', 'Skill updated.');
    }

    public function toggle(Skill $skill)
    {
        $skill->update(['is_active' => ! $skill->is_active]);

        return back()->with('toast', $skill->is_active
            ? $skill->name . ' is selectable again.'
            : $skill->name . ' is hidden from the picker (existing profiles keep it).');
    }

    public function destroy(Skill $skill)
    {
        $name = $skill->name;
        $skill->delete();

        return back()->with('toast', $name . ' removed from the library.');
    }
}
