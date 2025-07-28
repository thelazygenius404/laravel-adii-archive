<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendrierConservation extends Model
{
    use HasFactory;

    protected $table = 'calendrier_conservation';

    protected $fillable = [
        'NO_regle',
        'delais_legaux',
        'nature_dossier',
        'reference',
        'plan_classement_id',
        'sort_final',
        'archive_courant',
        'archive_intermediaire',
        'observation',
    ];

    protected $casts = [
        'delais_legaux' => 'integer',
        'archive_courant' => 'integer',
        'archive_intermediaire' => 'integer',
    ];

    /**
     * Get the plan classement that owns this calendrier.
     */
    public function planClassement()
    {
        return $this->belongsTo(PlanClassement::class, 'plan_classement_id');
    }

    /**
     * Scope to search by various fields.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('NO_regle', 'LIKE', "%{$search}%")
              ->orWhere('nature_dossier', 'LIKE', "%{$search}%")
              ->orWhere('reference', 'LIKE', "%{$search}%")
              ->orWhereHas('planClassement', function($query) use ($search) {
                  $query->where('objet_classement', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope to filter by sort final.
     */
    public function scopeBySortFinal($query, $sortFinal)
    {
        return $query->where('sort_final', $sortFinal);
    }

    /**
     * Get the total conservation duration.
     */
    public function getTotalDurationAttribute()
    {
        return $this->archive_courant + $this->archive_intermediaire;
    }

    /**
     * Get conservation status based on sort final.
     */
    public function getStatusAttribute()
    {
        return match($this->sort_final) {
            'C' => 'Conservation',
            'E' => 'Élimination',
            'T' => 'Tri',
            default => 'Non défini'
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->sort_final) {
            'C' => 'bg-success',
            'E' => 'bg-danger',
            'T' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Get short nature description.
     */
    public function getShortNatureAttribute()
    {
        return strlen($this->nature_dossier) > 50 
            ? substr($this->nature_dossier, 0, 50) . '...' 
            : $this->nature_dossier;
    }
}