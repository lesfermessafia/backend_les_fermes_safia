<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Dashboard Admin" color="blue" />

    <div class="container mx-auto px-4 py-12">
        <div class="mb-12 text-center">
            <div class="mb-6 flex justify-center">
                <img src="{{ url('img/toolou-safia-logo.png') }}" alt="Les Fermes Safia" class="h-32 w-auto" />
            </div>
            <h1 class="mb-3 text-4xl font-bold text-[#305327]">Système de Gestion Les Fermes Safia</h1>
            <p class="text-xl text-gray-600">Produits De Notre Terroir - Dashboard Admin</p>
        </div>

        <!-- <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-2xl font-semibold text-[#305327]">Architecture du Système</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-[#008d36] bg-[#008d36]/10 p-4">
                    <h3 class="mb-2 font-semibold text-[#008d36]">📱 App Mobile Client</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Authentification complète</li>
                        <li>• Passer commande</li>
                        <li>• Système récompenses</li>
                        <li>• Historique & profil</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-[#305327] bg-[#305327]/10 p-4">
                    <h3 class="mb-2 font-semibold text-[#305327]">🚚 App Mobile Livreur</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Connexion sécurisée</li>
                        <li>• Commandes du jour</li>
                        <li>• Carte GPS intégrée</li>
                        <li>• Validation livraison</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-gray-300 bg-gray-50 p-4">
                    <h3 class="mb-2 font-semibold text-gray-700">🖥️ Dashboard Web Admin</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Centre de contrôle complet</li>
                        <li>• Statistiques & graphiques</li>
                        <li>• Gestion multi-entités</li>
                        <li>• Configuration flexible</li>
                    </ul>
                </div>
            </div>
        </div> -->

        <h2 class="mb-6 text-2xl font-semibold text-[#305327]">Pages du Dashboard Admin</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#008d36] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36] transition-colors group-hover:bg-[#008d36] group-hover:text-white">
                        <span class="text-2xl">📊</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Tableau de bord</h3>
                    <p class="mt-2 text-sm text-gray-600">Vue d'ensemble avec statistiques et graphiques</p>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#305327] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#305327]/10 text-[#305327] transition-colors group-hover:bg-[#305327] group-hover:text-white">
                        <span class="text-2xl">👥</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Employé</h3>
                    <p class="mt-2 text-sm text-gray-600">Liste et détails des employés</p>
                </div>
            </a>

            <a href="{{ route('admin.entites.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-gray-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors group-hover:bg-gray-600 group-hover:text-white">
                        <span class="text-2xl">🏚️</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Ferme</h3>
                    <p class="mt-2 text-sm text-gray-600">Gestion des sites, fermes et magasin</p>
                </div>
            </a>

            <!-- <a href="{{ route('admin.entites.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#008d36] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36] transition-colors group-hover:bg-[#008d36] group-hover:text-white">
                        <span class="text-2xl">🏪</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Magasin</h3>
                    <p class="mt-2 text-sm text-gray-600">Points de stockage</p>
                </div>
            </a> -->

            <a href="{{ route('admin.aliments.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#305327] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#305327]/10 text-[#305327] transition-colors group-hover:bg-[#305327] group-hover:text-white">
                        <span class="text-2xl">🌾</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Aliment</h3>
                    <p class="mt-2 text-sm text-gray-600">Stock et mouvements d'aliments</p>
                </div>
            </a>

            <a href="{{ route('admin.poulets.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-gray-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors group-hover:bg-gray-600 group-hover:text-white">
                        <span class="text-2xl">🐔</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Poulet</h3>
                    <p class="mt-2 text-sm text-gray-600">Élevage et arrivages</p>
                </div>
            </a>

            <a href="{{ route('admin.formules.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#008d36] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36] transition-colors group-hover:bg-[#008d36] group-hover:text-white">
                        <span class="text-2xl">📋</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Formule</h3>
                    <p class="mt-2 text-sm text-gray-600">Formules d'alimentation</p>
                </div>
            </a>

            <a href="{{ route('admin.matieres-premieres.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-[#305327] hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#305327]/10 text-[#305327] transition-colors group-hover:bg-[#305327] group-hover:text-white">
                        <span class="text-2xl">🧪</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Matière Première</h3>
                    <p class="mt-2 text-sm text-gray-600">Ingrédients et composants</p>
                </div>
            </a>

            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                        <span class="text-2xl">👤</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Client</h3>
                    <p class="mt-2 text-sm text-gray-600">Base clients et profils</p>
                </div>
            </div>

            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36]">
                        <span class="text-2xl">📦</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Commande</h3>
                    <p class="mt-2 text-sm text-gray-600">Suivi et gestion des commandes</p>
                </div>
            </div>

            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#305327]/10 text-[#305327]">
                        <span class="text-2xl">🚚</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Livreur</h3>
                    <p class="mt-2 text-sm text-gray-600">Personnel et affectation zones</p>
                </div>
            </div>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-gray-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors group-hover:bg-gray-600 group-hover:text-white">
                        <span class="text-2xl">📦</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion de Stock</h3>
                    <p class="mt-2 text-sm text-gray-600">Inventaire et mouvements</p>
                </div>
            </a>
<!-- on le marque comme non disponible pour le moment avec une ecriture rouge "bientot disponible" -->
            <div class="group block h-full cursor-not-allowed">
                <div class="relative h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm opacity-60">
                    <span class="absolute top-2 right-2 text-xs font-semibold text-red-600">Bientôt disponible</span>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-[#008d36]/10 text-[#008d36]">
                        <span class="text-2xl">🏬</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Boutique</h3>
                    <p class="mt-2 text-sm text-gray-600">Points de vente et performances</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
