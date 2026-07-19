<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Aliments - Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Aliments - Comptable" color="green" />

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

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-[#305327]">Stocks d'aliments</h2>
                <button onclick="openMouvementModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Enregistrer un mouvement
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Aliment</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Formule</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Fabrique</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Utilise</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Disponible</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th>
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
                                @php
                                    $disponible = $stock->quantite_fabriquer - $stock->quantite_utiliser;
                                    $statusColor = match($stock->status) {
                                        'en attente' => 'bg-yellow-100 text-yellow-800',
                                        'en production' => 'bg-blue-100 text-blue-800',
                                        'production terminer' => 'bg-green-100 text-green-800',
                                        'annule' => 'bg-red-100 text-red-800',
                                        'consommer' => 'bg-orange-100 text-orange-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-1 rounded-full text-xs w-fit {{ $statusColor }}">
                                        {{ $stock->status ?? 'en attente' }}
                                    </span>
                                    @if($stock->status === 'en attente')
                                        <div class="flex flex-wrap gap-1">
                                            <form method="POST" action="{{ route('comptable.aliments.stock.status', $stock->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="en production">
                                                <button type="submit" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition">Production</button>
                                            </form>
                                            <form method="POST" action="{{ route('comptable.aliments.stock.status', $stock->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="annule">
                                                <button type="submit" class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition">Annuler</button>
                                            </form>
                                        </div>
                                    @elseif($stock->status === 'en production')
                                        <form method="POST" action="{{ route('comptable.aliments.stock.status', $stock->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="production terminer">
                                            <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Terminer</button>
                                        </form>
                                    @elseif($stock->status === 'production terminer' && $disponible <= 0)
                                        <form method="POST" action="{{ route('comptable.aliments.stock.status', $stock->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="consommer">
                                            <button type="submit" class="text-xs bg-orange-600 text-white px-2 py-1 rounded hover:bg-orange-700 transition">Consommer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($stock->status === 'production terminer')
                                    <button onclick="openMouvementModal({{ $stock->id }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Enregistrer un mouvement">
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
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Aucun stock d'aliment disponible.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Historique des mouvements -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-2xl font-bold text-[#305327] mb-6">Historique des mouvements</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Aliment</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantite</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gérant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiques as $historique)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->stockAliment->code_stock ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->stockAliment->aliment->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $historique->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $historique->type === 'entree' ? 'Entree' : 'Sortie' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->date_mouvement }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->gerant ? $historique->gerant->prenom . ' ' . $historique->gerant->nom : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucun mouvement enregistré.
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

    <!-- Modal Mouvement -->
    <div id="mouvementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Enregistrer un mouvement</h3>
            <form id="mouvementForm" method="POST" action="{{ route('comptable.aliments.mouvement') }}">
                @csrf
                <div class="mb-4">
                    <label for="mouvementStockSelect" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <select id="mouvementStockSelect" name="stock_aliment_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner un stock...</option>
                        @foreach($stockAliments as $s)
                            @if($s->status === 'production terminer')
                                <option value="{{ $s->id }}">{{ $s->code_stock }} - {{ $s->aliment->nom }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="mouvementType" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="mouvementType" name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="entree">Entree</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="mouvementQuantite" class="block text-sm font-medium text-gray-700 mb-1">Quantite</label>
                    <input type="number" id="mouvementQuantite" name="quantite" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label for="mouvementDate" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="mouvementDate" name="date_mouvement" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
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

    <!-- Modal Details -->
    <div id="stockDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Details du stock</h3>
                <button onclick="closeStockDetailsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="stockDetailsContent">
                <p class="text-gray-500">Chargement...</p>
            </div>
        </div>
    </div>

    <script>
        function openMouvementModal(stockId = null) {
            document.getElementById('mouvementModal').classList.remove('hidden');
            document.getElementById('mouvementModal').classList.add('flex');
            document.getElementById('mouvementForm').reset();
            document.getElementById('mouvementDate').value = new Date().toISOString().split('T')[0];

            const select = document.getElementById('mouvementStockSelect');
            if (stockId) {
                select.value = stockId;
            } else {
                select.value = '';
            }
        }

        function closeMouvementModal() {
            document.getElementById('mouvementModal').classList.add('hidden');
            document.getElementById('mouvementModal').classList.remove('flex');
            document.getElementById('mouvementForm').reset();
        }

        function closeStockDetailsModal() {
            document.getElementById('stockDetailsModal').classList.add('hidden');
            document.getElementById('stockDetailsModal').classList.remove('flex');
        }

        function viewStockDetails(stockId) {
            const url = '{{ route('comptable.aliments.stock.details', ':id') }}'.replace(':id', stockId);
            fetch(url)
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
                                    <p class="text-sm text-gray-600">Statut</p>
                                    <p class="font-medium">${stock.status}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantite Fabriquee</p>
                                    <p class="font-medium">${stock.quantite_fabriquer} ${stock.aliment.unite || ''}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantite Utilisee</p>
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
                                <h4 class="text-lg font-semibold mb-3 text-[#305327]">Details de la Formule</h4>
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <p class="text-sm text-gray-600">Nom: <span class="font-medium text-gray-900">${stock.formule.nom}</span></p>
                                </div>
                                <h5 class="text-md font-semibold mb-2 text-[#305327]">Composants</h5>
                                <ul class="list-disc list-inside space-y-1">
                                    ${composants.map(c => `
                                        <li class="text-sm text-gray-700">
                                            <span class="font-medium">${c.nom}</span> : ${c.pourcentage}%
                                            <span class="text-gray-500">(Quantite calculee: ${c.quantite_calculee})</span>
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
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-[#305327]">Quantite</th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-[#305327]">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${historiques.map(h => `
                                            <tr class="border-b">
                                                <td class="px-4 py-2 text-sm">
                                                    <span class="px-2 py-1 rounded-full text-xs ${h.type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                                        ${h.type === 'entree' ? 'Entree' : 'Sortie'}
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
                                <p class="text-sm text-gray-500">Aucun mouvement enregistre.</p>
                            </div>
                        `;
                    }

                    document.getElementById('stockDetailsContent').innerHTML = html;
                    document.getElementById('stockDetailsModal').classList.remove('hidden');
                    document.getElementById('stockDetailsModal').classList.add('flex');
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des details du stock');
                });
        }

        function getStatusClasses(status) {
            switch (status) {
                case 'en attente': return 'bg-yellow-100 text-yellow-800';
                case 'en production': return 'bg-blue-100 text-blue-800';
                case 'production terminer': return 'bg-green-100 text-green-800';
                case 'annule': return 'bg-red-100 text-red-800';
                case 'consommer': return 'bg-orange-100 text-orange-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function updateStatusStyle(select) {
            select.className = 'status-select text-xs border-gray-300 rounded-md focus:ring-[#008d36] focus:border-[#008d36] py-1 px-2 ' + getStatusClasses(select.value);
        }
    </script>
</body>
</html>
