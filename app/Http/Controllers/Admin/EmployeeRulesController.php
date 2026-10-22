<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeRule;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmployeeRulesController extends Controller
{
    public function index()
    {
        $handbook        = EmployeeRule::where('type', 'handbook')->latest()->get();
        $codeOfConduct   = EmployeeRule::where('type', 'code_of_conduct')->latest()->get();
        $staffCount      = StaffProfile::where('status', 'active')->count();
        $linkedUserCount = User::whereHas('staffProfile')->count();

        return view('admin.employee-rules.index', compact(
            'handbook', 'codeOfConduct', 'staffCount', 'linkedUserCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'         => 'required|in:handbook,code_of_conduct',
            'title'        => 'required|string|max:255',
            'document'     => 'required|file|mimes:pdf|max:20480',
            'notify_staff' => 'nullable|boolean',
        ]);

        $file     = $request->file('document');
        $filePath = $file->store('employee-rules', 'public');

        $rule = EmployeeRule::create([
            'type'         => $request->type,
            'title'        => $request->title,
            'file_path'    => $filePath,
            'file_name'    => $file->getClientOriginalName(),
            'uploaded_by'  => auth()->user()->name ?? auth()->user()->email,
            'notify_staff' => $request->boolean('notify_staff', true),
        ]);

        if ($rule->notify_staff) {
            $this->notifyAllStaff($rule);
            $rule->update(['notified_at' => now()]);
        }

        return redirect()->route('admin.employee-rules.index')
            ->with('success', "{$rule->type_label} uploaded successfully!" .
                ($rule->notify_staff ? ' All staff have been notified.' : ''));
    }

    public function download(EmployeeRule $employeeRule)
    {
        return response()->file(storage_path('app/public/' . $employeeRule->file_path));
    }

    public function destroy(EmployeeRule $employeeRule)
    {
        Storage::disk('public')->delete($employeeRule->file_path);
        $type = $employeeRule->type_label;
        $employeeRule->delete();

        return redirect()->route('admin.employee-rules.index')
            ->with('success', "{$type} deleted successfully.");
    }

    public function notify(EmployeeRule $employeeRule)
    {
        $this->notifyAllStaff($employeeRule);
        $employeeRule->update(['notified_at' => now()]);

        return redirect()->route('admin.employee-rules.index')
            ->with('success', 'Notification sent to all staff.');
    }

    private function notifyAllStaff(EmployeeRule $rule): void
    {
        $users = User::whereHas('staffProfile')->get();

        foreach ($users as $user) {
            Mail::send('emails.employee-rule-notification', [
                'user'    => $user,
                'rule'    => $rule,
                'viewUrl' => route('my.employee-rules'),
            ], function ($m) use ($user, $rule) {
                $m->to($user->email, $user->name)
                  ->subject("New document available: {$rule->title}");
            });
        }
    }
}