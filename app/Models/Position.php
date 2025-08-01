<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'vide',
        'tablette_id',
    ];

    protected $casts = [
        'vide' => 'boolean',
    ];

    /**
     * Get the tablette that owns the position.
     */
    public function tablette()
    {
        return $this->belongsTo(Tablette::class);
    }

    /**
     * Get the boite for the position.
     */
    public function boite()
    {
        return $this->hasOne(Boite::class);
    }

    /**
     * Get the travée through tablette.
     */
    public function travee()
    {
        return $this->hasOneThrough(Travee::class, Tablette::class, 'id', 'id', 'tablette_id', 'travee_id');
    }

    /**
     * Get the salle through tablette and travée.
     */
    public function salle()
    {
        return $this->hasOneThrough(
            Salle::class, 
            Tablette::class, 
            'id', 
            'id', 
            'tablette_id', 
            'travee_id'
        )->join('travees', 'tablettes.travee_id', '=', 'travees.id')
         ->join('salles', 'travees.salle_id', '=', 'salles.id');
    }

    /**
     * Get the full location path.
     */
    public function getFullPathAttribute()
    {
        return $this->tablette->full_path . ' > ' . $this->nom;
    }

    /**
     * Mark position as occupied.
     */
    public function markAsOccupied()
    {
        $this->update(['vide' => false]);
        
        // Update salle capacity
        $salle = $this->tablette->travee->salle;
        $salle->updateCapaciteActuelle();
    }

    /**
     * Mark position as free.
     */
    public function markAsFree()
    {
        $this->update(['vide' => true]);
        
        // Update salle capacity
        $salle = $this->tablette->travee->salle;
        $salle->updateCapaciteActuelle();
    }

    /**
     * Scope to get available positions.
     */
    public function scopeAvailable($query)
    {
        return $query->where('vide', true);
    }

    /**
     * Scope to get occupied positions.
     */
    public function scopeOccupied($query)
    {
        return $query->where('vide', false);
    }
    public function prevPosition()
    {
        return $this->tablette->positions()
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function nextPosition()
    {
        return $this->tablette->positions()
            ->where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first();
    }
}