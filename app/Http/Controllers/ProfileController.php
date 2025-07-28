<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (name, email, password).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Validation des données
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:utilisateurs,email,' . $user->id],
            'current_password' => ['nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Suivre les modifications
        $modifications = [];

        // Mise à jour du nom
        if ($user->nom !== $validated['nom']) {
            $user->nom = $validated['nom'];
            $modifications[] = 'nom';
        }

        // Mise à jour du prénom
        if ($user->prenom !== $validated['prenom']) {
            $user->prenom = $validated['prenom'];
            $modifications[] = 'prénom';
        }

        // Mise à jour de l'email
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null; // Reset verification si l'email change
            $modifications[] = 'email';
        }

        // Mise à jour du mot de passe si fourni
        if (!empty($validated['password'])) {
            // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
            if (empty($validated['current_password'])) {
                return back()->withErrors([
                    'current_password' => 'Le mot de passe actuel est requis pour changer le mot de passe.'
                ]);
            }
            
            $user->password = Hash::make($validated['password']);
            $modifications[] = 'mot de passe';
        }

        $user->save();

        // Générer le message de succès personnalisé
        $message = $this->generateSuccessMessage($modifications);

        return Redirect::route('profile.edit')->with([
            'status' => 'profile-updated',
            'success_message' => $message
        ]);
    }

    /**
     * Generate a custom success message based on what was modified.
     */
    private function generateSuccessMessage(array $modifications): string
    {
        if (empty($modifications)) {
            return 'Aucune modification détectée.';
        }

        if (count($modifications) === 1) {
            switch ($modifications[0]) {
                case 'nom':
                    return 'Le nom de famille a été modifié avec succès.';
                case 'prénom':
                    return 'Le prénom a été modifié avec succès.';
                case 'email':
                    return 'L\'adresse email a été modifiée avec succès.';
                case 'mot de passe':
                    return 'Le mot de passe a été modifié avec succès.';
                default:
                    return 'Les informations ont été modifiées avec succès.';
            }
        }

        // Si plusieurs modifications
        if (count($modifications) === 2) {
            return 'Le ' . implode(' et le ', $modifications) . ' ont été modifiés avec succès.';
        }

        // Si plus de 2 modifications
        $dernierElement = array_pop($modifications);
        return 'Le ' . implode(', le ', $modifications) . ' et le ' . $dernierElement . ' ont été modifiés avec succès.';
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}