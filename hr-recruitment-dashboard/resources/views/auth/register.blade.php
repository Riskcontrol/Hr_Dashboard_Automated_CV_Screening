<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h2>
        <p class="text-gray-600">Join our HR Dashboard platform</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user mr-2 text-gray-400"></i>Full Name
            </label>
            <input id="name" 
                   class="form-input-premium w-full px-4 py-3 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   placeholder="Enter your full name"
                   required 
                   autofocus 
                   autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
        </div>

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
                       placeholder="Create a strong password"
                       required 
                       autocomplete="new-password" />
                <button type="button" 
                        onclick="togglePasswordRegister('password')" 
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i id="password-toggle-icon" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-gray-400"></i>Confirm Password
            </label>
            <div class="relative">
                <input id="password_confirmation" 
                       class="form-input-premium w-full px-4 py-3 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none pr-12"
                       type="password"
                       name="password_confirmation" 
                       placeholder="Confirm your password"
                       required 
                       autocomplete="new-password" />
                <button type="button" 
                        onclick="togglePasswordRegister('password_confirmation')" 
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i id="password_confirmation-toggle-icon" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Register Button -->
        <div class="pt-4">
            <button type="submit" 
                    class="btn-premium w-full text-white font-semibold py-3 px-6 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 text-lg">
                <i class="fas fa-user-plus mr-2"></i>Create Account
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-6 border-t border-gray-200">
            <p class="text-gray-600 mb-3">Already have an account?</p>
            <a href="{{ route('login') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In Instead
            </a>
        </div>
    </form>

    <script>
        function togglePasswordRegister(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + '-toggle-icon');
            
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
    </form>
</x-guest-layout>
