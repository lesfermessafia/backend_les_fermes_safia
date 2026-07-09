<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
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
                        @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-50">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour créer/éditer un utilisateur -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouvel Utilisateur</h3>
            <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <input type="hidden" id="userId" name="id" value="">
                
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
