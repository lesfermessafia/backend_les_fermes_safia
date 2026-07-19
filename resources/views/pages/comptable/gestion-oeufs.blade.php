<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Oeufs - Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Oeufs - Comptable" color="green" />

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('comptable.dashboard') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium transition duration-200">
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

        <!-- Total -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <p class="text-sm text-[#305327] font-medium">Total Tablettes ({{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }} oeufs/tablette)</p>
            <p class="text-3xl font-bold text-[#305327]">{{ $totalQuantite }}</p>
            <p class="text-sm text-gray-500">≈ {{ number_format($totalQuantite * \App\Models\StockOeuf::OEUFS_PAR_TABLETTE, 0, ',', ' ') }} oeufs</p>
        </div>

        <!-- Stocks -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-[#305327]">Stocks d'oeufs</h2>
                <div class="flex gap-2">
                    <button onclick="openMouvementModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                        + Enregistrer un mouvement
                    </button>
                    <button onclick="openStockModal()" class="bg-[#305327] text-white px-4 py-2 rounded-lg hover:bg-[#008d36] transition duration-200">
                        + Nouveau Stock
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Ferme</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Tablettes</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Oeufs</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date d'entree</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_ferme }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($stock->quantite * \App\Models\StockOeuf::OEUFS_PAR_TABLETTE, 0, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->date_entree ? $stock->date_entree->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="openMouvementModal({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Enregistrer un mouvement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                                <button onclick="viewStockDetails({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327]" title="Voir l'historique">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Aucun stock d'oeufs disponible.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $stocks->links() }}
            </div>
        </div>

        <!-- Historique -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-2xl font-bold text-[#305327] mb-6">Historique des mouvements</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Tablettes</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Oeufs</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gerant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiques as $historique)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->stockOeuf->code_ferme ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $historique->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $historique->type === 'entree' ? 'Entree' : 'Sortie' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($historique->quantite * \App\Models\StockOeuf::OEUFS_PAR_TABLETTE, 0, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->date_mouvement }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->gerant ? $historique->gerant->prenom . ' ' . $historique->gerant->nom : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucun mouvement enregistre.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $historiques->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Stock -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Nouveau Stock d'oeufs</h3>
            <form method="POST" action="{{ route('comptable.oeufs.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                    <select name="code_ferme" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner une ferme...</option>
                        @foreach($fermes as $ferme)
                            <option value="{{ $ferme->nom }}">{{ $ferme->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantite (tablettes de {{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }} oeufs)</label>
                    <input type="number" id="stockQuantite" name="quantite" required min="0" oninput="updateOeufsEquivalent('stock')" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">≈ <span id="stockOeufsEquivalent">0</span> oeufs</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'entree</label>
                    <input type="date" name="date_entree" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327]">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Mouvement -->
    <div id="mouvementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Enregistrer un mouvement</h3>
            <form id="mouvementForm" method="POST" action="{{ route('comptable.oeufs.mouvement') }}">
                @csrf
                <div class="mb-4">
                    <label for="mouvementStockSelect" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <select id="mouvementStockSelect" name="stock_oeuf_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner un stock...</option>
                        @foreach($stocks as $s)
                            <option value="{{ $s->id }}">{{ $s->code_ferme }} - {{ $s->quantite }} tablettes</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="entree">Entree</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantite (tablettes)</label>
                    <input type="number" name="quantite" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date du mouvement</label>
                    <input type="date" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeMouvementModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327]">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Details -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Historique du stock</h3>
            <div id="detailsContent" class="space-y-2">
                <p class="text-gray-500">Chargement...</p>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeDetailsModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Fermer</button>
            </div>
        </div>
    </div>

    <script>
        const OEUFS_PAR_TABLETTE = {{ \App\Models\StockOeuf::OEUFS_PAR_TABLETTE }};

        function updateOeufsEquivalent(prefix) {
            const input = document.getElementById(prefix + 'Quantite');
            const output = document.getElementById(prefix + 'OeufsEquivalent');
            if (input && output) {
                output.textContent = (parseInt(input.value || 0) * OEUFS_PAR_TABLETTE).toLocaleString('fr-FR');
            }
        }

        function openStockModal() {
            document.getElementById('stockModal').classList.remove('hidden');
            document.getElementById('stockModal').classList.add('flex');
        }

        function closeStockModal() {
            document.getElementById('stockModal').classList.add('hidden');
            document.getElementById('stockModal').classList.remove('flex');
        }

        function openMouvementModal(stockId = null) {
            const modal = document.getElementById('mouvementModal');
            const select = document.getElementById('mouvementStockSelect');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (stockId) {
                select.value = stockId;
            } else {
                select.value = '';
            }
        }

        function closeMouvementModal() {
            document.getElementById('mouvementModal').classList.add('hidden');
            document.getElementById('mouvementModal').classList.remove('flex');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
            document.getElementById('detailsModal').classList.remove('flex');
        }

        async function viewStockDetails(stockId) {
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('detailsContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            content.innerHTML = '<p class="text-gray-500">Chargement...</p>';
            try {
                const response = await fetch(`/comptable/oeufs/stock/${stockId}/details`);
                const data = await response.json();
                if (data.error) {
                    content.innerHTML = `<p class="text-red-500">${data.error}</p>`;
                    return;
                }
                let html = `<p class="font-semibold">${data.stock.code_ferme} - ${data.stock.quantite} tablettes</p>`;
                if (data.historiques && data.historiques.length) {
                    html += `<table class="w-full mt-4"><thead><tr class="bg-gray-50"><th class="px-2 py-1 text-left text-sm">Type</th><th class="px-2 py-1 text-left text-sm">Tablettes</th><th class="px-2 py-1 text-left text-sm">Date</th></tr></thead><tbody>`;
                    data.historiques.forEach(h => {
                        const typeClass = h.type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        html += `<tr class="border-b"><td class="px-2 py-1 text-sm"><span class="px-2 py-1 rounded-full text-xs ${typeClass}">${h.type}</span></td><td class="px-2 py-1 text-sm">${h.quantite}</td><td class="px-2 py-1 text-sm">${h.date_mouvement}</td></tr>`;
                    });
                    html += '</tbody></table>';
                } else {
                    html += '<p class="text-gray-500 mt-2">Aucun mouvement.</p>';
                }
                content.innerHTML = html;
            } catch (e) {
                content.innerHTML = '<p class="text-red-500">Erreur de chargement.</p>';
            }
        }

        window.onclick = function(event) {
            if (event.target.id === 'stockModal') closeStockModal();
            if (event.target.id === 'mouvementModal') closeMouvementModal();
            if (event.target.id === 'detailsModal') closeDetailsModal();
        }
    </script>
</body>
</html>
