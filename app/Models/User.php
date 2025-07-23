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
        'role',
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
        ];
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
    public function isServiceProducteur()
    {
        return $this->role === 'service_producteur';
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
            'service_producteur' => 'Service Producteur',
            default => $this->role
        };
    }
}
