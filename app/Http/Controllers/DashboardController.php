<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Ferme;
use App\Models\Magasin;
use App\Models\User;
use App\Models\Poulet;
use App\Models\StockPoulet;
use App\Models\HistoriqueStockPoulet;
use App\Models\MatierePremiere;
use App\Models\MouvementStock;
use App\Models\Lot;
use App\Models\Aliment;
use App\Models\StockAliment;
use App\Models\HistoriqueAliment;
use App\Models\Formule;
use App\Models\StockOeuf;
use App\Models\HistoriqueOeuf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $stats = $this->buildStats($startDate, $endDate);

        return view('dashboard.admin-stats', [
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function getStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        return response()->json($this->buildStats($startDate, $endDate));
    }

    private function buildStats($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Vue d'ensemble (compteurs globaux, non affectés par la période)
        $overview = [
            'sites' => Site::count(),
            'fermes' => Ferme::count(),
            'magasins' => Magasin::count(),
            'users' => User::count(),
            'poulets' => Poulet::count(),
            'matieresPremieres' => MatierePremiere::count(),
            'aliments' => Aliment::count(),
            'formules' => Formule::count(),
            'lots' => Lot::count(),
        ];

        // Utilisateurs par rôle et statut
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $usersByStatus = [
            'actifs' => User::where('bloquer', false)->count(),
            'bloques' => User::where('bloquer', true)->count(),
        ];

        // Stock poulets (état actuel global)
        $stockPouletsByStatut = StockPoulet::select('statut', DB::raw('sum(quantite) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $totalPouletsEnStock = (int) StockPoulet::whereNotIn('statut', StockPoulet::statutsFinaux())->sum('quantite');

        // Mouvements de stock poulets dans la période
        $mouvementsPoulets = HistoriqueStockPoulet::whereBetween('date_mouvement', [$start, $end])
            ->select('type_mouvement', DB::raw('sum(quantite) as total'))
            ->groupBy('type_mouvement')
            ->pluck('total', 'type_mouvement');

        // Évolution journalière des mouvements de stock poulets
        $evolutionPouletsRaw = HistoriqueStockPoulet::whereBetween('date_mouvement', [$start, $end])
            ->select('date_mouvement', 'type_mouvement', DB::raw('sum(quantite) as total'))
            ->groupBy('date_mouvement', 'type_mouvement')
            ->orderBy('date_mouvement')
            ->get();

        $evolutionPoulets = [];
        foreach ($evolutionPouletsRaw as $row) {
            $date = $row->date_mouvement instanceof \Carbon\Carbon
                ? $row->date_mouvement->format('Y-m-d')
                : $row->date_mouvement;
            if (!isset($evolutionPoulets[$date])) {
                $evolutionPoulets[$date] = ['entree' => 0, 'sortie' => 0];
            }
            $evolutionPoulets[$date][$row->type_mouvement] = (float) $row->total;
        }
        ksort($evolutionPoulets);

        // Matières premières - mouvements de stock dans la période
        $mouvementsMatieres = MouvementStock::whereBetween('date_mouvement', [$start, $end])
            ->select('type', DB::raw('sum(quantite) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        // Stock total actuel des matières premières (global)
        $stockMatieresTotal = (float) (DB::table('lot_matiere_premiere')
            ->select(DB::raw('sum(quantite - quantite_utiliser) as total'))
            ->first()->total ?? 0);

        // Top 5 matières premières les plus mouvementées dans la période
        $topMatieres = MouvementStock::whereBetween('date_mouvement', [$start, $end])
            ->join('matiere_premieres', 'mouvement_stocks.matiere_id', '=', 'matiere_premieres.id')
            ->select('matiere_premieres.nom', DB::raw('sum(mouvement_stocks.quantite) as total'))
            ->groupBy('matiere_premieres.nom')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Aliments - historique dans la période
        $mouvementsAliments = HistoriqueAliment::whereBetween('date_mouvement', [$start, $end])
            ->select('type', DB::raw('sum(quantite) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $stockAlimentsTotal = (float) StockAliment::sum(DB::raw('quantite_fabriquer - quantite_utiliser'));

        // Oeufs - historique dans la période
        $mouvementsOeufs = HistoriqueOeuf::whereBetween('date_mouvement', [$start, $end])
            ->select('type', DB::raw('sum(quantite) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $stockOeufsTotal = (int) StockOeuf::sum('quantite');

        return [
            'overview' => $overview,
            'usersByRole' => $usersByRole,
            'usersByStatus' => $usersByStatus,
            'stockPoulets' => [
                'byStatut' => $stockPouletsByStatut,
                'totalEnStock' => $totalPouletsEnStock,
                'mouvements' => $mouvementsPoulets,
                'evolution' => $evolutionPoulets,
            ],
            'matieresPremieres' => [
                'stockTotal' => $stockMatieresTotal,
                'mouvements' => $mouvementsMatieres,
                'topMatieres' => $topMatieres,
            ],
            'aliments' => [
                'stockTotal' => $stockAlimentsTotal,
                'mouvements' => $mouvementsAliments,
            ],
            'oeufs' => [
                'stockTotal' => $stockOeufsTotal,
                'mouvements' => $mouvementsOeufs,
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ];
    }
}
