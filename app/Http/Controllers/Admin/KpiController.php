<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

/**
 * KpiController
 * Handles all KPI tracking operations.
 * Auto-calculates scores and ratings when actual values are saved.
 */
class KpiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kpi::with('staffProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('staffProfile', fn($q2) => $q2->where('full_name', 'like', "%$search%"));
            });
        }

        if ($request->filled('department'))  $query->where('department', $request->department);
        if ($request->filled('rating'))      $query->where('rating', $request->rating);
        if ($request->filled('period_type')) $query->where('period_type', $request->period_type);
        if ($request->filled('status'))      $query->where('status', $request->status);

        $kpis        = $query->latest()->paginate(15)->withQueryString();
        $departments = Kpi::distinct()->pluck('department')->filter()->sort()->values();

        $stats = [
            'total'     => Kpi::count(),
            'excellent' => Kpi::where('rating', 'excellent')->count(),
            'good'      => Kpi::where('rating', 'good')->count(),
            'poor'      => Kpi::whereIn('rating', ['poor', 'critical'])->count(),
            'avg_score' => round(Kpi::avg('score_percentage') ?? 0, 1),
        ];

        return view('admin.kpis.index', compact('kpis', 'departments', 'stats'));
    }

    public function create()
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.kpis.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'period_type'      => 'required|in:monthly,quarterly,annually',
            'period_start'     => 'required|date',
            'period_end'       => 'required|date|after_or_equal:period_start',
            'target_value'     => 'required|numeric|min:0',
            'actual_value'     => 'nullable|numeric|min:0',
            'unit'             => 'nullable|string|max:50',
            'status'           => 'required|in:active,completed,cancelled',
            'notes'            => 'nullable|string',
            'reviewed_by'      => 'nullable|string|max:255',
        ]);

        if (isset($validated['actual_value']) && $validated['actual_value'] !== null) {
            $score = Kpi::calculateScore($validated['target_value'], $validated['actual_value']);
            $validated['score_percentage'] = $score;
            $validated['rating']           = Kpi::determineRating($score);
        }

        Kpi::create($validated);

        return redirect()->route('admin.kpis.index')->with('success', 'KPI created successfully!');
    }

    public function show(Kpi $kpi)
    {
        $kpi->load('staffProfile');
        $staffKpis = Kpi::where('staff_profile_id', $kpi->staff_profile_id)
            ->where('id', '!=', $kpi->id)->latest()->take(5)->get();
        return view('admin.kpis.show', compact('kpi', 'staffKpis'));
    }

    public function edit(Kpi $kpi)
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.kpis.edit', compact('kpi', 'staff'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'period_type'      => 'required|in:monthly,quarterly,annually',
            'period_start'     => 'required|date',
            'period_end'       => 'required|date|after_or_equal:period_start',
            'target_value'     => 'required|numeric|min:0',
            'actual_value'     => 'nullable|numeric|min:0',
            'unit'             => 'nullable|string|max:50',
            'status'           => 'required|in:active,completed,cancelled',
            'notes'            => 'nullable|string',
            'reviewed_by'      => 'nullable|string|max:255',
        ]);

        if (isset($validated['actual_value']) && $validated['actual_value'] !== null) {
            $score = Kpi::calculateScore($validated['target_value'], $validated['actual_value']);
            $validated['score_percentage'] = $score;
            $validated['rating']           = Kpi::determineRating($score);
        } else {
            $validated['score_percentage'] = null;
            $validated['rating']           = null;
        }

        $kpi->update($validated);

        return redirect()->route('admin.kpis.show', $kpi)->with('success', 'KPI updated successfully!');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return redirect()->route('admin.kpis.index')->with('success', 'KPI deleted successfully.');
    }
}