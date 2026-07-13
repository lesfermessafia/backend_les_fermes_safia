<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Poulets - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Poulets" color="blue" />

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
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Poulets</h2>
                <div class="flex gap-2">
                    <button onclick="deleteSelectedPoulets()" id="deleteSelectedBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 hidden">
                        Supprimer la sélection
                    </button>
                    <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouveau Poulet
                    </button>
                </div>
            </div>

            <!-- Filtre -->
            <div class="mb-4">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchPoulets" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchPoulets" value="{{ request('search') }}" placeholder="Code, nom ou race..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchPoulets()">
                    </div>
                    <button onclick="resetSearchPoulets()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]">
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Photo</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Race</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($poulets as $poulet)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <input type="checkbox" class="poulet-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="{{ $poulet->id }}" onchange="updateDeleteButton()">
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($poulet->photo)
                                    <img src="{{ url('img/' . $poulet->photo) }}" alt="{{ $poulet->nom }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $poulet->code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $poulet->nom }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $poulet->race }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editPoulet({{ $poulet->id }})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                                <button onclick="deletePoulet({{ $poulet->id }})" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucun poulet ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="pagination-poulets" class="mt-4">
                {{ $poulets->links() }}
            </div>

            <div id="stats-poulets" class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                    <p class="text-sm text-[#305327] font-medium">Total Poulets</p>
                    <p class="text-2xl font-bold text-[#305327]">{{ $totalPoulets }}</p>
                </div>
                @foreach ($statsByRace as $race => $count)
                <div class="bg-[#008d36]/10 rounded-lg p-4 border border-[#008d36]/20">
                    <p class="text-sm text-[#008d36] font-medium">Race : {{ $race }}</p>
                    <p class="text-2xl font-bold text-[#008d36]">{{ $count }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Section Stocks de Poulets -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-[#305327]">Stocks de Poulets</h2>
                <div class="flex gap-2">
                    <button onclick="deleteSelectedStocks()" id="deleteSelectedStocksBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 hidden">
                        Supprimer la sélection
                    </button>
                    <button onclick="openStockModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouveau Stock
                    </button>
                </div>
            </div>

            <!-- Filtres Stocks -->
            <form method="GET" action="{{ route('admin.poulets.index') }}" class="mb-4 filter-form-stocks">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="search_stocks" class="block text-sm font-medium text-gray-700 mb-1">Recherche Stocks</label>
                        <input type="text" id="search_stocks" name="search" value="{{ request('search') }}" placeholder="Code ou race..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div>
                        <label for="ferme_id" class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                        <select id="ferme_id" name="ferme_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="">Toutes les fermes</option>
                            @foreach($fermes as $ferme)
                                <option value="{{ $ferme->id }}" {{ request('ferme_id') == $ferme->id ? 'selected' : '' }}>{{ $ferme->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="">Tous les statuts</option>
                            <option value="en_stock" {{ request('statut') == 'en_stock' ? 'selected' : '' }}>En stock</option>
                            <option value="vendu" {{ request('statut') == 'vendu' ? 'selected' : '' }}>Vendu</option>
                            <option value="mort" {{ request('statut') == 'mort' ? 'selected' : '' }}>Mort</option>
                            <option value="en_production" {{ request('statut') == 'en_production' ? 'selected' : '' }}>En production</option>
                        </select>
                    </div>
                    <div>
                        <label for="poulet_id" class="block text-sm font-medium text-gray-700 mb-1">Poulet</label>
                        <select id="poulet_id" name="poulet_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="">Tous les poulets</option>
                            @foreach($pouletsStocks as $poulet)
                                <option value="{{ $poulet->id }}" {{ request('poulet_id') == $poulet->id ? 'selected' : '' }}>{{ $poulet->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">
                                <input type="checkbox" id="selectAllStocks" onchange="toggleSelectAllStocks()" class="w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]">
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Ferme</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Poulet</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Poids Moyen</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <input type="checkbox" class="stock-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="{{ $stock->id }}" onchange="updateDeleteStocksButton()">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_stock }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->ferme ? $stock->ferme->nom : 'Non assigné' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->poulet ? $stock->poulet->nom : 'Non assigné' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                    {{ $stock->statut === 'en_stock' ? 'bg-green-100 text-green-700' : 
                                    ($stock->statut === 'vendu' ? 'bg-blue-100 text-blue-700' : 
                                    ($stock->statut === 'mort' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ $stock->statut }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->poids_moyen ?? '-' }} kg</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editStock({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="openMouvementModal({{ $stock->id }})" class="text-blue-600 hover:text-blue-800 mr-2" title="Mouvement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                                <button onclick="viewHistorique({{ $stock->id }})" class="text-purple-600 hover:text-purple-800 mr-2" title="Historique">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteStock({{ $stock->id }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Aucun stock de poulet ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="pagination-stocks" class="mt-4">
                {{ $stocks->links() }}
            </div>

            <!-- Dashboard KPIs Stocks -->
            <div id="stats-stocks" class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                    <p class="text-sm text-[#305327] font-medium">Total Poulets</p>
                    <p class="text-2xl font-bold text-[#305327]" id="stats-total-stocks">{{ $totalQuantiteStocks }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-4 border border-green-200">
                    <p class="text-sm text-green-700 font-medium">En Stock</p>
                    <p class="text-2xl font-bold text-green-700" id="stats-en-stock">{{ $totalEnStock }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-4 border border-blue-200">
                    <p class="text-sm text-blue-700 font-medium">Vendus</p>
                    <p class="text-2xl font-bold text-blue-700" id="stats-vendus">{{ $totalVendus }}</p>
                </div>
                <div class="bg-red-100 rounded-lg p-4 border border-red-200">
                    <p class="text-sm text-red-700 font-medium">Morts</p>
                    <p class="text-2xl font-bold text-red-700" id="stats-morts">{{ $totalMorts }}</p>
                </div>
            </div>

            <!-- Graphiques Stocks -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-[#305327] mb-4">Répartition par Race</h3>
                    <canvas id="chartByRace"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-[#305327] mb-4">Répartition par Statut</h3>
                    <canvas id="chartByStatut"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="pouletModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouveau Poulet</h3>
            <form id="pouletForm" method="POST" action="{{ route('admin.poulets.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="_method" name="_method" value="">
                <input type="hidden" id="pouletId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <img id="pouletPhotoPreview" src="" alt="Photo actuelle" class="w-16 h-16 object-cover rounded mb-2 hidden">
                    <input type="file" id="pouletPhoto" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laissez vide pour conserver la photo actuelle.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="pouletNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Race</label>
                    <input type="text" id="pouletRace" name="race" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Stock -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 id="stockModalTitle" class="text-xl font-bold mb-4">Nouveau Stock</h3>
            <form id="stockForm" method="POST" action="{{ route('admin.poulets.stocks.store') }}">
                @csrf
                <input type="hidden" id="_method_stock" name="_method" value="">
                <input type="hidden" id="stockId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                    <select id="stock_ferme_id" name="ferme_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Non assigné</option>
                        @foreach($fermes as $ferme)
                            <option value="{{ $ferme->id }}">{{ $ferme->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poulet</label>
                    <select id="stock_poulet_id" name="poulet_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner un poulet</option>
                        @foreach($pouletsStocks as $poulet)
                            <option value="{{ $poulet->id }}">{{ $poulet->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" id="stock_quantite" name="quantite" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'entrée</label>
                    <input type="date" id="stock_date_entree" name="date_entree" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="stock_statut" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="en_stock">En stock</option>
                        <option value="vendu">Vendu</option>
                        <option value="mort">Mort</option>
                        <option value="en_production">En production</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poids moyen (kg)</label>
                    <input type="number" id="stock_poids_moyen" name="poids_moyen" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Âge (jours)</label>
                    <input type="number" id="stock_age_jours" name="age_jours" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="stock_notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Mouvement -->
    <div id="mouvementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Enregistrer un Mouvement</h3>
            <form id="mouvementForm" method="POST" action="{{ route('admin.poulets.stocks.mouvement') }}">
                @csrf
                <input type="hidden" id="mouvement_stock_id" name="stock_poulet_id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de mouvement</label>
                    <select id="type_mouvement" name="type_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" id="mouvement_quantite" name="quantite" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <select id="motif" name="motif" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner...</option>
                        <option value="Achat">Achat</option>
                        <option value="Naissance">Naissance</option>
                        <option value="Vente">Vente</option>
                        <option value="Mortalité">Mortalité</option>
                        <option value="Transfert">Transfert</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date du mouvement</label>
                    <input type="date" id="date_mouvement" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="mouvement_notes" name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeMouvementModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let poulets = @json($poulets->items());
        let stocks = @json($stocks->items());
        const statsByPouletStocks = @json($statsByPouletStocks);
        const statsByStatutStocks = @json($statsByStatutStocks);

        function openModal() {
            document.getElementById('pouletModal').classList.remove('hidden');
            document.getElementById('pouletModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouveau Poulet';
            document.getElementById('pouletForm').action = '{{ route('admin.poulets.store') }}';
            document.getElementById('_method').value = '';
            document.getElementById('pouletId').value = '';
            document.getElementById('pouletNom').value = '';
            document.getElementById('pouletRace').value = '';
            document.getElementById('pouletPhoto').value = '';
            document.getElementById('pouletPhotoPreview').classList.add('hidden');
            document.getElementById('pouletPhotoPreview').src = '';
        }

        function closeModal() {
            document.getElementById('pouletModal').classList.add('hidden');
            document.getElementById('pouletModal').classList.remove('flex');
        }

        function editPoulet(id) {
            const poulet = poulets.find(p => p.id === id);
            if (poulet) {
                document.getElementById('pouletModal').classList.remove('hidden');
                document.getElementById('pouletModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Poulet';
                document.getElementById('pouletForm').action = '{{ route('admin.poulets.update', ':id') }}'.replace(':id', id);
                document.getElementById('_method').value = 'PUT';
                document.getElementById('pouletId').value = poulet.id;
                document.getElementById('pouletNom').value = poulet.nom;
                document.getElementById('pouletRace').value = poulet.race;
                document.getElementById('pouletPhoto').value = '';

                const preview = document.getElementById('pouletPhotoPreview');
                if (poulet.photo) {
                    preview.src = '/img/' + poulet.photo;
                    preview.classList.remove('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            }
        }

        function deletePoulet(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce poulet ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.poulets.destroy', ':id') }}'.replace(':id', id);

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
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.poulet-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.poulet-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            if (checkboxes.length > 0) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.textContent = `Supprimer la sélection (${checkboxes.length})`;
            } else {
                deleteBtn.classList.add('hidden');
            }
        }

        function deleteSelectedPoulets() {
            const checkboxes = document.querySelectorAll('.poulet-checkbox:checked');
            if (checkboxes.length === 0) return;

            if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkboxes.length} poulet(s) ?`)) {
                const ids = Array.from(checkboxes).map(cb => cb.value);

                fetch('{{ route('admin.poulets.destroyMultiple') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    alert('Erreur lors de la suppression: ' + error.message);
                });
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

        function renderPoulets(items) {
            const tbody = document.querySelector('.overflow-x-auto tbody');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun poulet ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(p => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <input type="checkbox" class="poulet-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="${p.id}" onchange="updateDeleteButton()">
                    </td>
                    <td class="px-4 py-3 text-sm">${p.photo ? `<img src="/img/${escapeHtml(p.photo)}" alt="${escapeHtml(p.nom)}" class="w-12 h-12 object-cover rounded">` : '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(p.code)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(p.nom)}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(p.race)}</td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="editPoulet(${p.id})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                        <button onclick="deletePoulet(${p.id})" class="text-red-600 hover:text-red-800">Supprimer</button>
                    </td>
                </tr>
            `).join('');
            updateDeleteButton();
        }

        function renderPouletStats(total, byRace) {
            const container = document.getElementById('stats-poulets');
            if (!container) return;
            let html = `
                <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                    <p class="text-sm text-[#305327] font-medium">Total Poulets</p>
                    <p class="text-2xl font-bold text-[#305327]">${escapeHtml(String(total))}</p>
                </div>
            `;
            if (byRace) {
                Object.entries(byRace).forEach(([race, count]) => {
                    html += `
                        <div class="bg-[#008d36]/10 rounded-lg p-4 border border-[#008d36]/20">
                            <p class="text-sm text-[#008d36] font-medium">Race : ${escapeHtml(race)}</p>
                            <p class="text-2xl font-bold text-[#008d36]">${escapeHtml(String(count))}</p>
                        </div>
                    `;
                });
            }
            container.innerHTML = html;
        }

        async function loadPoulets(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                poulets = data.poulets;
                renderPoulets(poulets);
                const paginationContainer = document.getElementById('pagination-poulets');
                if (paginationContainer && data.pagination !== undefined) {
                    paginationContainer.innerHTML = data.pagination;
                }
                renderPouletStats(data.total !== undefined ? data.total : 0, data.byRace);
            } catch (error) {
                alert('Erreur lors du chargement des poulets: ' + error.message);
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

        // Recherche automatique pour les poulets
        const debouncedSearchPoulets = debounce(function() {
            const search = document.getElementById('searchPoulets').value;
            const url = new URL(window.location.href);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            history.pushState({}, '', url.toString());
            loadPoulets(url.toString());
        }, 500);

        function resetSearchPoulets() {
            document.getElementById('searchPoulets').value = '';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            history.pushState({}, '', url.toString());
            loadPoulets(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchPoulets = document.getElementById('searchPoulets');
            if (searchPoulets) {
                const params = new URLSearchParams(window.location.search);
                searchPoulets.value = params.get('search') || '';
            }
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const searchPoulets = document.getElementById('searchPoulets');
            if (searchPoulets) {
                searchPoulets.value = params.get('search') || '';
            }
            loadPoulets(window.location.href);
        });

        // Fonctions pour les stocks de poulets
        function openStockModal() {
            document.getElementById('stockModal').classList.remove('hidden');
            document.getElementById('stockModal').classList.add('flex');
            document.getElementById('stockModalTitle').textContent = 'Nouveau Stock';
            document.getElementById('stockForm').action = '{{ route('admin.poulets.stocks.store') }}';
            document.getElementById('_method_stock').value = '';
            document.getElementById('stockId').value = '';
            document.getElementById('stock_ferme_id').value = '';
            document.getElementById('stock_poulet_id').value = '';
            document.getElementById('stock_quantite').value = '';
            document.getElementById('stock_date_entree').value = '';
            document.getElementById('stock_statut').value = 'en_stock';
            document.getElementById('stock_poids_moyen').value = '';
            document.getElementById('stock_age_jours').value = '';
            document.getElementById('stock_notes').value = '';
        }

        function closeStockModal() {
            document.getElementById('stockModal').classList.add('hidden');
            document.getElementById('stockModal').classList.remove('flex');
        }

        function editStock(id) {
            const stock = stocks.find(s => s.id === id);
            if (stock) {
                document.getElementById('stockModal').classList.remove('hidden');
                document.getElementById('stockModal').classList.add('flex');
                document.getElementById('stockModalTitle').textContent = 'Modifier Stock';
                document.getElementById('stockForm').action = '{{ route('admin.poulets.stocks.update', ':id') }}'.replace(':id', id);
                document.getElementById('_method_stock').value = 'PUT';
                document.getElementById('stockId').value = stock.id;
                document.getElementById('stock_ferme_id').value = stock.ferme_id || '';
                document.getElementById('stock_poulet_id').value = stock.poulet_id || '';
                document.getElementById('stock_quantite').value = stock.quantite;
                document.getElementById('stock_date_entree').value = stock.date_entree || '';
                document.getElementById('stock_statut').value = stock.statut;
                document.getElementById('stock_poids_moyen').value = stock.poids_moyen || '';
                document.getElementById('stock_age_jours').value = stock.age_jours || '';
                document.getElementById('stock_notes').value = stock.notes || '';
            }
        }

        function deleteStock(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.poulets.stocks.destroy', ':id') }}'.replace(':id', id);
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
        }

        function toggleSelectAllStocks() {
            const selectAll = document.getElementById('selectAllStocks');
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDeleteStocksButton();
        }

        function updateDeleteStocksButton() {
            const checkboxes = document.querySelectorAll('.stock-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedStocksBtn');
            if (checkboxes.length > 0) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.textContent = `Supprimer la sélection (${checkboxes.length})`;
            } else {
                deleteBtn.classList.add('hidden');
            }
        }

        function deleteSelectedStocks() {
            const checkboxes = document.querySelectorAll('.stock-checkbox:checked');
            if (checkboxes.length === 0) return;

            if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkboxes.length} stock(s) ?`)) {
                const ids = Array.from(checkboxes).map(cb => cb.value);

                fetch('{{ route('admin.poulets.stocks.destroyMultiple') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    alert('Erreur lors de la suppression: ' + error.message);
                });
            }
        }

        function openMouvementModal(stockId) {
            document.getElementById('mouvementModal').classList.remove('hidden');
            document.getElementById('mouvementModal').classList.add('flex');
            document.getElementById('mouvement_stock_id').value = stockId;
            document.getElementById('type_mouvement').value = 'entree';
            document.getElementById('mouvement_quantite').value = '';
            document.getElementById('motif').value = '';
            document.getElementById('date_mouvement').value = '';
            document.getElementById('mouvement_notes').value = '';
        }

        function closeMouvementModal() {
            document.getElementById('mouvementModal').classList.add('hidden');
            document.getElementById('mouvementModal').classList.remove('flex');
        }

        function viewHistorique(id) {
            window.location.href = '{{ route('admin.poulets.stocks.historique', ':id') }}'.replace(':id', id);
        }

        function updateCharts(byPoulet, byStatut) {
            // Chart par poulet
            const ctxRace = document.getElementById('chartByRace').getContext('2d');
            if (window.chartRace) window.chartRace.destroy();
            window.chartRace = new Chart(ctxRace, {
                type: 'pie',
                data: {
                    labels: Object.keys(byPoulet),
                    datasets: [{
                        data: Object.values(byPoulet),
                        backgroundColor: ['#008d36', '#305327', '#4ade80', '#166534', '#22c55e'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });

            // Chart par statut
            const ctxStatut = document.getElementById('chartByStatut').getContext('2d');
            if (window.chartStatut) window.chartStatut.destroy();
            window.chartStatut = new Chart(ctxStatut, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(byStatut),
                    datasets: [{
                        data: Object.values(byStatut),
                        backgroundColor: ['#22c55e', '#3b82f6', '#ef4444', '#eab308'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Initialiser les graphiques au chargement
        document.addEventListener('DOMContentLoaded', function() {
            updateCharts(statsByPouletStocks, statsByStatutStocks);
        });
    </script>
</body>
</html>
