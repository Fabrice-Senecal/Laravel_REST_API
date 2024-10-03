<?php
/**
 * @author Fabrice Senécal & Cameron Chouinard
 */

namespace App\Http\Algorithmes;

use App\Models\Bateau;
use Nette\Utils\ArrayList;

class AlgorithmePlacementBateaux
{
    // Tableau pour stocker les positions des bateaux placés
    private $bateauxPositions = [];

    /**
     * Méthode statique pour placer les bateaux dans une partie
     *
     * @return void
     */
    public static function placerBateaux(): void
    {

        $algo = new self();


        $types = ['PORTE-AVIONS', 'CUIRASSÉ', 'DESTROYER', 'SOUS-MARIN', 'PATROUILLEUR'];


        $algo->creerEtPlacerBateaux($types);


    }

    /**
     * Obtient la taille d'un bateau selon son type
     *
     * @param string $type Le type de bateau
     * @return int La taille du bateau
     */
    private function getShipSize($type): Int{
        $shipSizes = [
            'PORTE-AVIONS' => 5,
            'CUIRASSÉ' => 4,
            'DESTROYER' => 3,
            'SOUS-MARIN' => 3,
            'PATROUILLEUR' => 2
        ];

        return $shipSizes[$type];
    }

    /**
     * Crée et place les bateaux dans la partie
     *
     * @param array $types Les types de bateaux à placer
     * @return void
     */
    public function creerEtPlacerBateaux($types): void {


        foreach ($types as $type) {
            $shipSize = $this->getShipSize($type);
            $positions = $this->generateShipPositions($shipSize);
            $orientation = $this->placerBateau($positions, $shipSize);

            while ($orientation === null) {
                $positions = $this->generateShipPositions($shipSize);
                $orientation = $this->placerBateau($positions, $shipSize);
            }

            $ship =  Bateau::create([
                'type' => $type,
                'positions' => $positions,
            ]);
            $this->bateauxPositions[] = [$ship->positions];
        }
    }

    /**
     * Vérifie si un bateau peut être placé dans une direction à partir d'une position donnée
     *
     * @param array $positions Les positions du bateau
     * @param int $shipSize La taille du bateau
     * @return string|null La direction dans laquelle le bateau peut être placé (horizontal ou vertical)
     */
    private function placerBateau($positions, $shipSize): ?string {
        if (!empty($positions)) {
            $horizontalInBounds = $this->estDansBoard($positions[0], $shipSize, 'h');
            $horizontalOverlaps = $this->overlapDeBateau($positions);
            $verticalInBounds = $this->estDansBoard($positions[0], $shipSize, 'v');
            $verticalOverlaps = $this->overlapDeBateau($positions);

            if ($horizontalInBounds && !$horizontalOverlaps) {
                return 'h';
            } elseif ($verticalInBounds && !$verticalOverlaps) {
                return 'v';
            }
        }
        return null;
    }

    /**
     * Génère les positions pour un bateau de taille donnée
     *
     * @param int $shipSize La taille du bateau
     * @return array Les positions du bateau
     */
    private function generateShipPositions($shipSize): array
    {
        $positions = [];
        $startingPosition = $this->getRandomPosition();

        $random = mt_rand(0,1);

        if ($random == 0) {
            $orientation = 'h';
        } else {
            $orientation = 'v';
        }

        list($startPositionLetter, $startPositionNumber) = explode('-', $startingPosition);
        for ($i = 0; $i < $shipSize; $i++) {
            if ($orientation === 'v') {
                $positions[] = $startPositionLetter . '-' . ((int)$startPositionNumber + $i);
            } elseif ($orientation === 'h') {
                $positions[] = chr(ord($startPositionLetter) + $i) . '-' . $startPositionNumber;
            }
        }

        return $positions;
    }

    /**
     * Vérifie si les positions d'un nouveau bateau overlappent celles des bateaux déjà placés
     *
     * @param array $positions Les positions du nouveau bateau
     * @return bool True si les positions overlappent, sinon False
     */
    private function overlapDeBateau($positions): Bool {
        $alreadyTakenPositions = [];
        foreach ($this->bateauxPositions as $ship) {
            foreach ($ship as $shipPositions) {
                foreach ($shipPositions as $position) {
                    $alreadyTakenPositions[] = $position;
                }
            }
        }

        foreach ($positions as $position1) {
            if (in_array($position1, $alreadyTakenPositions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si un bateau peut être placé dans les limites du plateau de jeu à partir d'une position donnée
     *
     * @param string $startPosition La position de départ
     * @param int $shipSize La taille du bateau
     * @param string $orientation L'orientation du bateau (horizontal ou vertical)
     * @return bool True si le bateau peut être placé dans les limites, sinon False
     */
    private function estDansBoard($startPosition, $shipSize, $orientation): Bool {
        list($startPositionLetter, $startPositionNumber) = explode('-', $startPosition);
        $lastPositionLetterAscii = ord($startPositionLetter) + $shipSize - 1;


            if ($lastPositionLetterAscii > ord('J')) {
                return false;
            }

            if ((int)$startPositionNumber + $shipSize - 1 > 10) {
                return false;
            }

        return true;
    }

    /**
     * Génère une position aléatoire sur le plateau de jeu
     *
     * @return string La position aléatoire générée
     */
    public static function getRandomPosition(): String {
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $columns = range(1, 10);
        $randomColumn = $columns[array_rand($columns)];
        $randomRow = $rows[array_rand($rows)];
        return $randomRow . '-' . $randomColumn;
    }


}
