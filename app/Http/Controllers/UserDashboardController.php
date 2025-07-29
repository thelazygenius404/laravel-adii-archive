<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques basiques pour l'utilisateur
        $stats = [
            'profile_completion' => $this->calculateProfileCompletion($user),
            'last_login' => $user->updated_at ?? $user->created_at,
            'account_created' => $user->created_at,
            'entite' => $user->entiteProductrice ? $user->entiteProductrice->nom_entite : 'Non assigné',
            'organisme' => $user->organisme ? $user->organisme->nom_org : 'Non assigné',
        ];
        
        return view('user.dashboard', compact('user', 'stats'));
    }
    
    /**
     * Calculate profile completion percentage.
     */
    private function calculateProfileCompletion($user)
    {
        $fields = ['nom', 'prenom', 'email', 'id_entite_productrices'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }
    
    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }
    
    /**
     * Show user notifications.
     */
    public function notifications()
    {
        $user = Auth::user();
        // Ici vous pourriez récupérer les notifications de l'utilisateur
        $notifications = collect(); // Placeholder
        
        return view('user.notifications', compact('user', 'notifications'));
    }
}