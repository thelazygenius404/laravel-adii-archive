<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boite extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'code_thematique',
        'code_topo',
        'capacite',
        'nbr_dossiers',
        'detruite',
        'position_id',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'nbr_dossiers' => 'integer',
        'detruite' => 'boolean',
    ];

    /**
     * Get the position that owns the boite.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the dossiers for the boite.
     */
    public function dossiers()
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * Get the tablette through position.
     */
    public function tablette()
    {
        return $this->hasOneThrough(Tablette::class, Position::class, 'id', 'id', 'position_id', 'tablette_id');
    }

    /**
     * Get the travée through position and tablette.
     */
    public function travee()
    {
        return $this->hasOneThrough(
            Travee::class, 
            Position::class, 
            'id', 
            'id', 
            'position_id', 
            'tablette_id'
        )->join('tablettes', 'positions.tablette_id', '=', 'tablettes.id')
         ->join('travees', 'tablettes.travee_id', '=', 'travees.id');
    }

    /**
     * Get the salle through position, tablette, and travée.
     */
    public function salle()
    {
        return $this->hasOneThrough(
            Salle::class, 
            Position::class, 
            'id', 
            'id', 
            'position_id', 
            'tablette_id'
        )->join('tablettes', 'positions.tablette_id', '=', 'tablettes.id')
         ->join('travees', 'tablettes.travee_id', '=', 'travees.id')
         ->join('salles', 'travees.salle_id', '=', 'salles.id');
    }

    /**
     * Get the full location path.
     */
    public function getFullLocationAttribute()
    {
        return $this->position->full_path . ' > Boîte ' . $this->numero;
    }

    /**
     * Get capacity utilization percentage.
     */
    public function getUtilisationPercentageAttribute()
    {
        if ($this->capacite == 0) {
            return 0;
        }
        return round(($this->nbr_dossiers / $this->capacite) * 100, 2);
    }

    /**
     * Get remaining capacity.
     */
    public function getCapaciteRestanteAttribute()
    {
        return $this->capacite - $this->nbr_dossiers;
    }

    /**
     * Check if boite is full.
     */
    public function isFull()
    {
        return $this->nbr_dossiers >= $this->capacite;
    }

    /**
     * Check if boite has space.
     */
    public function hasSpace()
    {
        return $this->nbr_dossiers < $this->capacite && !$this->detruite;
    }

    /**
     * Update dossiers count.
     */
    public function updateNbrDossiers()
    {
        $count = $this->dossiers()->where('statut', '!=', 'elimine')->count();
        $this->update(['nbr_dossiers' => $count]);
    }

    /**
     * Scope to search boites.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('numero', 'LIKE', "%{$search}%")
                    ->orWhere('code_thematique', 'LIKE', "%{$search}%")
                    ->orWhere('code_topo', 'LIKE', "%{$search}%");
    }

    /**
     * Scope to get non-destroyed boites.
     */
    public function scopeActive($query)
    {
        return $query->where('detruite', false);
    }

    /**
     * Scope to get destroyed boites.
     */
    public function scopeDestroyed($query)
    {
        return $query->where('detruite', true);
    }

    /**
     * Mark boite as destroyed (logical deletion).
     */
    public function markAsDestroyed()
    {
        $this->update(['detruite' => true]);
        
        // Mark position as free
        $this->position->markAsFree();
        
        // Update all dossiers status
        $this->dossiers()->update(['statut' => 'elimine', 'disponible' => false]);
    }

    /**
     * Restore destroyed boite.
     */
    public function restoreFromDestroyed()
    {
        // Check if position is still available
        if (!$this->position->vide) {
            throw new \Exception('La position est déjà occupée par une autre boîte.');
        }

        $this->update(['detruite' => false]);
        
        // Mark position as occupied
        $this->position->markAsOccupied();
        
        // Restore dossiers status (optional - might need manual review)
        $this->dossiers()->where('statut', 'elimine')
                         ->update(['statut' => 'archive', 'disponible' => true]);
    }

    /**
     * Check if boite can accommodate new dossiers.
     */
    public function canAccommodate($numberOfDossiers)
    {
        return !$this->detruite && ($this->nbr_dossiers + $numberOfDossiers) <= $this->capacite;
    }

    /**
     * Add dossier to boite.
     */
    public function addDossier($dossier)
    {
        if (!$this->canAccommodate(1)) {
            throw new \Exception('La boîte n\'a pas d\'espace suffisant.');
        }

        $dossier->update(['boite_id' => $this->id]);
        $this->updateNbrDossiers();
    }

    /**
     * Remove dossier from boite.
     */
    public function removeDossier($dossier)
    {
        if ($dossier->boite_id !== $this->id) {
            throw new \Exception('Ce dossier n\'appartient pas à cette boîte.');
        }

        $dossier->update(['boite_id' => null]);
        $this->updateNbrDossiers();
    }

    /**
     * Get efficiency score (how well the boite is utilized).
     */
    public function getEfficiencyScoreAttribute()
    {
        if ($this->capacite == 0) {
            return 0;
        }

        $utilization = $this->utilisation_percentage;
        
        // Perfect score at 80-90% utilization
        if ($utilization >= 80 && $utilization <= 90) {
            return 100;
        }
        
        // Good score at 60-95% utilization
        if ($utilization >= 60 && $utilization <= 95) {
            return 80;
        }
        
        // Decent score at 40-99% utilization
        if ($utilization >= 40 && $utilization <= 99) {
            return 60;
        }
        
        // Poor score for very low or 100% utilization
        return 30;
    }

    /**
     * Get color indicator for utilization.
     */
    public function getUtilizationColorAttribute()
    {
        $percentage = $this->utilisation_percentage;
        
        if ($percentage == 0) {
            return 'gray';
        } elseif ($percentage < 30) {
            return 'red';
        } elseif ($percentage < 60) {
            return 'orange';
        } elseif ($percentage < 90) {
            return 'green';
        } else {
            return 'blue'; // Full or near full
        }
    }

    /**
     * Scope to get boites with specific utilization range.
     */
    public function scopeUtilizationBetween($query, $min, $max)
    {
        return $query->whereRaw('(nbr_dossiers * 100 / capacite) BETWEEN ? AND ?', [$min, $max]);
    }

    /**
     * Scope to get overfull boites.
     */
    public function scopeOverfull($query)
    {
        return $query->whereRaw('nbr_dossiers > capacite');
    }

    /**
     * Scope to get empty boites.
     */
    public function scopeEmpty($query)
    {
        return $query->where('nbr_dossiers', 0);
    }

    /**
     * Get boites that could be consolidated.
     */
    public static function getConsolidationCandidates($threshold = 50)
    {
        return static::active()
                    ->where('nbr_dossiers', '>', 0)
                    ->whereRaw('(nbr_dossiers * 100 / capacite) < ?', [$threshold])
                    ->with(['position.tablette.travee.salle.organisme', 'dossiers'])
                    ->orderBy('utilisation_percentage')
                    ->get();
    }

    /**
     * Get the age of the boite in days.
     */
    public function getAgeInDaysAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get the last activity date (last dossier added/removed).
     */
    public function getLastActivityAttribute()
    {
        $lastDossier = $this->dossiers()->latest('updated_at')->first();
        return $lastDossier ? $lastDossier->updated_at : $this->created_at;
    }

    /**
     * Check if boite is inactive (no activity for specified days).
     */
    public function isInactive($days = 365)
    {
        return $this->last_activity->diffInDays(now()) > $days;
    }

    /**
     * Get recommended actions for this boite.
     */
    public function getRecommendedActionsAttribute()
    {
        $actions = [];
        $utilization = $this->utilisation_percentage;

        if ($this->detruite) {
            $actions[] = 'Boîte détruite - Aucune action requise';
            return $actions;
        }

        if ($utilization == 0) {
            $actions[] = 'Boîte vide - Considérer la suppression ou le réemploi';
        } elseif ($utilization < 30) {
            $actions[] = 'Faible utilisation - Envisager la consolidation';
        } elseif ($utilization > 95) {
            $actions[] = 'Boîte presque pleine - Préparer l\'archivage ou une nouvelle boîte';
        } elseif ($utilization >= 100) {
            $actions[] = 'Boîte pleine - Archivage requis';
        }

        if ($this->isInactive(180)) {
            $actions[] = 'Pas d\'activité récente - Vérifier les dossiers';
        }

        if ($this->dossiers()->dueForElimination()->count() > 0) {
            $actions[] = 'Contient des dossiers à éliminer';
        }

        if (empty($actions)) {
            $actions[] = 'Boîte en bon état - Aucune action particulière requise';
        }

        return $actions;
    }

    /**
     * Generate barcode for the boite.
     */
    public function generateBarcode()
    {
        // Simple barcode generation based on numero and position
        $data = $this->numero . '-' . $this->position_id;
        return strtoupper(md5($data));
    }

    /**
     * Get QR code data for the boite.
     */
    public function getQrCodeDataAttribute()
    {
        return json_encode([
            'type' => 'boite',
            'id' => $this->id,
            'numero' => $this->numero,
            'position' => $this->position->nom,
            'localisation' => $this->full_location,
            'capacite' => $this->capacite,
            'nbr_dossiers' => $this->nbr_dossiers,
            'generated_at' => now()->toISOString()
        ]);
    }
}