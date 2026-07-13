<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Entités - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map-site, #map-ferme, #map-magasin {
            height: 300px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 1rem;
            z-index: 1;
        }
        #map-overview {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 2rem;
            z-index: 1;
        }
        .leaflet-pane {
            z-index: 1 !important;
        }
        .leaflet-top {
            z-index: 2 !important;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Entités" color="blue" />

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

        <div id="ajaxError" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
            <p id="ajaxErrorMessage"></p>
        </div>

        <div id="ajaxSuccess" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden">
            <p id="ajaxSuccessMessage"></p>
        </div>

        <!-- Onglets pour les différentes entités -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-[#305327] mb-6">Gestion des Entités</h2>
            
            <div class="flex border-b border-gray-200 mb-6">
                <button onclick="switchTab('sites')" id="tab-sites" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-[#008d36] text-[#008d36]">
                    Sites
                </button>
                <button onclick="switchTab('fermes')" id="tab-fermes" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-[#008d36]">
                    Fermes
                </button>
                <button onclick="switchTab('magasins')" id="tab-magasins" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-[#008d36]">
                    Magasins
                </button>
            </div>

            <!-- Contenu Sites -->
            <div id="content-sites" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#305327]">Liste des Sites</h3>
                    <button onclick="openSiteModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouveau Site
                    </button>
                </div>

                <!-- Filtres Sites -->
                <div class="mb-4">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label for="searchSites" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input type="text" id="searchSites" value="{{ request('search') }}" placeholder="Nom, adresse, gérant..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchSites()">
                        </div>
                        <button onclick="resetSearchSites()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Adresse</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Latitude</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Longitude</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Longueur</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Largeur</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gérant</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sites as $site)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $site->nom }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $site->adresse ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $site->latitude ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $site->longitude ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $site->longueur ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $site->largeur ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $site->gerantUser ? $site->gerantUser->nom . ' ' . $site->gerantUser->prenom : '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <button onclick="editSite({{ $site->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteSite({{ $site->id }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    Aucun site ne correspond aux critères sélectionnés.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="pagination-sites" class="mt-4">
                    {{ $sites->links() }}
                </div>
            </div>

            <!-- Contenu Fermes -->
            <div id="content-fermes" class="tab-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#305327]">Liste des Fermes</h3>
                    <button onclick="openFermeModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouvelle Ferme
                    </button>
                </div>

                <!-- Filtres Fermes -->
                <div class="mb-4">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label for="searchFermes" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input type="text" id="searchFermes" value="{{ request('search') }}" placeholder="Nom, site, gérant..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchFermes()">
                        </div>
                        <div class="w-full md:w-56">
                            <label for="siteFermes" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                            <select id="siteFermes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchFermes()">
                                <option value="">Tous les sites</option>
                                @foreach ($allSites as $site)
                                    <option value="{{ $site->id }}" {{ request('site') == $site->id ? 'selected' : '' }}>{{ $site->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button onclick="resetSearchFermes()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Site</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Latitude</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Longitude</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Longueur</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Largeur</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gérant</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fermes as $ferme)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $ferme->nom }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ferme->site ? $ferme->site->nom : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ferme->latitude ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ferme->longitude ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ferme->longueur ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ferme->largeur ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ferme->gerantUser ? $ferme->gerantUser->nom . ' ' . $ferme->gerantUser->prenom : '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <button onclick="viewFermePoulets({{ $ferme->id }})" class="text-blue-600 hover:text-blue-800 mr-2" title="Voir poulets">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editFerme({{ $ferme->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteFerme({{ $ferme->id }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    Aucune ferme ne correspond aux critères sélectionnés.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="pagination-fermes" class="mt-4">
                    {{ $fermes->links() }}
                </div>
            </div>

            <!-- Contenu Magasins -->
            <div id="content-magasins" class="tab-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#305327]">Liste des Magasins</h3>
                    <button onclick="openMagasinModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouveau Magasin
                    </button>
                </div>

                <!-- Filtres Magasins -->
                <div class="mb-4">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label for="searchMagasins" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input type="text" id="searchMagasins" value="{{ request('search') }}" placeholder="Nom, site, gérant..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchMagasins()">
                        </div>
                        <div class="w-full md:w-56">
                            <label for="siteMagasins" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                            <select id="siteMagasins" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchMagasins()">
                                <option value="">Tous les sites</option>
                                @foreach ($allSites as $site)
                                    <option value="{{ $site->id }}" {{ request('site') == $site->id ? 'selected' : '' }}>{{ $site->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button onclick="resetSearchMagasins()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Site</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Latitude</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Longitude</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Longueur</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Largeur</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gérant</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($magasins as $magasin)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $magasin->nom }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $magasin->site ? $magasin->site->nom : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $magasin->latitude ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $magasin->longitude ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $magasin->longueur ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $magasin->largeur ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $magasin->gerantUser ? $magasin->gerantUser->nom . ' ' . $magasin->gerantUser->prenom : '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <button onclick="viewMagasinStocks({{ $magasin->id }})" class="text-blue-600 hover:text-blue-800 mr-2" title="Voir stocks">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editMagasin({{ $magasin->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteMagasin({{ $magasin->id }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    Aucun magasin ne correspond aux critères sélectionnés.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="pagination-magasins" class="mt-4">
                    {{ $magasins->links() }}
                </div>
            </div>

            <div id="stats-entites" class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Statistiques des entités</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                        <p class="text-sm text-[#305327] font-medium">Total Sites</p>
                        <p class="text-2xl font-bold text-[#305327]" id="stats-total-sites">{{ $stats['totalSites'] }}</p>
                    </div>
                    <div class="bg-[#008d36]/10 rounded-lg p-4 border border-[#008d36]/20">
                        <p class="text-sm text-[#008d36] font-medium">Total Fermes</p>
                        <p class="text-2xl font-bold text-[#008d36]" id="stats-total-fermes">{{ $stats['totalFermes'] }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <p class="text-sm text-blue-700 font-medium">Total Magasins</p>
                        <p class="text-2xl font-bold text-blue-700" id="stats-total-magasins">{{ $stats['totalMagasins'] }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Fermes par site</h4>
                        <ul class="space-y-1 text-sm" id="stats-fermes-by-site">
                            @forelse ($stats['fermesBySite'] as $site => $count)
                            <li class="flex justify-between"><span>{{ $site }}</span><span class="font-semibold">{{ $count }}</span></li>
                            @empty
                            <li class="text-gray-500">Aucune donnée.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Magasins par site</h4>
                        <ul class="space-y-1 text-sm" id="stats-magasins-by-site">
                            @forelse ($stats['magasinsBySite'] as $site => $count)
                            <li class="flex justify-between"><span>{{ $site }}</span><span class="font-semibold">{{ $count }}</span></li>
                            @empty
                            <li class="text-gray-500">Aucune donnée.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte de vue d'ensemble -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-[#305327] mb-4">Carte des emplacements</h2>
            <div id="map-overview"></div>
            <p class="text-sm text-gray-500 mt-2">Cette carte affiche toutes les fermes et magasins avec leurs localisations</p>
        </div>
    </div>

    <!-- Modal Site -->
    <div id="siteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <h3 id="siteModalTitle" class="text-xl font-bold mb-4">Nouveau Site</h3>
            <form id="siteForm" method="POST" action="{{ route('admin.entites.sites.store') }}">
                @csrf
                <input type="hidden" id="siteId" name="id" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="siteNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" id="siteAdresse" name="adresse" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Localisation sur la carte</label>
                    <div class="relative">
                        <button type="button" onclick="locateUser('site')" class="absolute top-2 right-2 z-[1000] bg-[#008d36] text-white px-3 py-2 rounded-md hover:bg-[#305327] transition duration-200 text-sm font-medium shadow-lg">
                            📍 Me localiser
                        </button>
                        <div id="map-site"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cliquez sur la carte pour définir la position</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" id="siteLongitude" name="longitude" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" id="siteLatitude" name="latitude" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longueur</label>
                        <input type="number" step="any" id="siteLongueur" name="longueur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Largeur</label>
                        <input type="number" step="any" id="siteLargeur" name="largeur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gérant</label>
                    <select id="siteGerant" name="gerant" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner un gérant</option>
                        @php
                            $users = \App\Models\User::all();
                        @endphp
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->nom }} {{ $user->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeSiteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ferme -->
    <div id="fermeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <h3 id="fermeModalTitle" class="text-xl font-bold mb-4">Nouvelle Ferme</h3>
            <form id="fermeForm" method="POST" action="{{ route('admin.entites.fermes.store') }}">
                @csrf
                <input type="hidden" id="fermeId" name="id" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="fermeNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                    <select id="fermeSite" name="idsite" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner un site</option>
                        @foreach ($allSites as $site)
                        <option value="{{ $site->id }}">{{ $site->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Localisation sur la carte</label>
                    <div class="relative">
                        <button type="button" onclick="locateUser('ferme')" class="absolute top-2 right-2 z-[1000] bg-[#008d36] text-white px-3 py-2 rounded-md hover:bg-[#305327] transition duration-200 text-sm font-medium shadow-lg">
                            📍 Me localiser
                        </button>
                        <div id="map-ferme"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cliquez sur la carte pour définir la position</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" id="fermeLongitude" name="longitude" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" id="fermeLatitude" name="latitude" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longueur</label>
                        <input type="number" step="any" id="fermeLongueur" name="longueur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Largeur</label>
                        <input type="number" step="any" id="fermeLargeur" name="largeur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gérant</label>
                    <select id="fermeGerant" name="gerant" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner un gérant</option>
                        @php
                            $users = \App\Models\User::all();
                        @endphp
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->nom }} {{ $user->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeFermeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Magasin -->
    <div id="magasinModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <h3 id="magasinModalTitle" class="text-xl font-bold mb-4">Nouveau Magasin</h3>
            <form id="magasinForm" method="POST" action="{{ route('admin.entites.magasins.store') }}">
                @csrf
                <input type="hidden" id="magasinId" name="id" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="magasinNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                    <select id="magasinSite" name="idsite" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner un site</option>
                        @foreach ($allSites as $site)
                        <option value="{{ $site->id }}">{{ $site->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Localisation sur la carte</label>
                    <div class="relative">
                        <button type="button" onclick="locateUser('magasin')" class="absolute top-2 right-2 z-[1000] bg-[#008d36] text-white px-3 py-2 rounded-md hover:bg-[#305327] transition duration-200 text-sm font-medium shadow-lg">
                            📍 Me localiser
                        </button>
                        <div id="map-magasin"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cliquez sur la carte pour définir la position</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" id="magasinLongitude" name="longitude" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" id="magasinLatitude" name="latitude" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longueur</label>
                        <input type="number" step="any" id="magasinLongueur" name="longueur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Largeur</label>
                        <input type="number" step="any" id="magasinLargeur" name="largeur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gérant</label>
                    <select id="magasinGerant" name="gerant" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner un gérant</option>
                        @php
                            $users = \App\Models\User::all();
                        @endphp
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->nom }} {{ $user->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeMagasinModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Poulets de la Ferme -->
    <div id="fermePouletsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <h3 id="fermePouletsModalTitle" class="text-xl font-bold mb-4">Poulets de la Ferme</h3>
            <div id="fermePouletsContent">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-[#305327] mb-3">Poulets Actuellement dans la Ferme</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code Stock</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Poulet</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date Entrée</th>
                                </tr>
                            </thead>
                            <tbody id="currentPouletsTable">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Chargement...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-[#305327] mb-3">Historique des Poulets</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code Stock</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Poulet</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date Sortie</th>
                                </tr>
                            </thead>
                            <tbody id="historiquePouletsTable">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Chargement...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeFermePouletsModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Stocks du Magasin -->
    <div id="magasinStocksModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <h3 id="magasinStocksModalTitle" class="text-xl font-bold mb-4">Stocks du Magasin</h3>
            <div id="magasinStocksContent">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Matière Première</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité Utilisée</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Unité</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Seuil d'Alerte</th>
                            </tr>
                        </thead>
                        <tbody id="magasinStocksTable">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Chargement...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeMagasinStocksModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        function showAjaxError(message) {
            const errorDiv = document.getElementById('ajaxError');
            const errorMessage = document.getElementById('ajaxErrorMessage');
            errorMessage.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        function showAjaxSuccess(message) {
            const successDiv = document.getElementById('ajaxSuccess');
            const successMessage = document.getElementById('ajaxSuccessMessage');
            successMessage.textContent = message;
            successDiv.classList.remove('hidden');
            setTimeout(() => {
                successDiv.classList.add('hidden');
            }, 3000);
        }

        function switchTab(tab, pushState = true) {
            // Cacher tous les contenus
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Réinitialiser tous les onglets
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-[#008d36]', 'text-[#008d36]');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Afficher le contenu sélectionné
            document.getElementById('content-' + tab).classList.remove('hidden');

            // Activer l'onglet sélectionné
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tab).classList.add('border-[#008d36]', 'text-[#008d36]');

            // Mettre à jour l'URL sans recharger
            if (pushState) {
                const params = new URLSearchParams(window.location.search);
                params.set('tab', tab);
                history.pushState({}, '', window.location.pathname + '?' + params.toString());
            }
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function renderEntityRows(items, type) {
            if (items.length === 0) {
                const labels = { sites: 'site', fermes: 'ferme', magasins: 'magasin' };
                return `<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Aucun ${labels[type]} ne correspond aux critères sélectionnés.</td></tr>`;
            }

            const typeSingular = type.slice(0, -1);
            return items.map(item => {
                const siteOrAdresse = type === 'sites'
                    ? escapeHtml(item.adresse ?? '-')
                    : escapeHtml(item.site ?? '-');
                
                let actionsHtml = '';
                if (type === 'fermes') {
                    actionsHtml = `
                        <button onclick="viewFermePoulets(${item.id})" class="text-blue-600 hover:text-blue-800 mr-2" title="Voir poulets">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <button onclick="edit${capitalize(typeSingular)}(${item.id})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="delete${capitalize(typeSingular)}(${item.id})" class="text-red-600 hover:text-red-800" title="Supprimer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    `;
                } else if (type === 'magasins') {
                    actionsHtml = `
                        <button onclick="viewMagasinStocks(${item.id})" class="text-blue-600 hover:text-blue-800 mr-2" title="Voir stocks">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </button>
                        <button onclick="edit${capitalize(typeSingular)}(${item.id})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="delete${capitalize(typeSingular)}(${item.id})" class="text-red-600 hover:text-red-800" title="Supprimer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    `;
                } else {
                    actionsHtml = `
                        <button onclick="edit${capitalize(typeSingular)}(${item.id})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="delete${capitalize(typeSingular)}(${item.id})" class="text-red-600 hover:text-red-800" title="Supprimer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    `;
                }
                
                return `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(item.nom)}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${siteOrAdresse}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(item.latitude ?? '-')}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(item.longitude ?? '-')}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(item.longueur ?? '-')}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(item.largeur ?? '-')}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(item.gerant ?? '-')}</td>
                        <td class="px-4 py-3 text-sm">${actionsHtml}</td>
                    </tr>
                `;
            }).join('');
        }

        function renderStats(stats) {
            if (!stats) return;

            const totalSites = document.getElementById('stats-total-sites');
            const totalFermes = document.getElementById('stats-total-fermes');
            const totalMagasins = document.getElementById('stats-total-magasins');
            const fermesBySite = document.getElementById('stats-fermes-by-site');
            const magasinsBySite = document.getElementById('stats-magasins-by-site');

            if (totalSites) totalSites.textContent = stats.totalSites !== undefined ? stats.totalSites : 0;
            if (totalFermes) totalFermes.textContent = stats.totalFermes !== undefined ? stats.totalFermes : 0;
            if (totalMagasins) totalMagasins.textContent = stats.totalMagasins !== undefined ? stats.totalMagasins : 0;

            if (fermesBySite) {
                fermesBySite.innerHTML = stats.fermesBySite && Object.keys(stats.fermesBySite).length
                    ? Object.entries(stats.fermesBySite).map(([site, count]) => `<li class="flex justify-between"><span>${escapeHtml(site)}</span><span class="font-semibold">${escapeHtml(String(count))}</span></li>`).join('')
                    : '<li class="text-gray-500">Aucune donnée.</li>';
            }
            if (magasinsBySite) {
                magasinsBySite.innerHTML = stats.magasinsBySite && Object.keys(stats.magasinsBySite).length
                    ? Object.entries(stats.magasinsBySite).map(([site, count]) => `<li class="flex justify-between"><span>${escapeHtml(site)}</span><span class="font-semibold">${escapeHtml(String(count))}</span></li>`).join('')
                    : '<li class="text-gray-500">Aucune donnée.</li>';
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

        // Recherche automatique pour les sites
        const debouncedSearchSites = debounce(function() {
            const search = document.getElementById('searchSites').value;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'sites');
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            history.pushState({}, '', url.toString());
            loadEntities(url.toString());
        }, 500);

        function resetSearchSites() {
            document.getElementById('searchSites').value = '';
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'sites');
            url.searchParams.delete('search');
            loadEntities(url.toString());
        }

        // Recherche automatique pour les fermes
        const debouncedSearchFermes = debounce(function() {
            const search = document.getElementById('searchFermes').value;
            const site = document.getElementById('siteFermes').value;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'fermes');
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            if (site) {
                url.searchParams.set('site', site);
            } else {
                url.searchParams.delete('site');
            }
            history.pushState({}, '', url.toString());
            loadEntities(url.toString());
        }, 500);

        function resetSearchFermes() {
            document.getElementById('searchFermes').value = '';
            document.getElementById('siteFermes').value = '';
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'fermes');
            url.searchParams.delete('search');
            url.searchParams.delete('site');
            history.pushState({}, '', url.toString());
            loadEntities(url.toString());
        }

        // Recherche automatique pour les magasins
        const debouncedSearchMagasins = debounce(function() {
            const search = document.getElementById('searchMagasins').value;
            const site = document.getElementById('siteMagasins').value;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'magasins');
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            if (site) {
                url.searchParams.set('site', site);
            } else {
                url.searchParams.delete('site');
            }
            history.pushState({}, '', url.toString());
            loadEntities(url.toString());
        }, 500);

        function resetSearchMagasins() {
            document.getElementById('searchMagasins').value = '';
            document.getElementById('siteMagasins').value = '';
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'magasins');
            url.searchParams.delete('search');
            url.searchParams.delete('site');
            history.pushState({}, '', url.toString());
            loadEntities(url.toString());
        }

        function updateTables(data) {
            const sites = data.sites.items || data.sites || [];
            const fermes = data.fermes.items || data.fermes || [];
            const magasins = data.magasins.items || data.magasins || [];

            document.querySelector('#content-sites tbody').innerHTML = renderEntityRows(sites, 'sites');
            document.querySelector('#content-fermes tbody').innerHTML = renderEntityRows(fermes, 'fermes');
            document.querySelector('#content-magasins tbody').innerHTML = renderEntityRows(magasins, 'magasins');

            const paginationSites = document.getElementById('pagination-sites');
            const paginationFermes = document.getElementById('pagination-fermes');
            const paginationMagasins = document.getElementById('pagination-magasins');

            if (paginationSites && data.sites.pagination !== undefined) {
                paginationSites.innerHTML = data.sites.pagination;
            }
            if (paginationFermes && data.fermes.pagination !== undefined) {
                paginationFermes.innerHTML = data.fermes.pagination;
            }
            if (paginationMagasins && data.magasins.pagination !== undefined) {
                paginationMagasins.innerHTML = data.magasins.pagination;
            }

            renderStats(data.stats);
        }

        async function loadEntities(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                updateTables(data);
            } catch (error) {
                showAjaxError('Erreur lors du chargement des entités: ' + error.message);
            }
        }

        function syncFilterInputsFromUrl() {
            const params = new URLSearchParams(window.location.search);
            
            // Sync sites
            const searchSites = document.getElementById('searchSites');
            if (searchSites) searchSites.value = params.get('search') || '';
            
            // Sync fermes
            const searchFermes = document.getElementById('searchFermes');
            const siteFermes = document.getElementById('siteFermes');
            if (searchFermes) searchFermes.value = params.get('search') || '';
            if (siteFermes) siteFermes.value = params.get('site') || '';
            
            // Sync magasins
            const searchMagasins = document.getElementById('searchMagasins');
            const siteMagasins = document.getElementById('siteMagasins');
            if (searchMagasins) searchMagasins.value = params.get('search') || '';
            if (siteMagasins) siteMagasins.value = params.get('site') || '';
        }

        // Restaurer l'onglet actif et activer les filtres AJAX au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab && ['sites', 'fermes', 'magasins'].includes(tab)) {
                switchTab(tab, false);
            }
            syncFilterInputsFromUrl();
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab && ['sites', 'fermes', 'magasins'].includes(tab)) {
                switchTab(tab, false);
            }
            syncFilterInputsFromUrl();
        });

        // Site Modal
        function openSiteModal() {
            document.getElementById('siteModal').classList.remove('hidden');
            document.getElementById('siteModal').classList.add('flex');
            document.getElementById('siteModalTitle').textContent = 'Nouveau Site';
            document.getElementById('siteForm').action = '{{ route('admin.entites.sites.store') }}';
            document.getElementById('siteId').value = '';
            document.getElementById('siteForm').reset();
        }

        function closeSiteModal() {
            document.getElementById('siteModal').classList.add('hidden');
            document.getElementById('siteModal').classList.remove('flex');
        }

        async function editSite(id) {
            try {
                const response = await fetch(`/admin/entites/sites/${id}`);
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const site = await response.json();

                document.getElementById('siteModal').classList.remove('hidden');
                document.getElementById('siteModal').classList.add('flex');
                document.getElementById('siteModalTitle').textContent = 'Modifier Site';
                document.getElementById('siteForm').action = `/admin/entites/sites/${id}`;
                document.getElementById('siteId').value = site.id;
                document.getElementById('siteNom').value = site.nom;
                document.getElementById('siteAdresse').value = site.adresse || '';
                document.getElementById('siteLongitude').value = site.longitude || '';
                document.getElementById('siteLatitude').value = site.latitude || '';
                document.getElementById('siteLongueur').value = site.longueur || '';
                document.getElementById('siteLargeur').value = site.largeur || '';
                document.getElementById('siteGerant').value = site.gerant || '';

                // Update map marker if coordinates exist
                setTimeout(() => {
                    if (site.latitude && site.longitude) {
                        if (markerSite) {
                            mapSite.removeLayer(markerSite);
                        }
                        markerSite = L.marker([site.latitude, site.longitude]).addTo(mapSite);
                        mapSite.setView([site.latitude, site.longitude], 13);
                    }
                }, 200);

                let methodInput = document.getElementById('_method_site');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.id = '_method_site';
                    methodInput.value = 'PUT';
                    document.getElementById('siteForm').appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            } catch (error) {
                showAjaxError('Erreur lors du chargement du site: ' + error.message);
            }
        }

        async function deleteSite(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce site ?')) {
                try {
                    const response = await fetch(`/admin/entites/sites/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        showAjaxSuccess('Site supprimé avec succès');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        const errorData = await response.json();
                        showAjaxError('Erreur lors de la suppression: ' + (errorData.message || response.statusText));
                    }
                } catch (error) {
                    showAjaxError('Erreur lors de la suppression: ' + error.message);
                }
            }
        }

        // Ferme Modal
        function openFermeModal() {
            document.getElementById('fermeModal').classList.remove('hidden');
            document.getElementById('fermeModal').classList.add('flex');
            document.getElementById('fermeModalTitle').textContent = 'Nouvelle Ferme';
            document.getElementById('fermeForm').action = '{{ route('admin.entites.fermes.store') }}';
            document.getElementById('fermeId').value = '';
            document.getElementById('fermeForm').reset();
        }

        function closeFermeModal() {
            document.getElementById('fermeModal').classList.add('hidden');
            document.getElementById('fermeModal').classList.remove('flex');
        }

        async function editFerme(id) {
            try {
                const response = await fetch(`/admin/entites/fermes/${id}`);
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const ferme = await response.json();

                document.getElementById('fermeModal').classList.remove('hidden');
                document.getElementById('fermeModal').classList.add('flex');
                document.getElementById('fermeModalTitle').textContent = 'Modifier Ferme';
                document.getElementById('fermeForm').action = `/admin/entites/fermes/${id}`;
                document.getElementById('fermeId').value = ferme.id;
                document.getElementById('fermeNom').value = ferme.nom;
                document.getElementById('fermeSite').value = ferme.idsite;
                document.getElementById('fermeLongitude').value = ferme.longitude || '';
                document.getElementById('fermeLatitude').value = ferme.latitude || '';
                document.getElementById('fermeLongueur').value = ferme.longueur || '';
                document.getElementById('fermeLargeur').value = ferme.largeur || '';
                document.getElementById('fermeGerant').value = ferme.gerant || '';

                // Update map marker if coordinates exist
                setTimeout(() => {
                    if (ferme.latitude && ferme.longitude) {
                        if (markerFerme) {
                            mapFerme.removeLayer(markerFerme);
                        }
                        markerFerme = L.marker([ferme.latitude, ferme.longitude]).addTo(mapFerme);
                        mapFerme.setView([ferme.latitude, ferme.longitude], 13);
                    }
                }, 200);

                let methodInput = document.getElementById('_method_ferme');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.id = '_method_ferme';
                    methodInput.value = 'PUT';
                    document.getElementById('fermeForm').appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            } catch (error) {
                showAjaxError('Erreur lors du chargement de la ferme: ' + error.message);
            }
        }

        async function deleteFerme(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette ferme ?')) {
                try {
                    const response = await fetch(`/admin/entites/fermes/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        showAjaxSuccess('Ferme supprimée avec succès');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        const errorData = await response.json();
                        showAjaxError('Erreur lors de la suppression: ' + (errorData.message || response.statusText));
                    }
                } catch (error) {
                    showAjaxError('Erreur lors de la suppression: ' + error.message);
                }
            }
        }

        // Magasin Modal
        function openMagasinModal() {
            document.getElementById('magasinModal').classList.remove('hidden');
            document.getElementById('magasinModal').classList.add('flex');
            document.getElementById('magasinModalTitle').textContent = 'Nouveau Magasin';
            document.getElementById('magasinForm').action = '{{ route('admin.entites.magasins.store') }}';
            document.getElementById('magasinId').value = '';
            document.getElementById('magasinForm').reset();
        }

        function closeMagasinModal() {
            document.getElementById('magasinModal').classList.add('hidden');
            document.getElementById('magasinModal').classList.remove('flex');
        }

        async function editMagasin(id) {
            try {
                const response = await fetch(`/admin/entites/magasins/${id}`);
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const magasin = await response.json();

                document.getElementById('magasinModal').classList.remove('hidden');
                document.getElementById('magasinModal').classList.add('flex');
                document.getElementById('magasinModalTitle').textContent = 'Modifier Magasin';
                document.getElementById('magasinForm').action = `/admin/entites/magasins/${id}`;
                document.getElementById('magasinId').value = magasin.id;
                document.getElementById('magasinNom').value = magasin.nom;
                document.getElementById('magasinSite').value = magasin.idsite;
                document.getElementById('magasinLongitude').value = magasin.longitude || '';
                document.getElementById('magasinLatitude').value = magasin.latitude || '';
                document.getElementById('magasinLongueur').value = magasin.longueur || '';
                document.getElementById('magasinLargeur').value = magasin.largeur || '';
                document.getElementById('magasinGerant').value = magasin.gerant || '';

                // Update map marker if coordinates exist
                setTimeout(() => {
                    if (magasin.latitude && magasin.longitude) {
                        if (markerMagasin) {
                            mapMagasin.removeLayer(markerMagasin);
                        }
                        markerMagasin = L.marker([magasin.latitude, magasin.longitude]).addTo(mapMagasin);
                        mapMagasin.setView([magasin.latitude, magasin.longitude], 13);
                    }
                }, 200);

                let methodInput = document.getElementById('_method_magasin');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.id = '_method_magasin';
                    methodInput.value = 'PUT';
                    document.getElementById('magasinForm').appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            } catch (error) {
                showAjaxError('Erreur lors du chargement du magasin: ' + error.message);
            }
        }

        async function deleteMagasin(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce magasin ?')) {
                try {
                    const response = await fetch(`/admin/entites/magasins/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        showAjaxSuccess('Magasin supprimé avec succès');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        const errorData = await response.json();
                        showAjaxError('Erreur lors de la suppression: ' + (errorData.message || response.statusText));
                    }
                } catch (error) {
                    showAjaxError('Erreur lors de la suppression: ' + error.message);
                }
            }
        }

        // Leaflet Maps
        let mapSite, mapFerme, mapMagasin, mapOverview;
        let markerSite, markerFerme, markerMagasin;
        let overviewMarkers = [];

        // Geolocation function
        function locateUser(type) {
            if (!navigator.geolocation) {
                alert('La géolocalisation n\'est pas supportée par votre navigateur');
                return;
            }

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Localisation...';
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    let map, marker, latInput, lngInput;

                    if (type === 'site') {
                        map = mapSite;
                        marker = markerSite;
                        latInput = document.getElementById('siteLatitude');
                        lngInput = document.getElementById('siteLongitude');
                    } else if (type === 'ferme') {
                        map = mapFerme;
                        marker = markerFerme;
                        latInput = document.getElementById('fermeLatitude');
                        lngInput = document.getElementById('fermeLongitude');
                    } else if (type === 'magasin') {
                        map = mapMagasin;
                        marker = markerMagasin;
                        latInput = document.getElementById('magasinLatitude');
                        lngInput = document.getElementById('magasinLongitude');
                    }

                    if (marker) {
                        map.removeLayer(marker);
                    }
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 15);

                    latInput.value = lat;
                    lngInput.value = lng;

                    // Update the marker reference
                    if (type === 'site') markerSite = marker;
                    else if (type === 'ferme') markerFerme = marker;
                    else if (type === 'magasin') markerMagasin = marker;

                    btn.innerHTML = originalText;
                    btn.disabled = false;
                },
                function(error) {
                    let errorMessage = 'Impossible de récupérer votre position';
                    if (error.code === 1) {
                        errorMessage = 'Accès à la localisation refusé. Veuillez autoriser la géolocalisation dans votre navigateur.';
                    } else if (error.code === 2) {
                        errorMessage = 'Position non disponible';
                    } else if (error.code === 3) {
                        errorMessage = 'Délai d\'attente dépassé';
                    }
                    alert(errorMessage);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        async function loadOverviewLocations() {
            try {
                const response = await fetch('/admin/entites/locations');
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const locations = await response.json();

                // Clear existing markers
                overviewMarkers.forEach(marker => mapOverview.removeLayer(marker));
                overviewMarkers = [];

                // Add markers for each location
                locations.forEach(location => {
                    let markerColor;
                    let typeLabel;

                    if (location.type === 'ferme') {
                        markerColor = '#008d36';
                        typeLabel = 'Ferme';
                    } else if (location.type === 'magasin') {
                        markerColor = '#305327';
                        typeLabel = 'Magasin';
                    } else {
                        markerColor = '#FF6B35';
                        typeLabel = 'Site';
                    }

                    const markerIcon = L.divIcon({
                        className: 'custom-marker',
                        html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });

                    const marker = L.marker([location.latitude, location.longitude], { icon: markerIcon })
                        .addTo(mapOverview)
                        .bindPopup(`
                            <div style="min-width: 200px;">
                                <h3 style="font-weight: bold; margin-bottom: 5px; color: #305327;">${location.nom}</h3>
                                <p style="margin: 0; font-size: 13px;"><strong>Type:</strong> ${typeLabel}</p>
                                ${location.site ? `<p style="margin: 0; font-size: 13px;"><strong>Site:</strong> ${location.site}</p>` : ''}
                                ${location.gerant ? `<p style="margin: 0; font-size: 13px;"><strong>Gérant:</strong> ${location.gerant}</p>` : ''}
                                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Lat: ${parseFloat(location.latitude).toFixed(4)}, Lng: ${parseFloat(location.longitude).toFixed(4)}</p>
                            </div>
                        `);

                    overviewMarkers.push(marker);
                });

                // Fit map to show all markers if there are any
                if (overviewMarkers.length > 0) {
                    const group = L.featureGroup(overviewMarkers);
                    mapOverview.fitBounds(group.getBounds().pad(0.1));
                }
            } catch (error) {
                console.error('Erreur lors du chargement des localisations:', error);
            }
        }

        function initMaps() {
            // Initialize Overview Map
            mapOverview = L.map('map-overview').setView([14.7167, -17.4440], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapOverview);

            // Load locations from API
            loadOverviewLocations();

            // Initialize Site Map
            mapSite = L.map('map-site').setView([14.7167, -17.4440], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapSite);

            mapSite.on('click', function(e) {
                if (markerSite) {
                    mapSite.removeLayer(markerSite);
                }
                markerSite = L.marker(e.latlng).addTo(mapSite);
                document.getElementById('siteLatitude').value = e.latlng.lat;
                document.getElementById('siteLongitude').value = e.latlng.lng;
            });

            // Initialize Ferme Map
            mapFerme = L.map('map-ferme').setView([14.7167, -17.4440], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapFerme);

            mapFerme.on('click', function(e) {
                if (markerFerme) {
                    mapFerme.removeLayer(markerFerme);
                }
                markerFerme = L.marker(e.latlng).addTo(mapFerme);
                document.getElementById('fermeLatitude').value = e.latlng.lat;
                document.getElementById('fermeLongitude').value = e.latlng.lng;
            });

            // Initialize Magasin Map
            mapMagasin = L.map('map-magasin').setView([14.7167, -17.4440], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapMagasin);

            mapMagasin.on('click', function(e) {
                if (markerMagasin) {
                    mapMagasin.removeLayer(markerMagasin);
                }
                markerMagasin = L.marker(e.latlng).addTo(mapMagasin);
                document.getElementById('magasinLatitude').value = e.latlng.lat;
                document.getElementById('magasinLongitude').value = e.latlng.lng;
            });
        }

        // Initialize maps when modals are opened
        function openSiteModal() {
            document.getElementById('siteModal').classList.remove('hidden');
            document.getElementById('siteModal').classList.add('flex');
            document.getElementById('siteModalTitle').textContent = 'Nouveau Site';
            document.getElementById('siteForm').action = '{{ route('admin.entites.sites.store') }}';
            document.getElementById('siteId').value = '';
            document.getElementById('siteForm').reset();
            
            setTimeout(() => {
                if (!mapSite) {
                    initMaps();
                } else {
                    mapSite.invalidateSize();
                }
                if (markerSite) {
                    mapSite.removeLayer(markerSite);
                    markerSite = null;
                }
            }, 100);
        }

        function openFermeModal() {
            document.getElementById('fermeModal').classList.remove('hidden');
            document.getElementById('fermeModal').classList.add('flex');
            document.getElementById('fermeModalTitle').textContent = 'Nouvelle Ferme';
            document.getElementById('fermeForm').action = '{{ route('admin.entites.fermes.store') }}';
            document.getElementById('fermeId').value = '';
            document.getElementById('fermeForm').reset();
            
            setTimeout(() => {
                if (!mapFerme) {
                    initMaps();
                } else {
                    mapFerme.invalidateSize();
                }
                if (markerFerme) {
                    mapFerme.removeLayer(markerFerme);
                    markerFerme = null;
                }
            }, 100);
        }

        function openMagasinModal() {
            document.getElementById('magasinModal').classList.remove('hidden');
            document.getElementById('magasinModal').classList.add('flex');
            document.getElementById('magasinModalTitle').textContent = 'Nouveau Magasin';
            document.getElementById('magasinForm').action = '{{ route('admin.entites.magasins.store') }}';
            document.getElementById('magasinId').value = '';
            document.getElementById('magasinForm').reset();
            
            setTimeout(() => {
                if (!mapMagasin) {
                    initMaps();
                } else {
                    mapMagasin.invalidateSize();
                }
                if (markerMagasin) {
                    mapMagasin.removeLayer(markerMagasin);
                    markerMagasin = null;
                }
            }, 100);
        }

        async function viewFermePoulets(fermeId) {
            document.getElementById('fermePouletsModal').classList.remove('hidden');
            document.getElementById('fermePouletsModal').classList.add('flex');
            document.getElementById('fermePouletsModalTitle').textContent = 'Poulets de la Ferme';
            
            // Réinitialiser les tables
            document.getElementById('currentPouletsTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Chargement...</td></tr>';
            document.getElementById('historiquePouletsTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Chargement...</td></tr>';

            try {
                const response = await fetch(`/admin/entites/fermes/${fermeId}/poulets`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    // Poulets actuels
                    let currentHtml = '';
                    if (data.current && data.current.length > 0) {
                        data.current.forEach(stock => {
                            currentHtml += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.code_stock}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.poulet ? stock.poulet.nom : 'Non assigné'}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.quantite}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                            ${stock.statut === 'en_stock' ? 'bg-green-100 text-green-700' : 
                                            (stock.statut === 'vendu' ? 'bg-blue-100 text-blue-700' : 
                                            (stock.statut === 'mort' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'))}">
                                            ${stock.statut}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">${stock.date_entree || '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        currentHtml = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucun poulet actuellement dans cette ferme</td></tr>';
                    }
                    document.getElementById('currentPouletsTable').innerHTML = currentHtml;

                    // Historique
                    let historiqueHtml = '';
                    if (data.historique && data.historique.length > 0) {
                        data.historique.forEach(stock => {
                            historiqueHtml += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.code_stock}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.poulet ? stock.poulet.nom : 'Non assigné'}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.quantite}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                            ${stock.statut === 'en_stock' ? 'bg-green-100 text-green-700' : 
                                            (stock.statut === 'vendu' ? 'bg-blue-100 text-blue-700' : 
                                            (stock.statut === 'mort' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'))}">
                                            ${stock.statut}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">${stock.date_sortie || '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        historiqueHtml = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucun historique pour cette ferme</td></tr>';
                    }
                    document.getElementById('historiquePouletsTable').innerHTML = historiqueHtml;
                } else {
                    document.getElementById('currentPouletsTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Erreur lors du chargement</td></tr>';
                    document.getElementById('historiquePouletsTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Erreur lors du chargement</td></tr>';
                }
            } catch (error) {
                document.getElementById('currentPouletsTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Erreur: ' + error.message + '</td></tr>';
                document.getElementById('historiquePouletsTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Erreur: ' + error.message + '</td></tr>';
            }
        }

        function closeFermePouletsModal() {
            document.getElementById('fermePouletsModal').classList.add('hidden');
            document.getElementById('fermePouletsModal').classList.remove('flex');
        }

        async function viewMagasinStocks(magasinId) {
            document.getElementById('magasinStocksModal').classList.remove('hidden');
            document.getElementById('magasinStocksModal').classList.add('flex');
            document.getElementById('magasinStocksModalTitle').textContent = 'Stocks du Magasin';
            
            // Réinitialiser la table
            document.getElementById('magasinStocksTable').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Chargement...</td></tr>';

            try {
                const response = await fetch(`/admin/entites/magasins/${magasinId}/stocks`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    let stocksHtml = '';
                    if (data && data.length > 0) {
                        data.forEach(stock => {
                            const quantiteRestante = stock.quantite - stock.quantite_utiliser;
                            const isBelowThreshold = stock.seuil_alerte && quantiteRestante < stock.seuil_alerte;
                            
                            stocksHtml += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.code}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.nom}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.quantite}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">${stock.quantite_utiliser}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">${stock.unite}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                            ${isBelowThreshold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}">
                                            ${stock.seuil_alerte ? stock.seuil_alerte : '-'}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        stocksHtml = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun stock dans ce magasin</td></tr>';
                    }
                    document.getElementById('magasinStocksTable').innerHTML = stocksHtml;
                } else {
                    document.getElementById('magasinStocksTable').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Erreur lors du chargement</td></tr>';
                }
            } catch (error) {
                document.getElementById('magasinStocksTable').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Erreur: ' + error.message + '</td></tr>';
            }
        }

        function closeMagasinStocksModal() {
            document.getElementById('magasinStocksModal').classList.add('hidden');
            document.getElementById('magasinStocksModal').classList.remove('flex');
        }

        // Initialize maps on page load
        document.addEventListener('DOMContentLoaded', function() {
            initMaps();
        });
    </script>
</body>
</html>
