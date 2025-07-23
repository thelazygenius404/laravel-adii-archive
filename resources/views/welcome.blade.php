{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADII - Système de Gestion des Archives</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        morocco: {
                            red: '#C1272D',
                            green: '#006233',
                            gold: '#DAA520'
                        },
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(193, 39, 45, 0.3); }
            50% { box-shadow: 0 0 40px rgba(193, 39, 45, 0.6); }
        }
        
        .gradient-morocco {
            background: linear-gradient(135deg, #006233 0%, #C1272D 100%);
        }
        
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(193, 39, 45, 0.1), rgba(0, 98, 51, 0.1));
            animation: float 15s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 80%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 60%;
            left: 10%;
            animation-delay: 3s;
        }
        
        .shape:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 30%;
            left: 70%;
            animation-delay: 6s;
        }
    </style>
</head>
<body class="bg-gray-50">
    {{-- Navigation --}}
    <nav class="bg-gray-400 shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    {{-- Logo ADII --}}
                    <div class="flex items-center">
                        {{-- Replace this path with your actual logo path --}}
                        <img src="{{ asset('images/logo.png') }}" alt="Logo ADII" class="h-14 w-auto mr-3 rounded-lg shadow-md">
                        <div>
                            <div class="font-bold text-xl text-gray-800">ADII</div>
                            <div class="text-xs text-gray-600">Archives Management</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-800">{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</div>
                                <div class="text-xs text-gray-500">
                                    @if(Auth::user()->role === 'admin')
                                        <i class="fas fa-crown text-morocco-red mr-1"></i>Administrateur
                                    @elseif(Auth::user()->role === 'gestionnaire_archives')
                                        <i class="fas fa-archive text-primary-600 mr-1"></i>Gestionnaire Archives
                                    @elseif(Auth::user()->role === 'service_producteurs')
                                        <i class="fas fa-users text-morocco-green mr-1"></i>Service Producteurs
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('dashboard') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition duration-300 shadow-md">
                                <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800 px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition duration-300 shadow-md">
                            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="gradient-morocco pt-32 pb-20 relative overflow-hidden">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center animate-fade-in">
                {{-- Coat of Arms --}}
                <div class="mb-8">
                    <div class="inline-block p-6 bg-blue-500 text-white rounded-3xl shadow-2xl animate-pulse-glow">
                        {{-- Replace this path with your actual logo path --}}
                        <img src="{{ asset('images/logo.png') }}" alt="Logo ADII" class="h-24 w-auto rounded-2xl">
                    </div>
                </div>
                
                <div class="mb-6">
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-4">
                        ADII
                    </h1>
                    <h2 class="text-2xl md:text-3xl font-semibold text-white/90 mb-4">
                        Administration des Douanes et Impôts Indirects
                    </h2>
                    <div class="w-32 h-1 bg-morocco-gold mx-auto mb-6"></div>
                    <p class="text-xl text-white/80 mb-8 max-w-3xl mx-auto leading-relaxed">
                        Système moderne de gestion et d'archivage documentaire pour l'administration douanière marocaine
                    </p>
                </div>
                
                @guest
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="bg-white text-morocco-red hover:bg-gray-100 px-8 py-4 rounded-xl text-lg font-semibold transition duration-300 shadow-lg inline-flex items-center">
                            <i class="fas fa-sign-in-alt mr-3"></i>Accès sécurisé
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </section>

    {{-- Mission Statement --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-slide-up">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Notre Mission</h2>
                <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                    Assurer la préservation, l'organisation et l'accessibilité du patrimoine documentaire de l'ADII 
                    tout en garantissant la conformité réglementaire et la sécurité des informations sensibles.
                </p>
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Services d'Archivage</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Solutions complètes pour la gestion documentaire de l'administration douanière
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Service 1 --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-l-4 border-morocco-red">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-file-invoice text-morocco-red text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Documents Douaniers</h3>
                        <p class="text-gray-600">
                            Archivage et gestion des déclarations douanières, manifestes de cargaison, 
                            certificats d'origine et documents de transit.
                        </p>
                    </div>
                </div>

                {{-- Service 2 --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-l-4 border-primary-600">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-balance-scale text-primary-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Conformité Réglementaire</h3>
                        <p class="text-gray-600">
                            Conservation des documents selon les exigences légales et réglementaires 
                            avec système de traçabilité complet.
                        </p>
                    </div>
                </div>

                {{-- Service 3 --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-l-4 border-morocco-green">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shield-alt text-morocco-green text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Sécurité Renforcée</h3>
                        <p class="text-gray-600">
                            Protection maximale des données sensibles avec contrôle d'accès granulaire 
                            et chiffrement des documents confidentiels.
                        </p>
                    </div>
                </div>

                {{-- Service 4 --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-l-4 border-morocco-gold">
                    <div class="text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-morocco-gold text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Recherche Avancée</h3>
                        <p class="text-gray-600">
                            Moteur de recherche intelligent pour retrouver rapidement tout document 
                            par référence, date, type ou contenu.
                        </p>
                    </div>
                </div>

                {{-- Service 5 --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-l-4 border-purple-600">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Reporting & Analytics</h3>
                        <p class="text-gray-600">
                            Tableaux de bord et rapports détaillés sur l'utilisation des archives 
                            et les statistiques d'activité.
                        </p>
                    </div>
                </div>

                {{-- Service 6 --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border-l-4 border-indigo-600">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-cloud text-indigo-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Archivage Digital</h3>
                        <p class="text-gray-600">
                            Numérisation et conservation numérique des documents papier 
                            avec préservation à long terme.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Access Levels Section --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Niveaux d'Accès</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Système de droits d'accès adapté aux différents profils professionnels
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Admin --}}
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-8 border-2 border-morocco-red/20 shadow-lg">
                    <div class="text-center">
                        <div class="bg-morocco-red w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-crown text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-morocco-red mb-4">Administrateur</h3>
                        <div class="text-left space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Gestion complète du système</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Configuration des droits d'accès</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Supervision des utilisateurs</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Rapports administratifs complets</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gestionnaire Archives --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border-2 border-primary-200 shadow-lg">
                    <div class="text-center">
                        <div class="bg-primary-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-archive text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-primary-600 mb-4">Gestionnaire Archives</h3>
                        <div class="text-left space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Gestion des collections d'archives</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Classification et indexation</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Validation des versements</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Maintenance documentaire</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Service Producteurs --}}
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 border-2 border-morocco-green/20 shadow-lg">
                    <div class="text-center">
                        <div class="bg-morocco-green w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-morocco-green mb-4">Service Producteurs</h3>
                        <div class="text-left space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Dépôt de documents</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Gestion des entités productrices</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Suivi des versements</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">Consultation personnalisée</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics Section --}}
    <section class="py-20 gradient-morocco text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Chiffres Clés</h2>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">
                    Le système ADII en quelques statistiques
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center glassmorphism rounded-2xl p-6">
                    <div class="text-4xl font-bold mb-2 text-morocco-gold">
                        <i class="fas fa-file-alt mr-2"></i>50K+
                    </div>
                    <p class="text-lg opacity-90">Documents Archivés</p>
                </div>
                <div class="text-center glassmorphism rounded-2xl p-6">
                    <div class="text-4xl font-bold mb-2 text-morocco-gold">
                        <i class="fas fa-building mr-2"></i>25+
                    </div>
                    <p class="text-lg opacity-90">Services ADII</p>
                </div>
                <div class="text-center glassmorphism rounded-2xl p-6">
                    <div class="text-4xl font-bold mb-2 text-morocco-gold">
                        <i class="fas fa-users mr-2"></i>200+
                    </div>
                    <p class="text-lg opacity-90">Utilisateurs Actifs</p>
                </div>
                <div class="text-center glassmorphism rounded-2xl p-6">
                    <div class="text-4xl font-bold mb-2 text-morocco-gold">
                        <i class="fas fa-shield-alt mr-2"></i>99.9%
                    </div>
                    <p class="text-lg opacity-90">Disponibilité</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Contact & Support</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Notre équipe technique est à votre disposition pour tout accompagnement
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Support Technique</h3>
                    <p class="text-gray-600 mb-4">Assistance technique 24h/7j</p>
                    <p class="text-primary-600 font-semibold">+212 537 XX XX XX</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-morocco-green text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Support Email</h3>
                    <p class="text-gray-600 mb-4">Réponse sous 24h maximum</p>
                    <p class="text-morocco-green font-semibold">archives@adii.gov.ma</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-map-marker-alt text-morocco-red text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Adresse</h3>
                    <p class="text-gray-600 mb-4">Siège ADII</p>
                    <p class="text-morocco-red font-semibold">Rabat, Maroc</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-morocco-red to-morocco-green rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-crown text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xl">ADII</div>
                            <div class="text-sm text-gray-300">Administration des Douanes et Impôts Indirects</div>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4 leading-relaxed">
                        Système de gestion des archives pour l'administration douanière marocaine, 
                        garantissant la préservation et l'accessibilité du patrimoine documentaire.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4 text-morocco-gold">Liens Utiles</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition">À propos de l'ADII</a></li>
                        <li><a href="#" class="hover:text-white transition">Réglementation</a></li>
                        <li><a href="#" class="hover:text-white transition">Procédures</a></li>
                        <li><a href="#" class="hover:text-white transition">Documentation</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4 text-morocco-gold">Support</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><i class="fas fa-envelope mr-2"></i>archives@adii.gov.ma</li>
                        <li><i class="fas fa-phone mr-2"></i>+212 537 XX XX XX</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i>Rabat, Maroc</li>
                        <li><i class="fas fa-clock mr-2"></i>24h/7j</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <p class="text-gray-300">
                            &copy; {{ date('Y') }} Administration des Douanes et Impôts Indirects - Royaume du Maroc
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            Tous droits réservés - Système d'archivage sécurisé
                        </p>
                    </div>
                    
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Mentions légales</span>
                            Mentions légales
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Politique de confidentialité</span>
                            Confidentialité
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Conditions d'utilisation</span>
                            CGU
                        </a>
                    </div>
                </div>
                
                {{-- Government Badge --}}
                <div class="text-center mt-6 pt-6 border-t border-gray-700">
                    <div class="inline-flex items-center text-sm text-gray-400">
                        <i class="fas fa-shield-alt text-morocco-gold mr-2"></i>
                        Site officiel de l'Administration des Douanes et Impôts Indirects du Royaume du Maroc
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- JavaScript for animations and interactions --}}
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = '0s';
                    entry.target.classList.add('animate-slide-up');
                }
            });
        }, observerOptions);

        // Observe all cards for animation
        document.querySelectorAll('.card-hover').forEach(card => {
            observer.observe(card);
        });

        // Add parallax effect to floating shapes
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.shape');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.3;
                const x = (mouseX - 0.5) * speed;
                const y = (mouseY - 0.5) * speed;
                shape.style.transform = `translate(${x}px, ${y}px)`;
            });
        });

        // Add navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 100) {
                navbar.classList.add('backdrop-blur-md', 'bg-white/90');
                navbar.classList.remove('bg-white');
            } else {
                navbar.classList.remove('backdrop-blur-md', 'bg-white/90');
                navbar.classList.add('bg-white');
            }
        });

        // Counter animation for statistics
        function animateCounters() {
            const counters = document.querySelectorAll('.text-4xl');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                if (target) {
                    let current = 0;
                    const increment = target / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        const suffix = counter.textContent.includes('K') ? 'K+' : 
                                     counter.textContent.includes('%') ? '%' : '+';
                        const icon = counter.querySelector('i') ? counter.querySelector('i').outerHTML + ' ' : '';
                        counter.innerHTML = icon + Math.floor(current) + suffix;
                    }, 50);
                }
            });
        }

        // Trigger counter animation when statistics section is visible
        const statsSection = document.querySelector('.gradient-morocco');
        if (statsSection) {
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.disconnect();
                    }
                });
            }, observerOptions);
            
            statsObserver.observe(statsSection);
        }

        // Add hover effects to service cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add click effect to login button
        const loginButtons = document.querySelectorAll('a[href*="login"]');
        loginButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.classList.add('absolute', 'bg-white/30', 'rounded-full', 'animate-ping');
                ripple.style.width = ripple.style.height = '20px';
                ripple.style.left = (e.clientX - this.offsetLeft - 10) + 'px';
                ripple.style.top = (e.clientY - this.offsetTop - 10) + 'px';
                
                this.style.position = 'relative';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add typing effect to main title (optional enhancement)
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.innerHTML = '';
            
            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            
            type();
        }

        // Initialize typing effect on page load
        document.addEventListener('DOMContentLoaded', function() {
            const titleElement = document.querySelector('h1');
            if (titleElement && !titleElement.dataset.typed) {
                const originalText = titleElement.textContent;
                titleElement.dataset.typed = 'true';
                setTimeout(() => {
                    typeWriter(titleElement, originalText, 150);
                }, 1000);
            }
        });

        // Add loading state management
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
            
            // Stagger animation of elements
            const animatedElements = document.querySelectorAll('.animate-fade-in, .animate-slide-up');
            animatedElements.forEach((element, index) => {
                element.style.animationDelay = (index * 0.1) + 's';
            });
        });

        // Performance optimization: Throttle scroll events
        function throttle(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Apply throttling to scroll events
        window.addEventListener('scroll', throttle(function() {
            // Navbar effect with throttling
            const navbar = document.querySelector('nav');
            if (window.scrollY > 100) {
                navbar.classList.add('backdrop-blur-md', 'bg-white/90');
                navbar.classList.remove('bg-white');
            } else {
                navbar.classList.remove('backdrop-blur-md', 'bg-white/90');
                navbar.classList.add('bg-white');
            }
        }, 16)); // ~60fps
    </script>

    {{-- Additional CSS for enhanced animations --}}
    <style>
        /* Loading state */
        body:not(.loaded) .animate-fade-in,
        body:not(.loaded) .animate-slide-up {
            opacity: 0;
        }

        /* Enhanced hover effects */
        .card-hover {
            transform-origin: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Navbar transition */
        nav {
            transition: all 0.3s ease;
        }

        /* Ripple effect for buttons */
        .relative {
            overflow: hidden;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #C1272D, #006233);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #a91e24, #004d28);
        }

        /* Enhanced focus states for accessibility */
        a:focus,
        button:focus {
            outline: 2px solid #DAA520;
            outline-offset: 2px;
        }

        /* Print styles */
        @media print {
            .floating-shapes,
            nav,
            footer {
                display: none !important;
            }
            
            body {
                background: white !important;
            }
        }
    </style>
</body>
</html> 