<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MatierePremiere;
use App\Models\Lot;
use App\Models\Magasin;
use App\Models\MouvementStock;
use App\Services\StockNotificationService;

class ComptableMatierePremiereController extends Controller
{
    public function index(Request $request)
    {
        $stocks = DB::table('lot_matiere_premiere as lmp')
            ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
            ->join('matiere_premieres as mp', 'lmp.matiere_premiere_id', '=', 'mp.id')
            ->leftJoin('magasins as m', 'lmp.magasin_id', '=', 'm.id')
            ->select(
                'lmp.id as lmp_id',
                'l.id as lot_id',
                'l.code_lot',
                'mp.id as matiere_id',
                'mp.nom as matiere_nom',
                'mp.unite',
                'm.id as magasin_id',
                'm.nom as magasin_nom',
                'lmp.quantite',
                'lmp.quantite_utiliser',
                DB::raw('lmp.quantite - lmp.quantite_utiliser as disponible')
            )
            ->orderBy('lmp.id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $matieres = MatierePremiere::orderBy('nom')->get();
        $magasins = Magasin::orderBy('nom')->get();

        $historiques = MouvementStock::with(['matiere', 'magasin', 'lot', 'gerant'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalDisponible = DB::table('lot_matiere_premiere')
                ->select(DB::raw('SUM(quantite - quantite_utiliser) as total'))
                ->value('total') ?? 0;

        return view('pages.comptable.gestion-matieres-premieres', compact(
            'stocks',
            'matieres',
            'magasins',
            'historiques',
            'totalDisponible'
        ));
    }

    public function storeLot(Request $request)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matiere_premieres,id',
            'magasin_id' => 'required|exists:magasins,id',
            'quantite' => 'required|numeric|min:0.01',
            'date_mouvement' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $lot = Lot::create([
                'code_lot' => Lot::generateCodeLot(),
                'created_by' => auth()->id(),
            ]);

            DB::table('lot_matiere_premiere')->insert([
                'lot_id' => $lot->id,
                'matiere_premiere_id' => $request->matiere_id,
                'magasin_id' => $request->magasin_id,
                'quantite' => $request->quantite,
                'quantite_utiliser' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            MouvementStock::create([
                'magasin_id' => $request->magasin_id,
                'matiere_id' => $request->matiere_id,
                'lot_id' => $lot->id,
                'type' => 'entree',
                'quantite' => $request->quantite,
                'date_mouvement' => $request->date_mouvement,
                'gerant_id' => auth()->id(),
                'observation' => 'Entree initiale - creation du lot ' . $lot->code_lot,
            ]);

            DB::commit();

            $matiere = MatierePremiere::find($request->matiere_id);
            StockNotificationService::notifyRoles(
                'Nouveau lot de matière première',
                'Le lot de ' . ($matiere->nom ?? 'matière première') . ' (' . $request->quantite . ' ' . ($matiere->unite ?? '') . ') a été créé.',
                'stock',
                route('comptable.matieres-premieres.index'),
                'indigo'
            );

            return redirect()->back()->with('success', 'Stock cree avec succes');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la creation du stock: ' . $e->getMessage());
        }
    }

    public function mouvement(Request $request)
    {
        $request->validate([
            'lmp_id' => 'required|exists:lot_matiere_premiere,id',
            'magasin_id' => 'required|exists:magasins,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|numeric|min:0.01',
            'date_mouvement' => 'required|date',
            'observation' => 'nullable|string',
        ]);

        $lmp = DB::table('lot_matiere_premiere')->find($request->lmp_id);

        if (!$lmp) {
            return redirect()->back()->with('error', 'Ligne de stock non trouvee');
        }

        $magasinId = $request->magasin_id;

        if ($lmp->magasin_id && $lmp->magasin_id != $magasinId) {
            return redirect()->back()->with('error', 'Le magasin selectionne ne correspond pas au stock');
        }

        $disponible = $lmp->quantite - $lmp->quantite_utiliser;

        if ($request->type === 'sortie' && $request->quantite > $disponible) {
            return redirect()->back()->with('error', 'Quantite insuffisante. Disponible: ' . $disponible);
        }

        DB::beginTransaction();
        try {
            $pivotUpdate = [
                'updated_at' => now(),
            ];

            if (!$lmp->magasin_id) {
                $pivotUpdate['magasin_id'] = $magasinId;
            }

            if ($request->type === 'entree') {
                $pivotUpdate['quantite'] = $lmp->quantite + $request->quantite;
            } else {
                $pivotUpdate['quantite_utiliser'] = $lmp->quantite_utiliser + $request->quantite;
            }

            DB::table('lot_matiere_premiere')
                ->where('id', $lmp->id)
                ->update($pivotUpdate);

            MouvementStock::create([
                'magasin_id' => $magasinId,
                'matiere_id' => $lmp->matiere_premiere_id,
                'lot_id' => $lmp->lot_id,
                'type' => $request->type,
                'quantite' => $request->quantite,
                'date_mouvement' => $request->date_mouvement,
                'gerant_id' => auth()->id(),
                'observation' => $request->observation,
            ]);

            DB::commit();

            $matiere = MatierePremiere::find($lmp->matiere_premiere_id);
            StockNotificationService::notifyRoles(
                'Mouvement matière première enregistré',
                ucfirst($request->type) . ' de ' . $request->quantite . ' ' . ($matiere->unite ?? '') . ' de ' . ($matiere->nom ?? 'matière première') . '.',
                'mouvement',
                route('comptable.matieres-premieres.index'),
                $request->type === 'sortie' ? 'orange' : 'indigo'
            );

            $disponibleApresMouvement = $lmp->quantite - $lmp->quantite_utiliser;
            if ($request->type === 'entree') {
                $disponibleApresMouvement += $request->quantite;
            } else {
                $disponibleApresMouvement -= $request->quantite;
            }

            if ($disponibleApresMouvement <= ($matiere->seuil_alerte ?? 0)) {
                StockNotificationService::notifyRoles(
                    'Alerte matière première',
                    'La matière « ' . ($matiere->nom ?? 'matière première') . ' » est sous son seuil d’alerte (' . $disponibleApresMouvement . ' disponible).',
                    'alerte',
                    route('comptable.matieres-premieres.index'),
                    'red'
                );
            }

            return redirect()->back()->with('success', 'Mouvement enregistre avec succes');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement du mouvement: ' . $e->getMessage());
        }
    }
}
