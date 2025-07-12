<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\KeywordSet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $stats = $this->getOverallStats();
        $chartData = $this->getChartData();
        $keywordEffectiveness = $this->getKeywordEffectiveness();
        
        return view('admin.analytics.index', compact('stats', 'chartData', 'keywordEffectiveness'));
    }

    private function getOverallStats()
    {
        $totalApps = Application::count();
        
        return [
            'total_applications' => $totalApps,
            'this_month' => Application::whereMonth('created_at', now()->month)->count(),
            'qualified_rate' => $totalApps > 0 ? round((Application::qualified()->count() / $totalApps) * 100, 2) : 0,
            'avg_processing_time' => Application::whereNotNull('processed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_time')
                ->first()->avg_time ?? 0,
            'top_position' => KeywordSet::withCount('applications')
                ->orderBy('applications_count', 'desc')
                ->first()->job_title ?? 'N/A',
        ];
    }

    private function getChartData()
    {
        // Applications by day (last 30 days)
        $dailyApps = Application::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Applications by status
        $statusData = Application::selectRaw('qualification_status, COUNT(*) as count')
            ->groupBy('qualification_status')
            ->get();

        return [
            'daily_applications' => $dailyApps,
            'status_breakdown' => $statusData,
        ];
    }

    private function getKeywordEffectiveness()
    {
        return KeywordSet::withCount([
            'applications',
            'applications as qualified_count' => function($query) {
                $query->where('qualification_status', 'qualified');
            }
        ])->get()->map(function($set) {
            $set->qualification_rate = $set->applications_count > 0 
                ? round(($set->qualified_count / $set->applications_count) * 100, 2)
                : 0;
            return $set;
        });
    }

    public function export()
    {
        $applications = Application::with('keywordSet')->get();
        
        $csvData = [
            ['ID', 'Name', 'Email', 'Position', 'Status', 'Match %', 'Submitted', 'Processed']
        ];

        foreach ($applications as $app) {
            $csvData[] = [
                $app->id,
                $app->applicant_name,
                $app->applicant_email,
                $app->keywordSet->job_title ?? 'N/A',
                $app->qualification_status,
                $app->match_percentage . '%',
                $app->created_at->format('Y-m-d H:i:s'),
                $app->processed_at ? $app->processed_at->format('Y-m-d H:i:s') : 'Not processed'
            ];
        }

        $filename = 'hr_analytics_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        return response()->streamDownload(function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}