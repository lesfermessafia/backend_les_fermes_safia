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
            <form method="GET" action="{{ route('admin.formules.index') }}" class="mb-4 filter-form">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchFormules" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchFormules" name="search" value="{{ request('search') }}" placeholder="Nom ou matière première..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">Filtrer</button>
                        <a href="{{ route('admin.formules.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200 inline-flex items-center reset-link">Réinitialiser</a>
                    </div>
                </div>
            </form>

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
                                <button onclick="editFormule({{ $formule['id'] }})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                                <button onclick="deleteFormule({{ $formule['id'] }})" class="text-red-600 hover:text-red-800">Supprimer</button>
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
                        <button onclick="editFormule(${f.id})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                        <button onclick="deleteFormule(${f.id})" class="text-red-600 hover:text-red-800">Supprimer</button>
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.filter-form');
            const resetLink = document.querySelector('.reset-link');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new URLSearchParams(new FormData(form));
                    const url = `${form.action}?${formData.toString()}`;
                    loadFormules(url);
                    history.pushState({}, '', url);
                });
            }

            if (resetLink) {
                resetLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    form.reset();
                    const url = form.action;
                    loadFormules(url);
                    history.pushState({}, '', url);
                });
            }
        });

        window.addEventListener('popstate', function() {
            loadFormules(window.location.href);
        });
    </script>
</body>
</html>
