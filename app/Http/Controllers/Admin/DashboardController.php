<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\KeywordSet;
use App\Models\StaffProfile;
use App\Models\LeaveRequest;
use App\Models\ComplianceRecord;
use App\Models\Appraisal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // -------------------------------------------------------
        // CV SCREENING STATS (original stats)
        // -------------------------------------------------------
        $stats = [
            'total_applications'            => 0,
            'qualified_applications'        => 0,
            'fairly_qualified_applications' => 0,
            'pending_applications'          => 0,
            'keyword_sets'                  => 0,
            'qualification_rate'            => 0,
        ];

        try {
            if (Schema::hasTable('applications')) {
                $stats['total_applications']            = Application::count();
                $stats['qualified_applications']        = Application::where('qualification_status', 'qualified')->count();
                $stats['fairly_qualified_applications'] = Application::where('qualification_status', 'Fairly Qualified')->count();
                $stats['pending_applications']          = Application::where('qualification_status', 'pending')->count();
            }
            if (Schema::hasTable('keyword_sets')) {
                $stats['keyword_sets'] = KeywordSet::where('is_active', true)->count();
            }
            $stats['qualification_rate'] = $stats['total_applications'] > 0
                ? round((($stats['qualified_applications'] + $stats['fairly_qualified_applications']) / $stats['total_applications']) * 100, 2)
                : 0;
        } catch (\Exception $e) {
            Log::info('Dashboard stats error: ' . $e->getMessage());
        }

        // -------------------------------------------------------
        // HR MODULE STATS
        // These power the quick-summary cards for each HR module.
        // Each stat is wrapped in try/catch so a missing table
        // never crashes the whole dashboard.
        // -------------------------------------------------------
        $hrStats = [
            'total_staff'         => 0,
            'active_staff'        => 0,
            'pending_leave'       => 0,
            'expiring_compliance' => 0, // Documents expiring within 30 days
            'expired_compliance'  => 0, // Already expired documents
            'overdue_appraisals'  => 0, // Appraisals past their due date
        ];

        try {
            if (Schema::hasTable('staff_profiles')) {
                $hrStats['total_staff']  = StaffProfile::count();
                $hrStats['active_staff'] = StaffProfile::where('status', 'active')->count();
            }
        } catch (\Exception $e) {
            Log::info('Staff stats error: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('leave_requests')) {
                $hrStats['pending_leave'] = LeaveRequest::where('status', 'pending')->count();
            }
        } catch (\Exception $e) {
            Log::info('Leave stats error: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('compliance_records')) {
                // Expiring within 30 days but not yet expired
                $hrStats['expiring_compliance'] = ComplianceRecord::whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '>=', now())
                    ->whereDate('expiry_date', '<=', now()->addDays(30))
                    ->count();

                // Already expired
                $hrStats['expired_compliance'] = ComplianceRecord::whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<', now())
                    ->count();
            }
        } catch (\Exception $e) {
            Log::info('Compliance stats error: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('appraisals')) {
                // Appraisals that are past their due date and not yet completed
                $hrStats['overdue_appraisals'] = Appraisal::whereIn('status', ['pending', 'in_progress'])
                    ->whereDate('due_date', '<', now())
                    ->count();
            }
        } catch (\Exception $e) {
            Log::info('Appraisal stats error: ' . $e->getMessage());
        }

        // -------------------------------------------------------
        // STAFF BIRTHDAYS
        // Shows staff members whose birthday falls within the next
        // 30 days so HR can acknowledge them.
        // We compare only the month and day (ignoring the year).
        // -------------------------------------------------------
        $upcomingBirthdays = collect();

        try {
            if (Schema::hasTable('staff_profiles')) {
                $today    = now();
                $in30Days = now()->addDays(30);
                $todayMMDD    = $today->format('m-d');
                $in30DaysMMDD = $in30Days->format('m-d');

                $upcomingBirthdays = StaffProfile::whereNotNull('date_of_birth')
                    ->where('status', 'active')
                    ->get()
                    ->filter(function ($staff) use ($todayMMDD, $in30DaysMMDD) {
                        // Extract month-day from their birthday
                        $birthdayMMDD = $staff->date_of_birth->format('m-d');

                        // Handle year wrap-around (e.g. today = Dec 20, window goes into Jan)
                        if ($todayMMDD <= $in30DaysMMDD) {
                            return $birthdayMMDD >= $todayMMDD && $birthdayMMDD <= $in30DaysMMDD;
                        } else {
                            return $birthdayMMDD >= $todayMMDD || $birthdayMMDD <= $in30DaysMMDD;
                        }
                    })
                    ->sortBy(function ($staff) {
                        // Sort by next upcoming birthday
                        $birthdayMMDD = $staff->date_of_birth->format('m-d');
                        $todayMMDD    = now()->format('m-d');
                        if ($birthdayMMDD >= $todayMMDD) {
                            return $birthdayMMDD;
                        }
                        return '99-' . $birthdayMMDD; // push past year-end dates to the end
                    })
                    ->values();
            }
        } catch (\Exception $e) {
            Log::info('Birthday stats error: ' . $e->getMessage());
        }

        // Recent applications (unchanged from original)
        $recentApplications = collect();
        try {
            if (Schema::hasTable('applications')) {
                $recentApplications = Application::with('keywordSet')
                    ->latest()
                    ->get();
            }
        } catch (\Exception $e) {
            // Keep empty collection
        }

        return view('admin.dashboard', compact(
            'stats',
            'hrStats',
            'upcomingBirthdays',
            'recentApplications'
        ));
    }
}