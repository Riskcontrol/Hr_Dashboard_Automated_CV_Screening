<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HR Recruitment Dashboard') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        @auth
            @if(Auth::user()->hasAdminPrivileges())
                <div class="flex">
                    <!-- Sidebar -->
                    <nav class="sidebar w-64 flex-shrink-0">
                        <div class="sticky top-0 p-6">
                            <div class="text-center mb-8">
                                <h2 class="text-xl font-bold text-white">HR Dashboard</h2>
                                <p class="text-primary-200 text-sm">Risk Control Services Nigeria</p>
                            </div>
                            
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-tachometer-alt mr-3"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.keyword-sets.index') }}" 
                                       class="nav-link {{ request()->routeIs('admin.keyword-sets.*') ? 'active' : '' }}">
                                        <i class="fas fa-tags mr-3"></i>
                                        Job Positions
                                    </a>
                                </li>
                                {{-- Applications route will be added later --}}
                                {{-- 
                                <li>
                                    <a href="{{ route('admin.applications.index') }}" 
                                       class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-alt mr-3"></i>
                                        Applications
                                    </a>
                                </li>
                                --}}
                                <li>
                                    <a href="{{ url('/') }}" target="_blank" class="nav-link">
                                        <i class="fas fa-external-link-alt mr-3"></i>
                                        View Application Form
                                    </a>
                                </li>
                                <li class="pt-4">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="nav-link w-full text-left">
                                            <i class="fas fa-sign-out-alt mr-3"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <!-- Main content -->
                    <main class="flex-1 overflow-auto">
            @endif
        @endauth

        @guest
            <main class="w-full">
        @endguest

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

        @auth
            @if(Auth::user()->hasAdminPrivileges())
                </div>
            @endif
        @endauth
    </div>

    @stack('scripts')
</body>
</html>