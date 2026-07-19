<?php

namespace App\Http\Controllers;

use App\Models\Ferme;
use App\Models\Magasin;
use App\Models\StockPoulet;
use App\Models\StockOeuf;
use App\Models\StockAliment;
use App\Models\MouvementStock;
use App\Models\HistoriqueAliment;
use App\Models\HistoriqueOeuf;
use App\Models\HistoriqueStockPoulet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComptableDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');
        $today = Carbon::today();

        if ($period === 'today') {
            $startDate = $today->copy()->startOfDay();
            $endDate = $today->copy()->endOfDay();
            $periodLabel = "Aujourd'hui";
        } elseif ($period === '30') {
            $startDate = $today->copy()->subDays(29)->startOfDay();
            $endDate = $today->copy()->endOfDay();
            $periodLabel = '30 derniers jours';
        } elseif ($period === 'custom' && $request->filled(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $periodLabel = 'Période personnalisée';
        } else {
            $period = 'week';
            $startDate = $today->copy()->startOfWeek();
            $endDate = $today->copy()->endOfDay();
            $periodLabel = 'Cette semaine';
        }

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $stats = [
            'fermes' => Ferme::count(),
            'magasins' => Magasin::count(),
            'poulets' => (int) StockPoulet::sum('quantite'),
            'oeufs' => [
                'tablettes' => (int) StockOeuf::sum('quantite'),
                'unites' => (int) StockOeuf::sum('quantite') * StockOeuf::OEUFS_PAR_TABLETTE,
            ],
            'matieres' => [
                'total' => (float) (DB::table('lot_matiere_premiere')->selectRaw('sum(quantite) as total')->first()->total ?? 0),
                'disponible' => (float) (DB::table('lot_matiere_premiere')->selectRaw('sum(quantite - quantite_utiliser) as total')->first()->total ?? 0),
                'utilise' => (float) (DB::table('lot_matiere_premiere')->selectRaw('sum(quantite_utiliser) as total')->first()->total ?? 0),
            ],
            'matieresByType' => DB::table('lot_matiere_premiere as lmp')
                ->join('matiere_premieres as mp', 'mp.id', '=', 'lmp.matiere_premiere_id')
                ->select(
                    'mp.nom',
                    'mp.unite',
                    DB::raw('sum(lmp.quantite) as total'),
                    DB::raw('sum(lmp.quantite_utiliser) as utilise'),
                    DB::raw('sum(lmp.quantite - lmp.quantite_utiliser) as disponible')
                )
                ->groupBy('mp.id', 'mp.nom', 'mp.unite')
                ->orderBy('mp.nom')
                ->get(),
            'aliments' => (float) StockAliment::sum(DB::raw('quantite_fabriquer - quantite_utiliser')),
            'pouletsByStatus' => StockPoulet::select('statut', DB::raw('sum(quantite) as total'))
                ->groupBy('statut')
                ->orderByDesc('total')
                ->pluck('total', 'statut'),
            'pouletsByFerme' => Ferme::query()
                ->join('stock_poulets', 'fermes.id', '=', 'stock_poulets.ferme_id')
                ->select('fermes.id', 'fermes.nom', DB::raw('sum(stock_poulets.quantite) as total'))
                ->groupBy('fermes.id', 'fermes.nom')
                ->havingRaw('sum(stock_poulets.quantite) > 0')
                ->orderByDesc('total')
                ->get(),
            'mouvements' => $this->mouvementsRecents($startDate, $endDate),
        ];

        return view('dashboard.comptable-general', compact('stats', 'period', 'periodLabel', 'startDate', 'endDate'));
    }

    private function mouvementsRecents(Carbon $startDate, Carbon $endDate)
    {
        return [
            'aliments' => [
                'entrees' => HistoriqueAliment::whereBetween('date_mouvement', [$startDate, $endDate])->where('type', 'entree')->sum('quantite'),
                'sorties' => HistoriqueAliment::whereBetween('date_mouvement', [$startDate, $endDate])->where('type', 'sortie')->sum('quantite'),
            ],
            'matieres' => [
                'entrees' => MouvementStock::whereBetween('date_mouvement', [$startDate, $endDate])->where('type', 'entree')->sum('quantite'),
                'sorties' => MouvementStock::whereBetween('date_mouvement', [$startDate, $endDate])->where('type', 'sortie')->sum('quantite'),
            ],
            'oeufs' => [
                'entrees' => HistoriqueOeuf::whereBetween('date_mouvement', [$startDate, $endDate])->where('type', 'entree')->sum('quantite'),
                'sorties' => HistoriqueOeuf::whereBetween('date_mouvement', [$startDate, $endDate])->where('type', 'sortie')->sum('quantite'),
            ],
            'poulets' => [
                'entrees' => HistoriqueStockPoulet::whereBetween('date_mouvement', [$startDate, $endDate])->where('type_mouvement', 'entree')->sum('quantite'),
                'sorties' => HistoriqueStockPoulet::whereBetween('date_mouvement', [$startDate, $endDate])->where('type_mouvement', 'sortie')->sum('quantite'),
            ],
        ];
    }
}
