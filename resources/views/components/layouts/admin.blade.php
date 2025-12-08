<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }" class="h-full bg-gradient-to-br from-slate-50 to-white overflow-x-hidden">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MORRA ERP' }}</title>

    {{-- Tailwind via CDN (tanpa NPM) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                container: { center: true, padding: '1rem' },
                extend: {
                    colors: {
                        primary: {50:'#eef7ff',100:'#daecff',200:'#b6d8ff',300:'#87beff',400:'#55a0ff',500:'#2f86ff',600:'#186ae6',700:'#1354b4',800:'#0f418c',900:'#0c356f'},
                        ink: { 900:'#0f172a', 700:'#334155', 500:'#64748b' }
                    },
                    boxShadow: {
                        card: '0 6px 20px rgba(15,23,42,.06)',
                        float: '0 14px 40px rgba(15,23,42,.10)',
                    },
                    borderRadius: { '2xl': '1rem' }
                }
            }
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <!-- Bootstrap (for modals) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Finance Components (Export/Import/Notifications) --}}
    <script src="{{ asset('js/finance-components.js') }}"></script>
    
    {{-- Chat Panel Component --}}
    <script src="{{ asset('js/chat-panel.js') }}"></script>

    {{-- Boxicons (untuk ikon di dashboard) --}}
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <style>
        /* Tambahan kecil untuk jaga overflow */
        html, body { width: 100%; }
        body { overflow-x: hidden; }
        svg { display: block; max-width: 100%; height: auto; }
        img { max-width: 100%; height: auto; }
        /* Hindari scroll karena transform sidebar (off-canvas) */
        aside[style], aside { contain: layout paint size; }
        
        /* ========================================
           CHAT SYSTEM STYLES
           ======================================== */
        
        /* Chat Button Styles */
        .chat-button {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 50;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #2f86ff 0%, #186ae6 100%);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .chat-button:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.15), 0 15px 15px -5px rgba(0, 0, 0, 0.08);
        }
        
        .chat-button:active {
            transform: scale(0.95);
        }
        
        /* Chat Panel Styles */
        .chat-panel {
            position: fixed;
            bottom: 6rem;
            right: 1.5rem;
            z-index: 40;
            max-height: calc(100vh - 8rem);
            background: white;
            border-radius: 1rem;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.10);
            border: 1px solid #e2e8f0;
        }
        
        /* Responsive Chat Panel */
        @media (max-width: 640px) {
            .chat-panel {
                bottom: 0;
                right: 0;
                left: 0;
                width: 100% !important;
                max-height: 100vh;
                border-radius: 0;
            }
            
            .chat-button {
                bottom: 1rem;
                right: 1rem;
            }
        }
        
        /* Chat Message Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 0.75;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }
        
        @keyframes bounce-dot {
            0%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-8px);
            }
        }
        
        /* Custom Scrollbar for Chat */
        .chat-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .chat-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .chat-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Chat Message Bubble Styles */
        .message-bubble-sent {
            background: linear-gradient(135deg, #2f86ff 0%, #186ae6 100%);
            color: white;
            border-radius: 1rem;
            border-bottom-right-radius: 0.25rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 8px rgba(47, 134, 255, 0.2);
        }
        
        .message-bubble-received {
            background: white;
            color: #0f172a;
            border-radius: 1rem;
            border-bottom-left-radius: 0.25rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }
        
        .message-bubble-chatbot {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #581c87;
            border-radius: 1rem;
            border-bottom-left-radius: 0.25rem;
            padding: 0.75rem 1rem;
            border: 1px solid #d8b4fe;
        }
        
        /* Smooth Transitions */
        .chat-transition-enter {
            animation: slideInUp 0.3s ease-out;
        }
        
        /* Ensure chat components don't interfere with modals */
        .modal-backdrop {
            z-index: 1040;
        }
        
        .modal {
            z-index: 1050;
        }
        
        /* Chat notification badge */
        .chat-badge {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 0.375rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            background: #ef4444;
            border-radius: 9999px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Typing indicator animation */
        .typing-dot {
            animation: bounce-dot 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        /* Connection status indicator */
        .connection-indicator {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        
        .connection-indicator.online {
            background: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }
        
        .connection-indicator.offline {
            background: #ef4444;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Smooth fade transitions */
        .fade-enter-active, .fade-leave-active {
            transition: opacity 0.3s ease;
        }
        
        .fade-enter-from, .fade-leave-to {
            opacity: 0;
        }
    </style>
</head>
<body class="h-full text-ink-900 overflow-x-hidden" 
      x-data="{ sidebarOpen: false, loading: true }"
      x-init="window.addEventListener('load', () => loading = false)">
    
    <!-- GLOBAL LOADING OVERLAY -->
    <div id="global-loading"
        class="fixed inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-md z-[9999] transition-opacity duration-700 opacity-100">
    <!-- LOGO DENGAN ANIMASI PULSASI -->
    <div class="relative">
        <img src="{{ url(asset('img/logo_xx.png')) }}"
            class="w-20 h-20 animate-bounce drop-shadow-lg" />
        <!-- RING CAHAYA INTERAKTIF -->
        <div class="absolute inset-0 rounded-full border-4 border-red-500 animate-ping"></div>
    </div>

    <!-- TEKS LOADING DENGAN GRADIENT -->
    <div class="mt-6 text-lg font-semibold bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent animate-pulse">
        Memuat data, mohon tunggu...
    </div>
    </div>


    {{-- 🔄 Modal Loading Overlay --}}
    <div
        x-data="{ modalLoading: false }"
        x-show="modalLoading"
        x-transition.opacity
        id="modal-loader"
        class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/20 backdrop-blur-[1px]"
        style="display: none;"
    >
        <div class="bg-white rounded-2xl shadow-card px-6 py-4 flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-primary-500 border-t-transparent"></div>
            <p class="text-sm text-primary-700 font-medium">MORRA Sedang Memuat, sabar ya...</p>
        </div>
    </div>


    {{-- Overlay mobile --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-[2px] lg:hidden"
        aria-hidden="true"></div>

    {{-- Sidebar component --}}
    <x-sidebar />

    {{-- Notification Toast Component --}}
    <x-notifications />

    {{-- Main area --}}
    <div class="lg:pl-80 transition-all overflow-x-hidden">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 border-b border-slate-200/70">
            <div class="relative">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-primary-50 via-transparent to-transparent"></div>
                <div class="backdrop-blur supports-[backdrop-filter]:bg-white/70 bg-white/60 shadow-sm">
                    <div class="max-w-7xl mx-auto h-16 px-4 flex items-center gap-3">
                        <button
                            class="p-2 -m-2 rounded-lg hover:bg-slate-100 lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka Sidebar">
                            <i class='bx bx-menu text-xl'></i>
                        </button>
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-primary-100">
                                <i class='bx bxs-dashboard text-primary-700 text-lg'></i>
                            </span>
                            <span class="font-semibold tracking-wide truncate">Admin ERP</span>
                            @isset($title)
                                <span class="text-slate-300">/</span>
                                <span class="text-slate-700 truncate">{{ $title }}</span>
                            @endisset
                        </div>
                        <div class="ml-auto flex items-center gap-3">
                            {{-- User Menu Dropdown --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-primary-700 font-semibold text-sm">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-slate-500">{{ auth()->user()->role->display_name ?? 'User' }}</p>
                                    </div>
                                    <i class='bx bx-chevron-down text-slate-400'></i>
                                </button>

                                {{-- Dropdown Menu --}}
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50"
                                     style="display: none;">
                                    
                                    {{-- User Info --}}
                                    <div class="px-4 py-3 border-b border-slate-200">
                                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                    </div>

                                    {{-- Menu Items --}}
                                    <a href="#" onclick="openProfileModal(); return false;" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i class='bx bx-user text-lg'></i>
                                        <span>Edit Profil</span>
                                    </a>
                                    
                                    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i class='bx bx-cog text-lg'></i>
                                        <span>Pengaturan</span>
                                    </a>

                                    <div class="border-t border-slate-200 my-2"></div>

                                    {{-- Logout --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class='bx bx-log-out text-lg'></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-6 overflow-x-hidden">
            {{ $slot }}
        </main>

        <footer class="max-w-7xl mx-auto px-4 pb-6 text-xs text-slate-500">
            © {{ date('Y') }} Admin ERP. Semua data dummy.
        </footer>
    </div>

    {{-- Chat System Components --}}
    <x-chat-button />
    <x-chat-panel />

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('loader', {
        show() { document.querySelector('[x-show="loading"]').__x.$data.loading = true },
        hide() { document.querySelector('[x-show="loading"]').__x.$data.loading = false }
    });
});

document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        const href = link.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
            document.querySelector('[x-show="loading"]').style.display = 'flex';
        }
    });
});
</script>

<script>
    // ========================================
    // GLOBAL UTILITIES FOR ERP OPTIMIZATION
    // ========================================
    
    // Modal Loader Utility
    window.ModalLoader = {
        show() {
            const el = document.querySelector('#modal-loader');
            if (el) el.style.display = 'flex';
        },
        hide() {
            const el = document.querySelector('#modal-loader');
            if (el) el.style.display = 'none';
        }
    };
    
    // API Cache Utility - Cache API responses untuk mengurangi request
    window.APICache = {
        cache: new Map(),
        ttl: 5 * 60 * 1000, // 5 menit default
        
        set(key, data, customTTL = null) {
            this.cache.set(key, {
                data,
                timestamp: Date.now(),
                ttl: customTTL || this.ttl
            });
        },
        
        get(key) {
            const item = this.cache.get(key);
            if (!item) return null;
            
            if (Date.now() - item.timestamp > item.ttl) {
                this.cache.delete(key);
                return null;
            }
            
            return item.data;
        },
        
        clear(key = null) {
            if (key) {
                this.cache.delete(key);
            } else {
                this.cache.clear();
            }
        }
    };
    
    // Optimized Fetch Utility dengan caching
    window.fetchWithCache = async function(url, options = {}, cacheTTL = null) {
        const cacheKey = url + JSON.stringify(options);
        
        // Check cache first
        const cached = window.APICache.get(cacheKey);
        if (cached) {
            console.log('📦 Cache hit:', url);
            return cached;
        }
        
        // Fetch from API
        console.log('🌐 API call:', url);
        const response = await fetch(url, options);
        const data = await response.json();
        
        // Store in cache
        window.APICache.set(cacheKey, data, cacheTTL);
        
        return data;
    };
    
    // Parallel Fetch Utility
    window.fetchParallel = async function(requests) {
        return Promise.all(
            requests.map(req => 
                fetch(req.url, req.options || {}).then(r => r.json())
            )
        );
    };
    
    // Debounce Utility untuk search/filter
    window.debounce = function(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Contoh otomatis jika kamu pakai jQuery untuk load modal
    if (window.$) {
        $(document).on('show.bs.modal', function (e) {
            const $modal = $(e.target);
            const url = $modal.data('url');
            if (url) {
                ModalLoader.show();
                $modal.find('.modal-content').load(url, function() {
                    ModalLoader.hide();
                });
            }
        });
    }

    // Kalau kamu pakai Fetch API untuk modal:
    window.loadModalContent = async function (selector, url) {
        try {
            ModalLoader.show();
            const res = await fetch(url);
            const html = await res.text();
            document.querySelector(selector).innerHTML = html;
        } catch (err) {
            console.error('Gagal memuat modal:', err);
        } finally {
            ModalLoader.hide();
        }
    }
</script>

<script>
  // Optimized loading - hide immediately when DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById("global-loading");
    if (overlay) {
      overlay.classList.add("opacity-0");
      setTimeout(() => overlay.style.display = "none", 300);
    }
  });
  
  // Fallback: hide after max 2 seconds
  setTimeout(() => {
    const overlay = document.getElementById("global-loading");
    if (overlay && overlay.style.display !== "none") {
      overlay.classList.add("opacity-0");
      setTimeout(() => overlay.style.display = "none", 300);
    }
  }, 2000);
</script>

{{-- Profile Modal --}}
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-slate-200 bg-slate-50">
                <h5 class="modal-title font-semibold">Edit Profil</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="profileForm">
                @csrf
                <div class="modal-body p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="profile_name" value="{{ auth()->user()->name }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" id="profile_email" value="{{ auth()->user()->email }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">No. Telepon</label>
                        <input type="text" name="phone" id="profile_phone" value="{{ auth()->user()->phone }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru <span class="text-xs text-slate-500">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="profile_password" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50">
                    <button type="button" class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" data-dismiss="modal">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openProfileModal() {
    $('#profileModal').modal('show');
}

$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("admin.users.update", auth()->id()) }}',
        type: 'PUT',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                alert('Profil berhasil diupdate');
                location.reload();
            }
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
        }
    });
});
</script>

@stack('scripts')

{{-- Laravel Echo for Real-time Broadcasting --}}
@auth
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    // Initialize Pusher and Laravel Echo
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        forceTLS: true,
        encrypted: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }
    });
    
    // Subscribe to user's private chat channel
    window.Echo.private('chat.{{ auth()->id() }}')
        .listen('.message.sent', (e) => {
            console.log('📨 New message received:', e.message);
            
            // Dispatch custom event for chat components to listen
            window.dispatchEvent(new CustomEvent('chat-message-received', {
                detail: e.message
            }));
        });
    
    console.log('✅ Laravel Echo initialized for user {{ auth()->id() }}');
</script>
@endauth

{{-- Emergency Fix: Prevent Auto-Opening Modals --}}
<script>
// Disable all auto-opening modals on page load
document.addEventListener('DOMContentLoaded', function() {
    // Close all Bootstrap modals
    $('.modal').modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
    
    // Remove any inline styles that might keep modals visible
    $('.modal').css('display', 'none');
    
    console.log('✅ All modals closed on page load');
});

// Prevent modals from auto-opening
$(document).on('show.bs.modal', '.modal', function(e) {
    // Allow manual triggers only
    if (!e.namespace || e.namespace !== 'bs.modal') {
        console.warn('⚠️ Prevented auto-opening modal:', this.id);
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
});
</script>

</body>

</html>
