@props(['title' => 'Dashboard', 'color' => 'blue'])

@php
    $navId = 'navbar-' . uniqid();
    $dashboardRoute = match (auth()->user()->role) {
        'admin' => route('admin.dashboard'),
        'comptable' => route('comptable.dashboard'),
        'superviseur' => route('superviseur.dashboard'),
        default => route('home'),
    };
@endphp

<nav class="bg-gradient-to-r from-[#305327] to-[#1f3a19] text-white shadow-lg fixed top-0 left-0 right-0 z-50 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo et Titre -->
            <a href="{{ $dashboardRoute }}" class="group flex items-center gap-2 sm:gap-3 min-w-0 rounded-xl px-2 py-1 -ml-2 transition hover:bg-white/10" aria-label="Retour au dashboard">
                <img src="{{ url('img/toolou-safia-logo.png') }}" alt="Logo" class="w-9 h-9 sm:w-10 sm:h-10 object-contain shrink-0 transition group-hover:scale-105">
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-lg font-bold truncate leading-tight">{{ $title }}</h1>
                    <p class="hidden sm:block text-xs text-gray-300 leading-tight">Les Fermes Safia</p>
                </div>
            </a>

            <!-- Actions desktop -->
            <div class="hidden md:flex items-center gap-4">
                @php
                    $unreadNotifications = auth()->user()->unreadNotifications()->latest()->limit(5)->get();
                    $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
                @endphp
                <a href="{{ route('discussion.index') }}" class="group inline-flex h-10 items-center gap-2 rounded-xl bg-[#008d36] px-4 text-sm font-bold shadow-sm ring-1 ring-white/20 transition hover:bg-[#00a844] hover:shadow-md" aria-label="Ouvrir la discussion">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-4 w-4 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8m-8 4h5m7-2a8 8 0 11-16 0c0 1.35.33 2.62.92 3.75L5 19l3.25-.92A8 8 0 0020 12z"></path></svg>
                    </span>
                    <span>Discussion</span>
                </a>
                <div class="relative group">
                    <a href="{{ route('notifications.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg hover:bg-white/10 transition" aria-label="Notifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span id="notification-count" class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold items-center justify-center {{ $unreadNotificationCount > 0 ? 'flex' : 'hidden' }}">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                    </a>
                    <div id="notification-preview" class="absolute right-0 top-12 w-80 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-100 p-3 hidden group-hover:block">
                        @forelse($unreadNotifications as $notification)
                        <a href="{{ route('notifications.read', $notification->id) }}" class="block p-2 rounded-lg hover:bg-gray-50">
                            <p class="text-sm font-semibold">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $notification->data['message'] ?? '' }}</p>
                        </a>
                        @empty
                        <p class="text-sm text-gray-500 p-2">Aucune notification non lue.</p>
                        @endforelse
                        <a href="{{ route('notifications.index') }}" class="block text-center text-sm font-semibold text-[#008d36] border-t pt-3 mt-2">Voir toutes les notifications</a>
                    </div>
                </div>
                <button type="button" id="profile-open-btn" class="profile-open-btn flex items-center gap-3 pr-3 border-r border-white/20 rounded-lg hover:bg-white/10 transition px-2 py-1 -ml-2">
                    <!-- Avatar utilisateur -->
                    @if(auth()->user()->photo_profil)
                        <img src="{{ url('img/' . auth()->user()->photo_profil) }}" alt="{{ auth()->user()->nom }}" class="w-10 h-10 rounded-full object-cover border-2 border-white/70 shrink-0">
                    @else
                        <div class="w-10 h-10 bg-[#008d36] rounded-full flex items-center justify-center border-2 border-white/70 shrink-0">
                            <span class="font-bold text-sm">
                                {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                            </span>
                        </div>
                    @endif

                    <!-- Détails utilisateur -->
                    <div class="text-right leading-tight">
                        <p class="font-semibold text-sm">{{ auth()->user()->nom }} {{ auth()->user()->prenom }}</p>
                        <p class="text-xs text-gray-300">
                            <span class="capitalize">{{ auth()->user()->role }}</span>
                            <span class="mx-1">•</span>
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </button>

                <!-- Bouton déconnexion -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#008d36] hover:bg-[#00b348] px-4 py-2 rounded-lg flex items-center gap-2 transition duration-200 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>

            <!-- Bouton menu mobile -->
            <button
                type="button"
                onclick="document.getElementById('{{ $navId }}').classList.toggle('hidden')"
                class="md:hidden p-2 rounded-lg hover:bg-white/10 transition duration-200 shrink-0"
                aria-label="Ouvrir le menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Menu mobile déroulant -->
        <div id="{{ $navId }}" class="hidden md:hidden pb-4">
            <div class="flex items-center gap-3 bg-white/10 rounded-lg p-3 mb-3">
                @if(auth()->user()->photo_profil)
                    <img src="{{ url('img/' . auth()->user()->photo_profil) }}" alt="{{ auth()->user()->nom }}" class="w-10 h-10 rounded-full object-cover border-2 border-white/70 shrink-0">
                @else
                    <div class="w-10 h-10 bg-[#008d36] rounded-full flex items-center justify-center border-2 border-white/70 shrink-0">
                        <span class="font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="font-semibold text-sm truncate">{{ auth()->user()->nom }} {{ auth()->user()->prenom }}</p>
                    <p class="text-xs text-gray-300 capitalize truncate">{{ auth()->user()->role }}</p>
                    <p class="text-xs text-gray-300 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <a href="{{ route('discussion.index') }}" class="mb-3 flex items-center gap-3 rounded-xl bg-[#008d36] px-4 py-3 text-sm font-bold shadow-sm ring-1 ring-white/20 transition hover:bg-[#00a844]">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8m-8 4h5m7-2a8 8 0 11-16 0c0 1.35.33 2.62.92 3.75L5 19l3.25-.92A8 8 0 0020 12z"></path></svg>
                </span>
                Ouvrir la discussion
            </a>

            <a href="{{ route('notifications.index') }}" class="mb-3 flex items-center justify-between rounded-lg bg-white/10 px-4 py-3 text-sm font-medium">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Notifications
                </span>
                @if($unreadNotificationCount > 0)<span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold">{{ $unreadNotificationCount }}</span>@endif
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-[#008d36] hover:bg-[#00b348] px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Modal profil -->
@php
$openProfile = session('open_profile') || session('password_code_sent') || $errors->has('old_password') || $errors->has('code') || $errors->has('nom') || $errors->has('prenom') || $errors->has('numero');
@endphp
<div id="profile-modal" class="fixed inset-0 z-[70] hidden" aria-modal="true" role="dialog">
    <div id="profile-modal-overlay" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex justify-between items-center p-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-[#305327]">Mon profil</h2>
                <button id="profile-close-btn" type="button" class="text-gray-400 hover:text-gray-700 text-3xl leading-none">&times;</button>
            </div>
            <div class="p-6 space-y-6 overflow-y-auto max-h-[80vh]">
                @if($errors->any())
                    <div class="bg-red-100 text-red-700 px-4 py-3 rounded">
                        {{ $errors->first() }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('status'))
                    <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <h3 class="font-semibold text-gray-700 mb-3">Informations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="profile-nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="profile-nom" name="nom" value="{{ auth()->user()->nom }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        </div>
                        <div>
                            <label for="profile-prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="profile-prenom" name="prenom" value="{{ auth()->user()->prenom }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        </div>
                        <div>
                            <label for="profile-email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                            <input type="email" id="profile-email" value="{{ auth()->user()->email }}" disabled class="w-full px-3 py-2 border border-gray-200 bg-gray-100 rounded-md text-gray-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label for="profile-numero" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" id="profile-numero" name="numero" value="{{ auth()->user()->numero }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 w-full bg-[#008d36] text-white font-bold py-2 px-4 rounded-md hover:bg-[#305327] transition">Enregistrer les informations</button>
                </form>

                <hr class="border-gray-100">

                @if(session('password_code_sent'))
                    <form method="POST" action="{{ route('profile.password.verify') }}">
                        @csrf
                        <h3 class="font-semibold text-gray-700 mb-3">Vérification du code</h3>
                        <label for="profile-code" class="block text-sm font-medium text-gray-700 mb-1">Code reçu par e-mail</label>
                        <input type="text" id="profile-code" name="code" required pattern="[0-9]{6}" inputmode="numeric" maxlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36] text-center text-2xl tracking-widest" placeholder="000000">
                        <button type="submit" class="mt-3 w-full bg-[#008d36] text-white font-bold py-2 px-4 rounded-md hover:bg-[#305327] transition">Valider le code</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('profile.password.code') }}">
                        @csrf
                        <h3 class="font-semibold text-gray-700 mb-3">Changer le mot de passe</h3>
                        <div class="space-y-3">
                            <input type="password" name="old_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" placeholder="Ancien mot de passe">
                            <input type="password" name="password" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" placeholder="Nouveau mot de passe">
                            <input type="password" name="password_confirmation" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" placeholder="Confirmer le nouveau mot de passe">
                        </div>
                        <button type="submit" class="mt-3 w-full bg-[#008d36] text-white font-bold py-2 px-4 rounded-md hover:bg-[#305327] transition">Envoyer le code de vérification</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        const modal = document.getElementById('profile-modal');
        const openBtns = document.querySelectorAll('.profile-open-btn');
        const closeBtn = document.getElementById('profile-close-btn');
        const overlay = document.getElementById('profile-modal-overlay');
        if (!modal) return;
        function open() { modal.classList.remove('hidden'); }
        function close() { modal.classList.add('hidden'); }
        openBtns.forEach(btn => btn.addEventListener('click', open));
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (overlay) overlay.addEventListener('click', close);
        @if($openProfile)
        open();
        @endif
    })();
</script>

<div id="notification-toast" class="fixed top-20 right-6 z-[60] hidden w-80 rounded-xl bg-white border border-green-200 shadow-xl p-4">
    <p id="notification-toast-title" class="font-bold text-[#305327]"></p>
    <p id="notification-toast-message" class="text-sm text-gray-600 mt-1"></p>
</div>

<script>
    document.body.dataset.userId = @json(auth()->id());
    document.body.dataset.notificationsUnreadUrl = @json(route('notifications.unread'));
    document.body.dataset.notificationReadUrl = @json(route('notifications.read', ['id' => '__ID__']));
</script>
@vite('resources/js/app.js')

<!-- Bouton retour en haut -->
<button id="backToTop" type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="fixed bottom-6 right-6 z-50 bg-[#008d36] text-white p-3 rounded-full shadow-lg hover:bg-[#305327] transition duration-300 opacity-0 pointer-events-none" aria-label="Retour en haut">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>
<script>
    (function () {
        const backToTop = document.getElementById('backToTop');
        if (!backToTop) return;
        function toggle() {
            if (window.scrollY > 200) {
                backToTop.classList.remove('opacity-0', 'pointer-events-none');
                backToTop.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                backToTop.classList.remove('opacity-100', 'pointer-events-auto');
                backToTop.classList.add('opacity-0', 'pointer-events-none');
            }
        }
        window.addEventListener('scroll', toggle);
        toggle();
    })();
</script>

<!-- Spacer pour compenser la navbar fixe -->
<div class="h-16"></div>
