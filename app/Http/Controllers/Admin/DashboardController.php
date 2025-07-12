<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\KeywordSet;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Simple stats without caching for now
        $stats = [
            'total_applications' => 0,
            'qualified_applications' => 0,
            'pending_applications' => 0,
            'keyword_sets' => 0,
            'qualification_rate' => 0,
        ];

        try {
            // Check if tables exist before querying
            if (\Illuminate\Support\Facades\Schema::hasTable('applications')) {
                $stats['total_applications'] = Application::count();
                $stats['qualified_applications'] = Application::where('qualification_status', 'qualified')->count();
                $stats['pending_applications'] = Application::where('qualification_status', 'pending')->count();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('keyword_sets')) {
                $stats['keyword_sets'] = KeywordSet::where('is_active', true)->count();
            }

            $stats['qualification_rate'] = $stats['total_applications'] > 0 
                ? round(($stats['qualified_applications'] / $stats['total_applications']) * 100, 2)
                : 0;

        } catch (\Exception $e) {
            // If any database error occurs, keep default stats
            \Log::info('Dashboard stats error: ' . $e->getMessage());
        }

        // Get recent applications if table exists
        $recentApplications = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('applications')) {
                $recentApplications = Application::with('keywordSet')
                    ->latest()
                    ->take(5)
                    ->get();
            }
        } catch (\Exception $e) {
            // Keep empty collection
        }

        return view('admin.dashboard', compact('stats', 'recentApplications'));
    }
}
