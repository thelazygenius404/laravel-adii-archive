<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'utilisateurs';
    protected $fillable = [
        'nom',
        'prenom', 
        'email',
        'password',
        'role',
        'id_entite_productrices',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_creation' => 'datetime',
        ];
    }

    /**
     * Get the entite productrice that owns the user.
     */
    public function entiteProductrice()
    {
        return $this->belongsTo(EntiteProductrice::class, 'id_entite_productrices');
    }

    /**
     * Get the organisme through entite productrice.
     */
    public function organisme()
    {
        return $this->hasOneThrough(Organisme::class, EntiteProductrice::class, 'id', 'id', 'id_entite_productrices', 'id_organisme');
    }

     // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user is gestionnaire_archives
    public function isGestionnaireArchives()
    {
        return $this->role === 'gestionnaire_archives';
    }
    
    public function isServiceProducteurs()
    {
        return $this->role === 'service_producteurs';
    }
    
    // Get full name
    public function getFullNameAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }
    
     // Get user type label for display
    public function getTypeLabel()
    {
        return match($this->role) {
            'admin' => 'Administrateur',
            'gestionnaire_archives' => 'Gestionnaire Archives',
            'service_producteurs' => 'Service Producteurs',
            default => $this->role
        };
    }

    /**
     * Get the role display name with badge class.
     */
    public function getRoleDisplayAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrateur',
            'gestionnaire_archives' => 'Gestionnaire d\'archives',
            'service_producteurs' => 'Service producteurs',
            default => $this->role
        };
    }

    /**
     * Get the entite hierarchy for display.
     */
    public function getEntiteHierarchyAttribute()
    {
        if (!$this->entiteProductrice) {
            return null;
        }
        
        return $this->entiteProductrice->full_name;
    }

    /**
     * Scope to filter users by organisme.
     */
    public function scopeByOrganisme($query, $organismeId)
    {
        return $query->whereHas('entiteProductrice', function ($query) use ($organismeId) {
            $query->where('id_organisme', $organismeId);
        });
    }

    /**
     * Scope to filter users by entite productrice.
     */
    public function scopeByEntiteProductrice($query, $entiteId)
    {
        return $query->where('id_entite_productrices', $entiteId);
    }

    /**
     * Check if user belongs to a specific organisme.
     */
    public function belongsToOrganisme($organismeId)
    {
        return $this->entiteProductrice && $this->entiteProductrice->id_organisme == $organismeId;
    }
}