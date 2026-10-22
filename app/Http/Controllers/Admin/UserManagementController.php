<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $stats = [
            'total'     => User::count(),
            'pending'   => User::where('status', 'pending')->count(),
            'active'    => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function approve(User $user)
    {
        $user->update([
            'status'      => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->user()->name,
        ]);
        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name}'s account has been approved. They can now log in.");
    }

    public function suspend(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot suspend your own account.');
        $user->update(['status' => 'suspended']);
        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name}'s account has been suspended.");
    }

    public function reactivate(User $user)
    {
        $user->update(['status' => 'active']);
        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name}'s account has been reactivated.");
    }

    /** HR changes a staff member's password */
    public function editPassword(User $user)
    {
        return view('admin.users.change-password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->route('admin.users.index')
            ->with('success', "Password updated for {$user->name}.");
    }

    /** HR or staff changes their OWN password */
    public function editOwnPassword()
    {
        // HR gets the admin layout, staff gets the my layout
        if (auth()->user()->hasAdminPrivileges()) {
            return view('admin.users.change-own-password');
        }
        return view('my.change-password');
    }

    public function updateOwnPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return redirect()->back()
            ->with('success', 'Your password has been updated successfully.');
    }
}