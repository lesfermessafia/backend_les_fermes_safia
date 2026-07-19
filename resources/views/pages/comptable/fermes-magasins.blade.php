<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fermes & Magasins - Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-50 min-h-screen">
    <x-navbar title="Fermes & Magasins" color="green" />

    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('comptable.dashboard') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium mb-6 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour au Dashboard
        </a>

        <h1 class="text-2xl font-bold text-[#305327] mb-6">Fermes & Magasins</h1>

        <!-- Fermes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-[#008d36] text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Fermes et contenus</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Nom</th>
                            <th class="px-4 py-3 text-left font-semibold">Site</th>
                            <th class="px-4 py-3 text-left font-semibold">Poulets</th>
                            <th class="px-4 py-3 text-left font-semibold">Œufs</th>
                            <th class="px-4 py-3 text-left font-semibold">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($fermes as $ferme)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $ferme->nom }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $ferme->site->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $ferme->totalPoulets }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $ferme->totalOeufsTablettes }}</td>
                            <td class="px-4 py-3">
                                <details class="group">
                                    <summary class="cursor-pointer text-[#008d36] hover:text-[#305327] font-medium select-none">Voir</summary>
                                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100 text-xs">
                                        @if($ferme->stocksPoulets->isNotEmpty())
                                        <div class="mb-3">
                                            <p class="font-semibold text-gray-700 mb-1">Poulets</p>
                                            <ul class="space-y-1">
                                                @foreach($ferme->stocksPoulets as $sp)
                                                <li class="flex justify-between">
                                                    <span>{{ $sp->code_stock }} - {{ $sp->poulet->race ?? '-' }} ({{ $sp->statut }})</span>
                                                    <span class="font-bold">{{ $sp->quantite }}</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        @if($ferme->oeufs->isNotEmpty())
                                        <div>
                                            <p class="font-semibold text-gray-700 mb-1">Stocks d'œufs</p>
                                            <ul class="space-y-1">
                                                @foreach($ferme->oeufs as $oeuf)
                                                <li class="flex justify-between">
                                                    <span>{{ $oeuf->date_entree }} - {{ $oeuf->quantite }} tablettes</span>
                                                    <span class="font-bold">{{ $oeuf->quantite * App\Models\StockOeuf::OEUFS_PAR_TABLETTE }} œufs</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        @if($ferme->stocksPoulets->isEmpty() && $ferme->oeufs->isEmpty())
                                        <p class="text-gray-500">Aucun contenu.</p>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucune ferme.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Magasins -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-[#008d36] text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Magasins et contenus</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Nom</th>
                            <th class="px-4 py-3 text-left font-semibold">Site</th>
                            <th class="px-4 py-3 text-left font-semibold">Qté</th>
                            <th class="px-4 py-3 text-left font-semibold">Disponible</th>
                            <th class="px-4 py-3 text-left font-semibold">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($magasins as $magasin)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $magasin->nom }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $magasin->site->nom ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ number_format($magasin->totalMatiere, 2, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-gray-900 font-semibold">{{ number_format($magasin->totalDisponible, 2, ',', ' ') }}</td>
                            <td class="px-4 py-3">
                                <details class="group">
                                    <summary class="cursor-pointer text-[#008d36] hover:text-[#305327] font-medium select-none">Voir</summary>
                                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100 text-xs">
                                        @if($magasin->matieres->isNotEmpty())
                                        <ul class="space-y-1">
                                            @foreach($magasin->matieres as $m)
                                            <li class="flex justify-between border-b border-gray-200 pb-1 last:border-0">
                                                <span>{{ $m->matiere_nom }} ({{ $m->matiere_unite }}) - {{ $m->code_lot }}</span>
                                                <span class="font-bold">{{ number_format($m->quantite, 2, ',', ' ') }} / {{ number_format($m->disponible, 2, ',', ' ') }} dispo</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <p class="text-gray-500">Aucune matière première.</p>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucun magasin.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Carte -->
        <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-[#008d36] text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Carte des emplacements</h2>
            </div>
            <div id="map" class="w-full h-96"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            function createIcon(color) {
                return L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color:' + color + '; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow:0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [18, 18],
                    iconAnchor: [9, 9]
                });
            }

            const markers = [];

            @foreach($fermes as $ferme)
                @if($ferme->latitude && $ferme->longitude)
                    const f{{ $ferme->id }} = L.marker([{{ $ferme->latitude }}, {{ $ferme->longitude }}], { icon: createIcon('#008d36') }).addTo(map)
                        .bindPopup('<strong>Ferme</strong><br>{{ addslashes($ferme->nom) }}<br>{{ addslashes($ferme->site->nom ?? '-') }}');
                    markers.push(f{{ $ferme->id }});
                @endif
            @endforeach

            @foreach($magasins as $magasin)
                @if($magasin->latitude && $magasin->longitude)
                    const m{{ $magasin->id }} = L.marker([{{ $magasin->latitude }}, {{ $magasin->longitude }}], { icon: createIcon('#3b82f6') }).addTo(map)
                        .bindPopup('<strong>Magasin</strong><br>{{ addslashes($magasin->nom) }}<br>{{ addslashes($magasin->site->nom ?? '-') }}');
                    markers.push(m{{ $magasin->id }});
                @endif
            @endforeach

            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            } else {
                map.setView([14.7167, -17.4677], 12);
            }
        });
    </script>
</body>
</html>
