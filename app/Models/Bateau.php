<?php
/**
 * @author Fabrice Senécal & Cameron Choinard
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\Readline\Hoa\Console;

/**
 * Modèle de Bateau.
 *
 * Représente un bateau enregistré.
 */
class Bateau extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'positions'];


    protected $table = 'bateaux';

    /**
     * Définit le cast pour l'attribut "positions" afin de le transformer en tableau lors de l'accès à la base de données.
     *
     * @return Attribute L'instance de l'attribut casté
     */
    protected function positions(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value)
        );
    }

}
