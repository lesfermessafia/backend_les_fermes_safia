<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Dashboard Comptable" color="green" />

    <div class="container mx-auto px-4 py-12">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-[#305327]">Système de Gestion Comptable</h1>
            <p class="text-xl text-gray-600 mt-2">Tableau de bord du comptable - Les Fermes Safia</p>
        </div>

        <h2 class="mb-6 text-2xl font-semibold text-[#305327]">Pages du Dashboard Comptable</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('comptable.aliments.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#008d36] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36] transition-colors group-hover:bg-[#008d36] group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Aliments</h3>
                    <p class="mt-2 text-sm text-gray-600">Stock, statuts et mouvements d'aliments</p>
                </div>
            </a>

            <a href="{{ route('comptable.oeufs.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-yellow-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 transition-colors group-hover:bg-yellow-500 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <ellipse cx="12" cy="12" rx="7" ry="9" stroke="currentColor" stroke-width="2" fill="none"></ellipse>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Oeufs</h3>
                    <p class="mt-2 text-sm text-gray-600">Stock, entrees et sorties d'oeufs</p>
                </div>
            </a>

            <a href="{{ route('comptable.matieres-premieres.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-blue-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-500 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Matieres Premieres</h3>
                    <p class="mt-2 text-sm text-gray-600">Stock, lots et mouvements de matieres</p>
                </div>
            </a>

            <a href="{{ route('comptable.poulets.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-orange-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 text-orange-600 transition-colors group-hover:bg-orange-500 group-hover:text-white">
                        <span class="text-2xl">🐔</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Poulets</h3>
                    <p class="mt-2 text-sm text-gray-600">Stocks, mouvements et statuts des poulets</p>
                </div>
            </a>

            <a href="{{ route('comptable.fermes-magasins.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-indigo-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Fermes & Magasins</h3>
                    <p class="mt-2 text-sm text-gray-600">Visualiser les contenus de chaque ferme et magasin</p>
                </div>
            </a>

            <a href="{{ route('comptable.tableau-de-bord') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-green-600 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 transition-colors group-hover:bg-green-600 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Tableau de bord général</h3>
                    <p class="mt-2 text-sm text-gray-600">Vue d'ensemble des stocks et mouvements</p>
                </div>
            </a>

            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#305327]/10 text-[#305327]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Rapports Financiers</h3>
                    <p class="mt-2 text-sm text-gray-600">Consulter les rapports et bilans</p>
                </div>
            </div>

            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Facturation</h3>
                    <p class="mt-2 text-sm text-gray-600">Gérer les factures et devis</p>
                </div>
            </div>

            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Paiements</h3>
                    <p class="mt-2 text-sm text-gray-600">Suivi des paiements et encaissements</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
