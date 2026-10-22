<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appraisal;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    public function index(Request $request)
    {
        $query = Appraisal::with('staffProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staffProfile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%");
            });
        }

        if ($request->filled('appraisal_type')) {
            $query->where('appraisal_type', $request->appraisal_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appraisals = $query->orderBy('due_date')->paginate(15)->withQueryString();

        $stats = [
            'total'     => Appraisal::count(),
            'pending'   => Appraisal::where('status', 'pending')->count(),
            'overdue'   => Appraisal::overdue()->count(),
            'due_soon'  => Appraisal::dueSoon()->count(),
            'completed' => Appraisal::where('status', 'completed')->count(),
        ];

        return view('admin.appraisals.index', compact('appraisals', 'stats'));
    }

    public function create()
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.appraisals.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_profile_id'    => 'required|exists:staff_profiles,id',
            'appraisal_type'      => 'required|in:probation_3month,probation_6month,annual,mid_year,pip',
            'form_type'           => 'nullable|in:self_evaluation,probation,annual',
            'due_date'            => 'required|date',
            'completed_date'      => 'nullable|date',
            'reviewer_name'       => 'required|string|max:255',
            'reviewer_role'       => 'nullable|string|max:100',
            'overall_rating'      => 'nullable|in:excellent,good,satisfactory,needs_improvement,unsatisfactory',
            'status'              => 'required|in:pending,in_progress,completed,cancelled',
            'probation_outcome'   => 'nullable|in:confirmed,extended,terminated',
            'performance_summary' => 'nullable|string',
            'strengths'           => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_next_period'   => 'nullable|string',
            'employee_comments'   => 'nullable|string',
            'reviewer_comments'   => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completed_date'])) {
            $validated['completed_date'] = now()->toDateString();
        }

        Appraisal::create($validated);

        return redirect()->route('admin.appraisals.index')
            ->with('success', 'Appraisal scheduled successfully!');
    }

    public function show(Appraisal $appraisal)
    {
        $appraisal->load('staffProfile');

        $otherAppraisals = Appraisal::where('staff_profile_id', $appraisal->staff_profile_id)
            ->where('id', '!=', $appraisal->id)
            ->orderBy('due_date', 'desc')
            ->take(5)
            ->get();

        return view('admin.appraisals.show', compact('appraisal', 'otherAppraisals'));
    }

    public function edit(Appraisal $appraisal)
    {
        $staff = StaffProfile::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.appraisals.edit', compact('appraisal', 'staff'));
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        $validated = $request->validate([
            'staff_profile_id'                    => 'required|exists:staff_profiles,id',
            'appraisal_type'                      => 'required|in:probation_3month,probation_6month,annual,mid_year,pip',
            'form_type'                           => 'nullable|in:self_evaluation,probation,annual',
            'due_date'                            => 'required|date',
            'completed_date'                      => 'nullable|date',
            'reviewer_name'                       => 'required|string|max:255',
            'reviewer_role'                       => 'nullable|string|max:100',
            'overall_rating'                      => 'nullable|in:excellent,good,satisfactory,needs_improvement,unsatisfactory',
            'status'                              => 'required|in:pending,in_progress,completed,cancelled',
            'probation_outcome'                   => 'nullable|in:confirmed,extended,terminated',
            'performance_summary'                 => 'nullable|string',
            'strengths'                           => 'nullable|string',
            'areas_for_improvement'               => 'nullable|string',
            'goals_next_period'                   => 'nullable|string',
            'employee_comments'                   => 'nullable|string',
            'reviewer_comments'                   => 'nullable|string',
            'self_mission_statement'              => 'nullable|string',
            'self_duties_understanding'           => 'nullable|string',
            'self_job_achievements'               => 'nullable|string',
            'self_other_achievements'             => 'nullable|string',
            'self_likes_dislikes'                 => 'nullable|string',
            'self_most_difficult'                 => 'nullable|string',
            'self_improvement_actions'            => 'nullable|string',
            'prob_professionalism'                => 'nullable|in:U,F,G,E',
            'prob_professionalism_comments'       => 'nullable|string',
            'prob_crisis_management'              => 'nullable|in:U,F,G,E',
            'prob_crisis_management_comments'     => 'nullable|string',
            'prob_quality_of_work'                => 'nullable|in:U,F,G,E',
            'prob_quality_of_work_comments'       => 'nullable|string',
            'prob_dependability'                  => 'nullable|in:U,F,G,E',
            'prob_dependability_comments'         => 'nullable|string',
            'prob_team_spirit'                    => 'nullable|in:U,F,G,E',
            'prob_team_spirit_comments'           => 'nullable|string',
            'prob_result_orientation'             => 'nullable|in:U,F,G,E',
            'prob_result_orientation_comments'    => 'nullable|string',
            'prob_followership'                   => 'nullable|in:U,F,G,E',
            'prob_followership_comments'          => 'nullable|string',
            'prob_self_discipline'                => 'nullable|in:U,F,G,E',
            'prob_self_discipline_comments'       => 'nullable|string',
            'prob_organisation_planning'          => 'nullable|in:U,F,G,E',
            'prob_organisation_planning_comments' => 'nullable|string',
            'prob_self_development'               => 'nullable|in:U,F,G,E',
            'prob_self_development_comments'      => 'nullable|string',
            'prob_skill_deficiencies'             => 'nullable|string',
            'prob_constraints'                    => 'nullable|string',
            'prob_appraisee_comments'             => 'nullable|string',
            'prob_observer_comments'              => 'nullable|string',
            'prob_hod_comments'                   => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completed_date'])) {
            $validated['completed_date'] = now()->toDateString();
        }

        $appraisal->recordEdit(auth()->user()->name ?? auth()->user()->email);
        $appraisal->fill($validated);
        $appraisal->save();

        return redirect()->route('admin.appraisals.show', $appraisal)
            ->with('success', 'Appraisal updated successfully!');
    }

    public function destroy(Appraisal $appraisal)
    {
        $type = $appraisal->appraisal_type_label;
        $appraisal->delete();

        return redirect()->route('admin.appraisals.index')
            ->with('success', "'{$type}' appraisal deleted successfully.");
    }

    /**
     * HR sends the appraisal form to the employee.
     */
    public function sendForm(Appraisal $appraisal)
    {
        $appraisal->recordEdit('HR — form sent to employee');
        $appraisal->sent_to_employee_at = now();
        $appraisal->status = 'in_progress';
        $appraisal->save();

        return redirect()->route('admin.appraisals.show', $appraisal)
            ->with('success', 'Appraisal form has been sent to the employee.');
    }
}