<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
        <p class="text-gray-600">Sign in to your HR Dashboard account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2 text-gray-400"></i>Email Address
            </label>
            <input id="email" 
                   class="form-input-premium w-full px-4 py-3 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   placeholder="Enter your email address"
                   required 
                   autofocus 
                   autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-gray-400"></i>Password
            </label>
            <div class="relative">
                <input id="password" 
                       class="form-input-premium w-full px-4 py-3 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none pr-12"
                       type="password"
                       name="password"
                       placeholder="Enter your password"
                       required 
                       autocomplete="current-password" />
                <button type="button" 
                        onclick="togglePassword()" 
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i id="password-toggle-icon" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center cursor-pointer">
                <input id="remember_me" 
                       type="checkbox" 
                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                       name="remember">
                <span class="ml-2 text-sm text-gray-600 font-medium">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200" 
                   href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="pt-4">
            <button type="submit" 
                    class="btn-premium w-full text-white font-semibold py-3 px-6 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 text-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>
        </div>

        <!-- Additional Links -->
        @if (Route::has('register'))
            <div class="text-center pt-6 border-t border-gray-200">
                <p class="text-gray-600 mb-3">Don't have an account?</p>
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                    <i class="fas fa-user-plus mr-2"></i>Create New Account
                </a>
            </div>
        @endif
    </form>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Add floating label effect
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input-premium');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        });
    </script>
</x-guest-layout>
