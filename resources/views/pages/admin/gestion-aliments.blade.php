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
            <form method="GET" action="{{ route('admin.aliments.index') }}" class="mb-4 filter-form">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="searchAliments" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="searchAliments" name="search" value="{{ request('search') }}" placeholder="Code ou nom..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">Filtrer</button>
                        <a href="{{ route('admin.aliments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200 inline-flex items-center reset-link">Réinitialiser</a>
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
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editAliment({{ $aliment->id }})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                                <button onclick="deleteAliment({{ $aliment->id }})" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
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
            document.getElementById('alimentPhoto').value = '';
            document.getElementById('alimentPhotoPreview').classList.add('hidden');
            document.getElementById('alimentPhotoPreview').src = '';
        }

        function closeModal() {
            document.getElementById('alimentModal').classList.add('hidden');
            document.getElementById('alimentModal').classList.remove('flex');
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.filter-form');
            const resetLink = document.querySelector('.reset-link');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new URLSearchParams(new FormData(form));
                    const url = `${form.action}?${formData.toString()}`;
                    loadAliments(url);
                    history.pushState({}, '', url);
                });
            }

            if (resetLink) {
                resetLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    form.reset();
                    const url = form.action;
                    loadAliments(url);
                    history.pushState({}, '', url);
                });
            }
        });

        window.addEventListener('popstate', function() {
            loadAliments(window.location.href);
        });
    </script>
</body>
</html>
