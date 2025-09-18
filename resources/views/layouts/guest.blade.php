<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HR Dashboard') }} - Professional Recruitment Platform</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            .bg-gradient-premium {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .bg-gradient-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .shadow-premium {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
            .form-input-premium {
                background: rgba(255, 255, 255, 0.9);
                border: 2px solid rgba(102, 126, 234, 0.1);
                transition: all 0.3s ease;
            }
            .form-input-premium:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                background: rgba(255, 255, 255, 1);
            }
            .btn-premium {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }
            .btn-premium:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            }
            .floating-animation {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gradient-premium min-h-screen">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Left Side - Branding & Info -->
            <div class="lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
                <div class="text-center text-white max-w-md">
                    <div class="floating-animation mb-8">
                        <div class="w-24 h-24 mx-auto mb-6 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-users-gear text-4xl text-white"></i>
                        </div>
                    </div>
                    
                    <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                        HR Dashboard
                    </h1>
                    <p class="text-xl mb-6 text-white/90 leading-relaxed">
                        Professional CV Screening & Recruitment Management Platform
                    </p>
                    <div class="space-y-3 text-white/80">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-robot mr-3 text-blue-200"></i>
                            <span>AI-Powered CV Analysis</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <i class="fas fa-chart-line mr-3 text-blue-200"></i>
                            <span>Advanced Analytics & Insights</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <i class="fas fa-shield-alt mr-3 text-blue-200"></i>
                            <span>Secure & Reliable Platform</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
                <div class="w-full max-w-md">
                    <div class="bg-gradient-card shadow-premium rounded-2xl p-8 lg:p-10">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Background decoration -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full"></div>
            <div class="absolute top-32 right-16 w-16 h-16 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-20 left-20 w-24 h-24 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-40 right-10 w-12 h-12 bg-white/10 rounded-full"></div>
        </div>
    </body>
</html>
