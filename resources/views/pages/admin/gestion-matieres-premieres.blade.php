<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Matières Premières - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Gestion Matières Premières" color="blue" />

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
                <h2 class="text-2xl font-bold text-[#305327]">Liste des Matières Premières</h2>
                <button onclick="openModal()" class="bg-[#008d36] text-white px-4 py-2 rounded-lg hover:bg-[#305327] transition duration-200">
                    + Nouvelle Matière Première
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Code</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Nom</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Image</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Unité</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($matieres as $matiere)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $matiere->code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $matiere->nom }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($matiere->image)
                                    <img src="{{ asset($matiere->image) }}" alt="{{ $matiere->nom }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $matiere->unite }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editMatiere({{ $matiere->id }})" class="text-[#008d36] hover:text-[#305327] mr-2">Modifier</button>
                                <button onclick="deleteMatiere({{ $matiere->id }})" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="matiereModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nouvelle Matière Première</h3>
            <form id="matiereForm" method="POST" action="{{ route('admin.matieres-premieres.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="matiereId" name="id" value="">
                <input type="hidden" id="_method" name="_method" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="matiereNom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <img id="matiereImagePreview" src="" alt="Image actuelle" class="w-16 h-16 object-cover rounded mb-2 hidden">
                    <input type="file" id="matiereImage" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laissez vide pour conserver l'image actuelle.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                    <input type="text" id="matiereUnite" name="unite" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]">
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
        const matieres = @json($matieres);

        function openModal() {
            document.getElementById('matiereModal').classList.remove('hidden');
            document.getElementById('matiereModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nouvelle Matière Première';
            document.getElementById('matiereForm').action = '{{ route('admin.matieres-premieres.store') }}';
            document.getElementById('matiereId').value = '';
            document.getElementById('matiereNom').value = '';
            document.getElementById('matiereImage').value = '';
            document.getElementById('matiereUnite').value = '';
            document.getElementById('_method').value = '';
            document.getElementById('matiereImagePreview').classList.add('hidden');
            document.getElementById('matiereImagePreview').src = '';
        }

        function closeModal() {
            document.getElementById('matiereModal').classList.add('hidden');
            document.getElementById('matiereModal').classList.remove('flex');
        }

        function editMatiere(id) {
            const matiere = matieres.find(m => m.id === id);
            if (matiere) {
                document.getElementById('matiereModal').classList.remove('hidden');
                document.getElementById('matiereModal').classList.add('flex');
                document.getElementById('modalTitle').textContent = 'Modifier Matière Première';
                document.getElementById('matiereForm').action = '{{ route('admin.matieres-premieres.update', ':id') }}'.replace(':id', id);
                document.getElementById('matiereId').value = matiere.id;
                document.getElementById('matiereNom').value = matiere.nom;
                document.getElementById('matiereImage').value = '';
                document.getElementById('matiereUnite').value = matiere.unite;
                document.getElementById('_method').value = 'PUT';

                const preview = document.getElementById('matiereImagePreview');
                if (matiere.image) {
                    preview.src = '/' + matiere.image;
                    preview.classList.remove('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            }
        }

        function deleteMatiere(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette matière première ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.matieres-premieres.destroy', ':id') }}'.replace(':id', id);
                
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
    </script>
</body>
</html>
