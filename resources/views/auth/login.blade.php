<x-guest-layout>
    <style>
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Base Styles */
        body {
            background: linear-gradient(135deg, #e6f0fa 0%, #ffffff 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: #1e3a8a;
        }
        .guest-layout {
            text-align: center;
            padding: 20px;
        }

        /* Card Styling */
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Header and Logo */
        .header-logo {
            margin-bottom: 20px;
            background-color: #3d8fcd;
            padding: 15px;
            border-radius: 10px;
        }
        .header-logo img {
            max-width: 150px;
            height: auto;
        }
        .header-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        .header-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font-size: 1rem;
            color: #1e3a8a;
            background: #f9fafb;
            transition: border-color 0.3s ease;
        }
        .form-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font-size: 1rem;
            color: #1e3a8a;
            background: #f9fafb;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            transition: border-color 0.3s ease;
        }
        .form-select:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        /* Button */
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .login-button:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
        .login-button:active {
            transform: translateY(0);
        }

        /* Additional Info */
        .info-text {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 15px;
        }
        .role-indicators {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }
        .role-indicator {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: #1e3a8a;
        }
        .role-indicator span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .role-indicator.admin span { background-color: #ef4444; }
        .role-indicator.gestionnaire span { background-color: #3b82f6; }
        .role-indicator.producteur span { background-color: #10b981; }

        /* Development Mode */
        .dev-mode {
            margin-top: 20px;
            padding: 15px;
            background-color: #fef3c7;
            border-radius: 5px;
            font-size: 0.9rem;
            color: #d97706;
        }
        .dev-mode a {
            color: #d97706;
            text-decoration: underline;
        }
        .dev-mode a:hover {
            color: #b45309;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 0.7rem;
            color: #64748b;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="guest-layout">
        <div class="login-card" style="animation: fadeIn 0.8s ease-out;">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="ADII Logo" />
            </div>
            <div class="header-title">ADII</div>
            <div class="header-subtitle">Système de gestion des archives</div>
            <div class="header-subtitle">Identifiez-vous SVP</div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Votre adresse email" required autofocus autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Votre mot de passe" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label for="role" class="form-label">Rôle</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="" disabled selected>-- Sélectionnez un rôle --</option>
                        <option value="admin">Administrateur</option>
                        <option value="gestionnaire_archives">Gestionnaire des dépôts</option>
                        <option value="service_producteurs">Service Producteurs</option>
                    </select>
                </div>
                <button type="submit" class="login-button">CONNEXION</button>
            </form>

            <div class="info-text">Accès autorisé uniquement au personnel ADII</div>
            <div class="role-indicators">
                <div class="role-indicator admin"><span></span>Administrateur</div>
                <div class="role-indicator gestionnaire"><span></span>Gestionnaire</div>
                <div class="role-indicator producteur"><span></span>Service Producteurs</div>
            </div>

            @if (app()->environment('local'))
                <div class="dev-mode">
                    <strong>Mode Développement :</strong><br>
                    <a href="#" onclick="fillDemo('admin@example.com', 'admin123')">Admin: admin@example.com / admin123</a><br>
                    <a href="#" onclick="fillDemo('gestionnaire@example.com', 'gestionnaire123')">Gestionnaire: gestionnaire@example.com / gestionnaire123</a><br>
                    <a href="#" onclick="fillDemo('producteurs@example.com', 'producteurs123')">Producteurs: producteurs@example.com / producteurs123</a>
                </div>
            @endif

            <div class="footer">
                © 2025 Administration des Douanes et Impôts Indirects - Tous droits réservés<br>
                <a href="#">Conditions d'utilisation</a> | <a href="#">Politique de confidentialité</a>
            </div>
        </div>

        <script>
            function fillDemo(email, password) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
                event.preventDefault();
            }
        </script>
    </div>
</x-guest-layout>