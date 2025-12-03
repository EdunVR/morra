<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - ERP System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen">
    
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            
            {{-- Logo --}}
            <div class="text-center mb-8">
                <img src="{{ url(asset('img/logo_xx.png')) }}" alt="Logo" class="h-24 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-slate-800">ERP System</h1>
                <p class="text-slate-600 mt-2">Masuk ke akun Anda</p>
            </div>

            {{-- Login Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
                
                {{-- Error Messages --}}
                @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-start gap-3">
                        <i class='bx bx-error-circle text-2xl text-red-600'></i>
                        <div class="flex-1">
                            <h3 class="font-semibold text-red-800 mb-1">Login Gagal</h3>
                            <ul class="text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-envelope text-slate-400 text-xl'></i>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                placeholder="nama@email.com"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-lock-alt text-slate-400 text-xl'></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center gap-2"
                    >
                        <i class='bx bx-log-in text-xl'></i>
                        Masuk
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-6 text-center text-sm text-slate-600">
                    <p>Default Login:</p>
                    <p class="font-mono text-xs mt-1">superadmin@morra.com / SuperAdmin@123</p>
                </div>
            </div>

            {{-- Copyright --}}
            <div class="mt-8 text-center text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} ERP System. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
    // Auto-refresh CSRF token every 30 minutes to prevent expiration
    setInterval(function() {
        fetch('{{ route("login") }}')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newToken = doc.querySelector('meta[name="csrf-token"]').content;
                document.querySelector('meta[name="csrf-token"]').content = newToken;
                document.querySelector('input[name="_token"]').value = newToken;
                console.log('CSRF token refreshed');
            })
            .catch(error => console.error('Failed to refresh CSRF token:', error));
    }, 30 * 60 * 1000); // 30 minutes

    // Handle form submission with CSRF error handling
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Memproses...';
    });

    // Show message if page was idle for too long
    let idleTime = 0;
    setInterval(function() {
        idleTime++;
        if (idleTime > 60) { // 60 minutes
            const notice = document.createElement('div');
            notice.className = 'fixed top-4 right-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4 shadow-lg max-w-sm';
            notice.innerHTML = `
                <div class="flex items-start gap-3">
                    <i class='bx bx-info-circle text-2xl text-yellow-600'></i>
                    <div>
                        <h4 class="font-semibold text-yellow-800">Sesi Mungkin Expired</h4>
                        <p class="text-sm text-yellow-700 mt-1">Jika login gagal, refresh halaman ini.</p>
                    </div>
                </div>
            `;
            document.body.appendChild(notice);
            setTimeout(() => notice.remove(), 10000);
            idleTime = 0;
        }
    }, 60000); // Check every minute

    // Reset idle time on activity
    document.addEventListener('mousemove', () => idleTime = 0);
    document.addEventListener('keypress', () => idleTime = 0);
    </script>

</body>
</html>
