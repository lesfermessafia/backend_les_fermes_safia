<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique Stock Poulet - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Historique Stock Poulet" color="blue" />

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.poulets.index') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour aux Stocks
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-[#305327] mb-2">Historique des Mouvements</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Code Stock</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->code_stock }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Poulet</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->poulet ? $stock->poulet->nom : 'Non assigné' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Ferme</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->ferme ? $stock->ferme->nom : 'Non assigné' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Quantité Actuelle</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->quantite }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Statut</p>
                        <p class="font-semibold">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                {{ $stock->statut === 'en_stock' ? 'bg-green-100 text-green-700' : 
                                ($stock->statut === 'vendu' ? 'bg-blue-100 text-blue-700' : 
                                ($stock->statut === 'mort' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                {{ $stock->statut }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Date d'entrée</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->date_entree ? $stock->date_entree->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Quantité</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Motif</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stock->historiques as $historique)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->date_mouvement->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                    {{ $historique->type_mouvement === 'entree' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $historique->type_mouvement === 'entree' ? 'Entrée' : 'Sortie' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $historique->motif }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $historique->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Aucun mouvement enregistré pour ce stock.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
