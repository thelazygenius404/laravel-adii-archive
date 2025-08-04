<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
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
     * Get status badge class for utilization percentage.
     */
    public function getStatusBadgeClassAttribute()
    {
        $percentage = $this->utilisation_percentage;
        
        if ($percentage >= 90) {
            return 'bg-danger';
        } elseif ($percentage >= 70) {
            return 'bg-warning';
        } elseif ($percentage >= 30) {
            return 'bg-success';
        } else {
            return 'bg-secondary';
        }
    }

    /**
     * Get status text.
     */
    public function getStatusAttribute()
    {
        $percentage = $this->utilisation_percentage;
        
        if ($percentage >= 90) {
            return 'Pleine';
        } elseif ($percentage >= 70) {
            return 'Bien utilisée';
        } elseif ($percentage >= 30) {
            return 'Active';
        } elseif ($percentage > 0) {
            return 'Peu utilisée';
        } else {
            return 'Vide';
        }
    }

    /**
     * Check if salle is full.
     */
    public function isFull()
    {
        return $this->capacite_actuelle >= $this->capacite_max;
    }

    /**
     * Check if salle is active (has some content).
     */
    public function isActive()
    {
        return $this->capacite_actuelle > 0;
    }

    /**
     * Check if salle has low utilization.
     */
    public function hasLowUtilization()
    {
        return $this->capacite_max > 0 && $this->utilisation_percentage < 30;
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
        
        return $this;
    }

    /**
     * Scope to search salles.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhereHas('organisme', function ($orgQuery) use ($search) {
                  $orgQuery->where('nom_org', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope to filter by organisme.
     */
    public function scopeByOrganisme($query, $organismeId)
    {
        return $query->where('organisme_id', $organismeId);
    }

    /**
     * Scope to filter active salles.
     */
    public function scopeActive($query)
    {
        return $query->where('capacite_actuelle', '>', 0);
    }

    /**
     * Scope to filter full salles.
     */
    public function scopeFull($query)
    {
        return $query->whereRaw('capacite_actuelle >= capacite_max');
    }

    /**
     * Scope to filter low utilization salles.
     */
    public function scopeLowUtilization($query)
    {
        return $query->whereRaw('(capacite_actuelle / NULLIF(capacite_max, 0)) * 100 < 30')
                    ->where('capacite_max', '>', 0);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        switch ($status) {
            case 'active':
                return $query->active();
            case 'full':
                return $query->full();
            case 'low':
                return $query->lowUtilization();
            default:
                return $query;
        }
    }

    /**
     * Scope to order by utilization percentage.
     */
    public function scopeOrderByUtilization($query, $direction = 'asc')
    {
        return $query->orderByRaw("(capacite_actuelle / NULLIF(capacite_max, 0)) {$direction}");
    }

    /**
     * Get formatted capacity display.
     */
    public function getFormattedCapacityAttribute()
    {
        return "{$this->capacite_actuelle}/{$this->capacite_max}";
    }

    /**
     * Get short description for display.
     */
    public function getShortDescriptionAttribute()
    {
        if (!$this->description) {
            return null;
        }
        
        return \Illuminate\Support\Str::limit($this->description, 50);
    }
}