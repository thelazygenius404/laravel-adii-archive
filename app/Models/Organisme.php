<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisme extends Model
{
    use HasFactory;

    protected $table = 'organismes';

    protected $fillable = [
        'nom_org',
    ];

    /**
     * Get the entite productrices for the organisme.
     */
    public function entiteProductrices()
    {
        return $this->hasMany(EntiteProductrice::class, 'id_organisme');
    }

    /**
     * Get the users for the organisme through entite productrices.
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, EntiteProductrice::class, 'id_organisme', 'id_entite_productrices');
    }
}