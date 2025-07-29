<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'titre',
        'date_creation',
        'cote_classement',
        'description',
        'mots_cles',
        'date_elimination_prevue',
        'statut',
        'type_piece',
        'disponible',
        'boite_id',
        'calendrier_conservation_id',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'date_elimination_prevue' => 'date',
        'disponible' => 'boolean',
    ];

    /**
     * Get the boite that owns the dossier.
     */
    public function boite()
    {
        return $this->belongsTo(Boite::class);
    }

    /**
     * Get the calendrier conservation that owns the dossier.
     */
    public function calendrierConservation()
    {
        return $this->belongsTo(CalendrierConservation::class);
    }

    /**
     * Get the position through boite.
     */
    public function position()
    {
        return $this->hasOneThrough(Position::class, Boite::class, 'id', 'id', 'boite_id', 'position_id');
    }

    /**
     * Get the full location path.
     */
    public function getFullLocationAttribute()
    {
        return $this->boite->full_location . ' > Dossier ' . $this->numero;
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute()
    {
        return match($this->statut) {
            'actif' => 'Actif',
            'archive' => 'Archivé',
            'elimine' => 'Éliminé',
            'en_cours' => 'En cours',
            default => $this->statut
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->statut) {
            'actif' => 'bg-success',
            'archive' => 'bg-info',
            'elimine' => 'bg-danger',
            'en_cours' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Check if dossier is due for elimination.
     */
    public function isDueForElimination()
    {
        return $this->date_elimination_prevue && $this->date_elimination_prevue <= now();
    }

    /**
     * Check if dossier is near elimination date.
     */
    public function isNearElimination($days = 30)
    {
        return $this->date_elimination_prevue && 
               $this->date_elimination_prevue <= now()->addDays($days);
    }

    /**
     * Calculate elimination date based on conservation rules.
     */
    public function calculateEliminationDate()
    {
        if (!$this->calendrierConservation) {
            return null;
        }

        $totalYears = $this->calendrierConservation->archive_courant + 
                     $this->calendrierConservation->archive_intermediaire;

        return $this->date_creation->addYears($totalYears);
    }

    /**
     * Get days until elimination.
     */
    public function getDaysUntilEliminationAttribute()
    {
        if (!$this->date_elimination_prevue) {
            return null;
        }

        return now()->diffInDays($this->date_elimination_prevue, false);
    }

    /**
     * Get conservation years elapsed.
     */
    public function getConservationYearsElapsedAttribute()
    {
        return $this->date_creation->diffInYears(now());
    }

    /**
     * Scope to search dossiers.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('numero', 'LIKE', "%{$search}%")
              ->orWhere('titre', 'LIKE', "%{$search}%")
              ->orWhere('cote_classement', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('mots_cles', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    /**
     * Scope to get available dossiers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('disponible', true);
    }

    /**
     * Scope to get dossiers due for elimination.
     */
    public function scopeDueForElimination($query)
    {
        return $query->where('date_elimination_prevue', '<=', now())
                    ->where('statut', '!=', 'elimine');
    }

    /**
     * Scope to get dossiers near elimination.
     */
    public function scopeNearElimination($query, $days = 30)
    {
        return $query->where('date_elimination_prevue', '<=', now()->addDays($days))
                    ->where('date_elimination_prevue', '>', now())
                    ->where('statut', '!=', 'elimine');
    }

    /**
     * Mark dossier as eliminated.
     */
    public function markAsEliminated()
    {
        $this->update([
            'statut' => 'elimine',
            'disponible' => false
        ]);

        // Update boite dossier count
        $this->boite->updateNbrDossiers();
    }

    /**
     * Archive the dossier.
     */
    public function archive()
    {
        $this->update(['statut' => 'archive']);
    }
}
