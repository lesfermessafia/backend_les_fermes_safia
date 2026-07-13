<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique Stock Œufs - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Historique Stock Œufs" color="blue" />

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.oeufs.index') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium transition duration-200">
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
                        <p class="text-sm text-gray-600">Ferme</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->code_ferme }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Quantité Actuelle (tablettes)</p>
                        <p class="font-semibold text-[#305327]">{{ $stock->quantite }} <span class="text-xs text-gray-500">(≈ {{ number_format($stock->quantite_oeufs, 0, ',', ' ') }} œufs)</span></p>
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
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Tablettes</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Œufs</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-[#305327]">Gérant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stock->historiques as $historique)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->date_mouvement->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                    {{ $historique->type === 'entree' ? 'bg-green-100 text-green-700' : 
                                    ($historique->type === 'vente' ? 'bg-blue-100 text-blue-700' : 
                                    ($historique->type === 'casse' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ ucfirst($historique->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $historique->quantite }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($historique->quantite * \App\Models\StockOeuf::OEUFS_PAR_TABLETTE, 0, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $historique->gerant ? $historique->gerant->nom . ' ' . $historique->gerant->prenom : '-' }}</td>
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
