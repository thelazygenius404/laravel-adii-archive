<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Notification Toast -->
            @if (session('status') === 'profile-updated')
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-init="setTimeout(() => show = false, 5000)"
                 class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ session('success_message', 'Profil mis à jour avec succès.') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif
            <!-- Formulaire unifié pour toutes les informations du profil -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Informations du Profil') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Mettez à jour les informations de votre profil : nom, email et mot de passe.') }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <!-- Prénom -->
                        <div>
                            <x-input-label for="prenom" :value="__('Prénom')" />
                            <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" 
                                         :value="old('prenom', $user->prenom)" required autofocus autocomplete="given-name" />
                            <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                        </div>

                        <!-- Nom -->
                        <div>
                            <x-input-label for="nom" :value="__('Nom de famille')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" 
                                         :value="old('nom', $user->nom)" required autocomplete="family-name" />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                         :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div>
                                    <p class="text-sm mt-2 text-gray-800">
                                        {{ __('Votre adresse email n\'est pas vérifiée.') }}

                                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ __('Cliquez ici pour renvoyer l\'email de vérification.') }}
                                        </button>
                                    </p>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 font-medium text-sm text-green-600">
                                            {{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Changement de mot de passe -->
                        <div class="border-t pt-6">
                            <h3 class="text-md font-medium text-gray-900 mb-4">
                                {{ __('Changer le mot de passe') }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('Laissez ces champs vides si vous ne souhaitez pas changer votre mot de passe.') }}
                            </p>

                            <!-- Mot de passe actuel -->
                            <div>
                                <x-input-label for="current_password" :value="__('Mot de passe actuel')" />
                                <x-text-input id="current_password" name="current_password" type="password" 
                                             class="mt-1 block w-full" autocomplete="current-password" />
                                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                            </div>

                            <!-- Nouveau mot de passe -->
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Nouveau mot de passe')" />
                                <x-text-input id="password" name="password" type="password" 
                                             class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirmation du nouveau mot de passe -->
                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Confirmer le nouveau mot de passe')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                                             class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Sauvegarder') }}</x-primary-button>

                            @if (session('status') === 'profile-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 4000)"
                                    class="text-sm text-green-600 font-medium"
                                >{{ session('success_message', 'Profil mis à jour avec succès.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Formulaire de suppression du compte (gardé séparé pour la sécurité) -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire caché pour la vérification d'email -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
</x-app-layout>