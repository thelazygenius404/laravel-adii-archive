<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CalendrierConservation extends Model
{
    use HasFactory;

    protected $table = 'calendrier_conservation';

    protected $fillable = [
        'pieces_constituant',
        'principal_secondaire',
        'delai_legal',
        'reference_juridique',
        'archives_courantes',
        'archives_intermediaires',
        'sort_final',
        'plan_classement_code',
        'observation',
    ];

    /**
     * Get the plan classement that owns this calendrier.
     */
    public function planClassement()
    {
        return $this->belongsTo(PlanClassement::class, 'plan_classement_code', 'code_classement');
    }

    /**
     * Scope to search by various fields.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('plan_classement_code', 'LIKE', "%{$search}%")
              ->orWhere('pieces_constituant', 'LIKE', "%{$search}%")
              ->orWhere('reference_juridique', 'LIKE', "%{$search}%")
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
     * Scope to filter by principal/secondaire.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('principal_secondaire', $type);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('plan_classement_code', 'LIKE', $category . '%');
    }

    /**
     * Scope to get rules with validation issues.
     */
    public function scopeWithValidationIssues($query)
    {
        return $query->get()->filter(function($rule) {
            return !empty($rule->getValidationIssues());
        });
    }

    /**
     * Get the total conservation duration in numeric format.
     * Tries to extract numeric values from text fields.
     */
    public function getTotalDurationAttribute()
    {
        $courante = $this->extractNumericValue($this->archives_courantes);
        $intermediaire = $this->extractNumericValue($this->archives_intermediaires);
        
        return $courante + $intermediaire;
    }

    /**
     * Extract numeric value from a text field.
     */
    private function extractNumericValue($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        
        // Try to extract number from text like "3 ans", "1 an", etc.
        preg_match('/(\d+)/', $value, $matches);
        return isset($matches[1]) ? (int) $matches[1] : 0;
    }

    /**
     * Get conservation status based on sort final.
     */
    public function getStatusAttribute()
    {
        return match($this->sort_final) {
            'C' => 'Conservation',
            'D' => 'Destruction',
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
            'D' => 'bg-danger',
            'T' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the rule number (same as plan_classement_code).
     */
    public function getNoRegleAttribute()
    {
        return $this->plan_classement_code;
    }

    /**
     * Get the type of dossiers from plan classement.
     */
    public function getTypeDossiersAttribute()
    {
        return $this->planClassement ? $this->planClassement->objet_classement : '';
    }

    /**
     * Get short nature description.
     */
    public function getShortNatureAttribute()
    {
        $description = $this->planClassement ? $this->planClassement->objet_classement : '';
        return strlen($description) > 50 
            ? substr($description, 0, 50) . '...' 
            : $description;
    }

    /**
     * Get full legal delay as text.
     */
    public function getDelaiLegalTextAttribute()
    {
        if (!$this->delai_legal || $this->delai_legal === '_') {
            return 'Non défini';
        }
        
        if (is_numeric($this->delai_legal)) {
            return $this->delai_legal . ' ans';
        }
        
        return $this->delai_legal;
    }

    /**
     * Get archives courantes as numeric value for calculations.
     */
    public function getArchivesCourantesNumericAttribute()
    {
        return $this->extractNumericValue($this->archives_courantes);
    }

    /**
     * Get archives intermédiaires as numeric value for calculations.
     */
    public function getArchivesIntermediairesNumericAttribute()
    {
        return $this->extractNumericValue($this->archives_intermediaires);
    }

    /**
     * Get formatted conservation duration.
     */
    public function getDureeConservationAttribute()
    {
        $courante = $this->archives_courantes_numeric;
        $intermediaire = $this->archives_intermediaires_numeric;
        
        if ($courante === 0 && $intermediaire === 0) {
            return 'Selon validité';
        }
        
        $parts = [];
        if ($courante > 0) {
            $parts[] = $courante . ' an' . ($courante > 1 ? 's' : '') . ' (courante)';
        }
        if ($intermediaire > 0) {
            $parts[] = $intermediaire . ' an' . ($intermediaire > 1 ? 's' : '') . ' (intermédiaire)';
        }
        
        return implode(' + ', $parts);
    }

    /**
     * Check if conservation period is coherent with legal delay.
     */
    public function isCoherentWithLegalDelay()
    {
        if (!$this->delai_legal || $this->delai_legal === '_' || !is_numeric($this->delai_legal)) {
            return true; // Cannot validate if no numeric legal delay
        }
        
        $legalDelay = (int) $this->delai_legal;
        $totalConservation = $this->total_duration;
        
        return $totalConservation >= $legalDelay;
    }

    /**
     * Get validation issues for this rule.
     */
    public function getValidationIssues()
    {
        $issues = [];
        
        // Check legal delay coherence
        if (!$this->isCoherentWithLegalDelay()) {
            $issues[] = 'Durée totale de conservation inférieure au délai légal requis';
        }
        
        // Check if no conservation period is defined
        if ($this->archives_courantes_numeric === 0 && $this->archives_intermediaires_numeric === 0) {
            if (!str_contains(strtolower($this->archives_courantes), 'validité')) {
                $issues[] = 'Aucune durée de conservation numérique définie';
            }
        }
        
        // Check if reference is missing for important documents
        if ($this->principal_secondaire === 'P' && (!$this->reference_juridique || $this->reference_juridique === '_')) {
            $issues[] = 'Référence juridique manquante pour un dossier principal';
        }
        
        // Check sort final logic
        if ($this->sort_final === 'C' && $this->total_duration < 30) {
            $issues[] = 'Conservation définitive avec une durée courte (< 30 ans)';
        }
        
        return $issues;
    }

    /**
     * Get category code from plan classement code.
     */
    public function getCategoryCodeAttribute()
    {
        $parts = explode('.', $this->plan_classement_code);
        return $parts[0] ?? '';
    }

    /**
     * Get category name.
     */
    public function getCategoryNameAttribute()
    {
        $categoryNames = [
            '100' => 'Organisation et administration',
            '510' => 'Régimes économiques douaniers',
            '520' => 'Transit et transport',
            '530' => 'Contentieux douanier',
            '540' => 'Recours et réclamations',
            '550' => 'Contrôle et vérification',
            '560' => 'Facilitations commerciales',
            '610' => 'Dédouanement des marchandises',
        ];

        return $categoryNames[$this->category_code] ?? 'Autre catégorie';
    }

    /**
     * Get priority level based on type and sort final.
     */
    public function getPriorityLevelAttribute()
    {
        if ($this->sort_final === 'C') return 'Haute'; // Conservation définitive
        if ($this->principal_secondaire === 'P') return 'Moyenne'; // Dossier principal
        return 'Normale'; // Dossier secondaire
    }

    /**
     * Get priority badge class.
     */
    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority_level) {
            'Haute' => 'bg-danger',
            'Moyenne' => 'bg-warning',
            'Normale' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Check if rule requires review.
     */
    public function requiresReview()
    {
        // Rules that might need review
        $reviewReasons = [];
        
        if (!empty($this->getValidationIssues())) {
            $reviewReasons[] = 'Problèmes de validation';
        }
        
        if ($this->sort_final === 'T' && !str_contains(strtolower($this->observation ?? ''), 'tri')) {
            $reviewReasons[] = 'Règles de tri non précisées';
        }
        
        if (str_contains(strtolower($this->archives_courantes), 'validité') && 
            (!$this->observation || strlen($this->observation) < 20)) {
            $reviewReasons[] = 'Critères de validité non précisés';
        }
        
        return $reviewReasons;
    }

    /**
     * Get human readable sort final.
     */
    public function getSortFinalHumanAttribute()
    {
        $labels = [
            'C' => 'Conservation définitive',
            'D' => 'Destruction',
            'T' => 'Tri sélectif'
        ];
        
        return $labels[$this->sort_final] ?? 'Non défini';
    }

    /**
     * Get conservation summary for reports.
     */
    public function getConservationSummaryAttribute()
    {
        return [
            'code' => $this->plan_classement_code,
            'type_dossier' => $this->planClassement ? $this->planClassement->objet_classement : '',
            'duree_conservation' => $this->duree_conservation,
            'sort_final' => $this->sort_final_human,
            'priorite' => $this->priority_level,
            'delai_legal' => $this->delai_legal_text,
            'reference' => $this->reference_juridique,
            'validation_ok' => empty($this->getValidationIssues()),
            'issues' => $this->getValidationIssues(),
            'review_needed' => !empty($this->requiresReview()),
            'review_reasons' => $this->requiresReview()
        ];
    }

    /**
     * Export to array for CSV/Excel export.
     */
    public function toExportArray()
    {
        return [
            'N° Règle' => $this->plan_classement_code,
            'Type de Dossiers' => $this->planClassement ? $this->planClassement->objet_classement : '',
            'Pièces Constituant le Dossier' => $this->pieces_constituant,
            'Principal/Secondaire' => $this->principal_secondaire,
            'Délai Légal' => $this->delai_legal_text,
            'Référence Juridique' => $this->reference_juridique,
            'Archives Courantes' => $this->archives_courantes,
            'Archives Intermédiaires' => $this->archives_intermediaires,
            'Durée Totale' => $this->duree_conservation,
            'Sort Final' => $this->sort_final_human,
            'Priorité' => $this->priority_level,
            'Catégorie' => $this->category_name,
            'Observation' => $this->observation,
            'Validation' => empty($this->getValidationIssues()) ? 'OK' : 'Issues détectées',
            'Date Création' => $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : '',
            'Dernière Mise à Jour' => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : ''
        ];
    }

    /**
     * Get formatted pieces constituant (truncated if too long).
     */
    public function getPiecesConstituantShortAttribute()
    {
        if (!$this->pieces_constituant) return 'Non spécifié';
        
        return strlen($this->pieces_constituant) > 100 
            ? substr($this->pieces_constituant, 0, 100) . '...' 
            : $this->pieces_constituant;
    }

    /**
     * Get formatted reference juridique (truncated if too long).
     */
    public function getReferenceJuridiqueShortAttribute()
    {
        if (!$this->reference_juridique || $this->reference_juridique === '_') {
            return 'Non spécifiée';
        }
        
        return strlen($this->reference_juridique) > 80 
            ? substr($this->reference_juridique, 0, 80) . '...' 
            : $this->reference_juridique;
    }

    /**
     * Get type display (Principal/Secondaire with icon).
     */
    public function getTypeDisplayAttribute()
    {
        return match($this->principal_secondaire) {
            'P' => '🔴 Principal',
            'S' => '🔵 Secondaire',
            default => '⚪ Non défini'
        };
    }

    /**
     * Check if this is a legal requirement.
     */
    public function isLegalRequirement()
    {
        return $this->delai_legal && 
               $this->delai_legal !== '_' && 
               $this->reference_juridique && 
               $this->reference_juridique !== '_';
    }

    /**
     * Get compliance status.
     */
    public function getComplianceStatusAttribute()
    {
        if (!$this->isLegalRequirement()) {
            return 'Non applicable';
        }
        
        if ($this->isCoherentWithLegalDelay()) {
            return 'Conforme';
        }
        
        return 'Non conforme';
    }

    /**
     * Get compliance badge class.
     */
    public function getComplianceBadgeClassAttribute()
    {
        return match($this->compliance_status) {
            'Conforme' => 'bg-success',
            'Non conforme' => 'bg-danger',
            'Non applicable' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    /**
     * Static method for dashboard statistics.
     */
    public static function getStatistics()
    {
        $total = self::count();
        
        return [
            'total' => $total,
            'par_sort_final' => self::selectRaw('sort_final, COUNT(*) as count')
                                  ->groupBy('sort_final')
                                  ->pluck('count', 'sort_final')
                                  ->toArray(),
            'par_categorie' => self::with('planClassement')
                                 ->get()
                                 ->groupBy('category_code')
                                 ->map->count()
                                 ->toArray(),
            'par_type' => self::selectRaw('principal_secondaire, COUNT(*) as count')
                            ->groupBy('principal_secondaire')
                            ->pluck('count', 'principal_secondaire')
                            ->toArray(),
            'avec_issues' => self::all()->filter(function($rule) {
                return !empty($rule->getValidationIssues());
            })->count(),
            'conservation_definitive' => self::where('sort_final', 'C')->count(),
            'conformite' => [
                'conforme' => self::all()->filter(function($rule) {
                    return $rule->compliance_status === 'Conforme';
                })->count(),
                'non_conforme' => self::all()->filter(function($rule) {
                    return $rule->compliance_status === 'Non conforme';
                })->count(),
            ],
            'priorites' => [
                'haute' => self::all()->filter(function($rule) {
                    return $rule->priority_level === 'Haute';
                })->count(),
                'moyenne' => self::all()->filter(function($rule) {
                    return $rule->priority_level === 'Moyenne';
                })->count(),
                'normale' => self::all()->filter(function($rule) {
                    return $rule->priority_level === 'Normale';
                })->count(),
            ]
        ];
    }

    /**
     * Get rules that need attention (validation issues or review needed).
     */
    public static function getRulesNeedingAttention()
    {
        return self::with('planClassement')
                   ->get()
                   ->filter(function($rule) {
                       return !empty($rule->getValidationIssues()) || !empty($rule->requiresReview());
                   })
                   ->sortBy('priority_level')
                   ->values();
    }

    /**
     * Search method with advanced filters.
     */
    public static function advancedSearch($filters = [])
    {
        $query = self::with('planClassement');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (!empty($filters['sort_final'])) {
            $query->bySortFinal($filters['sort_final']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['priority'])) {
            $query->whereIn('id', 
                self::all()->filter(function($rule) use ($filters) {
                    return $rule->priority_level === $filters['priority'];
                })->pluck('id')
            );
        }

        if (!empty($filters['compliance'])) {
            $query->whereIn('id', 
                self::all()->filter(function($rule) use ($filters) {
                    return $rule->compliance_status === $filters['compliance'];
                })->pluck('id')
            );
        }

        if (isset($filters['with_issues']) && $filters['with_issues']) {
            $query->whereIn('id', 
                self::all()->filter(function($rule) {
                    return !empty($rule->getValidationIssues());
                })->pluck('id')
            );
        }

        return $query;
    }
}