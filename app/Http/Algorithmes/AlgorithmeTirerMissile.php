<?php
/**
 * @author Fabrice Senécal & Cameron Chouinard
 */

namespace App\Http\Algorithmes;

use App\Models\Missile;
use App\Models\partie;
use PhpParser\Node\Expr\AssignOp\Concat;

class AlgorithmeTirerMissile
{
    // Tableau pour stocker les positions déjà tirées
    private $dejaTire = [];

    // Booléen pour indiquer si l'algorithme est en mode hunt
    private $huntMode = false;

    // Booléens pour indiquer si le bateau touché est vertical ou horizontal
    private $bateauToucheEstVertical = null;

    private $bateauToucheEstHorizontal = null;

    // Booléen pour indiquer si une position a été touchée avant le mode hunt
    private $toucheAvantHuntMode = false;

    // Dernière position touchée
    private $positionTouchee = null;

    // Instance statique de l'algorithme
    private static $algo = null;

    /**
     * Algorithme de tir de missile, pour l'instant est random parce que logique trop compliquée pour smol monkey brain.
     *
     * @param $partie_id partie id de la partie
     * @return string|null retourne la position du missile a tirer
     */
    public static function tirerMissile($partie_id): ?string
    {
        if (self::$algo == null )
            self::$algo = new self();

        $position = null;
        $positionDernierTouche = null;

        do {
            $position = self::$algo->tirerAuHasard();
        } while (self::$algo->checkSiPositionDejaTire($position));

        self::$algo->dejaTire[] = $position;

        return $position;

        /*
         * WIP MONKEY BRAIN CANNOT ALGORITHM, ALORS RANDOM POUR L'INSTANT
         * On a laissé ce code la ici parce qu'on pensait que ca allait peut-ëtre (super farfetched maybe but hey)
         * nous donner des brownie points.
         *
         * C'est en quelque sorte ou on pensait s'en aller avec l'algo de tir missile avant d'atteindre un mur insurmontable
         * parce que mes capacités intellectuelles sont équivalentes a celles d'un nouveau née qui se serait fait
         * échapé a la naissance.  --Cameron Chouinard
         */


        // Regarde dernier tir, si on a un touché et on était pas en Hunt Mode, on rentre en Hunt Mode.
        /*
        if (!empty(self::$algo->dejaTire)) {
            self::$algo->regarderDernierTir($partie_id, $positionDernierTouche);
        }


        // Va tirer au hasard si pas in Hunt Mode. Regarde si on a deja tiré dans cette case avant, ajoute
        // la case dans les positions tirées
        if (!self::$algo->huntMode) {
            do {
                $position = self::$algo->tirerAuHasard();
            } while (self::$algo->checkSiPositionDejaTire($position));

            self::$algo->dejaTire[] = $position;
        }

        // Si on n'est pas en mode hunt, tire au hasard en évitant les positions déjà tirées
        if (self::$algo->huntMode) {
            // Devrait toujours entrer ici la première fois d'une Hunt Mode.
            if (self::$algo->toucheAvantHuntMode) {
                $positionTouchee = self::$algo->getLastMissilePosition($partie_id);
                self::$algo->toucheAvantHuntMode = false;
            }

            list($toucheeLetter, $toucheeNumber) = explode('-', $positionTouchee);

            // Essaye les 4 directions autour de la position touchée
            if (self::$algo->bateauToucheEstHorizontal == null && self::$algo->bateauToucheEstVertical == null) {
                $position = self::$algo->huntModeSansDirection($toucheeLetter, $toucheeNumber);
            }

            if (self::$algo->bateauToucheEstHorizontal) {
                $toucheeLetter = chr(ord($toucheeLetter));
                $toucheeNumber = (int)$toucheeNumber;
            }
        }

        return $position;
        */
    }

    /**
     * Méthode privée pour tirer au hasard
     *
     * @return String une position au hasard sans les positions deja tirées
     */
    private function tirerAuHasard(): String {
        return AlgorithmePlacementBateaux::getRandomPosition();
    }

    /**
     * Méthode privée pour vérifier si une position a déjà été tirée
     *
     * @param $position $position la position qu'on désire regarder
     * @return bool retourne false si on a pas deja tiré a cette position, true sinon
     */
    private function checkSiPositionDejaTire($position): Bool {
        foreach ($this->dejaTire as $positionDedans) {
            list($dedansLetter, $dedansNumber) = explode('-', $positionDedans);
            list($positionLetter, $positionNumber) = explode('-', $position);

            if ($dedansLetter === $positionLetter && $dedansNumber === $positionNumber) {
                return true;
            }
        }
        return false;
    }

    /**
     * Méthode privée pour obtenir le résultat d'un tir à une position donnée
     *
     * @param $partie_id $partie_id le id de la partie
     * @param $position $position la position du missile qu'on désire connaitre le resultat
     * @return int|null le resultat du tir
     */
    private function getMissileResult($partie_id, $position): ?int
    {
        return Missile::where('partie_id', $partie_id)
            ->where('position', $position)
            ->first()
            ->resultat ?? null;
    }

    /**
     * Méthode privée pour obtenir le résultat du dernier tir
     *
     * @param $partie_id $partie_id le id de la partie
     * @return int le resultat du tir
     */
    private function getLastMissileResult($partie_id): int {
        return Missile::where('partie_id', $partie_id)
            ->first()->resultat;
    }

    /**
     * Méthode privée pour obtenir la position du dernier tir
     *
     * @param $partie_id $partie_id le id de la partie
     * @return String la position du dernier missile
     */
    private function getLastMissilePosition($partie_id): String {
        return Missile::where('partie_id', $partie_id)
            ->first()->position;
    }

    /**
     * Méthode privée pour vérifier si une position est dans le plateau de jeu
     *
     * @param $position $position la position a regarder
     * @return bool Si dans l'aire de jeu, true sinon false
     */
    private function estDansBoard($position): Bool {
        list($positionLetter, $positionNumber) = explode('-', $position);

        if ($positionLetter > ord('J')) {
            return false;
        }

        if ($positionNumber > 10) {
            return false;
        }

        return true;
    }

    /**
     * Méthode privée pour choisir la prochaine position à tirer en mode hunt sans direction
     *
     * @param $toucheeLetter $toucheeLetter la lettre de la position touchée avant de tomber en hunt mode
     * @param $toucheeNumber $toucheeNumber le nombre de la position touchée avant de tomber en hunt mode
     * @return String $position commence en haut et va en sens anti-horaire pour trouver la next position a hunt
     */
    private function huntModeSansDirection($toucheeLetter, $toucheeNumber): String {
        //Haut
        if ((self::$algo->estDansBoard(chr(ord($toucheeLetter) - 1) . '-' . $toucheeNumber))
            &&
            ((!self::$algo->checkSiPositionDejaTire((chr(ord($toucheeLetter) - 1) . '-' . $toucheeNumber))))) {
            $position = chr(ord($toucheeLetter) - 1) . '-' . $toucheeNumber;
        }
        //Gauche
        elseif ((self::$algo->estDansBoard(chr(ord($toucheeLetter)) . '-' . (int)$toucheeNumber - 1))
            &&
            ((!self::$algo->checkSiPositionDejaTire((chr(ord($toucheeLetter)) . '-' . (int)$toucheeNumber - 1))))) {
            $position = chr(ord($toucheeLetter)) . '-' . ((int)$toucheeNumber - 1);
        }
        //Bas
        elseif ((self::$algo->estDansBoard(chr(ord($toucheeLetter) + 1) . '-' . $toucheeNumber))
            &&
            ((!self::$algo->checkSiPositionDejaTire((chr(ord($toucheeLetter) + 1) . '-' . $toucheeNumber))))) {
            $position = chr(ord($toucheeLetter) + 1) . '-' . $toucheeNumber;
        }
        //Droite
        elseif ((self::$algo->estDansBoard(chr(ord($toucheeLetter)) . '-' . ((int)$toucheeNumber + 1)))
            &&
            ((!self::$algo->checkSiPositionDejaTire((chr(ord($toucheeLetter)) . '-' . ((int)$toucheeNumber + 1)))))) {
            $position = chr(ord($toucheeLetter)) . '-' . ((int)$toucheeNumber + 1);
        }
        //Houston we got a problem.
        else {
            $position = "Uh-oh";
        }

        return $position;
    }

    /**
     * Méthode privée pour analyser le dernier tir et ajuster les paramètres en conséquence
     *
     * @param $partie_id $partie_id le id de la partie
     * @param $positionDernierTouche $positionDernierTouche la position du dernier missile qui a touché
     * @return void
     */
    private function regarderDernierTir($partie_id, $positionDernierTouche): void {
        $resultat = self::$algo->getLastMissileResult($partie_id);

        if (self::$algo->huntMode && $resultat == 1) {
            $position1 = self::$algo->getLastMissilePosition($partie_id);

            list($dernierToucheLetter, $dernierToucheNumber) = explode('-', $positionDernierTouche);
            list($position1Letter, $position1ToucheNumber) = explode('-', $position1);

            if($position1Letter == $dernierToucheLetter)
                self::$algo->bateauToucheEstHorizontal = true;
            else
                self::$algo->bateauToucheEstVertical = true;
        }

        if (!self::$algo->huntMode && $resultat == 1) {
            self::$algo->huntMode = true;
            self::$algo->toucheAvantHuntMode = true;
        }
    }
}
