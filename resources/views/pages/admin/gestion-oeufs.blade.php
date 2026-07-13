<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Œufs - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Œufs" color="blue" />

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
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Stocks d'Œufs</h2>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche (Ferme)</label>
                        <input type="text" id="search" value="{{ request('search') }}" placeholder="Nom de la ferme..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchStocks()">
                    </div>
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date d'entrée (à partir de)</label>
                        <input type="date" id="date_debut" value="{{ request('date_debut') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchStocks()">
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date d'entrée (jusqu'à)</label>
                        <input type="date" id="date_fin" value="{{ request('date_fin') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="debouncedSearchStocks()">
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
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Ferme</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Tablettes</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Œufs</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date d'entrée</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <input type="checkbox" class="stock-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="{{ $stock->id }}" onchange="updateDeleteButton()">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_ferme }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($stock->quantite_oeufs, 0, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->date_entree ? $stock->date_entree->format('d/m/Y') : '-' }}</td>
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
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucun stock d'œufs ne correspond aux critères sélectionnés.
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
                    <p class="text-sm text-[#305327] font-medium">Total Tablettes ({{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }} œufs/tablette)</p>
                    <p class="text-2xl font-bold text-[#305327]" id="stats-total">{{ $totalQuantite }}</p>
                    <p class="text-xs text-gray-500">≈ <span id="stats-total-oeufs">{{ number_format($totalOeufs, 0, ',', ' ') }}</span> œufs</p>
                </div>
                <div class="bg-green-100 rounded-lg p-4 border border-green-200">
                    <p class="text-sm text-green-700 font-medium">Entrées (total)</p>
                    <p class="text-2xl font-bold text-green-700" id="stats-entree">{{ $statsByType['entree'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-4 border border-blue-200">
                    <p class="text-sm text-blue-700 font-medium">Ventes (total)</p>
                    <p class="text-2xl font-bold text-blue-700" id="stats-vente">{{ $statsByType['vente'] ?? 0 }}</p>
                </div>
                <div class="bg-red-100 rounded-lg p-4 border border-red-200">
                    <p class="text-sm text-red-700 font-medium">Casses (total)</p>
                    <p class="text-2xl font-bold text-red-700" id="stats-casse">{{ $statsByType['casse'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-[#305327] mb-4">Répartition par Ferme</h3>
                    <canvas id="chartByFerme"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-[#305327] mb-4">Mouvements par Type</h3>
                    <canvas id="chartByType"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Stock -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouveau Stock</h3>
            <form id="stockForm" method="POST" action="{{ route('admin.oeufs.store') }}">
                @csrf
                <input type="hidden" id="_method" name="_method" value="">
                <input type="hidden" id="stockId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                    <select id="code_ferme" name="code_ferme" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Sélectionner une ferme...</option>
                        @foreach($fermes as $ferme)
                            <option value="{{ $ferme->nom }}">{{ $ferme->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité (en tablettes de {{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }} œufs)</label>
                    <input type="number" id="quantite" name="quantite" required min="0" oninput="updateOeufsEquivalent()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">≈ <span id="oeufs-equivalent">0</span> œufs</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'entrée</label>
                    <input type="date" id="date_entree" name="date_entree" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
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
            <form id="mouvementForm" method="POST" action="{{ route('admin.oeufs.mouvement') }}">
                @csrf
                <input type="hidden" id="mouvement_stock_id" name="stock_oeuf_id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de mouvement</label>
                    <select id="type_mouvement" name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                        <option value="vente">Vente</option>
                        <option value="casse">Casse</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité (en tablettes de {{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }} œufs)</label>
                    <input type="number" id="mouvement_quantite" name="quantite" required min="1" oninput="updateMouvementOeufsEquivalent()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">≈ <span id="mouvement-oeufs-equivalent">0</span> œufs</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date du mouvement</label>
                    <input type="date" id="date_mouvement" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
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
        const statsByFerme = @json($statsByFerme);
        const statsByType = @json($statsByType);
        const OEUFS_PAR_TABLETTE = {{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }};

        function updateOeufsEquivalent() {
            const quantite = parseInt(document.getElementById('quantite').value) || 0;
            document.getElementById('oeufs-equivalent').textContent = (quantite * OEUFS_PAR_TABLETTE).toLocaleString('fr-FR');
        }

        function updateMouvementOeufsEquivalent() {
            const quantite = parseInt(document.getElementById('mouvement_quantite').value) || 0;
            document.getElementById('mouvement-oeufs-equivalent').textContent = (quantite * OEUFS_PAR_TABLETTE).toLocaleString('fr-FR');
        }

        function openModal() {
            document.getElementById('stockModal').classList.remove('hidden');
            document.getElementById('stockModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouveau Stock';
            document.getElementById('stockForm').action = '{{ route('admin.oeufs.store') }}';
            document.getElementById('_method').value = '';
            document.getElementById('stockId').value = '';
            document.getElementById('code_ferme').value = '';
            document.getElementById('quantite').value = '';
            document.getElementById('date_entree').value = '';
            updateOeufsEquivalent();
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
                document.getElementById('stockForm').action = '{{ route('admin.oeufs.update', ':id') }}'.replace(':id', id);
                document.getElementById('_method').value = 'PUT';
                document.getElementById('stockId').value = stock.id;
                document.getElementById('code_ferme').value = stock.code_ferme;
                document.getElementById('quantite').value = stock.quantite;
                document.getElementById('date_entree').value = stock.date_entree ? stock.date_entree.split('T')[0] : '';
                updateOeufsEquivalent();
            }
        }

        function deleteStock(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.oeufs.destroy', ':id') }}'.replace(':id', id);
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

                fetch('{{ route('admin.oeufs.destroyMultiple') }}', {
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
            document.getElementById('date_mouvement').value = '';
            updateMouvementOeufsEquivalent();
        }

        function closeMouvementModal() {
            document.getElementById('mouvementModal').classList.add('hidden');
            document.getElementById('mouvementModal').classList.remove('flex');
        }

        function viewHistorique(id) {
            window.location.href = '{{ route('admin.oeufs.historique', ':id') }}'.replace(':id', id);
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function formatDateDisplay(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return d.toLocaleDateString('fr-FR');
        }

        function renderStocks(items) {
            const tbody = document.querySelector('.overflow-x-auto tbody');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun stock d\'œufs ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(s => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <input type="checkbox" class="stock-checkbox w-4 h-4 text-[#008d36] rounded focus:ring-[#008d36]" value="${s.id}" onchange="updateDeleteButton()">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(s.code_ferme)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${s.quantite}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${(s.quantite * OEUFS_PAR_TABLETTE).toLocaleString('fr-FR')}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${formatDateDisplay(s.date_entree)}</td>
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
            document.getElementById('stats-total-oeufs').textContent = (stats.totalOeufs ?? (stats.totalQuantite * OEUFS_PAR_TABLETTE)).toLocaleString('fr-FR');
            document.getElementById('stats-entree').textContent = (stats.byType && stats.byType.entree) || 0;
            document.getElementById('stats-vente').textContent = (stats.byType && stats.byType.vente) || 0;
            document.getElementById('stats-casse').textContent = (stats.byType && stats.byType.casse) || 0;
        }

        function updateCharts(byFerme, byType) {
            const ctxFerme = document.getElementById('chartByFerme').getContext('2d');
            if (window.chartFerme) window.chartFerme.destroy();
            window.chartFerme = new Chart(ctxFerme, {
                type: 'pie',
                data: {
                    labels: Object.keys(byFerme),
                    datasets: [{
                        data: Object.values(byFerme),
                        backgroundColor: ['#008d36', '#305327', '#4ade80', '#166534', '#22c55e', '#84cc16'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            const ctxType = document.getElementById('chartByType').getContext('2d');
            if (window.chartType) window.chartType.destroy();
            window.chartType = new Chart(ctxType, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(byType),
                    datasets: [{
                        data: Object.values(byType),
                        backgroundColor: ['#22c55e', '#3b82f6', '#eab308', '#ef4444'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
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
                    updateCharts(data.stats.byFerme, data.stats.byType);
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

        // Recherche automatique pour les stocks d'œufs
        const debouncedSearchStocks = debounce(function() {
            const search = document.getElementById('search').value;
            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = document.getElementById('date_fin').value;
            const url = new URL(window.location.href);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            if (dateDebut) {
                url.searchParams.set('date_debut', dateDebut);
            } else {
                url.searchParams.delete('date_debut');
            }
            if (dateFin) {
                url.searchParams.set('date_fin', dateFin);
            } else {
                url.searchParams.delete('date_fin');
            }
            history.pushState({}, '', url.toString());
            loadStocks(url.toString());
        }, 500);

        function resetSearchStocks() {
            document.getElementById('search').value = '';
            document.getElementById('date_debut').value = '';
            document.getElementById('date_fin').value = '';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('date_debut');
            url.searchParams.delete('date_fin');
            history.pushState({}, '', url.toString());
            loadStocks(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('search');
            const dateDebut = document.getElementById('date_debut');
            const dateFin = document.getElementById('date_fin');

            if (search || dateDebut || dateFin) {
                const params = new URLSearchParams(window.location.search);
                if (search) search.value = params.get('search') || '';
                if (dateDebut) dateDebut.value = params.get('date_debut') || '';
                if (dateFin) dateFin.value = params.get('date_fin') || '';
            }

            // Initialiser les graphiques
            updateCharts(statsByFerme, statsByType);
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const search = document.getElementById('search');
            const dateDebut = document.getElementById('date_debut');
            const dateFin = document.getElementById('date_fin');

            if (search) search.value = params.get('search') || '';
            if (dateDebut) dateDebut.value = params.get('date_debut') || '';
            if (dateFin) dateFin.value = params.get('date_fin') || '';

            loadStocks(window.location.href);
        });
    </script>
</body>
</html>
