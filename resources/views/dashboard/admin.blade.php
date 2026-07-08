<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Dashboard Admin" color="blue" />

    <div class="container mx-auto px-4 py-12">
        <div class="mb-12 text-center">
            <div class="mb-6 flex justify-center">
                <img src="{{ asset('images/toolou-safia-logo.png') }}" alt="Les Fermes Safia" class="h-32 w-auto" />
            </div>
            <h1 class="mb-3 text-4xl font-bold text-gray-900">Système de Gestion Les Fermes Safia</h1>
            <p class="text-xl text-gray-600">Produits De Notre Terroir - Dashboard Admin</p>
        </div>

        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-2xl font-semibold text-gray-900">Architecture du Système</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h3 class="mb-2 font-semibold text-blue-700">📱 App Mobile Client</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Authentification complète</li>
                        <li>• Passer commande</li>
                        <li>• Système récompenses</li>
                        <li>• Historique & profil</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <h3 class="mb-2 font-semibold text-green-700">🚚 App Mobile Livreur</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Connexion sécurisée</li>
                        <li>• Commandes du jour</li>
                        <li>• Carte GPS intégrée</li>
                        <li>• Validation livraison</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-purple-200 bg-purple-50 p-4">
                    <h3 class="mb-2 font-semibold text-purple-700">🖥️ Dashboard Web Admin</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Centre de contrôle complet</li>
                        <li>• Statistiques & graphiques</li>
                        <li>• Gestion multi-entités</li>
                        <li>• Configuration flexible</li>
                    </ul>
                </div>
            </div>
        </div>

        <h2 class="mb-6 text-2xl font-semibold text-gray-900">Pages du Dashboard Admin</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-blue-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                        <span class="text-2xl">📊</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Tableau de bord</h3>
                    <p class="mt-2 text-sm text-gray-600">Vue d'ensemble avec statistiques et graphiques</p>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-green-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 transition-colors group-hover:bg-green-600 group-hover:text-white">
                        <span class="text-2xl">👥</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Employé</h3>
                    <p class="mt-2 text-sm text-gray-600">Liste et détails des employés</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-purple-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white">
                        <span class="text-2xl">🏚️</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Ferme</h3>
                    <p class="mt-2 text-sm text-gray-600">Gestion des fermes et sites</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-yellow-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 transition-colors group-hover:bg-yellow-600 group-hover:text-white">
                        <span class="text-2xl">🏪</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Magasin</h3>
                    <p class="mt-2 text-sm text-gray-600">Points de stockage</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-orange-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 text-orange-600 transition-colors group-hover:bg-orange-600 group-hover:text-white">
                        <span class="text-2xl">🌾</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Aliment</h3>
                    <p class="mt-2 text-sm text-gray-600">Stock et mouvements d'aliments</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-red-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 text-red-600 transition-colors group-hover:bg-red-600 group-hover:text-white">
                        <span class="text-2xl">🐔</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Poulet</h3>
                    <p class="mt-2 text-sm text-gray-600">Élevage et arrivages</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-indigo-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                        <span class="text-2xl">📋</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Formule</h3>
                    <p class="mt-2 text-sm text-gray-600">Formules d'alimentation</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-pink-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-pink-100 text-pink-600 transition-colors group-hover:bg-pink-600 group-hover:text-white">
                        <span class="text-2xl">🧪</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Matière Première</h3>
                    <p class="mt-2 text-sm text-gray-600">Ingrédients et composants</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-teal-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-teal-100 text-teal-600 transition-colors group-hover:bg-teal-600 group-hover:text-white">
                        <span class="text-2xl">👤</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Client</h3>
                    <p class="mt-2 text-sm text-gray-600">Base clients et profils</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-cyan-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 transition-colors group-hover:bg-cyan-600 group-hover:text-white">
                        <span class="text-2xl">📦</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Commande</h3>
                    <p class="mt-2 text-sm text-gray-600">Suivi et gestion des commandes</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-lime-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-lime-100 text-lime-600 transition-colors group-hover:bg-lime-600 group-hover:text-white">
                        <span class="text-2xl">🚚</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Livreur</h3>
                    <p class="mt-2 text-sm text-gray-600">Personnel et affectation zones</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-amber-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-600 transition-colors group-hover:bg-amber-600 group-hover:text-white">
                        <span class="text-2xl">📦</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion de Stock</h3>
                    <p class="mt-2 text-sm text-gray-600">Inventaire et mouvements</p>
                </div>
            </a>

            <a href="#" class="group block h-full">
                <div class="h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-emerald-500 hover:shadow-lg">
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                        <span class="text-2xl">🏬</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Gestion Boutique</h3>
                    <p class="mt-2 text-sm text-gray-600">Points de vente et performances</p>
                </div>
            </a>
        </div>

        <div class="mt-12 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Stack Technique</h3>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="font-medium text-blue-700">Frontend Mobile</p>
                    <p class="text-sm text-gray-600">Flutter / React Native</p>
                </div>
                <div>
                    <p class="font-medium text-green-700">Frontend Web</p>
                    <p class="text-sm text-gray-600">Laravel Blade + Tailwind</p>
                </div>
                <div>
                    <p class="font-medium text-purple-700">Backend API</p>
                    <p class="text-sm text-gray-600">Laravel (PHP)</p>
                </div>
                <div>
                    <p class="font-medium text-orange-700">Services</p>
                    <p class="text-sm text-gray-600">Wave, Google Maps, FCM</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
