<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Matieres Premieres - Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Matieres Premieres - Comptable" color="green" />

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

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <p class="text-sm text-[#305327] font-medium">Stock total disponible</p>
            <p class="text-3xl font-bold text-[#305327]">{{ number_format($totalDisponible, 2, ',', ' ') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-[#305327]">Stocks de matieres premieres</h2>
                <div class="flex gap-2 flex-wrap">
                    <button onclick="openMouvementModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition">+ Mouvement</button>
                    <button onclick="openLotModal()" class="bg-[#305327] text-white px-4 py-2 rounded-lg hover:bg-[#008d36] transition">+ Nouveau stock</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Lot</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Matiere</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Magasin</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantite</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Utilise</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Disponible</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->code_lot }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->matiere_nom }} ({{ $stock->unite }})</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->magasin_nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock->quantite_utiliser }}</td>
                            <td class="px-4 py-3 text-sm font-medium {{ $stock->disponible < 10 ? 'text-red-600' : 'text-green-600' }}">{{ $stock->disponible }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="openMouvementModal({{ $stock->lmp_id }}, {{ $stock->magasin_id ? $stock->magasin_id : 'null' }})" class="text-[#008d36] hover:text-[#305327]" title="Enregistrer un mouvement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Aucun stock de matiere premiere.</td>
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
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Matiere</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Lot</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Magasin</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantite</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gerant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiques as $h)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->date_mouvement }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->matiere->nom ?? '-' }} ({{ $h->matiere->unite ?? '-' }})</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->lot->code_lot ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $h->magasin->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $h->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $h->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $h->gerant ? $h->gerant->prenom . ' ' . $h->gerant->nom : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Aucun mouvement enregistre.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $historiques->links() }}</div>
        </div>
    </div>

    <!-- Modal Nouveau Stock (Lot) -->
    <div id="lotModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Nouveau stock (lot)</h3>
            <form method="POST" action="{{ route('comptable.matieres-premieres.lot.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matiere</label>
                    <select name="matiere_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner...</option>
                        @foreach($matieres as $m)
                            <option value="{{ $m->id }}">{{ $m->nom }} ({{ $m->unite }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                    <select name="magasin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner...</option>
                        @foreach($magasins as $mag)
                            <option value="{{ $mag->id }}">{{ $mag->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantite</label>
                    <input type="number" step="0.01" name="quantite" required min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeLotModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327]">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Mouvement -->
    <div id="mouvementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Enregistrer un mouvement</h3>
            <form id="mouvementForm" method="POST" action="{{ route('comptable.matieres-premieres.mouvement') }}">
                @csrf
                <input type="hidden" id="lmpIdInput" name="lmp_id" value="">
                <div class="mb-4" id="lmpSelectContainer">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <select id="lmpSelect" name="lmp_id" onchange="onStockChange(this)" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner un stock...</option>
                        @foreach($stocks as $s)
                            <option value="{{ $s->lmp_id }}" data-magasin-id="{{ $s->magasin_id ?? '' }}">{{ $s->code_lot }} - {{ $s->matiere_nom }} ({{ $s->magasin_nom ?? '-' }}) - Dispo: {{ $s->disponible }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Magasin</label>
                    <select id="magasinSelect" name="magasin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="">Selectionner un magasin...</option>
                        @foreach($magasins as $mag)
                            <option value="{{ $mag->id }}">{{ $mag->nom }}</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantite</label>
                    <input type="number" step="0.01" name="quantite" required min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date_mouvement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observation</label>
                    <textarea name="observation" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeMouvementModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327]">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
        function openLotModal() { toggleModal('lotModal', true); }
        function closeLotModal() { toggleModal('lotModal', false); }
        function openMouvementModal(lmpId = null, magasinId = null) {
            toggleModal('mouvementModal', true);
            const input = document.getElementById('lmpIdInput');
            const select = document.getElementById('lmpSelect');
            const container = document.getElementById('lmpSelectContainer');
            const magasinSelect = document.getElementById('magasinSelect');
            if (lmpId) {
                input.value = lmpId;
                select.value = lmpId;
                container.classList.add('hidden');
            } else {
                input.value = '';
                select.value = '';
                container.classList.remove('hidden');
            }
            magasinSelect.value = magasinId ?? '';
        }
        function onStockChange(select) {
            document.getElementById('lmpIdInput').value = select.value;
            const option = select.options[select.selectedIndex];
            const magasinId = option ? option.getAttribute('data-magasin-id') : '';
            document.getElementById('magasinSelect').value = magasinId ?? '';
        }
        function closeMouvementModal() { toggleModal('mouvementModal', false); }
        window.onclick = function(event) {
            if (event.target.id === 'lotModal') closeLotModal();
            if (event.target.id === 'mouvementModal') closeMouvementModal();
        }
    </script>
</body>
</html>
