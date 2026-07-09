@props(['title' => 'Dashboard', 'color' => 'blue'])

<nav class="bg-[#305327] text-white p-3 shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <!-- Logo et Titre -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/toolou-safia-logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <h1 class="text-lg font-bold">{{ $title }} - Les Fermes Safia</h1>
            </div>

            <!-- Informations utilisateur -->
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <!-- Avatar utilisateur -->
                    <div class="w-10 h-10 bg-[#008d36] rounded-full flex items-center justify-center border-2 border-white">
                        <span class="font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                        </span>
                    </div>
                    
                    <!-- Détails utilisateur -->
                    <div class="text-right">
                        <p class="font-semibold">{{ auth()->user()->nom }} {{ auth()->user()->prenom }}</p>
                        <p class="text-xs text-gray-200">
                            <span class="capitalize">{{ auth()->user()->role }}</span>
                            <span class="mx-1">•</span>
                            {{ auth()->user()->email }}
                        </p>
                        <p class="text-xs text-gray-200">{{ auth()->user()->numero }}</p>
                    </div>
                </div>

                <!-- Bouton déconnexion -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#008d36] hover:bg-[#305327] px-4 py-2 rounded-lg flex items-center gap-2 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Spacer pour compenser la navbar fixe -->
<div class="h-16"></div>
