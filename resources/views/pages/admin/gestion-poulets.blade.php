<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Poulets - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Nouveau Poulet
                </button>
            </div>

            <!-- Filtre -->
            <form method="GET" action="{{ route('admin.poulets.index') }}" class="mb-4 filter-form">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchPoulets" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchPoulets" name="search" value="{{ request('search') }}" placeholder="Code, nom ou race..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">Filtrer</button>
                        <a href="{{ route('admin.poulets.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200 inline-flex items-center reset-link">Réinitialiser</a>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
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
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
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

    <script>
        let poulets = @json($poulets->items());

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
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucun poulet ne correspond aux critères sélectionnés.</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(p => `
                <tr class="border-b hover:bg-gray-50">
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.filter-form');
            const resetLink = document.querySelector('.reset-link');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new URLSearchParams(new FormData(form));
                    const url = `${form.action}?${formData.toString()}`;
                    loadPoulets(url);
                    history.pushState({}, '', url);
                });
            }

            if (resetLink) {
                resetLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    form.reset();
                    const url = form.action;
                    loadPoulets(url);
                    history.pushState({}, '', url);
                });
            }
        });

        window.addEventListener('popstate', function() {
            loadPoulets(window.location.href);
        });
    </script>
</body>
</html>
