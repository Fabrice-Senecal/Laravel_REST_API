<?php
/**
 * @author Fabrice Senécal & Cameron Choinard
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle de Partie.
 *
 * Représente un cas enregistré.
 */
class partie extends Model
{
    use HasFactory;

    protected $fillable = [ 'adversaire', 'bateaux'];

    protected $table = 'parties';

    /**
     * Définit le cast pour l'attribut "bateaux" afin de le transformer en tableau lors de l'accès à la base de données.
     *
     * @return Attribute L'instance de l'attribut casté
     */
    protected function bateaux(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value)
        );
    }

}
