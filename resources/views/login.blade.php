@auth
    @if(auth()->user()->role === 'admin')
        <script>window.location.href = "{{ route('admin.dashboard') }}";</script>
    @elseif(auth()->user()->role === 'comptable')
        <script>window.location.href = "{{ route('comptable.dashboard') }}";</script>
    @elseif(auth()->user()->role === 'superviseur')
        <script>window.location.href = "{{ route('superviseur.dashboard') }}";</script>
    @endif
@endauth

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#305327]">Les Fermes Safia</h1>
            <p class="text-gray-600 mt-2">Système de Gestion</p>
            <div class="mt-4 flex justify-center">
                <img src="{{ url('img/toolou-safia-logo.png') }}" alt="Les Fermes Safia" class="h-20 w-auto" />
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"
                    placeholder="admin@example.com">
            </div>

            <div class="mb-2">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"
                    placeholder="••••••••">
            </div>

            <div class="mb-6 text-right">
                <a href="{{ route('password.forgot') }}" class="text-sm text-[#008d36] hover:underline">Mot de passe oublié ?</a>
            </div>

            <button type="submit"
                class="w-full bg-[#008d36] text-white font-bold py-2 px-4 rounded-md hover:bg-[#305327] transition duration-200">
                Se connecter
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Comptes de test:</p>
            <p class="mt-1">Admin: admin@example.com / admin</p>
            <p>Comptable: comptable@example.com / comptable</p>
            <p>Superviseur: superviseur@example.com / superviseur</p>
        </div>
    </div>

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
</body>
</html>
