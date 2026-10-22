<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('module', 'like', "%$s%");
            });
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs    = $query->paginate(50)->withQueryString();
        $modules = ActivityLog::distinct()->orderBy('module')->pluck('module')->filter();

        $stats = [
            'total'        => ActivityLog::count(),
            'today'        => ActivityLog::today()->count(),
            'unique_users' => ActivityLog::whereDate('created_at', now()->toDateString())
                                ->distinct('user_id')->count('user_id'),
            'last_action'  => ActivityLog::latest()->value('description'),
        ];

        return view('admin.activity-log.index', compact('logs', 'modules', 'stats'));
    }
}