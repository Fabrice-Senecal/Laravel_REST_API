<?php
/**
 * @author Fabrice Senécal & Cameron Choinard
 */

namespace App\Http\Controllers;

use App\Http\Algorithmes\AlgorithmePlacementBateaux;
use App\Http\Algorithmes\AlgorithmeTirerMissile;
use App\Http\Resources\MissileResource;
use App\Http\Resources\PartieResource;
use App\Models\Bateau;
use App\Models\Missile;
use App\Models\partie;
use Illuminate\Http\Request;

class PartieController extends Controller
{

    /**
     * Crée une nouvelle partie en plaçant les bateaux et en enregistrant les détails de la partie.
     *
     * @param Request $request Les détails de la nouvelle partie (adversaire)
     * @return PartieResource La ressource représentant la nouvelle partie créée
     */
    public function nouvellePartie(Request $request): PartieResource
    {
        AlgorithmePlacementBateaux::placerBateaux();
        $bateaux = Bateau::all();

        $listeBateau = [];

        foreach ($bateaux as $bateau) {
            $listeBateau[] = [$bateau->type, $bateau->positions];
        }

        $partie = partie::create([
            'adversaire' => $request->adversaire,
            'bateaux' => $listeBateau
        ]);

        return new PartieResource($partie);
    }

    /**
     * Tire un missile pendant une partie en utilisant l'algorithme de tir de missile.
     *
     * @param int $partie L'identifiant de la partie en cours
     * @return MissileResource La ressource représentant le missile tiré
     */
    public function tirerMissile($partie): MissileResource
    {
        $missile = Missile::create([
            'position' => AlgorithmeTirerMissile::tirerMissile($partie),
            'partie_id' => $partie
        ]);
        return new MissileResource($missile);
    }

    /**
     * Met à jour le résultat d'un tir de missile reçu pendant une partie.
     *
     * @param Request $request Les détails du tir de missile reçu (position et résultat)
     * @param int $partie L'identifiant de la partie en cours
     * @param string $coordonees Les coordonnées de la position du tir de missile reçu
     * @return MissileResource La ressource représentant le missile mis à jour
     */
    public function recevoirTire(Request $request, $partie, $coordonees): MissileResource
    {

        $missile = Missile::where('position', $coordonees)
            ->where('partie_id', $partie)
            ->first();

        $missile->update(['resultat' => $request->resultat]);

        return new MissileResource($missile);
    }

    /**
     * Supprime la partie spécifiée du stockage.
     *
     * @param partie $partie La partie à supprimer
     * @return PartieResource La ressource représentant la partie supprimée
     */
    public function destroy(partie $partie): PartieResource
    {
        $partie->delete();
        return new PartieResource($partie);
    }
}
