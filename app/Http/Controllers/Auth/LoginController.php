<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Assurez-vous que c'est la vue de votre formulaire
    }

    public function login(Request $request)
    {
        // Validation des champs
         $credentials = $request->validate([
            'email' => [
                'required', 
                'email', 
                'max:255'
            ],
            'password' => [
                'required', 
                'string', 
                'min:8'
            ],
            'role' => [
                'required', 
                'in:admin,gestionnaire_archives,service_producteurs,user'
            ]
        ], [
            // Messages personnalisés pour l'email
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            
            // Messages personnalisés pour le mot de passe
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            
            // Messages personnalisés pour le rôle
            'role.required' => 'Veuillez sélectionner un rôle.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
        ]);

        // Tentative d'authentification
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => $credentials['role']
        ], $request->boolean('remember'))) {
            
            $request->session()->regenerate();
            
            // Log de connexion réussie
            Log::info('Connexion réussie', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Redirection vers le dashboard général qui redirigera automatiquement selon le rôle
            return redirect()->intended(route('dashboard'));
        }

        // Si l'authentification échoue
        // Log de tentative de connexion échouée
        Log::warning('Tentative de connexion échouée', [
            'email' => $credentials['email'],
            'role' => $credentials['role'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Si l'authentification échoue, retourner avec les erreurs
        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte avec le rôle sélectionné.',
        ])->withInput($request->only('email', 'role'));
    }

    public function logout(Request $request)
    {
        // Log de déconnexion (optionnel)
        if (Auth::check()) {
            Log::info('Déconnexion', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('status', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Méthode pour vérifier si un utilisateur existe avec les identifiants fournis
     * (utilisée pour des vérifications supplémentaires si nécessaire)
     */
    protected function userExists($email, $role)
    {
        return \App\Models\User::where('email', $email)
                              ->where('role', $role)
                              ->exists();
    }

    /**
     * Méthode pour gérer les tentatives de connexion multiples
     * (protection contre les attaques par force brute)
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $maxAttempts = 5;
        $decayMinutes = 15;
        
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $maxAttempts
        );
    }

    /**
     * Obtenir la clé de limitation pour les tentatives de connexion
     */
    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('email')).'|'.$request->ip();
    }

    /**
     * Obtenir l'instance du limiteur de taux
     */
    protected function limiter()
    {
        return app('Illuminate\Cache\RateLimiter');
    }
}