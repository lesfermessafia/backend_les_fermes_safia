<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Tableau de Bord" color="blue" />

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-[#008d36] hover:text-[#305327] font-medium transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour au Dashboard
            </a>
        </div>

        <!-- Sélecteur de période -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-end gap-4 justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-[#305327] mb-1">Vue Globale</h2>
                    <p class="text-sm text-gray-500">Statistiques générales de l'activité de l'entreprise</p>
                </div>
                <div class="flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Du</label>
                        <input type="date" id="startDate" value="{{ $startDate }}" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="onDateRangeChange()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Au</label>
                        <input type="date" id="endDate" value="{{ $endDate }}" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36]" onchange="onDateRangeChange()">
                    </div>
                    <div class="flex gap-1">
                        <button onclick="applyPreset('today')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition duration-200">Aujourd'hui</button>
                        <button onclick="applyPreset('7days')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition duration-200">7 jours</button>
                        <button onclick="applyPreset('30days')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition duration-200">30 jours</button>
                        <button onclick="applyPreset('month')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition duration-200">Ce mois</button>
                        <button onclick="applyPreset('year')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition duration-200">Cette année</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="dashboardLoader" class="hidden fixed top-20 right-6 bg-white shadow-lg rounded-lg px-4 py-2 text-sm text-[#305327] font-medium z-50">
            Chargement...
        </div>

        <!-- Vue d'ensemble (compteurs globaux) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-[#008d36]">
                <p class="text-xs text-gray-500 font-medium">Sites</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-sites">{{ $stats['overview']['sites'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-[#305327]">
                <p class="text-xs text-gray-500 font-medium">Fermes</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-fermes">{{ $stats['overview']['fermes'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 font-medium">Magasins</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-magasins">{{ $stats['overview']['magasins'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 font-medium">Utilisateurs</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-users">{{ $stats['overview']['users'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-500 font-medium">Types de Poulets</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-poulets">{{ $stats['overview']['poulets'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
                <p class="text-xs text-gray-500 font-medium">Matières Premières</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-matieresPremieres">{{ $stats['overview']['matieresPremieres'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-orange-500">
                <p class="text-xs text-gray-500 font-medium">Aliments</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-aliments">{{ $stats['overview']['aliments'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-teal-500">
                <p class="text-xs text-gray-500 font-medium">Formules</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-formules">{{ $stats['overview']['formules'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-indigo-500">
                <p class="text-xs text-gray-500 font-medium">Lots</p>
                <p class="text-2xl font-bold text-[#305327]" id="ov-lots">{{ $stats['overview']['lots'] }}</p>
            </div>
        </div>

        <!-- Stats de la période -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-xs text-gray-500 font-medium">Poulets en stock (actuel)</p>
                <p class="text-2xl font-bold text-[#008d36]" id="stat-poulets-en-stock">{{ $stats['stockPoulets']['totalEnStock'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-xs text-gray-500 font-medium">Stock matières premières (actuel)</p>
                <p class="text-2xl font-bold text-[#008d36]" id="stat-stock-matieres">{{ number_format($stats['matieresPremieres']['stockTotal'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-xs text-gray-500 font-medium">Stock aliments (actuel)</p>
                <p class="text-2xl font-bold text-[#008d36]" id="stat-stock-aliments">{{ number_format($stats['aliments']['stockTotal'], 2) }}</p>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Évolution des mouvements de stock poulets</h3>
                <canvas id="chartEvolutionPoulets" height="200"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Stock poulets par statut (actuel)</h3>
                <canvas id="chartStockPouletsStatut" height="200"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Mouvements matières premières (période)</h3>
                <canvas id="chartMouvementsMatieres" height="200"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Top 5 matières premières mouvementées</h3>
                <canvas id="chartTopMatieres" height="200"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Mouvements aliments (période)</h3>
                <canvas id="chartMouvementsAliments" height="200"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Mouvements œufs (période)</h3>
                <canvas id="chartMouvementsOeufs" height="200"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-[#305327] mb-4">Utilisateurs par rôle</h3>
                <canvas id="chartUsersByRole" height="200"></canvas>
            </div>
        </div>
    </div>

    <script>
        let charts = {};
        const initialStats = @json($stats);

        function destroyCharts() {
            Object.values(charts).forEach(chart => chart.destroy());
            charts = {};
        }

        function makeChart(id, config) {
            const ctx = document.getElementById(id);
            if (!ctx) return;
            charts[id] = new Chart(ctx, config);
        }

        function renderCharts(stats) {
            destroyCharts();

            // Évolution des mouvements poulets
            const evolutionLabels = Object.keys(stats.stockPoulets.evolution || {});
            const evolutionEntree = evolutionLabels.map(d => stats.stockPoulets.evolution[d].entree || 0);
            const evolutionSortie = evolutionLabels.map(d => stats.stockPoulets.evolution[d].sortie || 0);

            makeChart('chartEvolutionPoulets', {
                type: 'line',
                data: {
                    labels: evolutionLabels,
                    datasets: [
                        { label: 'Entrées', data: evolutionEntree, borderColor: '#008d36', backgroundColor: 'rgba(0,141,54,0.1)', tension: 0.3, fill: true },
                        { label: 'Sorties', data: evolutionSortie, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.1)', tension: 0.3, fill: true },
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            // Stock poulets par statut
            const statutLabels = Object.keys(stats.stockPoulets.byStatut || {});
            const statutValues = Object.values(stats.stockPoulets.byStatut || {});
            makeChart('chartStockPouletsStatut', {
                type: 'doughnut',
                data: {
                    labels: statutLabels,
                    datasets: [{ data: statutValues, backgroundColor: ['#008d36', '#22c55e', '#3b82f6', '#dc2626', '#f59e0b', '#a855f7', '#f97316', '#06b6d4'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            // Mouvements matières premières
            const matLabels = Object.keys(stats.matieresPremieres.mouvements || {});
            const matValues = Object.values(stats.matieresPremieres.mouvements || {});
            makeChart('chartMouvementsMatieres', {
                type: 'bar',
                data: {
                    labels: matLabels,
                    datasets: [{ label: 'Quantité', data: matValues, backgroundColor: ['#008d36', '#dc2626'] }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            // Top matières premières
            const topMatieres = stats.matieresPremieres.topMatieres || [];
            makeChart('chartTopMatieres', {
                type: 'bar',
                data: {
                    labels: topMatieres.map(m => m.nom),
                    datasets: [{ label: 'Quantité mouvementée', data: topMatieres.map(m => m.total), backgroundColor: '#305327' }]
                },
                options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } } }
            });

            // Mouvements aliments
            const alimentLabels = Object.keys(stats.aliments.mouvements || {});
            const alimentValues = Object.values(stats.aliments.mouvements || {});
            makeChart('chartMouvementsAliments', {
                type: 'pie',
                data: {
                    labels: alimentLabels,
                    datasets: [{ data: alimentValues, backgroundColor: ['#008d36', '#f59e0b'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            // Mouvements oeufs
            const oeufLabels = Object.keys(stats.oeufs.mouvements || {});
            const oeufValues = Object.values(stats.oeufs.mouvements || {});
            makeChart('chartMouvementsOeufs', {
                type: 'pie',
                data: {
                    labels: oeufLabels,
                    datasets: [{ data: oeufValues, backgroundColor: ['#008d36', '#dc2626', '#f59e0b', '#3b82f6'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            // Utilisateurs par rôle
            const roleLabels = Object.keys(stats.usersByRole || {});
            const roleValues = Object.values(stats.usersByRole || {});
            makeChart('chartUsersByRole', {
                type: 'doughnut',
                data: {
                    labels: roleLabels,
                    datasets: [{ data: roleValues, backgroundColor: ['#305327', '#008d36', '#3b82f6', '#f59e0b'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

        }

        function updateCards(stats) {
            document.getElementById('ov-sites').textContent = stats.overview.sites;
            document.getElementById('ov-fermes').textContent = stats.overview.fermes;
            document.getElementById('ov-magasins').textContent = stats.overview.magasins;
            document.getElementById('ov-users').textContent = stats.overview.users;
            document.getElementById('ov-poulets').textContent = stats.overview.poulets;
            document.getElementById('ov-matieresPremieres').textContent = stats.overview.matieresPremieres;
            document.getElementById('ov-aliments').textContent = stats.overview.aliments;
            document.getElementById('ov-formules').textContent = stats.overview.formules;
            document.getElementById('ov-lots').textContent = stats.overview.lots;

            document.getElementById('stat-poulets-en-stock').textContent = stats.stockPoulets.totalEnStock;
            document.getElementById('stat-stock-matieres').textContent = Number(stats.matieresPremieres.stockTotal).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('stat-stock-aliments').textContent = Number(stats.aliments.stockTotal).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function showLoader() {
            document.getElementById('dashboardLoader').classList.remove('hidden');
        }
        function hideLoader() {
            document.getElementById('dashboardLoader').classList.add('hidden');
        }

        async function fetchStats(startDate, endDate) {
            showLoader();
            try {
                const url = new URL('{{ route('admin.dashboard.stats.data') }}');
                url.searchParams.set('start_date', startDate);
                url.searchParams.set('end_date', endDate);

                const response = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();

                updateCards(data);
                renderCharts(data);

                const pageUrl = new URL(window.location.href);
                pageUrl.searchParams.set('start_date', startDate);
                pageUrl.searchParams.set('end_date', endDate);
                history.pushState({}, '', pageUrl.toString());
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            } finally {
                hideLoader();
            }
        }

        function onDateRangeChange() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            if (!startDate || !endDate) return;
            fetchStats(startDate, endDate);
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function applyPreset(preset) {
            const today = new Date();
            let start = new Date();
            let end = new Date();

            switch (preset) {
                case 'today':
                    start = today;
                    end = today;
                    break;
                case '7days':
                    start.setDate(today.getDate() - 6);
                    break;
                case '30days':
                    start.setDate(today.getDate() - 29);
                    break;
                case 'month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'year':
                    start = new Date(today.getFullYear(), 0, 1);
                    break;
            }

            document.getElementById('startDate').value = formatDate(start);
            document.getElementById('endDate').value = formatDate(end);
            fetchStats(formatDate(start), formatDate(end));
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderCharts(initialStats);
        });
    </script>
</body>
</html>
