<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superviseur - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Dashboard Superviseur" color="purple" />

    <div class="container mx-auto p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4 text-[#305327]">Panel Superviseur</h2>
            <p class="text-gray-600">Bienvenue dans le tableau de bord superviseur.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-[#008d36]/10 p-4 rounded-lg">
                    <h3 class="font-bold text-[#008d36]">Surveillance des Fermes</h3>
                    <p class="text-sm text-gray-600 mt-2">Surveiller l'activité des fermes</p>
                </div>
                <div class="bg-[#305327]/10 p-4 rounded-lg">
                    <h3 class="font-bold text-[#305327]">Rapports d'Activité</h3>
                    <p class="text-sm text-gray-600 mt-2">Consulter les rapports d'activité</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="font-bold text-gray-700">Alertes</h3>
                    <p class="text-sm text-gray-600 mt-2">Gérer les alertes et notifications</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
