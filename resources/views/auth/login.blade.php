<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Aplikasi POS</title>
    <meta name="description" content="Login ke Aplikasi POS untuk memulai transaksi">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    borderRadius: { '4xl': '2rem' },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-up': 'fadeUp 0.6s ease-out forwards',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-12px)' },
                        },
                        fadeUp: {
                            '0%': { opacity: 0, transform: 'translateY(24px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style type="text/tailwindcss">
        body { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%); }
        .bg-mesh {
            background-image:
                radial-gradient(at 20% 50%, rgba(59, 130, 246, 0.15) 0, transparent 50%),
                radial-gradient(at 80% 20%, rgba(139, 92, 246, 0.1) 0, transparent 50%),
                radial-gradient(at 50% 80%, rgba(16, 185, 129, 0.08) 0, transparent 50%);
        }
        .form-input {
            @apply w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-11 pr-4 text-slate-800 text-sm
                   placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                   outline-none transition-all duration-200 shadow-sm;
        }
        .form-input.error {
            @apply border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10;
        }
        .brand-icon-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%) !important;
        }
        .card-glow-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%) !important;
        }
        .btn-submit-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%) !important;
            color: #ffffff !important;
        }
        .btn-submit-gradient:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%) !important;
        }
        /* Hide Edge/IE default reveal password icon */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center p-4 bg-mesh">

    {{-- Background Decorations --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay:1.5s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-400/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Login Card Container --}}
    <div class="w-full max-w-md relative animate-fade-up">
        <!-- Glow effect behind the card -->
        <div class="absolute inset-0 card-glow-gradient rounded-4xl blur-2xl opacity-15 -z-10 animate-pulse-slow"></div>
        
        <div class="bg-white rounded-4xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] border border-slate-100 overflow-hidden">
            
            {{-- Logo and Title Header --}}
            <div class="px-8 pt-10 pb-5 text-center">
                {{-- Decorative icon container --}}
                <div class="inline-flex items-center justify-center w-16 h-16 brand-icon-gradient rounded-2xl mb-4 shadow-[0_8px_20px_rgba(37,99,235,0.3)] animate-float">
                    <i class="fa-solid fa-cash-register text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Aplikasi POS</h1>
                <p class="text-sm text-slate-400 mt-1">Sistem Point of Sale & Kasir Modern</p>
            </div>

            {{-- Form Content Area --}}
            <div class="px-8 pb-10 pt-2">

                {{-- Alert Error --}}
                @if ($errors->any() || session('error'))
                <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl p-4">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
                    <div class="text-sm text-red-700 font-medium leading-relaxed">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                        @if(session('error')) <p>{{ session('error') }}</p> @endif
                    </div>
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" id="login-form" novalidate class="space-y-5">
                    @csrf

                    {{-- Username --}}
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Username
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-user text-slate-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                            </div>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                autofocus
                                class="form-input {{ $errors->has('username') ? 'error' : '' }}">
                        </div>
                        @error('username')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5 font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Password
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                class="form-input pr-12 {{ $errors->has('password') ? 'error' : '' }}">
                            {{-- Toggle visibility --}}
                            <button type="button" id="toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5 font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer group select-none">
                            <input type="checkbox" name="remember" id="remember"
                                class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500/30 focus:ring-offset-0 cursor-pointer transition-all">
                            <span class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors">Ingat akun saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit" id="btn-login"
                            class="w-full btn-submit-gradient hover:shadow-[0_10px_25px_rgba(37,99,235,0.4)]
                                   transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0
                                   flex items-center justify-center gap-2 text-sm font-semibold py-3.5 rounded-2xl
                                   shadow-[0_8px_20px_rgba(37,99,235,0.25)]">
                            <i class="fa-solid fa-right-to-bracket text-base"></i>
                            <span id="btn-text">Masuk ke Sistem</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="bg-slate-50 border-t border-slate-100 px-8 py-4 text-center">
                <p class="text-xs text-slate-400 font-medium">
                    &copy; {{ date('Y') }} Aplikasi POS &mdash; Sistem Kasir Modern
                </p>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const toggleBtn = document.getElementById('toggle-password');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const rememberCheckbox = document.getElementById('remember');
        const loginForm = document.getElementById('login-form');

        // Load remembered credentials on page load
        if (localStorage.getItem('remember_me') === 'true') {
            if (usernameInput) usernameInput.value = localStorage.getItem('remember_username') || '';
            if (passwordInput) passwordInput.value = localStorage.getItem('remember_password') || '';
            if (rememberCheckbox) rememberCheckbox.checked = true;
        }

        // Toggle password visibility
        toggleBtn?.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.className = `fa-solid ${isPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`;
        });

        // Form submission: save credentials and show loading state
        loginForm?.addEventListener('submit', function() {
            // Save or clear credentials based on checkbox state
            if (rememberCheckbox && rememberCheckbox.checked) {
                localStorage.setItem('remember_me', 'true');
                localStorage.setItem('remember_username', usernameInput.value || '');
                localStorage.setItem('remember_password', passwordInput.value || '');
            } else {
                localStorage.removeItem('remember_me');
                localStorage.removeItem('remember_username');
                localStorage.removeItem('remember_password');
            }

            // Show loading animation on the button
            const btn = document.getElementById('btn-login');
            const text = document.getElementById('btn-text');
            if (btn && text) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                text.textContent = 'Memproses...';
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = 'fa-solid fa-spinner fa-spin';
                }
            }
        });
    </script>
</body>
</html>
