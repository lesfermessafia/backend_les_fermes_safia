<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Comptable - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar title="Dashboard Comptable" color="green" />

    <div class="container mx-auto p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Panel Comptable</h2>
            <p class="text-gray-600">Bienvenue dans le tableau de bord comptable.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-bold text-blue-800">Mouvements de Stock</h3>
                    <p class="text-sm text-blue-600 mt-2">Gérer les entrées et sorties de stock</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="font-bold text-yellow-800">Rapports Financiers</h3>
                    <p class="text-sm text-yellow-600 mt-2">Consulter les rapports financiers</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <h3 class="font-bold text-red-800">Facturation</h3>
                    <p class="text-sm text-red-600 mt-2">Gérer les factures et paiements</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
