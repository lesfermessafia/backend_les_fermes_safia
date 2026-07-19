@props(['title' => 'Dashboard', 'color' => 'blue'])

@php
    $navId = 'navbar-' . uniqid();
@endphp

<nav class="bg-gradient-to-r from-[#305327] to-[#1f3a19] text-white shadow-lg fixed top-0 left-0 right-0 z-50 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo et Titre -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <img src="{{ url('img/toolou-safia-logo.png') }}" alt="Logo" class="w-9 h-9 sm:w-10 sm:h-10 object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-lg font-bold truncate leading-tight">{{ $title }}</h1>
                    <p class="hidden sm:block text-xs text-gray-300 leading-tight">Les Fermes Safia</p>
                </div>
            </div>

            <!-- Actions desktop -->
            <div class="hidden md:flex items-center gap-4">
                <div class="flex items-center gap-3 pr-3 border-r border-white/20">
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
                </div>

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
