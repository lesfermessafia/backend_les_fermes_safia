<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord général - Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-50 min-h-screen">
    <x-navbar title="Tableau de bord général" color="green" />

    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('comptable.dashboard') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium mb-6 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour au Dashboard
        </a>

        <div class="rounded-2xl bg-gradient-to-r from-[#305327] to-[#008d36] text-white px-6 py-7 mb-8 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-wider text-white/75 font-semibold">Vue générale</p>
                    <h1 class="text-3xl font-bold mt-1">Tableau de bord comptable</h1>
                    <p class="text-white/80 mt-2">Suivi rapide des stocks, des sites et des mouvements.</p>
                </div>
                <div class="rounded-xl bg-white/15 px-5 py-3 text-sm">
                    <p class="text-white/70">Dernière mise à jour</p>
                    <p class="font-semibold">{{ now()->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('comptable.tableau-de-bord') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-700 mb-2">Période des mouvements</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="period" value="today" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $period === 'today' ? 'bg-[#008d36] text-white' : 'bg-gray-100 text-gray-700 hover:bg-green-100' }}">Aujourd'hui</button>
                        <button type="submit" name="period" value="week" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $period === 'week' ? 'bg-[#008d36] text-white' : 'bg-gray-100 text-gray-700 hover:bg-green-100' }}">Cette semaine</button>
                        <button type="submit" name="period" value="30" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $period === '30' ? 'bg-[#008d36] text-white' : 'bg-gray-100 text-gray-700 hover:bg-green-100' }}">30 jours</button>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-500 mb-1">Du</label>
                        <input id="start_date" name="start_date" type="date" value="{{ $period === 'custom' ? $startDate->format('Y-m-d') : '' }}" class="rounded-lg border-gray-300 text-sm focus:border-[#008d36] focus:ring-[#008d36]">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-gray-500 mb-1">Au</label>
                        <input id="end_date" name="end_date" type="date" value="{{ $period === 'custom' ? $endDate->format('Y-m-d') : '' }}" class="rounded-lg border-gray-300 text-sm focus:border-[#008d36] focus:ring-[#008d36]">
                    </div>
                    <button type="submit" name="period" value="custom" class="px-4 py-2 rounded-lg bg-[#305327] text-white text-sm font-semibold hover:bg-[#008d36] transition">Appliquer</button>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Période sélectionnée : <span class="font-semibold text-[#305327]">{{ $periodLabel }}</span> ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</p>
        </form>

        <!-- Indicateurs clés -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-[#008d36]">
                <p class="text-sm text-gray-500 font-medium">Sites d'exploitation</p>
                <p class="text-3xl font-bold text-[#305327] mt-2">{{ $stats['fermes'] }}</p>
                <p class="text-xs text-gray-400 mt-1">fermes actives</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-blue-500">
                <p class="text-sm text-gray-500 font-medium">Magasins</p>
                <p class="text-3xl font-bold text-[#305327] mt-2">{{ $stats['magasins'] }}</p>
                <p class="text-xs text-gray-400 mt-1">points de stockage</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-orange-500">
                <p class="text-sm text-gray-500 font-medium">Poulets en stock</p>
                <p class="text-3xl font-bold text-[#305327] mt-2">{{ number_format($stats['poulets'], 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-1">tous statuts confondus</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-yellow-500">
                <p class="text-sm text-gray-500 font-medium">Œufs disponibles</p>
                <p class="text-3xl font-bold text-[#305327] mt-2">{{ number_format($stats['oeufs']['tablettes'], 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-1">tablettes · {{ number_format($stats['oeufs']['unites'], 0, ',', ' ') }} unités</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-indigo-500">
                <p class="text-sm text-gray-500 font-medium">Matières disponibles</p>
                <p class="text-3xl font-bold text-[#305327] mt-2">{{ number_format($stats['matieres']['disponible'], 2, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-1">sur {{ number_format($stats['matieres']['total'], 2, ',', ' ') }} au total</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-green-500">
                <p class="text-sm text-gray-500 font-medium">Aliments disponibles</p>
                <p class="text-3xl font-bold text-[#305327] mt-2">{{ number_format($stats['aliments'], 2, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-1">quantité restante</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-[#305327]">État du cheptel</h2>
                        <p class="text-sm text-gray-500 mt-1">Nombre de poulets par ferme</p>
                    </div>
                    <span class="rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs font-bold">{{ number_format($stats['poulets'], 0, ',', ' ') }} sujets</span>
                </div>
                <div class="p-6">
                    @if($stats['pouletsByFerme']->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($stats['pouletsByFerme'] as $ferme)
                        @php $pourcentage = $stats['poulets'] > 0 ? round(($ferme->total / $stats['poulets']) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-gray-700">{{ $ferme->nom }}</span>
                                <span class="text-sm font-bold text-[#305327]">{{ number_format($ferme->total, 0, ',', ' ') }} <span class="text-gray-400 font-normal">({{ $pourcentage }}%)</span></span>
                            </div>
                            <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#305327] to-[#008d36]" style="width: {{ $pourcentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-10 text-gray-500">Aucune ferme ne contient de poulets.</div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-[#305327]">Matières premières</h2>
                        <p class="text-sm text-gray-500 mt-1">Statistiques par matière</p>
                    </div>
                    <span class="rounded-full bg-indigo-100 text-indigo-700 px-3 py-1 text-xs font-bold">{{ $stats['matieresByType']->count() }} matières</span>
                </div>
                <div class="p-5 max-h-[430px] overflow-y-auto space-y-3">
                    @forelse($stats['matieresByType'] as $matiere)
                    @php $pourcentageDisponible = $matiere->total > 0 ? round(($matiere->disponible / $matiere->total) * 100) : 0; @endphp
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <p class="font-bold text-gray-800">{{ $matiere->nom }}</p>
                                <p class="text-xs text-gray-500">Unité : {{ $matiere->unite }}</p>
                            </div>
                            <span class="text-sm font-bold text-green-700">{{ number_format($matiere->disponible, 2, ',', ' ') }} dispo</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-gray-200 overflow-hidden mb-3">
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ $pourcentageDisponible }}%"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-lg bg-white p-2"><p class="text-[11px] text-gray-500">Total</p><p class="font-bold text-gray-800">{{ number_format($matiere->total, 2, ',', ' ') }}</p></div>
                            <div class="rounded-lg bg-white p-2"><p class="text-[11px] text-gray-500">Utilisé</p><p class="font-bold text-orange-600">{{ number_format($matiere->utilise, 2, ',', ' ') }}</p></div>
                            <div class="rounded-lg bg-white p-2"><p class="text-[11px] text-gray-500">Dispo.</p><p class="font-bold text-green-700">{{ $pourcentageDisponible }}%</p></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-500">Aucune matière première en stock.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Mouvements des 7 derniers jours -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-[#305327]">Activité récente</h2>
                    <p class="text-sm text-gray-500 mt-1">Mouvements enregistrés : {{ $periodLabel }}</p>
                </div>
                <span class="text-xs font-semibold text-gray-500 bg-gray-100 rounded-full px-3 py-1">4 modules suivis</span>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach([
                    ['label' => 'Aliments', 'data' => $stats['mouvements']['aliments'], 'color' => 'green'],
                    ['label' => 'Matières premières', 'data' => $stats['mouvements']['matieres'], 'color' => 'indigo'],
                    ['label' => 'Œufs', 'data' => $stats['mouvements']['oeufs'], 'color' => 'yellow'],
                    ['label' => 'Poulets', 'data' => $stats['mouvements']['poulets'], 'color' => 'orange'],
                ] as $mouvement)
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800">{{ $mouvement['label'] }}</h3>
                        <span class="h-2.5 w-2.5 rounded-full bg-{{ $mouvement['color'] }}-500"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-white p-3 border border-green-100">
                            <p class="text-xs text-gray-500">Entrées</p>
                            <p class="text-lg font-bold text-green-700 mt-1">{{ number_format($mouvement['data']['entrees'], 2, ',', ' ') }}</p>
                        </div>
                        <div class="rounded-lg bg-white p-3 border border-red-100">
                            <p class="text-xs text-gray-500">Sorties</p>
                            <p class="text-lg font-bold text-red-600 mt-1">{{ number_format($mouvement['data']['sorties'], 2, ',', ' ') }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between text-xs text-gray-500">
                        <span>Solde mouvements</span>
                        <span class="font-bold text-gray-700">{{ number_format($mouvement['data']['entrees'] - $mouvement['data']['sorties'], 2, ',', ' ') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
