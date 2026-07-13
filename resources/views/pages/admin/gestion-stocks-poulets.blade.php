<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Stocks Poulets - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Stocks Poulets" color="blue" />

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
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Stocks de Poulets</h2>
                <div class="flex gap-2">
                    <button onclick="deleteSelectedStocks()" id="deleteSelectedBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 hidden">
                        Supprimer la sélection
                    </button>
                    <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Nouveau Stock
                    </button>
                </div>
            </div>

            <!-- Filtres -->
            <div class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="search" value="{{ request('search') }}" placeholder="Code ou race..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchStocks()">
                    </div>
                    <div>
                        <label for="ferme_id" class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                        <select id="ferme_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchStocks()">
                            <option value="">Toutes les fermes</option>
                            @foreach($fermes as $ferme)
                                <option value="{{ $ferme->id }}" {{ request('ferme_id') == $ferme->id ? 'selected' : '' }}>{{ $ferme->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select id="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchStocks()">
                            <option value="">Tous les statuts</option>
                            <option value="en_stock" {{ request('statut') == 'en_stock' ? 'selected' : '' }}>En stock</option>
                            <option value="vendu" {{ request('statut') == 'vendu' ? 'selected' : '' }}>Vendu</option>
                            <option value="mort" {{ request('statut') == 'mort' ? 'selected' : '' }}>Mort</option>
                            <option value="en_production" {{ request('statut') == 'en_production' ? 'selected' : '' }}>En production</option>
                        </select>
                    </div>
                    <div>
                        <label for="race" class="block text-sm font-medium text-gray-700 mb-1">Race</label>
                        <select id="race" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchStocks()">
                            <option value="">Toutes les races</option>
                            @foreach($races as $race)
                                <option value="{{ $race }}" {{ request('race') == $race ? 'selected' : '' }}>{{ $race }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button onclick="resetSearchStocks()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]">
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Ferme</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Race</th>
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
                                <input type="checkbox" class="stock-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="{{ $stock->id }}" onchange="updateDeleteButton()">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_stock }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->ferme ? $stock->ferme->nom : 'Non assigné' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->race }}</td>
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

            <!-- Dashboard KPIs -->
            <div id="stats-stocks" class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                    <p class="text-sm text-[#305327] font-medium">Total Poulets</p>
                    <p class="text-2xl font-bold text-[#305327]" id="stats-total">{{ $totalQuantite }}</p>
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

            <!-- Graphiques -->
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

    <!-- Modal Stock -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouveau Stock</h3>
            <form id="stockForm" method="POST" action="{{ route('admin.stocks-poulets.store') }}">
                @csrf
                <input type="hidden" id="_method" name="_method" value="">
                <input type="hidden" id="stockId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                    <select id="ferme_id" name="ferme_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Non assigné</option>
                        @foreach($fermes as $ferme)
                            <option value="{{ $ferme->id }}">{{ $ferme->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Race</label>
                    <input type="text" id="race" name="race" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" id="quantite" name="quantite" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'entrée</label>
                    <input type="date" id="date_entree" name="date_entree" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="statut" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="en_stock">En stock</option>
                        <option value="vendu">Vendu</option>
                        <option value="mort">Mort</option>
                        <option value="en_production">En production</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poids moyen (kg)</label>
                    <input type="number" id="poids_moyen" name="poids_moyen" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Âge (jours)</label>
                    <input type="number" id="age_jours" name="age_jours" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"></textarea>
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

    <!-- Modal Mouvement -->
    <div id="mouvementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Enregistrer un Mouvement</h3>
            <form id="mouvementForm" method="POST" action="{{ route('admin.stocks-poulets.mouvement') }}">
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
        let stocks = @json($stocks->items());
        const statsByRace = @json($statsByRace);
        const statsByStatut = @json($statsByStatut);

        function openModal() {
            document.getElementById('stockModal').classList.remove('hidden');
            document.getElementById('stockModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouveau Stock';
            document.getElementById('stockForm').action = '{{ route('admin.stocks-poulets.store') }}';
            document.getElementById('_method').value = '';
            document.getElementById('stockId').value = '';
            document.getElementById('ferme_id').value = '';
            document.getElementById('race').value = '';
            document.getElementById('quantite').value = '';
            document.getElementById('date_entree').value = '';
            document.getElementById('statut').value = 'en_stock';
            document.getElementById('poids_moyen').value = '';
            document.getElementById('age_jours').value = '';
            document.getElementById('notes').value = '';
        }

        function closeModal() {
            document.getElementById('stockModal').classList.add('hidden');
            document.getElementById('stockModal').classList.remove('flex');
        }

        function editStock(id) {
            const stock = stocks.find(s => s.id === id);
            if (stock) {
                document.getElementById('stockModal').classList.remove('hidden');
                document.getElementById('stockModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Stock';
                document.getElementById('stockForm').action = '{{ route('admin.stocks-poulets.update', ':id') }}'.replace(':id', id);
                document.getElementById('_method').value = 'PUT';
                document.getElementById('stockId').value = stock.id;
                document.getElementById('ferme_id').value = stock.ferme_id || '';
                document.getElementById('race').value = stock.race;
                document.getElementById('quantite').value = stock.quantite;
                document.getElementById('date_entree').value = stock.date_entree || '';
                document.getElementById('statut').value = stock.statut;
                document.getElementById('poids_moyen').value = stock.poids_moyen || '';
                document.getElementById('age_jours').value = stock.age_jours || '';
                document.getElementById('notes').value = stock.notes || '';
            }
        }

        function deleteStock(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.stocks-poulets.destroy', ':id') }}'.replace(':id', id);
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
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.stock-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
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

                fetch('{{ route('admin.stocks-poulets.destroyMultiple') }}', {
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
            window.location.href = '{{ route('admin.stocks-poulets.historique', ':id') }}'.replace(':id', id);
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderStocks(items) {
            const tbody = document.querySelector('.overflow-x-auto tbody');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Aucun stock de poulet ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(s => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <input type="checkbox" class="stock-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="${s.id}" onchange="updateDeleteButton()">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(s.code_stock)}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${s.ferme ? escapeHtml(s.ferme.nom) : 'Non assigné'}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(s.race)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${s.quantite}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            ${s.statut === 'en_stock' ? 'bg-green-100 text-green-700' : 
                            (s.statut === 'vendu' ? 'bg-blue-100 text-blue-700' : 
                            (s.statut === 'mort' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'))}">
                            ${escapeHtml(s.statut)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">${s.poids_moyen ? escapeHtml(s.poids_moyen) + ' kg' : '-'}</td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="editStock(${s.id})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="openMouvementModal(${s.id})" class="text-blue-600 hover:text-blue-800 mr-2" title="Mouvement">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </button>
                        <button onclick="viewHistorique(${s.id})" class="text-purple-600 hover:text-purple-800 mr-2" title="Historique">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                        <button onclick="deleteStock(${s.id})" class="text-red-600 hover:text-red-800" title="Supprimer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
            `).join('');
            updateDeleteButton();
        }

        function updateStats(stats) {
            document.getElementById('stats-total').textContent = stats.totalQuantite;
            document.getElementById('stats-en-stock').textContent = stats.enStock;
            document.getElementById('stats-vendus').textContent = stats.vendus;
            document.getElementById('stats-morts').textContent = stats.morts;
        }

        function updateCharts(byRace, byStatut) {
            // Chart par race
            const ctxRace = document.getElementById('chartByRace').getContext('2d');
            if (window.chartRace) window.chartRace.destroy();
            window.chartRace = new Chart(ctxRace, {
                type: 'pie',
                data: {
                    labels: Object.keys(byRace),
                    datasets: [{
                        data: Object.values(byRace),
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

        async function loadStocks(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                stocks = data.stocks;
                renderStocks(stocks);
                const paginationContainer = document.getElementById('pagination-stocks');
                if (paginationContainer && data.pagination !== undefined) {
                    paginationContainer.innerHTML = data.pagination;
                }
                if (data.stats) {
                    updateStats(data.stats);
                    updateCharts(data.stats.byRace, data.stats.byStatut);
                }
            } catch (error) {
                alert('Erreur lors du chargement des stocks: ' + error.message);
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

        // Recherche automatique pour les stocks poulets
        const debouncedSearchStocks = debounce(function() {
            const search = document.getElementById('search').value;
            const fermeId = document.getElementById('ferme_id').value;
            const statut = document.getElementById('statut').value;
            const race = document.getElementById('race').value;
            const url = new URL(window.location.href);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            if (fermeId) {
                url.searchParams.set('ferme_id', fermeId);
            } else {
                url.searchParams.delete('ferme_id');
            }
            if (statut) {
                url.searchParams.set('statut', statut);
            } else {
                url.searchParams.delete('statut');
            }
            if (race) {
                url.searchParams.set('race', race);
            } else {
                url.searchParams.delete('race');
            }
            history.pushState({}, '', url.toString());
            loadStocks(url.toString());
        }, 500);

        function resetSearchStocks() {
            document.getElementById('search').value = '';
            document.getElementById('ferme_id').value = '';
            document.getElementById('statut').value = '';
            document.getElementById('race').value = '';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('ferme_id');
            url.searchParams.delete('statut');
            url.searchParams.delete('race');
            history.pushState({}, '', url.toString());
            loadStocks(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('search');
            const fermeId = document.getElementById('ferme_id');
            const statut = document.getElementById('statut');
            const race = document.getElementById('race');
            
            if (search || fermeId || statut || race) {
                const params = new URLSearchParams(window.location.search);
                if (search) search.value = params.get('search') || '';
                if (fermeId) fermeId.value = params.get('ferme_id') || '';
                if (statut) statut.value = params.get('statut') || '';
                if (race) race.value = params.get('race') || '';
            }

            // Initialiser les graphiques
            updateCharts(statsByRace, statsByStatut);
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const search = document.getElementById('search');
            const fermeId = document.getElementById('ferme_id');
            const statut = document.getElementById('statut');
            const race = document.getElementById('race');
            
            if (search) search.value = params.get('search') || '';
            if (fermeId) fermeId.value = params.get('ferme_id') || '';
            if (statut) statut.value = params.get('statut') || '';
            if (race) race.value = params.get('race') || '';
            
            loadStocks(window.location.href);
        });
    </script>
</body>
</html>
