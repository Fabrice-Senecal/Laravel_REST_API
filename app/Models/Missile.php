<?php
/**
 * @author Fabrice Senécal & Cameron Choinard
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle de Missile.
 *
 * Représente un cas enregistré.
 */
class Missile extends Model
{
    use HasFactory;
    protected  $fillable = ['position','partie_id', 'resultat'];

}
