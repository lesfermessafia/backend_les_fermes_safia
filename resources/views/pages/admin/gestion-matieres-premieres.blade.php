<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Matières Premières - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Matières Premières" color="blue" />

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour au Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Matières Premières</h2>
                <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Nouvelle Matière Première
                </button>
            </div>

            <!-- Filtre -->
            <div class="mb-4">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchMatieres" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchMatieres" value="{{ request('search') }}" placeholder="Code, nom ou unité..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchMatieres()">
                    </div>
                    <button onclick="resetSearchMatieres()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Image</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Unité</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($matieres as $matiere)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $matiere->code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $matiere->nom }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($matiere->image)
                                    <img src="{{ url('img/' . $matiere->image) }}" alt="{{ $matiere->nom }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $matiere->unite }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editMatiere({{ $matiere->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteMatiere({{ $matiere->id }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Aucune matière première ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="pagination-matieres" class="mt-4">
                {{ $matieres->links() }}
            </div>

        </div>

        <!-- Section Gestion du Stock -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-2xl font-bold text-[#305327] mb-6 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Gestion du Stock
            </h2>
            
            <!-- Onglets -->
            <div class="flex border-b border-gray-200 mb-6 space-x-1">
                <button onclick="showStockTab('overview')" id="tab-overview" class="px-4 py-2 border-b-2 border-[#008d36] text-[#008d36] font-medium rounded-t hover:bg-green-50 transition duration-200">
                    Vue d'ensemble
                </button>
                <button onclick="showStockTab('mouvement')" id="tab-mouvement" class="px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 font-medium rounded-t transition duration-200">
                    Mouvements
                </button>
                <button onclick="showStockTab('historique')" id="tab-historique" class="px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 font-medium rounded-t transition duration-200">
                    Historique
                </button>
                <button onclick="showStockTab('lots')" id="tab-lots" class="px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 font-medium rounded-t transition duration-200">
                    Lots
                </button>
                <button onclick="showStockTab('statistiques')" id="tab-statistiques" class="px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 font-medium rounded-t transition duration-200">
                    Statistiques
                </button>
            </div>

            <!-- Vue d'ensemble -->
            <div id="stock-overview" class="stock-tab">
                <!-- Cartes KPI -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-[#305327] to-[#008d36] text-white rounded-lg p-4 shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs opacity-90">Matières suivies</p>
                                <p id="kpiMatieres" class="text-2xl font-bold">0</p>
                            </div>
                            <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">Stock disponible total</p>
                                <p id="kpiDisponible" class="text-2xl font-bold text-[#008d36]">0</p>
                            </div>
                            <svg class="w-8 h-8 text-[#008d36] opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">Nombre de lots</p>
                                <p id="kpiLots" class="text-2xl font-bold text-gray-700">{{ count($lots ?? []) }}</p>
                            </div>
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                    <div class="bg-white border border-red-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">Matières en alerte</p>
                                <p id="kpiAlertes" class="text-2xl font-bold text-red-600">0</p>
                            </div>
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" id="filterSearch" oninput="debouncedLoadOverview()" placeholder="Nom ou code..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par magasin</label>
                        <select id="filterMagasin" onchange="loadStockOverview()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                            <option value="">Tous les magasins</option>
                            @foreach($magasins ?? [] as $magasin)
                            <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par lot</label>
                        <select id="filterLot" onchange="loadStockOverview()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                            <option value="">Tous les lots</option>
                            @foreach($lots ?? [] as $lot)
                            <option value="{{ $lot->id }}">{{ $lot->code_lot }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#305327] text-white">
                                <th class="px-4 py-3 text-left text-sm font-semibold">Matière Première</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Stock Total</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Utilisé</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Disponible</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Statut</th>
                            </tr>
                        </thead>
                        <tbody id="stock-overview-body" class="divide-y divide-gray-200">
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mouvements -->
            <div id="stock-mouvement" class="stock-tab hidden">
                <div class="mb-6">
                    <button onclick="openLotModal()" class="bg-[#305327] text-white px-4 py-2 rounded-lg hover:bg-[#008d36] transition duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Créer un nouveau lot
                    </button>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-[#305327] mb-1">Enregistrer un mouvement de stock</h3>
                    <p class="text-xs text-gray-500 mb-4">Une <strong>entrée</strong> peut ajouter une matière à n'importe quel lot/magasin. Une <strong>sortie</strong> est limitée aux stocks disponibles.</p>
                    <form id="mouvementForm" onsubmit="submitMouvement(event)">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select id="mouvementType" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                                    <option value="entree">Entrée</option>
                                    <option value="sortie">Sortie</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Matière Première</label>
                                <select id="mouvementMatiere" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                                    <option value="">Sélectionner...</option>
                                    @foreach($allMatieres ?? [] as $matiere)
                                    <option value="{{ $matiere->id }}">{{ $matiere->nom }} ({{ $matiere->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                                <select id="mouvementMagasin" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                                    <option value="">Sélectionner...</option>
                                    @foreach($magasins ?? [] as $magasin)
                                    <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lot</label>
                                <select id="mouvementLot" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                                    <option value="">Sélectionner...</option>
                                    @foreach($lots ?? [] as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code_lot }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                                <input type="number" id="mouvementQuantite" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                                <p id="mouvementDisponible" class="hidden mt-1 text-xs text-gray-500"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observation</label>
                                <input type="text" id="mouvementObservation" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                            </div>
                        </div>
                        <button type="submit" class="bg-[#008d36] text-white px-6 py-2 rounded-lg hover:bg-[#305327] transition duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Enregistrer le mouvement
                        </button>
                    </form>
                </div>
            </div>

            <!-- Historique -->
            <div id="stock-historique" class="stock-tab hidden">
                <div class="mb-4 flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matière Première</label>
                        <select id="histMatiere" onchange="loadHistorique()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                            <option value="">Toutes</option>
                            @foreach($matieres ?? [] as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="histType" onchange="loadHistorique()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                            <option value="">Tous</option>
                            <option value="entree">Entrée</option>
                            <option value="sortie">Sortie</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#305327] text-white">
                                <th class="px-4 py-3 text-left text-sm font-semibold">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Matière</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Type</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Quantité</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Magasin</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Lot</th>
                            </tr>
                        </thead>
                        <tbody id="historique-body" class="divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Lots -->
            <div id="stock-lots" class="stock-tab hidden">
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#305327] text-white">
                                <th class="px-4 py-3 text-left text-sm font-semibold">Code Lot</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Date de création</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Statut</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lots-body" class="divide-y divide-gray-200">
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Statistiques -->
            <div id="stock-statistiques" class="stock-tab hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                        <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Alertes de Stock
                        </h3>
                        <div id="alertes-stock" class="space-y-2">
                            <p class="text-gray-500">Chargement...</p>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Top Matières Utilisées (30 jours)
                        </h3>
                        <div id="top-matieres" class="space-y-2">
                            <p class="text-gray-500">Chargement...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour créer un lot -->
    <div id="lotModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Créer un nouveau lot</h3>
            <form id="lotForm" onsubmit="submitLot(event)">
                @csrf
                <div id="matieresContainer">
                    <div class="matiere-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Matière Première</label>
                            <select class="lot-matiere w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Sélectionner...</option>
                                @foreach($allMatieres ?? [] as $matiere)
                                <option value="{{ $matiere->id }}">{{ $matiere->nom }} ({{ $matiere->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                            <input type="number" step="0.01" min="0.01" class="lot-quantite w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                            <select class="lot-magasin w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Sélectionner...</option>
                                @foreach($magasins ?? [] as $magasin)
                                <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="removeMatiereRow(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addMatiereRow()" class="mb-4 px-4 py-2 bg-[#305327] text-white rounded-md hover:bg-[#008d36] transition duration-200">
                    + Ajouter une matière première
                </button>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeLotModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Créer le lot
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal pour afficher les détails d'une matière première -->
    <div id="matiereDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="matiereDetailsTitle" class="text-xl font-bold text-[#305327]">Détails de la matière première</h3>
                <button onclick="closeMatiereDetailsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="matiereDetailsContent">
                <p class="text-gray-500">Chargement...</p>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation générique -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1100]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 id="confirmTitle" class="text-xl font-bold text-[#305327]">Confirmation</h3>
            </div>
            <p id="confirmMessage" class="text-gray-600 mb-6">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                    Annuler
                </button>
                <button id="confirmButton" class="px-4 py-2 bg-[#008d36] text-white rounded-lg hover:bg-[#305327] transition duration-200">
                    Confirmer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les détails d'un lot -->
    <div id="lotDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="lotDetailsTitle" class="text-xl font-bold text-[#305327]">Détails du lot</h3>
                <button onclick="closeLotDetailsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="lotDetailsContent">
                <p class="text-gray-500">Chargement...</p>
            </div>
            <div id="lotActions" class="mt-4 pt-4 border-t border-gray-200 hidden">
                <div class="flex gap-3">
                    <button onclick="openAddMatiereModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter une matière première
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter une matière première à un lot -->
    <div id="addMatiereModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1100]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-[#305327]">Ajouter une matière première au lot</h3>
                <button onclick="closeAddMatiereModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matière Première</label>
                    <select id="addMatiereSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                        <option value="">Sélectionner...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                    <select id="addMagasinSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                        <option value="">Sélectionner...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" id="addMatiereQuantite" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeAddMatiereModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">Annuler</button>
                <button onclick="addMatiereToLot()" class="px-4 py-2 bg-[#008d36] text-white rounded-lg hover:bg-[#305327] transition duration-200">Ajouter</button>
            </div>
        </div>
    </div>

    <!-- Modal pour augmenter la quantité d'une matière première -->
    <div id="increaseQuantityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1100]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-[#305327]">Augmenter la quantité</h3>
                <button onclick="closeIncreaseQuantityModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <p id="increaseMatiereInfo" class="text-sm text-gray-600"></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité à ajouter</label>
                    <input type="number" id="increaseQuantity" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#008d36] focus:border-transparent">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeIncreaseQuantityModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">Annuler</button>
                <button onclick="confirmIncreaseQuantity()" class="px-4 py-2 bg-[#008d36] text-white rounded-lg hover:bg-[#305327] transition duration-200">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les détails d'un mouvement -->
    <div id="mouvementDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="mouvementDetailsTitle" class="text-xl font-bold text-[#305327]">Détails du mouvement</h3>
                <button onclick="closeMouvementDetailsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="mouvementDetailsContent">
                <p class="text-gray-500">Chargement...</p>
            </div>
        </div>
    </div>

    <!-- Conteneur des notifications toast -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[2000] flex flex-col gap-2"></div>

    <!-- Modal -->
    <div id="matiereModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouvelle Matière Première</h3>
            <form id="matiereForm" method="POST" action="{{ route('admin.matieres-premieres.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="matiereId" name="id" value="">
                <input type="hidden" id="_method" name="_method" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="matiereNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <img id="matiereImagePreview" src="" alt="Image actuelle" class="w-16 h-16 object-cover rounded mb-2 hidden">
                    <input type="file" id="matiereImage" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laissez vide pour conserver l'image actuelle.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                    <input type="text" id="matiereUnite" name="unite" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte</label>
                    <input type="number" id="matiereSeuil" name="seuil_alerte" step="0.01" min="0" value="10" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Alerte lorsque le stock disponible passe sous ce seuil (défaut : 10).</p>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Annuler
                    </button>
                    <button type="button" onclick="confirmMatiereSubmit()" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let matieres = @json($matieres->items());

        function openModal() {
            document.getElementById('matiereModal').classList.remove('hidden');
            document.getElementById('matiereModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouvelle Matière Première';
            document.getElementById('matiereForm').action = '{{ route('admin.matieres-premieres.store') }}';
            document.getElementById('matiereId').value = '';
            document.getElementById('matiereNom').value = '';
            document.getElementById('matiereImage').value = '';
            document.getElementById('matiereUnite').value = '';
            document.getElementById('matiereSeuil').value = '10';
            document.getElementById('_method').value = '';
            document.getElementById('matiereImagePreview').classList.add('hidden');
            document.getElementById('matiereImagePreview').src = '';
        }

        function closeModal() {
            document.getElementById('matiereModal').classList.add('hidden');
            document.getElementById('matiereModal').classList.remove('flex');
        }

        function confirmMatiereSubmit() {
            const isEdit = document.getElementById('_method').value === 'PUT';
            const title = isEdit ? 'Modifier une matière première' : 'Créer une matière première';
            const message = isEdit 
                ? 'Êtes-vous sûr de vouloir modifier cette matière première ?' 
                : 'Êtes-vous sûr de vouloir créer cette nouvelle matière première ?';
            
            showConfirmModal(title, message, () => {
                document.getElementById('matiereForm').submit();
            });
        }

        function editMatiere(id) {
            const matiere = matieres.find(m => m.id === id);
            if (matiere) {
                document.getElementById('matiereModal').classList.remove('hidden');
                document.getElementById('matiereModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Matière Première';
                document.getElementById('matiereForm').action = '{{ route('admin.matieres-premieres.update', ':id') }}'.replace(':id', id);
                document.getElementById('matiereId').value = matiere.id;
                document.getElementById('matiereNom').value = matiere.nom;
                document.getElementById('matiereImage').value = '';
                document.getElementById('matiereUnite').value = matiere.unite;
                document.getElementById('matiereSeuil').value = matiere.seuil_alerte ?? 10;
                document.getElementById('_method').value = 'PUT';

                const preview = document.getElementById('matiereImagePreview');
                if (matiere.image) {
                    preview.src = '/img/' + matiere.image;
                    preview.classList.remove('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            }
        }

        function deleteMatiere(id) {
            showConfirmModal(
                'Supprimer une matière première',
                'Êtes-vous sûr de vouloir supprimer cette matière première ? Cette action est irréversible.',
                () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.matieres-premieres.destroy', ':id') }}'.replace(':id', id);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) { alert(message); return; }

            const config = {
                success: { bg: 'bg-green-600', icon: 'M5 13l4 4L19 7' },
                error: { bg: 'bg-red-600', icon: 'M6 18L18 6M6 6l12 12' },
                info: { bg: 'bg-blue-600', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
            };
            const c = config[type] || config.success;

            const toast = document.createElement('div');
            toast.className = `${c.bg} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.icon}"></path></svg>
                <span class="text-sm">${escapeHtml(message)}</span>
            `;
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        function renderMatieres(items) {
            const tbody = document.querySelector('.overflow-x-auto tbody');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucune matière première ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(m => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(m.code)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(m.nom)}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${m.image ? `<img src="/img/${escapeHtml(m.image)}" alt="${escapeHtml(m.nom)}" class="w-12 h-12 object-cover rounded">` : '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(m.unite)}</td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="editMatiere(${m.id})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                        <button onclick="deleteMatiere(${m.id})" class="text-red-600 hover:text-red-800">Supprimer</button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadMatieres(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                matieres = data.matieres;
                renderMatieres(matieres);
                const paginationContainer = document.getElementById('pagination-matieres');
                if (paginationContainer && data.pagination !== undefined) {
                    paginationContainer.innerHTML = data.pagination;
                }
            } catch (error) {
                showToast('Erreur lors du chargement des matières premières: ' + error.message, 'error');
            }
        }

        // Fonction debounce pour éviter trop de requêtes
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Recherche automatique pour les matières premières
        const debouncedSearchMatieres = debounce(function() {
            const search = document.getElementById('searchMatieres').value;
            const url = new URL(window.location.href);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            history.pushState({}, '', url.toString());
            loadMatieres(url.toString());
        }, 500);

        function resetSearchMatieres() {
            document.getElementById('searchMatieres').value = '';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            history.pushState({}, '', url.toString());
            loadMatieres(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchMatieres = document.getElementById('searchMatieres');
            if (searchMatieres) {
                const params = new URLSearchParams(window.location.search);
                searchMatieres.value = params.get('search') || '';
            }
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const searchMatieres = document.getElementById('searchMatieres');
            if (searchMatieres) {
                searchMatieres.value = params.get('search') || '';
            }
            loadMatieres(window.location.href);
        });

        // Gestion du Stock
        function showStockTab(tabName) {
            document.querySelectorAll('.stock-tab').forEach(tab => tab.classList.add('hidden'));
            document.querySelectorAll('[id^="tab-"]').forEach(btn => {
                btn.classList.remove('border-[#008d36]', 'text-[#008d36]');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            document.getElementById('stock-' + tabName).classList.remove('hidden');
            document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tabName).classList.add('border-[#008d36]', 'text-[#008d36]');

            if (tabName === 'overview') loadStockOverview();
            if (tabName === 'historique') loadHistorique();
            if (tabName === 'lots') loadLots();
            if (tabName === 'statistiques') loadStatistiques();
        }

        let overviewSearchTimer = null;
        function debouncedLoadOverview() {
            clearTimeout(overviewSearchTimer);
            overviewSearchTimer = setTimeout(loadStockOverview, 300);
        }

        function stockStatusBadge(m) {
            if (m.stock_disponible <= 0) {
                return '<span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">Rupture</span>';
            }
            if (m.alerte_stock) {
                return '<span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">Stock faible</span>';
            }
            return '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">OK</span>';
        }

        async function loadStockOverview() {
            const magasinId = document.getElementById('filterMagasin').value;
            const lotId = document.getElementById('filterLot').value;
            const search = document.getElementById('filterSearch')?.value || '';
            
            let url = '{{ route("admin.matieres-premieres.stock.index") }}';
            const params = new URLSearchParams();
            if (magasinId) params.append('magasin_id', magasinId);
            if (lotId) params.append('lot_id', lotId);
            if (search) params.append('search', search);
            if (params.toString()) url += '?' + params.toString();

            try {
                const response = await fetch(url);
                const data = await response.json();
                
                // Mettre à jour les KPI
                const totalDispo = data.reduce((sum, m) => sum + Number(m.stock_disponible || 0), 0);
                const nbAlertes = data.filter(m => m.alerte_stock).length;
                document.getElementById('kpiMatieres').textContent = data.length;
                document.getElementById('kpiDisponible').textContent = totalDispo.toLocaleString('fr-FR');
                document.getElementById('kpiAlertes').textContent = nbAlertes;

                const tbody = document.getElementById('stock-overview-body');
                if (data.length === 0) {
                    tbody.innerHTML = `
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            Aucun stock trouvé
                        </td></tr>`;
                    return;
                }

                tbody.innerHTML = data.map(m => `
                    <tr class="border-b hover:bg-gray-50 cursor-pointer" onclick="showMatiereDetails(${m.id})">
                        <td class="px-4 py-3 text-sm font-medium text-[#008d36]">${escapeHtml(m.nom)} (${escapeHtml(m.code)})</td>
                        <td class="px-4 py-3 text-sm">${escapeHtml(String(m.stock_total))}</td>
                        <td class="px-4 py-3 text-sm">${escapeHtml(String(m.stock_utilise))}</td>
                        <td class="px-4 py-3 text-sm font-medium ${m.stock_disponible <= 0 ? 'text-gray-500' : (m.alerte_stock ? 'text-orange-600' : 'text-green-600')}">${escapeHtml(String(m.stock_disponible))}</td>
                        <td class="px-4 py-3 text-sm">${stockStatusBadge(m)}</td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        async function submitMouvement(event) {
            event.preventDefault();
            
            const matiereId = document.getElementById('mouvementMatiere').value;
            const magasinId = document.getElementById('mouvementMagasin').value;
            const lotId = document.getElementById('mouvementLot').value;
            const type = document.getElementById('mouvementType').value;
            const quantite = document.getElementById('mouvementQuantite').value;
            const observation = document.getElementById('mouvementObservation').value;

            const typeLabel = type === 'entree' ? 'Entrée' : 'Sortie';

            showConfirmModal(
                `Enregistrer un mouvement de ${typeLabel}`,
                `Êtes-vous sûr de vouloir enregistrer une ${typeLabel.toLowerCase()} de ${quantite} unités ?`,
                async () => {
                    const formData = new FormData();
                    formData.append('matiere_id', matiereId);
                    formData.append('magasin_id', magasinId);
                    formData.append('lot_id', lotId);
                    formData.append('type', type);
                    formData.append('quantite', quantite);
                    formData.append('observation', observation);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("admin.matieres-premieres.stock.mouvement") }}', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (response.ok) {
                            showToast('Mouvement enregistré avec succès', 'success');
                            document.getElementById('mouvementForm').reset();
                            // Réinitialiser les selects
                            document.getElementById('mouvementMagasin').innerHTML = '<option value="">Sélectionner...</option>';
                            document.getElementById('mouvementLot').innerHTML = '<option value="">Sélectionner...</option>';
                            document.getElementById('mouvementDisponible')?.classList.add('hidden');
                        } else {
                            showToast(data.error || 'Erreur inconnue', 'error');
                        }
                    } catch (error) {
                        showToast('Erreur: ' + error.message, 'error');
                    }
                }
            );
        }

        // Listes complètes (utilisées pour les entrées qui peuvent ajouter une matière à n'importe quel lot/magasin)
        const allMagasinsList = @json(($magasins ?? [])->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom])->values());
        const allLotsList = @json(($lots ?? [])->map(fn($l) => ['id' => $l->id, 'code_lot' => $l->code_lot])->values());

        // Recharger les magasins selon la matière et le type de mouvement
        async function refreshMagasinOptions() {
            const matiereId = document.getElementById('mouvementMatiere').value;
            const type = document.getElementById('mouvementType').value;
            const magasinSelect = document.getElementById('mouvementMagasin');
            const lotSelect = document.getElementById('mouvementLot');

            magasinSelect.innerHTML = '<option value="">Sélectionner...</option>';
            lotSelect.innerHTML = '<option value="">Sélectionner...</option>';

            if (!matiereId) return;

            // Pour une entrée, on autorise tous les magasins (ajout possible d'une nouvelle matière)
            if (type === 'entree') {
                allMagasinsList.forEach(magasin => {
                    const option = document.createElement('option');
                    option.value = magasin.id;
                    option.textContent = magasin.nom;
                    magasinSelect.appendChild(option);
                });
                return;
            }

            // Pour une sortie, seulement les magasins qui ont du stock disponible
            try {
                const response = await fetch(`/admin/matieres-premieres/stock/matiere/${matiereId}/magasins`);
                const magasins = await response.json();

                if (response.ok && magasins.length > 0) {
                    magasins.forEach(magasin => {
                        const option = document.createElement('option');
                        option.value = magasin.id;
                        option.textContent = magasin.nom;
                        magasinSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'Aucun magasin disponible';
                    option.disabled = true;
                    magasinSelect.appendChild(option);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Recharger les lots selon la matière, le magasin et le type de mouvement
        async function refreshLotOptions() {
            const matiereId = document.getElementById('mouvementMatiere').value;
            const magasinId = document.getElementById('mouvementMagasin').value;
            const type = document.getElementById('mouvementType').value;
            const lotSelect = document.getElementById('mouvementLot');

            lotSelect.innerHTML = '<option value="">Sélectionner...</option>';

            if (!matiereId || !magasinId) return;

            // Pour une entrée, on autorise tous les lots
            if (type === 'entree') {
                allLotsList.forEach(lot => {
                    const option = document.createElement('option');
                    option.value = lot.id;
                    option.textContent = lot.code_lot;
                    lotSelect.appendChild(option);
                });
                updateDisponibleHint();
                return;
            }

            // Pour une sortie, seulement les lots avec quantité disponible
            try {
                const response = await fetch(`/admin/matieres-premieres/stock/matiere/${matiereId}/magasin/${magasinId}/lots`);
                const lots = await response.json();

                if (response.ok && lots.length > 0) {
                    lots.forEach(lot => {
                        const option = document.createElement('option');
                        option.value = lot.id;
                        option.textContent = `${lot.code_lot} (Disponible: ${lot.disponible})`;
                        option.dataset.disponible = lot.disponible;
                        lotSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'Aucun lot disponible';
                    option.disabled = true;
                    lotSelect.appendChild(option);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        document.getElementById('mouvementMatiere').addEventListener('change', refreshMagasinOptions);
        document.getElementById('mouvementMagasin').addEventListener('change', refreshLotOptions);

        // Afficher la quantité disponible du lot sélectionné (utile pour les sorties)
        function updateDisponibleHint() {
            const lotSelect = document.getElementById('mouvementLot');
            const hint = document.getElementById('mouvementDisponible');
            const quantiteInput = document.getElementById('mouvementQuantite');
            if (!hint) return;

            const selected = lotSelect.options[lotSelect.selectedIndex];
            const disponible = selected ? selected.dataset.disponible : undefined;

            if (disponible === undefined || lotSelect.value === '') {
                hint.classList.add('hidden');
                quantiteInput.removeAttribute('max');
                return;
            }

            hint.textContent = `Disponible dans ce lot : ${disponible}`;
            hint.classList.remove('hidden');

            const type = document.getElementById('mouvementType').value;
            if (type === 'sortie') {
                quantiteInput.max = disponible;
            } else {
                quantiteInput.removeAttribute('max');
            }
        }

        document.getElementById('mouvementLot').addEventListener('change', updateDisponibleHint);
        document.getElementById('mouvementType').addEventListener('change', function() {
            // Le type modifie la liste des magasins/lots autorisés
            refreshMagasinOptions();
            updateDisponibleHint();
        });

        async function loadHistorique() {
            const matiereId = document.getElementById('histMatiere').value;
            const type = document.getElementById('histType').value;
            
            let url = '{{ route("admin.matieres-premieres.stock.historique") }}';
            const params = new URLSearchParams();
            if (matiereId) params.append('matiere_id', matiereId);
            if (type) params.append('type', type);
            if (params.toString()) url += '?' + params.toString();

            try {
                const response = await fetch(url);
                const data = await response.json();
                
                const tbody = document.getElementById('historique-body');
                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun mouvement trouvé</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.map(m => `
                    <tr class="border-b hover:bg-gray-50 cursor-pointer" onclick="showMouvementDetails(${m.id})">
                        <td class="px-4 py-3 text-sm">${new Date(m.date_mouvement).toLocaleDateString('fr-FR')}</td>
                        <td class="px-4 py-3 text-sm">${escapeHtml(m.matiere?.nom || '-')}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs ${m.type === 'entree' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                ${m.type === 'entree' ? 'Entrée' : 'Sortie'}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">${escapeHtml(String(m.quantite))}</td>
                        <td class="px-4 py-3 text-sm">${escapeHtml(m.magasin?.nom || '-')}</td>
                        <td class="px-4 py-3 text-sm">${escapeHtml(m.lot?.code_lot || '-')}</td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        async function loadStatistiques() {
            try {
                const response = await fetch('{{ route("admin.matieres-premieres.stock.statistiques") }}');
                const data = await response.json();

                // Alertes
                const alertesDiv = document.getElementById('alertes-stock');
                if (data.alertes.length === 0) {
                    alertesDiv.innerHTML = '<p class="text-green-600">Aucune alerte de stock</p>';
                } else {
                    alertesDiv.innerHTML = data.alertes.map(a => `
                        <div class="bg-red-50 border border-red-200 rounded p-3">
                            <p class="font-medium text-red-800">${escapeHtml(a.nom)} (${escapeHtml(a.code)})</p>
                            <p class="text-sm text-red-600">Lot: ${escapeHtml(a.code_lot)} - Disponible: ${escapeHtml(String(a.disponible))}</p>
                        </div>
                    `).join('');
                }

                // Top matières
                const topDiv = document.getElementById('top-matieres');
                if (data.top_matieres.length === 0) {
                    topDiv.innerHTML = '<p class="text-gray-500">Aucune donnée</p>';
                } else {
                    topDiv.innerHTML = data.top_matieres.map((m, i) => `
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <p class="font-medium text-blue-800">${i + 1}. ${escapeHtml(m.matiere?.nom || '-')}</p>
                            <p class="text-sm text-blue-600">Total utilisé: ${escapeHtml(String(m.total))}</p>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Charger la vue d'ensemble au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadStockOverview();
        });

        // Gestion du modal de création de lot
        function openLotModal() {
            document.getElementById('lotModal').classList.remove('hidden');
            document.getElementById('lotModal').classList.add('flex');
        }

        function closeLotModal() {
            document.getElementById('lotModal').classList.add('hidden');
            document.getElementById('lotModal').classList.remove('flex');
            document.getElementById('lotForm').reset();
            // Réinitialiser à une seule ligne
            const container = document.getElementById('matieresContainer');
            container.innerHTML = `
                <div class="matiere-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matière Première</label>
                        <select class="lot-matiere w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Sélectionner...</option>
                            @foreach($allMatieres ?? [] as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->nom }} ({{ $matiere->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                        <input type="number" step="0.01" min="0.01" class="lot-quantite w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                        <select class="lot-magasin w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Sélectionner...</option>
                            @foreach($magasins ?? [] as $magasin)
                            <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeMatiereRow(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            Supprimer
                        </button>
                    </div>
                </div>
            `;
        }

        // Gestion du modal des détails de matière première
        async function showMatiereDetails(matiereId) {
            document.getElementById('matiereDetailsModal').classList.remove('hidden');
            document.getElementById('matiereDetailsModal').classList.add('flex');
            document.getElementById('matiereDetailsContent').innerHTML = '<p class="text-gray-500">Chargement...</p>';

            try {
                const response = await fetch(`/admin/matieres-premieres/stock/matiere/${matiereId}/details`);
                const data = await response.json();

                if (response.ok) {
                    document.getElementById('matiereDetailsTitle').textContent = 
                        `${data.matiere.nom} (${data.matiere.code}) - ${data.matiere.unite}`;

                    if (data.stocks.length === 0) {
                        document.getElementById('matiereDetailsContent').innerHTML = 
                            '<p class="text-gray-500">Aucun stock trouvé pour cette matière première</p>';
                        return;
                    }

                    let html = '<div class="space-y-4">';
                    data.stocks.forEach(stock => {
                        html += `
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="font-semibold text-[#305327] mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    ${escapeHtml(stock.magasin_nom)}
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                                    <div>
                                        <span class="text-sm text-gray-500">Total:</span>
                                        <p class="font-medium">${escapeHtml(String(stock.total_quantite))}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Utilisé:</span>
                                        <p class="font-medium">${escapeHtml(String(stock.total_utilise))}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Disponible:</span>
                                        <p class="font-medium ${stock.total_disponible < 10 ? 'text-red-600' : 'text-green-600'}">${escapeHtml(String(stock.total_disponible))}</p>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block mb-2">Lots:</span>
                                    <div class="space-y-2">
                                        ${stock.lots.map(lot => `
                                            <div class="bg-white rounded p-2 border border-gray-200 flex justify-between items-center">
                                                <span class="text-sm font-medium">${escapeHtml(lot.code_lot)}</span>
                                                <div class="text-sm text-gray-600">
                                                    <span>Q: ${escapeHtml(String(lot.quantite))}</span>
                                                    <span class="mx-2">|</span>
                                                    <span>U: ${escapeHtml(String(lot.quantite_utiliser))}</span>
                                                    <span class="mx-2">|</span>
                                                    <span class="${lot.disponible < 10 ? 'text-red-600' : 'text-green-600'}">D: ${escapeHtml(String(lot.disponible))}</span>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('matiereDetailsContent').innerHTML = html;
                } else {
                    document.getElementById('matiereDetailsContent').innerHTML = 
                        '<p class="text-red-500">Erreur: ' + (data.error || 'Erreur inconnue') + '</p>';
                }
            } catch (error) {
                document.getElementById('matiereDetailsContent').innerHTML = 
                    '<p class="text-red-500">Erreur: ' + error.message + '</p>';
            }
        }

        function closeMatiereDetailsModal() {
            document.getElementById('matiereDetailsModal').classList.add('hidden');
            document.getElementById('matiereDetailsModal').classList.remove('flex');
        }

        // Gestion du modal des détails de lot
        async function loadLots() {
            try {
                const response = await fetch('/admin/matieres-premieres/stock/lots');
                const lots = await response.json();
                console.log(lots);
                const tbody = document.getElementById('lots-body');
                if (lots.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Aucun lot trouvé</td></tr>';
                    return;
                }

                tbody.innerHTML = lots.map(lot => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-[#008d36] cursor-pointer" onclick="showLotDetails(${lot.id})">${escapeHtml(lot.code_lot)}</td>
                        <td class="px-4 py-3 text-sm">${new Date(lot.created_at).toLocaleDateString('fr-FR')}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Actif</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="event.stopPropagation(); deleteLot(${lot.id})" class="text-red-600 hover:text-red-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        async function showLotDetails(lotId) {
            document.getElementById('lotDetailsModal').classList.remove('hidden');
            document.getElementById('lotDetailsModal').classList.add('flex');
            document.getElementById('lotDetailsContent').innerHTML = '<p class="text-gray-500">Chargement...</p>';

            try {
                const response = await fetch(`/admin/matieres-premieres/stock/lot/${lotId}/details`);
                const data = await response.json();

                if (response.ok) {
                    document.getElementById('lotDetailsTitle').textContent = 
                        `Lot ${data.lot.code_lot}`;

                    if (data.matieres.length === 0) {
                        document.getElementById('lotDetailsContent').innerHTML = 
                            '<p class="text-gray-500">Aucune matière première dans ce lot</p>';
                        return;
                    }

                    // Grouper par magasin
                    const groupedByMagasin = {};
                    data.matieres.forEach(m => {
                        if (!groupedByMagasin[m.magasin_id]) {
                            groupedByMagasin[m.magasin_id] = {
                                nom: m.magasin_nom,
                                matieres: []
                            };
                        }
                        groupedByMagasin[m.magasin_id].matieres.push(m);
                    });

                    let html = `
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500">Code:</span>
                                    <p class="font-medium">${escapeHtml(data.lot.code_lot)}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Date création:</span>
                                    <p class="font-medium">${new Date(data.lot.created_at).toLocaleDateString('fr-FR')}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                    `;

                    Object.values(groupedByMagasin).forEach(group => {
                        html += `
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="font-semibold text-[#305327] mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    ${escapeHtml(group.nom)}
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-white">
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Matière</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Initial</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Utilisé</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Restant</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            ${group.matieres.map(m => `
                                                <tr>
                                                    <td class="px-3 py-2 text-sm">${escapeHtml(m.matiere_nom)} (${escapeHtml(m.matiere_code)})</td>
                                                    <td class="px-3 py-2 text-sm">${escapeHtml(String(m.quantite))} ${escapeHtml(m.matiere_unite)}</td>
                                                    <td class="px-3 py-2 text-sm">${escapeHtml(String(m.quantite_utiliser))} ${escapeHtml(m.matiere_unite)}</td>
                                                    <td class="px-3 py-2 text-sm font-medium ${m.disponible < 10 ? 'text-red-600' : 'text-green-600'}">${escapeHtml(String(m.disponible))} ${escapeHtml(m.matiere_unite)}</td>
                                                    <td class="px-3 py-2 text-sm flex gap-2">
                                                        <button onclick="increaseMatiereQuantity(${data.lot.id}, ${m.matiere_id}, ${m.magasin_id}, '${escapeHtml(m.matiere_nom)}', '${escapeHtml(m.magasin_nom)}')" class="text-green-600 hover:text-green-800 transition" title="Augmenter la quantité">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                            </svg>
                                                        </button>
                                                        <button onclick="deleteMatiereFromLot(${data.lot.id}, ${m.matiere_id}, ${m.magasin_id})" class="text-red-600 hover:text-red-800 transition" title="Supprimer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    });

                    html += '</div>';
                    document.getElementById('lotDetailsContent').innerHTML = html;
                    document.getElementById('lotActions').classList.remove('hidden');
                } else {
                    document.getElementById('lotDetailsContent').innerHTML = 
                        '<p class="text-red-500">Erreur: ' + (data.error || 'Erreur inconnue') + '</p>';
                }
            } catch (error) {
                document.getElementById('lotDetailsContent').innerHTML = 
                    '<p class="text-red-500">Erreur: ' + error.message + '</p>';
            }
        }

        function closeLotDetailsModal() {
            document.getElementById('lotDetailsModal').classList.add('hidden');
            document.getElementById('lotDetailsModal').classList.remove('flex');
        }

        // Gestion du modal des détails de mouvement
        async function showMouvementDetails(mouvementId) {
            document.getElementById('mouvementDetailsModal').classList.remove('hidden');
            document.getElementById('mouvementDetailsModal').classList.add('flex');
            document.getElementById('mouvementDetailsContent').innerHTML = '<p class="text-gray-500">Chargement...</p>';

            try {
                const response = await fetch(`/admin/matieres-premieres/stock/mouvement/${mouvementId}/details`);
                const data = await response.json();
                console.log(data);
                if (response.ok) {
                    const typeLabel = data.mouvement.type === 'entree' ? 'Entrée' : 'Sortie';
                    const typeClass = data.mouvement.type === 'entree' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

                    document.getElementById('mouvementDetailsTitle').textContent = 
                        `${typeLabel} - ${data.mouvement.matiere?.nom || '-'}`;

                    let html = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500">Date:</span>
                                    <p class="font-medium">${new Date(data.mouvement.date_mouvement).toLocaleDateString('fr-FR')} ${new Date(data.mouvement.date_mouvement).toLocaleTimeString('fr-FR')}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Type:</span>
                                    <p><span class="px-2 py-1 rounded-full text-xs ${typeClass}">${typeLabel}</span></p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Quantité:</span>
                                    <p class="font-medium">${escapeHtml(String(data.mouvement.quantite))}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Matière:</span>
                                    <p class="font-medium">${escapeHtml(data.mouvement.matiere?.nom || '-')} (${escapeHtml(data.mouvement.matiere?.code || '-')})</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Magasin:</span>
                                    <p class="font-medium">${escapeHtml(data.mouvement.magasin?.nom || '-')}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Lot:</span>
                                    <p class="font-medium">${escapeHtml(data.mouvement.lot?.code_lot || '-')}</p>
                                </div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Gérant:</span>
                                <p class="font-medium">${escapeHtml((data.mouvement.gerant?.nom || '') + ' ' + (data.mouvement.gerant?.prenom || '') || '-')}</p>
                                <p class="text-sm text-gray-600">${escapeHtml(data.mouvement.gerant?.email || '-')}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Observation:</span>
                                <p class="font-medium bg-gray-50 p-3 rounded">${escapeHtml(data.mouvement.observation || 'Aucune observation')}</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('mouvementDetailsContent').innerHTML = html;
                } else {
                    document.getElementById('mouvementDetailsContent').innerHTML = 
                        '<p class="text-red-500">Erreur: ' + (data.error || 'Erreur inconnue') + '</p>';
                }
            } catch (error) {
                document.getElementById('mouvementDetailsContent').innerHTML = 
                    '<p class="text-red-500">Erreur: ' + error.message + '</p>';
            }
        }

        function closeMouvementDetailsModal() {
            document.getElementById('mouvementDetailsModal').classList.add('hidden');
            document.getElementById('mouvementDetailsModal').classList.remove('flex');
        }

        // Gestion du modal de confirmation
        let confirmCallback = null;

        function showConfirmModal(title, message, callback) {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            confirmCallback = callback;
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
            confirmCallback = null;
        }

        document.getElementById('confirmButton').addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            closeConfirmModal();
        });

        function addMatiereRow() {
            const container = document.getElementById('matieresContainer');
            const newRow = document.createElement('div');
            newRow.className = 'matiere-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded';
            newRow.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matière Première</label>
                    <select class="lot-matiere w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">Sélectionner...</option>
                        @foreach($allMatieres ?? [] as $matiere)
                        <option value="{{ $matiere->id }}">{{ $matiere->nom }} ({{ $matiere->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" step="0.01" min="0.01" class="lot-quantite w-full px-3 py-2 border border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                    <select class="lot-magasin w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">Sélectionner...</option>
                        @foreach($magasins ?? [] as $magasin)
                        <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="removeMatiereRow(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                        Supprimer
                    </button>
                </div>
            `;
            container.appendChild(newRow);
        }

        function removeMatiereRow(button) {
            const container = document.getElementById('matieresContainer');
            if (container.children.length > 1) {
                button.closest('.matiere-row').remove();
            } else {
                showToast('Vous devez avoir au moins une matière première', 'error');
            }
        }

        async function submitLot(event) {
            event.preventDefault();
            
            const matieres = [];
            const rows = document.querySelectorAll('.matiere-row');
            
            rows.forEach(row => {
                const matiereId = row.querySelector('.lot-matiere').value;
                const quantite = row.querySelector('.lot-quantite').value;
                const magasinId = row.querySelector('.lot-magasin').value;
                
                if (matiereId && quantite && magasinId) {
                    matieres.push({
                        matiere_id: parseInt(matiereId),
                        quantite: parseFloat(quantite),
                        magasin_id: parseInt(magasinId)
                    });
                }
            });

            if (matieres.length === 0) {
                showToast('Veuillez ajouter au moins une matière première', 'error');
                return;
            }

            showConfirmModal(
                'Créer un nouveau lot',
                `Êtes-vous sûr de vouloir créer un nouveau lot avec ${matieres.length} matière(s) première(s) ?`,
                async () => {
                    const payload = {
                        matieres: matieres,
                        _token: '{{ csrf_token() }}'
                    };

                    try {
                        const response = await fetch('{{ route("admin.matieres-premieres.stock.storeLot") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json();
                        
                        if (response.ok) {
                            showToast('Lot créé avec succès: ' + data.lot.code_lot, 'success');
                            closeLotModal();
                            // Ajouter le nouveau lot au select
                            const lotSelect = document.getElementById('mouvementLot');
                            const option = document.createElement('option');
                            option.value = data.lot.id;
                            option.textContent = data.lot.code_lot;
                            lotSelect.appendChild(option);
                            lotSelect.value = data.lot.id;
                        } else {
                            showToast(data.error || 'Erreur inconnue', 'error');
                        }
                    } catch (error) {
                        showToast('Erreur: ' + error.message, 'error');
                    }
                }
            );
        }

        async function deleteLot(lotId) {
            showConfirmModal(
                'Supprimer le lot',
                'Êtes-vous sûr de vouloir supprimer ce lot ? Tous les enregistrements liés (matières premières, mouvements) seront également supprimés.',
                async () => {
                    try {
                        const response = await fetch(`/admin/matieres-premieres/stock/lot/${lotId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        const data = await response.json();

                        if (response.ok) {
                            showToast('Lot supprimé avec succès', 'success');
                            loadLots();
                            loadStockOverview();
                        } else {
                            showToast(data.error || 'Erreur inconnue', 'error');
                        }
                    } catch (error) {
                        showToast('Erreur: ' + error.message, 'error');
                    }
                }
            );
        }

        async function deleteMatiereFromLot(lotId, matiereId, magasinId) {
            showConfirmModal(
                'Supprimer la matière première du lot',
                'Êtes-vous sûr de vouloir supprimer cette matière première du lot ?',
                async () => {
                    try {
                        const response = await fetch('/admin/matieres-premieres/stock/matiere/delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                lot_id: lotId,
                                matiere_id: matiereId,
                                magasin_id: magasinId,
                            }),
                        });
                        const data = await response.json();

                        if (response.ok) {
                            showToast('Matière première supprimée du lot avec succès', 'success');
                            showLotDetails(lotId);
                            loadStockOverview();
                        } else {
                            showToast(data.error || 'Erreur inconnue', 'error');
                        }
                    } catch (error) {
                        showToast('Erreur: ' + error.message, 'error');
                    }
                }
            );
        }

        let currentLotId = null;
        let currentMatiereId = null;
        let currentMagasinId = null;

        function openAddMatiereModal() {
            document.getElementById('addMatiereModal').classList.remove('hidden');
            document.getElementById('addMatiereModal').classList.add('flex');
            loadMatieresAndMagasins();
        }

        function closeAddMatiereModal() {
            document.getElementById('addMatiereModal').classList.add('hidden');
            document.getElementById('addMatiereModal').classList.remove('flex');
            document.getElementById('addMatiereSelect').value = '';
            document.getElementById('addMagasinSelect').value = '';
            document.getElementById('addMatiereQuantite').value = '';
        }

        function openIncreaseQuantityModal(lotId, matiereId, magasinId, matiereNom, magasinNom) {
            currentLotId = lotId;
            currentMatiereId = matiereId;
            currentMagasinId = magasinId;
            document.getElementById('increaseMatiereInfo').textContent = `Matière: ${matiereNom} - Magasin: ${magasinNom}`;
            document.getElementById('increaseQuantity').value = '';
            document.getElementById('increaseQuantityModal').classList.remove('hidden');
            document.getElementById('increaseQuantityModal').classList.add('flex');
        }

        function closeIncreaseQuantityModal() {
            document.getElementById('increaseQuantityModal').classList.add('hidden');
            document.getElementById('increaseQuantityModal').classList.remove('flex');
            document.getElementById('increaseQuantity').value = '';
        }

        async function loadMatieresAndMagasins() {
            try {
                const matieresResponse = await fetch('{{ route('admin.matieres-premieres.all') }}');
                const matieres = await matieresResponse.json();

                const matiereSelect = document.getElementById('addMatiereSelect');
                matiereSelect.innerHTML = '<option value="">Sélectionner...</option>';
                matieres.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.id;
                    option.textContent = `${m.nom} (${m.code})`;
                    matiereSelect.appendChild(option);
                });

                // Utiliser les magasins déjà disponibles dans la vue
                const magasins = @json(($magasins ?? [])->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom])->values());
                const magasinSelect = document.getElementById('addMagasinSelect');
                magasinSelect.innerHTML = '<option value="">Sélectionner...</option>';
                magasins.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.id;
                    option.textContent = m.nom;
                    magasinSelect.appendChild(option);
                });
            } catch (error) {
                showToast('Erreur lors du chargement des données', 'error');
                console.error('Erreur:', error);
            }
        }

        async function addMatiereToLot() {
            const matiereId = document.getElementById('addMatiereSelect').value;
            const magasinId = document.getElementById('addMagasinSelect').value;
            const quantite = document.getElementById('addMatiereQuantite').value;

            if (!matiereId || !magasinId || !quantite) {
                showToast('Veuillez remplir tous les champs', 'error');
                return;
            }

            try {
                const response = await fetch('/admin/matieres-premieres/stock/matiere/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        lot_id: currentLotId,
                        matiere_id: parseInt(matiereId),
                        magasin_id: parseInt(magasinId),
                        quantite: parseFloat(quantite),
                    }),
                });
                const data = await response.json();

                if (response.ok) {
                    showToast('Matière première ajoutée au lot avec succès', 'success');
                    closeAddMatiereModal();
                    showLotDetails(currentLotId);
                    loadStockOverview();
                } else {
                    showToast(data.error || 'Erreur inconnue', 'error');
                }
            } catch (error) {
                showToast('Erreur: ' + error.message, 'error');
            }
        }

        async function confirmIncreaseQuantity() {
            const quantite = document.getElementById('increaseQuantity').value;
            if (!quantite || isNaN(quantite) || parseFloat(quantite) <= 0) {
                showToast('Veuillez entrer une quantité valide', 'error');
                return;
            }

            try {
                const response = await fetch('/admin/matieres-premieres/stock/matiere/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        lot_id: currentLotId,
                        matiere_id: currentMatiereId,
                        magasin_id: currentMagasinId,
                        quantite: parseFloat(quantite),
                    }),
                });
                const data = await response.json();

                if (response.ok) {
                    showToast('Quantité augmentée avec succès', 'success');
                    closeIncreaseQuantityModal();
                    showLotDetails(currentLotId);
                    loadStockOverview();
                } else {
                    showToast(data.error || 'Erreur inconnue', 'error');
                }
            } catch (error) {
                showToast('Erreur: ' + error.message, 'error');
            }
        }

        async function increaseMatiereQuantity(lotId, matiereId, magasinId, matiereNom, magasinNom) {
            openIncreaseQuantityModal(lotId, matiereId, magasinId, matiereNom, magasinNom);
        }

        // Modifier showLotDetails pour stocker l'ID du lot courant
        const originalShowLotDetails = showLotDetails;
        showLotDetails = function(lotId) {
            currentLotId = lotId;
            originalShowLotDetails(lotId);
        };
    </script>
</body>
</html>
