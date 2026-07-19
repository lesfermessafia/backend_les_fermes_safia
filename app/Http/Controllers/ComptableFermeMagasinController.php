<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ferme;
use App\Models\Magasin;
use App\Models\StockPoulet;
use App\Models\StockOeuf;

class ComptableFermeMagasinController extends Controller
{
    public function index(Request $request)
    {
        $fermes = Ferme::with(['stocksPoulets.poulet'])->orderBy('nom')->get();

        foreach ($fermes as $ferme) {
            $ferme->oeufs = StockOeuf::where('code_ferme', $ferme->nom)->get();
            $ferme->totalPoulets = $ferme->stocksPoulets->sum('quantite');
            $ferme->totalOeufsTablettes = $ferme->oeufs->sum('quantite');
        }

        $magasins = Magasin::orderBy('nom')->get();

        foreach ($magasins as $magasin) {
            $magasin->matieres = DB::table('lot_matiere_premiere as lmp')
                ->join('matiere_premieres as mp', 'lmp.matiere_premiere_id', '=', 'mp.id')
                ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
                ->where('lmp.magasin_id', $magasin->id)
                ->select(
                    'mp.nom as matiere_nom',
                    'mp.unite as matiere_unite',
                    'l.code_lot',
                    'lmp.quantite',
                    'lmp.quantite_utiliser',
                    DB::raw('lmp.quantite - lmp.quantite_utiliser as disponible')
                )
                ->get();
            $magasin->totalMatiere = $magasin->matieres->sum('quantite');
            $magasin->totalDisponible = $magasin->matieres->sum('disponible');
        }

        $totals = [
            'fermes' => $fermes->count(),
            'magasins' => $magasins->count(),
            'poulets' => $fermes->sum('totalPoulets'),
            'oeufs' => $fermes->sum('totalOeufsTablettes'),
            'matiere' => $magasins->sum('totalMatiere'),
            'disponible' => $magasins->sum('totalDisponible'),
        ];

        return view('pages.comptable.fermes-magasins', compact('fermes', 'magasins', 'totals'));
    }
}
