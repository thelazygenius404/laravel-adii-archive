<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tablette extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'travee_id',
    ];

    /**
     * Get the travée that owns the tablette.
     */
    public function travee()
    {
        return $this->belongsTo(Travee::class);
    }

    /**
     * Get the positions for the tablette.
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get the salle through travée.
     */
    public function salle()
    {
        return $this->hasOneThrough(Salle::class, Travee::class, 'id', 'id', 'travee_id', 'salle_id');
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
        return $this->travee->full_path . ' > ' . $this->nom;
    }

    /**
     * Check if tablette has available positions.
     */
    public function hasAvailablePositions()
    {
        return $this->positions()->where('vide', true)->exists();
    }
}