<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'capacite_max',
        'capacite_actuelle',
        'organisme_id',
    ];

    protected $casts = [
        'capacite_max' => 'integer',
        'capacite_actuelle' => 'integer',
    ];

    /**
     * Get the organisme that owns the salle.
     */
    public function organisme()
    {
        return $this->belongsTo(Organisme::class);
    }

    /**
     * Get the travées for the salle.
     */
    public function travees()
    {
        return $this->hasMany(Travee::class);
    }

    /**
     * Get all tablettes through travées.
     */
    public function tablettes()
    {
        return $this->hasManyThrough(Tablette::class, Travee::class);
    }

    /**
     * Get all positions through travées and tablettes.
     */
    public function positions()
    {
        return $this->hasManyThrough(Position::class, Tablette::class, 'travee_id', 'tablette_id', 'id', 'id')
                   ->join('travees', 'tablettes.travee_id', '=', 'travees.id')
                   ->where('travees.salle_id', $this->id);
    }

    /**
     * Get capacity utilization percentage.
     */
    public function getUtilisationPercentageAttribute()
    {
        if ($this->capacite_max == 0) {
            return 0;
        }
        return round(($this->capacite_actuelle / $this->capacite_max) * 100, 2);
    }

    /**
     * Get remaining capacity.
     */
    public function getCapaciteRestanteAttribute()
    {
        return $this->capacite_max - $this->capacite_actuelle;
    }

    /**
     * Get total capacity (alias for capacite_max for backward compatibility).
     */
    public function getCapaciteTotaleAttribute()
    {
        return $this->capacite_max;
    }

    /**
     * Get positions occupied (alias for capacite_actuelle).
     */
    public function getPositionsOccupeesAttribute()
    {
        return $this->capacite_actuelle;
    }

    /**
     * Get tablettes count.
     */
    public function getTablettesCountAttribute()
    {
        return $this->tablettes()->count();
    }

    /**
     * Get last activity (placeholder - implement based on your needs).
     */
    public function getDerniereActiviteAttribute()
    {
        // Return null for now - you can implement this based on your activity tracking
        return null;
    }

    /**
     * Check if salle is full.
     */
    public function isFull()
    {
        return $this->capacite_actuelle >= $this->capacite_max;
    }

    /**
     * Update current capacity based on actual positions.
     */
    public function updateCapaciteActuelle()
    {
        $occupiedPositions = Position::whereHas('tablette.travee', function ($query) {
            $query->where('salle_id', $this->id);
        })->where('vide', false)->count();

        $this->update(['capacite_actuelle' => $occupiedPositions]);
    }

    /**
     * Scope to search salles.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nom', 'LIKE', "%{$search}%");
    }
}