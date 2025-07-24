<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,gestionnaire_archives,service_producteurs'
        ]);

        // Tentative d'authentification
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirection selon le rôle
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role === 'gestionnaire_archives') {
                return redirect()->route('gestionnaire.dashboard');
            } else {
                return redirect()->route('producteur.dashboard');
            }
        }

        // Si l'authentification échoue
        return back()->withErrors([
            'email' => 'Identifiants incorrects ou rôle invalide.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}