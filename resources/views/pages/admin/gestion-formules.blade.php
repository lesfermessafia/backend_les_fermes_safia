<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Formules - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Formules" color="blue" />

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
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Formules</h2>
                <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Nouvelle Formule
                </button>
            </div>

            <!-- Filtre -->
            <div class="mb-4">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchFormules" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchFormules" value="{{ request('search') }}" placeholder="Nom ou matière première..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" oninput="debouncedSearchFormules()">
                    </div>
                    <button onclick="resetSearchFormules()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">Réinitialiser</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Photo</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Composants</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($formules as $formule)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                @if($formule['photo'])
                                    <img src="{{ url('img/' . $formule['photo']) }}" alt="{{ $formule['nom'] }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $formule['nom'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <ul class="list-disc list-inside">
                                    @foreach ($formule['composant'] as $c)
                                        <li>{{ $c['matiere_nom'] }} : {{ $c['quantite'] }} %</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editFormule({{ $formule['id'] }})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteFormule({{ $formule['id'] }})" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Aucune formule ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="pagination-formules" class="mt-4">
                {{ $formules->links() }}
            </div>

            <div id="stats-formules" class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[#305327]/10 rounded-lg p-4 border border-[#305327]/20">
                    <p class="text-sm text-[#305327] font-medium">Total Formules</p>
                    <p class="text-2xl font-bold text-[#305327]" id="stats-formules-total">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-[#008d36]/10 rounded-lg p-4 border border-[#008d36]/20">
                    <p class="text-sm text-[#008d36] font-medium">Total Composants</p>
                    <p class="text-2xl font-bold text-[#008d36]" id="stats-formules-composants">{{ $stats['totalComposants'] }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <p class="text-sm text-blue-700 font-medium">Moyenne composants / formule</p>
                    <p class="text-2xl font-bold text-blue-700" id="stats-formules-moyenne">{{ $stats['avgComposants'] }}</p>
                </div>
            </div>
        </div>

        <!-- Section Utilisation des Formules -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-2xl font-bold text-[#305327] mb-6">Utilisation des Formules dans les Stocks</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($formuleUsageStats as $formuleId => $stats)
                    @php
                        $formule = \App\Models\Formule::find($formuleId);
                    @endphp
                    @if ($formule)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-[#008d36] transition duration-200 cursor-pointer" onclick="showFormuleUsage({{ $formuleId }}, '{{ $formule->nom }}')">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-[#305327]">{{ $formule->nom }}</h3>
                                <span class="bg-[#008d36] text-white text-xs px-2 py-1 rounded-full">{{ $stats['count'] }} utilisation{{ $stats['count'] > 1 ? 's' : '' }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Cliquez pour voir les détails</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Historique Utilisation Formule -->
    <div id="formuleUsageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="formuleUsageModalTitle" class="text-xl font-bold">Historique d'utilisation</h3>
                <button onclick="closeFormuleUsageModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="formuleUsageContent">
                <!-- Le contenu sera rempli par JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="formuleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouvelle Formule</h3>
            <form id="formuleForm" method="POST" action="{{ route('admin.formules.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="_method" name="_method" value="">
                <input type="hidden" id="formuleId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <img id="formulePhotoPreview" src="" alt="Photo actuelle" class="w-16 h-16 object-cover rounded mb-2 hidden">
                    <input type="file" id="formulePhoto" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laissez vide pour conserver la photo actuelle.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="formuleNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>

                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Composants</label>
                        <button type="button" onclick="addComposantRow()" class="text-xs bg-[#008d36] text-white px-2 py-1 rounded hover:bg-[#305327]">+ Ajouter</button>
                    </div>
                    <div id="composantsContainer" class="space-y-2"></div>
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
        let formules = @json($formules->items());
        const matieresPremieres = @json($matieresPremieres);
        const formuleUsageStats = @json($formuleUsageStats);
        let composantIndex = 0;

        function addComposantRow(matiereId = '', quantite = '') {
            const container = document.getElementById('composantsContainer');
            const row = document.createElement('div');
            row.className = 'flex gap-2 items-center composant-row';

            const select = document.createElement('select');
            select.name = `composant[${composantIndex}][matiere_id]`;
            select.required = true;
            select.className = 'flex-1 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#008d36]';
            select.innerHTML = '<option value="">-- Matière première --</option>' +
                matieresPremieres.map(m => `<option value="${m.id}" ${m.id === matiereId ? 'selected' : ''}>${m.nom}</option>`).join('');

            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.step = '0.01';
            qtyInput.min = '0';
            qtyInput.max = '100';
            qtyInput.name = `composant[${composantIndex}][quantite]`;
            qtyInput.value = quantite;
            qtyInput.required = true;
            qtyInput.placeholder = 'Pourcentage (%)';
            qtyInput.className = 'w-28 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#008d36]';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '✕';
            removeBtn.className = 'text-red-600 hover:text-red-800 px-2';
            removeBtn.onclick = () => row.remove();

            row.appendChild(select);
            row.appendChild(qtyInput);
            row.appendChild(removeBtn);
            container.appendChild(row);
            composantIndex++;
        }

        function resetComposants() {
            document.getElementById('composantsContainer').innerHTML = '';
            composantIndex = 0;
        }

        function openModal() {
            document.getElementById('formuleModal').classList.remove('hidden');
            document.getElementById('formuleModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouvelle Formule';
            document.getElementById('formuleForm').action = '{{ route('admin.formules.store') }}';
            document.getElementById('_method').value = '';
            document.getElementById('formuleId').value = '';
            document.getElementById('formuleNom').value = '';
            document.getElementById('formulePhoto').value = '';
            document.getElementById('formulePhotoPreview').classList.add('hidden');
            document.getElementById('formulePhotoPreview').src = '';
            resetComposants();
            addComposantRow();
        }

        function closeModal() {
            document.getElementById('formuleModal').classList.add('hidden');
            document.getElementById('formuleModal').classList.remove('flex');
        }

        function editFormule(id) {
            const formule = formules.find(f => f.id === id);
            if (formule) {
                document.getElementById('formuleModal').classList.remove('hidden');
                document.getElementById('formuleModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Formule';
                document.getElementById('formuleForm').action = '{{ route('admin.formules.update', ':id') }}'.replace(':id', id);
                document.getElementById('_method').value = 'PUT';
                document.getElementById('formuleId').value = formule.id;
                document.getElementById('formuleNom').value = formule.nom;
                document.getElementById('formulePhoto').value = '';

                const preview = document.getElementById('formulePhotoPreview');
                if (formule.photo) {
                    preview.src = '/img/' + formule.photo;
                    preview.classList.remove('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }

                resetComposants();
                formule.composant.forEach(c => addComposantRow(c.matiere_id, c.quantite));
            }
        }

        function deleteFormule(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette formule ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.formules.destroy', ':id') }}'.replace(':id', id);

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

        function showFormuleUsage(formuleId, formuleNom) {
            const stats = formuleUsageStats[formuleId];
            if (!stats) return;

            document.getElementById('formuleUsageModalTitle').textContent = `Historique d'utilisation - ${formuleNom}`;

            const content = document.getElementById('formuleUsageContent');
            if (stats.stocks.length === 0) {
                content.innerHTML = '<p class="text-gray-500 text-center py-4">Aucune utilisation trouvée pour cette formule.</p>';
            } else {
                let html = '<div class="overflow-x-auto"><table class="w-full"><thead><tr class="bg-gray-50"><th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code Stock</th><th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Aliment</th><th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité Fabriquée</th><th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité Utilisée</th><th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th></tr></thead><tbody>';
                stats.stocks.forEach(stock => {
                    html += `<tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">${stock.code_stock}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${stock.aliment_nom} (${stock.aliment_code})</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${stock.quantite_fabriquer}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${stock.quantite_utiliser}</td>
                        <td class="px-4 py-3 text-sm"><span class="px-2 py-1 rounded-full text-xs font-semibold ${stock.status === 'production terminer' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">${stock.status}</span></td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                content.innerHTML = html;
            }

            document.getElementById('formuleUsageModal').classList.remove('hidden');
            document.getElementById('formuleUsageModal').classList.add('flex');
        }

        function closeFormuleUsageModal() {
            document.getElementById('formuleUsageModal').classList.add('hidden');
            document.getElementById('formuleUsageModal').classList.remove('flex');
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderFormules(items) {
            const tbody = document.querySelector('.overflow-x-auto tbody');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Aucune formule ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(f => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">${f.photo ? `<img src="/img/${escapeHtml(f.photo)}" alt="${escapeHtml(f.nom)}" class="w-12 h-12 object-cover rounded">` : '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(f.nom)}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <ul class="list-disc list-inside">
                            ${(f.composant || []).map(c => `<li>${escapeHtml(c.matiere_nom)} : ${escapeHtml(c.quantite)} %</li>`).join('')}
                        </ul>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="editFormule(${f.id})" class="text-[#008d36] hover:text-[#305327] mr-2" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="deleteFormule(${f.id})" class="text-red-600 hover:text-red-800" title="Supprimer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadFormules(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                formules = data.formules;
                renderFormules(formules);
                const paginationContainer = document.getElementById('pagination-formules');
                if (paginationContainer && data.pagination !== undefined) {
                    paginationContainer.innerHTML = data.pagination;
                }
                if (data.stats) {
                    const totalEl = document.getElementById('stats-formules-total');
                    const composantsEl = document.getElementById('stats-formules-composants');
                    const moyenneEl = document.getElementById('stats-formules-moyenne');
                    if (totalEl) totalEl.textContent = data.stats.total;
                    if (composantsEl) composantsEl.textContent = data.stats.totalComposants;
                    if (moyenneEl) moyenneEl.textContent = data.stats.avgComposants;
                }
            } catch (error) {
                alert('Erreur lors du chargement des formules: ' + error.message);
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

        // Recherche automatique pour les formules
        const debouncedSearchFormules = debounce(function() {
            const search = document.getElementById('searchFormules').value;
            const url = new URL(window.location.href);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            history.pushState({}, '', url.toString());
            loadFormules(url.toString());
        }, 500);

        function resetSearchFormules() {
            document.getElementById('searchFormules').value = '';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            history.pushState({}, '', url.toString());
            loadFormules(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchFormules = document.getElementById('searchFormules');
            if (searchFormules) {
                const params = new URLSearchParams(window.location.search);
                searchFormules.value = params.get('search') || '';
            }
        });

        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const searchFormules = document.getElementById('searchFormules');
            if (searchFormules) {
                searchFormules.value = params.get('search') || '';
            }
            loadFormules(window.location.href);
        });
    </script>
</body>
</html>
