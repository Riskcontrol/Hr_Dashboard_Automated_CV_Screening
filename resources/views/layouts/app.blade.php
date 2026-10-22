<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HR Recruitment Dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="min-h-screen">

    @auth
        <div class="flex">

            @if(Auth::user()->hasAdminPrivileges())
                <nav class="sidebar w-64 flex-shrink-0">
                    <div class="sticky top-0 p-6 overflow-y-auto" style="max-height:100vh;">

                        <div class="text-center mb-8">
                            <h2 class="text-xl font-bold text-white">HR Dashboard</h2>
                            <p class="text-primary-200 text-sm">Risk Control Services Nigeria</p>
                        </div>

                        <ul class="space-y-2">

                            <li>
                                <a href="{{ route('admin.dashboard') }}"
                                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.keyword-sets.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.keyword-sets.*') ? 'active' : '' }}">
                                    <i class="fas fa-tags mr-3"></i> Job Positions
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.applications.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-alt mr-3"></i> Applications
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.staff.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                                    <i class="fas fa-users mr-3"></i> Staff Profiles
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.leave.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-alt mr-3"></i> Annual Leave
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.kpis.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.kpis.*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line mr-3"></i> KPI Tracking
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.compliance.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.compliance.*') ? 'active' : '' }}">
                                    <i class="fas fa-shield-alt mr-3"></i> Compliance
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.appraisals.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.appraisals.*') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list mr-3"></i> Appraisals
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.appraisal-schedule.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.appraisal-schedule.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-check mr-3"></i> Appraisal Schedule
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.attendance.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-clock mr-3"></i> Absenteeism
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.exit-reports.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.exit-reports.*') ? 'active' : '' }}">
                                    <i class="fas fa-door-open mr-3"></i> Exit Reports
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.employee-rules.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.employee-rules.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-pdf mr-3"></i> Employee Rules PDF
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.pension.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.pension.*') ? 'active' : '' }}">
                                    <i class="fas fa-shield-alt mr-3"></i> Pension
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.users.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-cog mr-3"></i> User Accounts
                                    @php try { $pendingCount = \App\Models\User::where('status','pending')->count(); } catch(\Exception $e) { $pendingCount = 0; } @endphp
                                    @if($pendingCount > 0)
                                        <span class="ml-auto bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.users.own-password') }}"
                                   class="nav-link {{ request()->routeIs('admin.users.own-password') ? 'active' : '' }}">
                                    <i class="fas fa-key mr-3"></i> Change Password
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.activity-log.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                                    <i class="fas fa-history mr-3"></i> Activity Log
                                </a>
                            </li>

                            <li class="pt-4 border-t border-primary-700 mt-4">
                                <a href="{{ url('/') }}" target="_blank" class="nav-link">
                                    <i class="fas fa-external-link-alt mr-3"></i> View Application Form
                                </a>
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link w-full text-left">
                                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>
                </nav>

            @else
                <nav class="sidebar w-64 flex-shrink-0">
                    <div class="sticky top-0 p-6 overflow-y-auto" style="max-height:100vh;">

                        <div class="text-center mb-8">
                            <h2 class="text-xl font-bold text-white">My Workspace</h2>
                            <p class="text-primary-200 text-sm">Risk Control Services Nigeria</p>
                        </div>

                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('my.dashboard') }}"
                                   class="nav-link {{ request()->routeIs('my.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my.leave.index') }}"
                                   class="nav-link {{ request()->routeIs('my.leave.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-alt mr-3"></i> My Leave
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my.appraisals.index') }}"
                                   class="nav-link {{ request()->routeIs('my.appraisals.*') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list mr-3"></i> My Appraisals
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my.employee-rules') }}"
                                   class="nav-link {{ request()->routeIs('my.employee-rules*') ? 'active' : '' }}">
                                    <i class="fas fa-file-pdf mr-3"></i> Employee Rules PDF
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my.password') }}"
                                   class="nav-link {{ request()->routeIs('my.password') ? 'active' : '' }}">
                                    <i class="fas fa-key mr-3"></i> Change Password
                                </a>
                            </li>

                            <li class="pt-4 border-t border-primary-700 mt-4">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link w-full text-left">
                                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
            @endif

            <main class="flex-1 overflow-auto">
                <div class="p-6">
                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>

        </div>
    @endauth

    @guest
        <main class="w-full">
            <div class="p-6">
                @if (session('success'))
                    <div class="alert alert-success mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    @endguest

</div>
@stack('scripts')
</body>
</html>