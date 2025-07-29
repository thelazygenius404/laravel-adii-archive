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

    /**
     * Get the salles for the organisme.
     */
    public function salles()
    {
        return $this->hasMany(Salle::class);
    }

    /**
     * Get all positions through salles.
     */
    public function positions()
    {
        return $this->hasManyThrough(
            Position::class,
            Salle::class,
            'organisme_id',
            'tablette_id',
            'id',
            'id'
        )->join('travees', 'salles.id', '=', 'travees.salle_id')
         ->join('tablettes', 'travees.id', '=', 'tablettes.travee_id');
    }

    /**
     * Get total storage capacity.
     */
    public function getTotalCapacityAttribute()
    {
        return $this->salles()->sum('capacite_max');
    }

    /**
     * Get current utilization.
     */
    public function getCurrentUtilizationAttribute()
    {
        return $this->salles()->sum('capacite_actuelle');
    }

    /**
     * Get utilization percentage.
     */
    public function getUtilizationPercentageAttribute()
    {
        $total = $this->total_capacity;
        if ($total == 0) {
            return 0;
        }
        return round(($this->current_utilization / $total) * 100, 2);
    }
}