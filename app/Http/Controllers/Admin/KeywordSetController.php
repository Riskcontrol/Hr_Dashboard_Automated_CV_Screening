<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeywordSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeywordSetController extends Controller
{
    public function index()
    {
        try {
            $keywordSets = KeywordSet::latest()->paginate(10);
        } catch (\Exception $e) {
            $keywordSets = collect()->paginate(10);
        }

        return view('admin.keyword-sets.index', compact('keywordSets'));
    }

    public function create()
    {
        return view('admin.keyword-sets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'keywords' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        // Process keywords - split by comma and clean up
        $keywords = array_map('trim', explode(',', $request->keywords));
        $keywords = array_filter($keywords); // Remove empty values

        try {
            KeywordSet::create([
                'job_title' => $request->job_title,
                'keywords' => $keywords,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id() ?? 1, // Fallback to 1 if no auth
            ]);

            return redirect()->route('admin.keyword-sets.index')
                ->with('success', 'Keyword set created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating keyword set: ' . $e->getMessage());
        }
    }

    public function show(KeywordSet $keywordSet)
    {
        // Simplified stats without database relationships for now
        $stats = [
            'total_applications' => 0,
            'qualified' => 0,
            'not_qualified' => 0,
            'pending' => 0,
        ];

        try {
            // Only try to get stats if applications table exists and has proper columns
            if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'keyword_set_id')) {
                $stats['total_applications'] = DB::table('applications')->where('keyword_set_id', $keywordSet->id)->count();
                
                if (Schema::hasColumn('applications', 'qualification_status')) {
                    $stats['qualified'] = DB::table('applications')
                        ->where('keyword_set_id', $keywordSet->id)
                        ->where('qualification_status', 'qualified')
                        ->count();
                        
                    $stats['not_qualified'] = DB::table('applications')
                        ->where('keyword_set_id', $keywordSet->id)
                        ->where('qualification_status', 'not_qualified')
                        ->count();
                        
                    $stats['pending'] = DB::table('applications')
                        ->where('keyword_set_id', $keywordSet->id)
                        ->where('qualification_status', 'pending')
                        ->count();
                }
            }
        } catch (\Exception $e) {
            // Keep default stats if any error occurs
            Log::info('Error getting keyword set stats: ' . $e->getMessage());
        }

        return view('admin.keyword-sets.show', compact('keywordSet', 'stats'));
    }

    public function edit(KeywordSet $keywordSet)
    {
        return view('admin.keyword-sets.edit', compact('keywordSet'));
    }

    public function update(Request $request, KeywordSet $keywordSet)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'keywords' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        $keywords = array_map('trim', explode(',', $request->keywords));
        $keywords = array_filter($keywords);

        try {
            $keywordSet->update([
                'job_title' => $request->job_title,
                'keywords' => $keywords,
                'description' => $request->description,
                'is_active' => $request->has('is_active'), // This properly handles checkbox
            ]);

            return redirect()->route('admin.keyword-sets.show', $keywordSet)
                ->with('success', 'Keyword set updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating keyword set: ' . $e->getMessage());
        }
    }

    public function destroy(KeywordSet $keywordSet)
    {
        try {
            $keywordSet->delete();
            return redirect()->route('admin.keyword-sets.index')
                ->with('success', 'Keyword set deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.keyword-sets.index')
                ->with('error', 'Error deleting keyword set: ' . $e->getMessage());
        }
    }

    // Add new method for toggling status
    public function toggleStatus(KeywordSet $keywordSet)
    {
        try {
            $keywordSet->update([
                'is_active' => !$keywordSet->is_active
            ]);

            $status = $keywordSet->is_active ? 'activated' : 'deactivated';
            
            return redirect()->route('admin.keyword-sets.show', $keywordSet)
                ->with('success', "Keyword set has been {$status} successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }
}