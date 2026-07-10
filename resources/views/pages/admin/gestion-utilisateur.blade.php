<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Utilisateurs" color="blue" />

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

        <div id="ajaxError" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
            <p id="ajaxErrorMessage"></p>
        </div>

        <div id="ajaxSuccess" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden">
            <p id="ajaxSuccessMessage"></p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-[#305327]">Gestion des Utilisateurs</h2>
                <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Nouvel Utilisateur
                </button>
            </div>

            <!-- Filtres -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nom, prénom, email, téléphone..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    </div>
                    <div class="w-full md:w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                        <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="all">Tous les rôles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="comptable" {{ request('role') == 'comptable' ? 'selected' : '' }}>Comptable</option>
                            <option value="superviseur" {{ request('role') == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                        </select>
                    </div>
                    <div class="w-full md:w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                            <option value="all">Tous les statuts</option>
                            <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="bloque" {{ request('status') == 'bloque' ? 'selected' : '' }}>Bloqué</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-[#008d36] text-white rounded-md hover:bg-[#305327] transition duration-200">Filtrer</button>
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200 inline-flex items-center">Réinitialiser</a>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Photo</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Prénom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Téléphone</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Rôle</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Statut</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                @if($user->photo_profil)
                                    <img src="{{ url('img/' . $user->photo_profil) }}" alt="{{ $user->nom }}" class="w-10 h-10 object-cover rounded-full">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">👤</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $user->nom }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $user->prenom }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->numero }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($user->role === 'admin') bg-[#305327]/10 text-[#305327]
                                    @elseif($user->role === 'comptable') bg-[#008d36]/10 text-[#008d36]
                                    @elseif($user->role === 'superviseur') bg-gray-100 text-gray-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($user->bloquer)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Bloqué</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Actif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-2">
                                    <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-800">
                                        ✏️
                                    </button>
                                    @if($user->role !== 'admin')
                                        <button onclick="toggleBlock({{ $user->id }}, {{ $user->bloquer ? 0 : 1 }})" class="text-yellow-600 hover:text-yellow-800">
                                            {{ $user->bloquer ? '🔓' : '🔒' }}
                                        </button>
                                        <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-800">
                                            🗑️
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Aucun utilisateur ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>

            <!-- Statistiques utilisateurs -->
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Statistiques des utilisateurs</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                    <div class="flex flex-col items-center">
                        <h4 class="text-sm font-medium text-gray-700 mb-2 text-center">Répartition par rôle</h4>
                        <canvas id="roleChart" class="max-h-48 w-full"></canvas>
                    </div>
                    <div class="flex flex-col items-center">
                        <h4 class="text-sm font-medium text-gray-700 mb-2 text-center">Répartition par statut</h4>
                        <canvas id="statusChart" class="max-h-48 w-full"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour créer/éditer un utilisateur -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouvel Utilisateur</h3>
            <form id="userForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="userId" name="id" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo de profil</label>
                    <img id="photoPreview" src="" alt="Photo actuelle" class="w-16 h-16 object-cover rounded-full mb-2 hidden">
                    <input type="file" id="photo_profil" name="photo_profil" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laissez vide pour conserver la photo actuelle.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="nom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" id="numero" name="numero" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                    <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                        <option value="admin">Admin</option>
                        <option value="comptable">Comptable</option>
                        <option value="superviseur">Superviseur</option>
                    </select>
                </div>
                
                <div class="mb-4" id="passwordField">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
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
        const roleStats = @json($roleStats);
        const statusStats = @json($statusStats);

        function initUserCharts() {
            const roleCtx = document.getElementById('roleChart');
            const statusCtx = document.getElementById('statusChart');

            if (!roleCtx || !statusCtx) return;

            const roleLabels = Object.keys(roleStats).map(r => r.charAt(0).toUpperCase() + r.slice(1));
            const roleData = Object.values(roleStats);
            const statusLabels = Object.keys(statusStats).map(s => s.charAt(0).toUpperCase() + s.slice(1));
            const statusData = Object.values(statusStats);

            new Chart(roleCtx, {
                type: 'doughnut',
                data: {
                    labels: roleLabels,
                    datasets: [{
                        data: roleData,
                        backgroundColor: ['#305327', '#008d36', '#9ca3af'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#008d36', '#dc2626'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        function showAjaxError(message) {
            const errorDiv = document.getElementById('ajaxError');
            const errorMessage = document.getElementById('ajaxErrorMessage');
            errorMessage.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        function showAjaxSuccess(message) {
            const successDiv = document.getElementById('ajaxSuccess');
            const successMessage = document.getElementById('ajaxSuccessMessage');
            successMessage.textContent = message;
            successDiv.classList.remove('hidden');
            setTimeout(() => {
                successDiv.classList.add('hidden');
            }, 3000);
        }

        function openModal() {
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('userModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouvel Utilisateur';
            document.getElementById('userForm').action = '{{ route('admin.users.store') }}';
            document.getElementById('userForm').method = 'POST';
            document.getElementById('userId').value = '';
            document.getElementById('userForm').reset();
            document.getElementById('passwordField').style.display = 'block';

            const methodInput = document.getElementById('_method');
            if (methodInput) {
                methodInput.value = '';
            }

            document.getElementById('photoPreview').classList.add('hidden');
            document.getElementById('photoPreview').src = '';
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userModal').classList.remove('flex');
        }

        async function editUser(id) {
            try {
                const response = await fetch(`/admin/users/${id}`);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const user = await response.json();

                document.getElementById('userModal').classList.remove('hidden');
                document.getElementById('userModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Utilisateur';
                document.getElementById('userForm').action = `/admin/users/${id}`;
                document.getElementById('userForm').method = 'POST';
                
                // Add method override for PUT
                let methodInput = document.getElementById('_method');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.id = '_method';
                    methodInput.value = 'PUT';
                    document.getElementById('userForm').appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                document.getElementById('userId').value = user.id;
                document.getElementById('nom').value = user.nom;
                document.getElementById('prenom').value = user.prenom;
                document.getElementById('email').value = user.email;
                document.getElementById('numero').value = user.numero;
                document.getElementById('role').value = user.role;
                document.getElementById('passwordField').style.display = 'none';
                document.getElementById('photo_profil').value = '';

                const preview = document.getElementById('photoPreview');
                if (user.photo_profil) {
                    preview.src = '/img/' + user.photo_profil;
                    preview.classList.remove('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            } catch (error) {
                showAjaxError('Erreur lors du chargement de l\'utilisateur: ' + error.message);
            }
        }

        async function toggleBlock(id, status) {
            if (confirm('Êtes-vous sûr de vouloir ' + (status ? 'débloquer' : 'bloquer') + ' cet utilisateur ?')) {
                try {
                    const response = await fetch(`/admin/users/${id}/toggle-block`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({})
                    });

                    if (response.ok) {
                        showAjaxSuccess('Statut de l\'utilisateur mis à jour avec succès');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        const errorData = await response.json();
                        showAjaxError('Erreur lors de la mise à jour du statut: ' + (errorData.message || response.statusText));
                    }
                } catch (error) {
                    showAjaxError('Erreur lors de la mise à jour du statut: ' + error.message);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', initUserCharts);

        async function deleteUser(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                try {
                    const response = await fetch(`/admin/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        showAjaxSuccess('Utilisateur supprimé avec succès');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        const errorData = await response.json();
                        showAjaxError('Erreur lors de la suppression: ' + (errorData.message || response.statusText));
                    }
                } catch (error) {
                    showAjaxError('Erreur lors de la suppression: ' + error.message);
                }
            }
        }
    </script>
</body>
</html>
