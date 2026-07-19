<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Aliments - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Aliments" color="blue" />

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
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Aliments</h2>
                <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Nouvel Aliment
                </button>
            </div>

            <!-- Filtre -->
            <div class="mb-4">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchAliments" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchAliments" value="{{ request('search') }}" placeholder="Code ou nom..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchAliments()">
                    </div>
                    <button onclick="resetSearchAliments()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Photo</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Unité</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aliments as $aliment)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                @if($aliment->photo)
                                    <img src="{{ url('img/' . $aliment->photo) }}" alt="{{ $aliment->nom }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $aliment->code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $aliment->nom }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $aliment->unite ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editAliment({{ $aliment->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteAliment({{ $aliment->id }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Aucun aliment ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="pagination-aliments" class="mt-4">
                {{ $aliments->links() }}
            </div>

            <div id="stats-aliments" class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                    <p class="text-sm text-[#305327] font-medium">Total Aliments</p>
                    <p class="text-2xl font-bold text-[#305327]">{{ $totalAliments }}</p>
                </div>
            </div>
        </div>

        <!-- Section Gestion des Stocks d'Aliments -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[#305327]">Stocks d'Aliments</h2>
                    <button onclick="openStockModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouveau Stock
                    </button>
                </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Aliment</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Formule</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité Fabriquée</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité Utilisée</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Disponible</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockAliments as $stock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_stock }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->aliment->nom }} ({{ $stock->aliment->code }})</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->formule->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite_fabriquer }} {{ $stock->aliment->unite ?? '' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite_utiliser }} {{ $stock->aliment->unite ?? '' }}</td>
                            <td class="px-4 py-3 text-sm font-medium {{ ($stock->quantite_fabriquer - $stock->quantite_utiliser) < 10 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $stock->quantite_fabriquer - $stock->quantite_utiliser }} {{ $stock->aliment->unite ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    {{ $stock->status === 'en attente' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($stock->status === 'en production' ? 'bg-blue-100 text-blue-800' : 
                                       ($stock->status === 'production terminer' ? 'bg-green-100 text-green-800' : 
                                       ($stock->status === 'annule' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ $stock->status ?? 'en attente' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editStock({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                @if($stock->status === 'production terminer')
                                <button onclick="openMouvementStockModal({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Mouvement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="text-gray-300 mr-2 cursor-not-allowed" title="Mouvement possible uniquement pour les stocks en 'production terminer'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </span>
                                @endif
                                <button onclick="viewStockDetails({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
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
                                Aucun stock d'aliment disponible.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>

            <!-- Section Stocks Terminés -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-[#305327] mb-4">Stocks Production Terminée</h2>
                @forelse ($stocksTermines as $stock)
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $stock->aliment->nom }} ({{ $stock->code_stock }})</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-2">
                            <span>Fabriqué: {{ $stock->quantite_fabriquer }} {{ $stock->aliment->unite ?? '' }}</span>
                            <span class="mx-2">|</span>
                            <span>Utilisé: {{ $stock->quantite_utiliser }} {{ $stock->aliment->unite ?? '' }}</span>
                            <span class="mx-2">|</span>
                            <span>Disponible: {{ $stock->quantite_fabriquer - $stock->quantite_utiliser }} {{ $stock->aliment->unite ?? '' }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-[#008d36] h-4 rounded-full transition-all duration-300" style="width: {{ ($stock->quantite_utiliser / $stock->quantite_fabriquer) * 100 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ round(($stock->quantite_utiliser / $stock->quantite_fabriquer) * 100, 1) }}% utilisé</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Aucun stock avec production terminée.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Stock Aliment -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="stockModalTitle" class="text-xl font-bold mb-4">Nouveau Stock d'Aliment</h3>
            <form id="stockForm" method="POST" action="{{ route('admin.aliments.stock.store') }}">
                @csrf
                <input type="hidden" id="stockId" name="id" value="">
                <input type="hidden" id="stockMethod" name="_method" value="">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aliment</label>
                    <select id="stockAliment" name="aliment_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner...</option>
                        @foreach($aliments->items() as $aliment)
                        <option value="{{ $aliment->id }}">{{ $aliment->nom }} ({{ $aliment->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Formule</label>
                    <select id="stockFormule" name="formule_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner...</option>
                        @if(isset($formules))
                        @foreach($formules as $formule)
                        <option value="{{ $formule->id }}">{{ $formule->nom }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité Fabriquée</label>
                    <input type="number" id="stockQuantite" name="quantite_fabriquer" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
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

    <!-- Modal Mouvement Stock Aliment -->
    <div id="mouvementStockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Mouvement de Stock</h3>
            <form id="mouvementStockForm" method="POST" action="{{ route('admin.aliments.stock.mouvement') }}">
                @csrf
                <input type="hidden" id="mouvementStockId" name="stock_aliment_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="mouvementType" name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" id="mouvementQuantite" name="quantite" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="mouvementDate" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeMouvementStockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Détails Stock Aliment -->
    <div id="stockDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Détails du Stock</h3>
                <button onclick="closeStockDetailsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="stockDetailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="alimentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouvel Aliment</h3>
            <form id="alimentForm" method="POST" action="{{ route('admin.aliments.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="_method" name="_method" value="">
                <input type="hidden" id="alimentId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <img id="alimentPhotoPreview" src="" alt="Photo actuelle" class="w-16 h-16 object-cover rounded mb-2 hidden">
                    <input type="file" id="alimentPhoto" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laissez vide pour conserver la photo actuelle.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="alimentNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                    <input type="text" id="alimentUnite" name="unite" placeholder="Ex: kg, litre, unité" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
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

    <script>
        let aliments = @json($aliments->items());

        function openModal() {
            document.getElementById('alimentModal').classList.remove('hidden');
            document.getElementById('alimentModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouvel Aliment';
            document.getElementById('alimentForm').action = '{{ route('admin.aliments.store') }}';
            document.getElementById('_method').value = '';
            document.getElementById('alimentId').value = '';
            document.getElementById('alimentNom').value = '';
            document.getElementById('alimentUnite').value = '';
            document.getElementById('alimentPhoto').value = '';
            document.getElementById('alimentPhotoPreview').classList.add('hidden');
            document.getElementById('alimentPhotoPreview').src = '';
        }

        function closeModal() {
            document.getElementById('alimentModal').classList.add('hidden');
            document.getElementById('alimentModal').classList.remove('flex');
        }

        function openStockModal() {
            document.getElementById('stockModal').classList.remove('hidden');
            document.getElementById('stockModal').classList.add('flex');
            document.getElementById('stockModalTitle').textContent = 'Nouveau Stock d\'Aliment';
            document.getElementById('stockForm').action = '{{ route('admin.aliments.stock.store') }}';
            document.getElementById('stockId').value = '';
            document.getElementById('stockMethod').value = '';
            document.getElementById('stockForm').reset();
        }

        function closeStockModal() {
            document.getElementById('stockModal').classList.add('hidden');
            document.getElementById('stockModal').classList.remove('flex');
            document.getElementById('stockForm').reset();
        }

        function editStock(stockId) {
            const stock = @json($stockAliments).find(s => s.id === stockId);
            if (stock) {
                document.getElementById('stockModal').classList.remove('hidden');
                document.getElementById('stockModal').classList.add('flex');
                document.getElementById('stockModalTitle').textContent = 'Modifier Stock d\'Aliment';
                document.getElementById('stockForm').action = '{{ route('admin.aliments.stock.update', ':id') }}'.replace(':id', stockId);
                document.getElementById('stockId').value = stockId;
                document.getElementById('stockMethod').value = 'PUT';
                document.getElementById('stockAliment').value = stock.aliment_id;
                document.getElementById('stockFormule').value = stock.formule_id;
                document.getElementById('stockQuantite').value = stock.quantite_fabriquer;
            }
        }

        function openMouvementStockModal(stockId) {
            document.getElementById('mouvementStockModal').classList.remove('hidden');
            document.getElementById('mouvementStockModal').classList.add('flex');
            document.getElementById('mouvementStockId').value = stockId;
            document.getElementById('mouvementStockForm').reset();
            document.getElementById('mouvementDate').value = new Date().toISOString().split('T')[0];
        }

        function closeMouvementStockModal() {
            document.getElementById('mouvementStockModal').classList.add('hidden');
            document.getElementById('mouvementStockModal').classList.remove('flex');
            document.getElementById('mouvementStockForm').reset();
        }

        function viewStockDetails(stockId) {
            fetch(`/admin/aliments/stock/${stockId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    const stock = data.stock;
                    const composants = data.composants;
                    const historiques = data.historiques;

                    let html = `
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold mb-3 text-[#305327]">Informations du Stock</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Code Stock</p>
                                    <p class="font-medium">${stock.code_stock}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Aliment</p>
                                    <p class="font-medium">${stock.aliment.nom} (${stock.aliment.code})</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Formule</p>
                                    <p class="font-medium">${stock.formule ? stock.formule.nom : '-'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p class="font-medium">${stock.status}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantité Fabriquée</p>
                                    <p class="font-medium">${stock.quantite_fabriquer} ${stock.aliment.unite || ''}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantité Utilisée</p>
                                    <p class="font-medium">${stock.quantite_utiliser} ${stock.aliment.unite || ''}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Disponible</p>
                                    <p class="font-medium ${stock.quantite_fabriquer - stock.quantite_utiliser < 10 ? 'text-red-600' : 'text-green-600'}">${stock.quantite_fabriquer - stock.quantite_utiliser} ${stock.aliment.unite || ''}</p>
                                </div>
                            </div>
                        </div>
                    `;

                    if (composants && composants.length > 0) {
                        html += `
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold mb-3 text-[#305327]">Détails de la Formule</h4>
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <p class="text-sm text-gray-600">Nom: <span class="font-medium text-gray-900">${stock.formule.nom}</span></p>
                                </div>
                                <h5 class="text-md font-semibold mb-2 text-[#305327]">Composants</h5>
                                <ul class="list-disc list-inside space-y-1">
                                    ${composants.map(c => `
                                        <li class="text-sm text-gray-700">
                                            <span class="font-medium">${c.nom}</span> : ${c.pourcentage}% 
                                            <span class="text-gray-500">(Quantité calculée: ${c.quantite_calculee})</span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        `;
                    }

                    if (historiques && historiques.length > 0) {
                        html += `
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold mb-3 text-[#305327]">Historique des Mouvements</h4>
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-[#305327]">Type</th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-[#305327]">Quantité</th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-[#305327]">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${historiques.map(h => `
                                            <tr class="border-b">
                                                <td class="px-4 py-2 text-sm">
                                                    <span class="px-2 py-1 rounded-full text-xs ${h.type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                                        ${h.type === 'entree' ? 'Entrée' : 'Sortie'}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-sm font-medium">${h.quantite}</td>
                                                <td class="px-4 py-2 text-sm">${h.date_mouvement}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold mb-3 text-[#305327]">Historique des Mouvements</h4>
                                <p class="text-sm text-gray-500">Aucun mouvement enregistré.</p>
                            </div>
                        `;
                    }

                    document.getElementById('stockDetailsContent').innerHTML = html;
                    document.getElementById('stockDetailsModal').classList.remove('hidden');
                    document.getElementById('stockDetailsModal').classList.add('flex');
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails du stock');
                });
        }

        function closeStockDetailsModal() {
            document.getElementById('stockDetailsModal').classList.add('hidden');
            document.getElementById('stockDetailsModal').classList.remove('flex');
        }

        function deleteStock(stockId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/aliments/stock/' + stockId;

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

        function editAliment(id) {
            const aliment = aliments.find(a => a.id === id);
            if (aliment) {
                document.getElementById('alimentModal').classList.remove('hidden');
                document.getElementById('alimentModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Aliment';
                document.getElementById('alimentForm').action = '{{ route('admin.aliments.update', ':id') }}'.replace(':id', id);
                document.getElementById('_method').value = 'PUT';
                document.getElementById('alimentId').value = aliment.id;
                document.getElementById('alimentNom').value = aliment.nom;
                document.getElementById('alimentUnite').value = aliment.unite || '';
                document.getElementById('alimentPhoto').value = '';

                const preview = document.getElementById('alimentPhotoPreview');
                if (aliment.photo) {
                    preview.src = '/img/' + aliment.photo;
                    preview.classList.remove('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            }
        }

        function deleteAliment(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet aliment ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.aliments.destroy', ':id') }}'.replace(':id', id);

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

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderAliments(items) {
            const tbody = document.querySelector('.overflow-x-auto tbody');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Aucun aliment ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(a => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">${a.photo ? `<img src="/img/${escapeHtml(a.photo)}" alt="${escapeHtml(a.nom)}" class="w-12 h-12 object-cover rounded">` : '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(a.code)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(a.nom)}</td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="editAliment(${a.id})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                        <button onclick="deleteAliment(${a.id})" class="text-red-600 hover:text-red-800">Supprimer</button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadAliments(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                aliments = data.aliments;
                renderAliments(aliments);
                const paginationContainer = document.getElementById('pagination-aliments');
                if (paginationContainer && data.pagination !== undefined) {
                    paginationContainer.innerHTML = data.pagination;
                }
                const statsTotal = document.querySelector('#stats-aliments p.text-2xl');
                if (statsTotal && data.total !== undefined) {
                    statsTotal.textContent = data.total;
                }
            } catch (error) {
                alert('Erreur lors du chargement des aliments: ' + error.message);
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

        // Recherche automatique pour les aliments
        const debouncedSearchAliments = debounce(function() {
            const search = document.getElementById('searchAliments').value;
            const url = new URL(window.location.href);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            history.pushState({}, '', url.toString());
            loadAliments(url.toString());
        }, 500);

        function resetSearchAliments() {
            document.getElementById('searchAliments').value = '';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            history.pushState({}, '', url.toString());
            loadAliments(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchAliments = document.getElementById('searchAliments');
            if (searchAliments) {
                const params = new URLSearchParams(window.location.search);
                searchAliments.value = params.get('search') || '';
            }
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const searchAliments = document.getElementById('searchAliments');
            if (searchAliments) {
                searchAliments.value = params.get('search') || '';
            }
            loadAliments(window.location.href);
        });
    </script>
</body>
</html>
