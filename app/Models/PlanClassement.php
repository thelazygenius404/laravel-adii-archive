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
    ];

    /**
     * Get the calendrier conservation records for this plan.
     */
    public function calendrierConservation()
    {
        return $this->hasMany(CalendrierConservation::class, 'plan_classement_id');
    }

    /**
     * Scope to search by code or object.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code_classement', 'LIKE', "%{$search}%")
              ->orWhere('objet_classement', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get the formatted code with leading zeros.
     */
    public function getFormattedCodeAttribute()
    {
        return str_pad($this->code_classement, 3, '0', STR_PAD_LEFT);
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
}