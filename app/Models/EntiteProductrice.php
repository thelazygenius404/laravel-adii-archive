<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntiteProductrice extends Model
{
    use HasFactory;

    protected $table = 'entite_productrices';

    protected $fillable = [
        'nom_entite',
        'entite_parent',
        'code_entite',
        'id_organisme',
    ];

    /**
     * Get the organisme that owns the entite productrice.
     */
    public function organisme()
    {
        return $this->belongsTo(Organisme::class, 'id_organisme');
    }

    /**
     * Get the parent entite productrice.
     */
    public function parent()
    {
        return $this->belongsTo(EntiteProductrice::class, 'entite_parent');
    }

    /**
     * Get the children entite productrices.
     */
    public function children()
    {
        return $this->hasMany(EntiteProductrice::class, 'entite_parent');
    }

    /**
     * Get all descendants (recursive children).
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the users for the entite productrice.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_entite_productrices');
    }

    /**
     * Scope to get root entites (without parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('entite_parent');
    }

    /**
     * Get the full hierarchy name.
     */
    public function getFullNameAttribute()
    {
        $names = collect([$this->nom_entite]);
        $parent = $this->parent;
        
        while ($parent) {
            $names->prepend($parent->nom_entite);
            $parent = $parent->parent;
        }
        
        return $names->implode(' > ');
    }

    /**
     * Check if this entite is an ancestor of another entite.
     */
    public function isAncestorOf(EntiteProductrice $entite)
    {
        $parent = $entite->parent;
        
        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        
        return false;
    }

    /**
     * Check if this entite is a descendant of another entite.
     */
    public function isDescendantOf(EntiteProductrice $entite)
    {
        return $entite->isAncestorOf($this);
    }
}