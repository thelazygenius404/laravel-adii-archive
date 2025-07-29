<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Travee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'salle_id',
    ];

    /**
     * Get the salle that owns the travée.
     */
    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    /**
     * Get the tablettes for the travée.
     */
    public function tablettes()
    {
        return $this->hasMany(Tablette::class);
    }

    /**
     * Get all positions through tablettes.
     */
    public function positions()
    {
        return $this->hasManyThrough(Position::class, Tablette::class);
    }

    /**
     * Get occupied positions count.
     */
    public function getPositionsOccupeesAttribute()
    {
        return $this->positions()->where('vide', false)->count();
    }

    /**
     * Get total positions count.
     */
    public function getTotalPositionsAttribute()
    {
        return $this->positions()->count();
    }

    /**
     * Get utilization percentage.
     */
    public function getUtilisationPercentageAttribute()
    {
        $total = $this->total_positions;
        if ($total == 0) {
            return 0;
        }
        return round(($this->positions_occupees / $total) * 100, 2);
    }

    /**
     * Get the full location path.
     */
    public function getFullPathAttribute()
    {
        return $this->salle->nom . ' > ' . $this->nom;
    }
}