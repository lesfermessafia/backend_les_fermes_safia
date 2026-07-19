<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Poulets - Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Poulets - Comptable" color="green" />

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
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-[#008d36]">
                <p class="text-xs text-gray-500">En stock</p>
                <p class="text-2xl font-bold text-[#305327]">{{ $totalEnStock }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-500">Vendus</p>
                <p class="text-2xl font-bold text-[#305327]">{{ $totalVendus }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-orange-500">
                <p class="text-xs text-gray-500">Réformés</p>
                <p class="text-2xl font-bold text-[#305327]">{{ $totalReforme }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-500">Non vendus</p>
                <p class="text-2xl font-bold text-[#305327]">{{ $totalNonVendu }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-[#305327]">Stocks de poulets</h2>
                <button onclick="openStockModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition">+ Nouveau stock</button>
            </div>

            <form method="GET" action="{{ route('comptable.poulets.index') }}" class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, race, fournisseur..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                        <select name="ferme_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="">Toutes</option>
                            @foreach($fermes as $f)
                                <option value="{{ $f->id }}" {{ request('ferme_id') == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="">Tous</option>
                            <option value="demarrage" {{ request('statut') == 'demarrage' ? 'selected' : '' }}>demarrage</option>
                            <option value="croissant" {{ request('statut') == 'croissant' ? 'selected' : '' }}>croissant</option>
                            <option value="finition" {{ request('statut') == 'finition' ? 'selected' : '' }}>finition</option>
                            <option value="Démarrage" {{ request('statut') == 'Démarrage' ? 'selected' : '' }}>Démarrage</option>
                            <option value="Pré-Ponte" {{ request('statut') == 'Pré-Ponte' ? 'selected' : '' }}>Pré-Ponte</option>
                            <option value="Ponte Régulière" {{ request('statut') == 'Ponte Régulière' ? 'selected' : '' }}>Ponte Régulière</option>
                            <option value="vendu" {{ request('statut') == 'vendu' ? 'selected' : '' }}>vendu</option>
                            <option value="Réforme" {{ request('statut') == 'Réforme' ? 'selected' : '' }}>Réforme</option>
                            <option value="non vendu" {{ request('statut') == 'non vendu' ? 'selected' : '' }}>non vendu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Race</label>
                        <select name="race" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="">Toutes</option>
                            @foreach($races as $r)
                                <option value="{{ $r }}" {{ request('race') == $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327]">Filtrer</button>
                        <a href="{{ route('comptable.poulets.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Réinitialiser</a>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Ferme</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Poulet</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Race</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Fournisseur</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantite</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Poids</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Age (j)</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_stock }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->ferme->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->poulet->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->poulet->race ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->fournisseur ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ in_array($stock->statut, ['vendu','Réforme','non vendu']) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $stock->statut }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->poids_moyen ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->age_actuel ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="openMouvementModal({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Mouvement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                                <button onclick="openStatusModal({{ $stock->id }}, '{{ $stock->poulet->type ?? '' }}')" class="text-blue-600 hover:text-blue-800" title="Statut">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">Aucun stock de poulets.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $stocks->links() }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-2xl font-bold text-[#305327] mb-6">Historique des mouvements</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantite</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Motif</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiques as $h)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->date_mouvement }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->stockPoulet->code_stock ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $h->type_mouvement === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $h->type_mouvement }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->motif }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $h->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun mouvement enregistre.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $historiques->links() }}</div>
        </div>
    </div>

    <!-- Modal Nouveau Stock -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Nouveau stock de poulets</h3>
            <form method="POST" action="{{ route('comptable.poulets.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ferme</label>
                    <select name="ferme_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner...</option>
                        @foreach($fermes as $f)
                            <option value="{{ $f->id }}">{{ $f->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poulet</label>
                    <select id="stockPouletSelect" name="poulet_id" required onchange="updateStatusOptions(this)" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner...</option>
                        @foreach($poulets as $p)
                            <option value="{{ $p->id }}" data-type="{{ $p->type }}">{{ $p->nom }} ({{ $p->race }} - {{ $p->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantite</label>
                    <input type="number" name="quantite" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'entree</label>
                    <input type="date" name="date_entree" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="stockStatusSelect" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner...</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poids moyen (kg)</label>
                    <input type="number" step="0.01" name="poids_moyen" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Age (jours)</label>
                    <input type="number" name="age_jours" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                    <input type="text" name="fournisseur" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"></textarea>
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
            <form method="POST" action="{{ route('comptable.poulets.mouvement') }}">
                @csrf
                <input type="hidden" id="mvtStockId" name="stock_poulet_id" value="">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="entree">Entree</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantite</label>
                    <input type="number" name="quantite" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <select name="motif" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="Entree">Entree</option>
                        <option value="Vente">Vente</option>
                        <option value="Mortalité">Mortalité</option>
                        <option value="Transfert">Transfert</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeMouvementModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327]">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Statut -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Modifier le statut</h3>
            <form id="statusForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="statusSelect" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner...</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const chairStatuses = ['demarrage', 'croissant', 'finition', 'vendu', 'non vendu'];
        const pondeuseStatuses = ['Démarrage', 'Pré-Ponte', 'Ponte Régulière', 'Réforme', 'non vendu'];

        function toggleModal(id, show) {
            const el = document.getElementById(id);
            if (show) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            } else {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }
        }
        function openStockModal() { toggleModal('stockModal', true); }
        function closeStockModal() { toggleModal('stockModal', false); }
        function openMouvementModal(stockId) {
            toggleModal('mouvementModal', true);
            document.getElementById('mvtStockId').value = stockId;
        }
        function closeMouvementModal() { toggleModal('mouvementModal', false); }
        function openStatusModal(stockId, type) {
            toggleModal('statusModal', true);
            document.getElementById('statusForm').action = '/comptable/poulets/' + stockId + '/status';
            const select = document.getElementById('statusSelect');
            select.innerHTML = '<option value="">Selectionner...</option>';
            const statuses = type === 'pondeuse' ? pondeuseStatuses : chairStatuses;
            statuses.forEach(function(s) {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                select.appendChild(opt);
            });
        }
        function closeStatusModal() { toggleModal('statusModal', false); }
        function updateStatusOptions(select) {
            const option = select.options[select.selectedIndex];
            const type = option ? option.getAttribute('data-type') : 'chair';
            const statusSelect = document.getElementById('stockStatusSelect');
            statusSelect.innerHTML = '<option value="">Selectionner...</option>';
            const statuses = type === 'pondeuse' ? pondeuseStatuses : chairStatuses;
            statuses.forEach(function(s) {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                statusSelect.appendChild(opt);
            });
        }
        window.onclick = function(event) {
            if (event.target.id === 'stockModal') closeStockModal();
            if (event.target.id === 'mouvementModal') closeMouvementModal();
            if (event.target.id === 'statusModal') closeStatusModal();
        }
    </script>
</body>
</html>
