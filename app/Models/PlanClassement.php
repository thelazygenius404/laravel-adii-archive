<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanClassement extends Model
{
    use HasFactory;

    protected $table = 'plan_classement';

    protected $fillable = [
        'code_classement',
        'objet_classement',
        'description',
    ];

    /**
     * Get the calendrier conservation for this plan classement.
     */
    public function calendrierConservation()
    {
        return $this->hasOne(CalendrierConservation::class, 'plan_classement_code', 'code_classement');
    }

    /**
     * Get all calendrier conservation rules for this plan classement.
     * (In case we have multiple rules per plan in the future)
     */
    public function calendrierConservations()
    {
        return $this->hasMany(CalendrierConservation::class, 'plan_classement_code', 'code_classement');
    }

    /**
     * Get formatted code for display.
     */
    public function getFormattedCodeAttribute()
    {
        return $this->code_classement;
    }

    /**
     * Get category from code.
     */
    public function getCategoryAttribute()
    {
        $parts = explode('.', $this->code_classement);
        return $parts[0];
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

        return $categoryNames[$this->category] ?? 'Catégorie ' . $this->category;
    }
    // Dans le modèle PlanClassement
    public static function getCategories()
    {
        return [
            '100' => 'Organisation et administration',
            '510' => 'Régimes économiques douaniers',
            '520' => 'Transit et transport',
            '530' => 'Contentieux douanier',
            '540' => 'Recours et réclamations',
            '550' => 'Contrôle et vérification',
            '560' => 'Facilitations commerciales',
            '610' => 'Dédouanement des marchandises',
        ];
    }

    /**
     * Scope to get plans by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('code_classement', 'LIKE', $category . '%');
    }

    /**
     * Scope to get plans without conservation rules.
     */
    public function scopeWithoutConservationRules($query)
    {
        return $query->whereNotIn('code_classement', 
            CalendrierConservation::pluck('plan_classement_code')
        );
    }

    /**
     * Scope to get plans with conservation rules.
     */
    public function scopeWithConservationRules($query)
    {
        return $query->whereIn('code_classement', 
            CalendrierConservation::pluck('plan_classement_code')
        );
    }

    /**
     * Check if this plan has a conservation rule.
     */
    public function hasConservationRule()
    {
        return $this->calendrierConservation()->exists();
    }

    /**
     * Get the level of this classification (how many dots in the code).
     */
    public function getLevelAttribute()
    {
        return substr_count($this->code_classement, '.') + 1;
    }

    /**
     * Get parent plan classement.
     */
    public function getParentAttribute()
    {
        $parts = explode('.', $this->code_classement);
        
        if (count($parts) <= 1) {
            return null; // This is a root category
        }
        
        // Remove the last part to get parent code
        array_pop($parts);
        $parentCode = implode('.', $parts);
        
        return self::where('code_classement', $parentCode)->first();
    }

    /**
     * Get children plan classements.
     */
    public function getChildrenAttribute()
    {
        return self::where('code_classement', 'LIKE', $this->code_classement . '.%')
                   ->where('code_classement', '!=', $this->code_classement)
                   ->orderBy('code_classement')
                   ->get();
    }

    /**
     * Scope to search by code or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code_classement', 'LIKE', "%{$search}%")
              ->orWhere('objet_classement', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get short description for display.
     */
    public function getShortDescriptionAttribute()
    {
        return strlen($this->objet_classement) > 100 
            ? substr($this->objet_classement, 0, 100) . '...' 
            : $this->objet_classement;
    }

    /**
     * Get hierarchical display name.
     */
    public function getHierarchicalNameAttribute()
    {
        return $this->code_classement . ' - ' . $this->objet_classement;
    }
}