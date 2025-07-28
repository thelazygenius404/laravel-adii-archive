{{-- resources/views/admin/organismes/partials/entity-tree-item.blade.php --}}
<div class="entity-item" id="entity-{{ $entity->id }}" data-level="{{ $level }}">
    <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm">
        <div class="d-flex align-items-center flex-grow-1">
            {{-- Toggle button pour les entités avec enfants --}}
            @if($entity->children->count() > 0)
                <button class="toggle-btn btn btn-sm btn-outline-secondary me-3" 
                        onclick="toggleEntity({{ $entity->id }})" 
                        title="Développer/Réduire">
                    <i class="fas fa-chevron-down transition-transform"></i>
                </button>
            @else
                <div class="me-5"></div>
            @endif
            
            {{-- Indentation visuelle basée sur le niveau --}}
            @for($i = 0; $i < $level; $i++)
                <div class="border-start border-2 border-muted me-3" style="height: 20px; margin-left: 10px;"></div>
            @endfor
            
            {{-- Icône et informations de l'entité --}}
            <div class="d-flex align-items-center flex-grow-1">
                <div class="avatar me-3">
                    @php
                        $bgColor = match($level) {
                            0 => 'bg-primary',
                            1 => 'bg-success', 
                            2 => 'bg-info',
                            3 => 'bg-warning',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <div class="{{ $bgColor }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 35px; height: 35px;">
                        @if($level === 0)
                            <i class="fas fa-building" style="font-size: 0.9rem;"></i>
                        @elseif($level === 1)
                            <i class="fas fa-sitemap" style="font-size: 0.8rem;"></i>
                        @elseif($level === 2)
                            <i class="fas fa-layer-group" style="font-size: 0.8rem;"></i>
                        @else
                            <i class="fas fa-cube" style="font-size: 0.7rem;"></i>
                        @endif
                    </div>
                </div>
                
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <h6 class="mb-0 me-2">{{ $entity->nom_entite }}</h6>
                        @if($level === 0)
                            <span class="badge bg-primary badge-sm">Racine</span>
                        @else
                            <span class="badge bg-secondary badge-sm">Niveau {{ $level + 1 }}</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center">
                        <small class="text-muted me-3">
                            <i class="fas fa-code me-1"></i>
                            {{ $entity->code_entite }}
                        </small>
                        @if($entity->parent)
                            <small class="text-muted">
                                <i class="fas fa-arrow-up me-1"></i>
                                Parent: {{ Str::limit($entity->parent->nom_entite, 20) }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Badges et actions --}}
        <div class="d-flex align-items-center">
            {{-- Statistiques --}}
            <div class="me-3">
                @if($entity->users->count() > 0)
                    <span class="badge bg-info me-1" title="{{ $entity->users->count() }} utilisateur(s)">
                        <i class="fas fa-users me-1"></i>{{ $entity->users->count() }}
                    </span>
                @endif
                
                @if($entity->children->count() > 0)
                    <span class="badge bg-success me-1" title="{{ $entity->children->count() }} sous-entité(s)">
                        <i class="fas fa-sitemap me-1"></i>{{ $entity->children->count() }}
                    </span>
                @endif
                
                {{-- Badge pour les entités sans enfants ni utilisateurs --}}
                @if($entity->children->count() === 0 && $entity->users->count() === 0)
                    <span class="badge bg-light text-dark" title="Entité vide">
                        <i class="fas fa-circle me-1"></i>Vide
                    </span>
                @endif
            </div>
            
            {{-- Boutons d'action --}}
            <div class="btn-group" role="group">
                <a href="{{ route('admin.entites.show', $entity) }}" 
                   class="btn btn-sm btn-outline-info" 
                   title="Voir les détails de {{ $entity->nom_entite }}"
                   data-bs-toggle="tooltip">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.entites.edit', $entity) }}" 
                   class="btn btn-sm btn-outline-primary" 
                   title="Modifier {{ $entity->nom_entite }}"
                   data-bs-toggle="tooltip">
                    <i class="fas fa-edit"></i>
                </a>
                
                {{-- Bouton pour ajouter une sous-entité --}}
                <a href="{{ route('admin.entites.create', ['parent' => $entity->id, 'organisme' => $entity->id_organisme]) }}" 
                   class="btn btn-sm btn-outline-success" 
                   title="Ajouter une sous-entité à {{ $entity->nom_entite }}"
                   data-bs-toggle="tooltip">
                    <i class="fas fa-plus"></i>
                </a>
                
                {{-- Menu dropdown pour actions avancées --}}
                <div class="btn-group" role="group">
                    <button type="button" 
                            class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            title="Plus d'actions">
                        <span class="visually-hidden">Plus d'actions</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.entites.show', $entity) }}">
                                <i class="fas fa-eye me-2"></i>Détails complets
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.users.create', ['entite' => $entity->id]) }}">
                                <i class="fas fa-user-plus me-2"></i>Ajouter un utilisateur
                            </a>
                        </li>
                        @if($entity->children->count() === 0 && $entity->users->count() === 0)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item text-danger" 
                                        onclick="confirmDeleteEntity({{ $entity->id }}, '{{ $entity->nom_entite }}')">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Conteneur pour les sous-entités --}}
    @if($entity->children->count() > 0)
        <div class="entity-children ms-4" id="children-{{ $entity->id }}">
            @foreach($entity->children->sortBy('nom_entite') as $child)
                @include('admin.organismes.partials.entity-tree-item', [
                    'entity' => $child, 
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div>

{{-- Styles spécifiques pour cet élément --}}
@once
    @push('styles')
    <style>
        .entity-item {
            transition: all 0.3s ease;
        }
        
        .entity-item:hover {
            transform: translateX(5px);
        }
        
        .entity-item.collapsed .entity-children {
            display: none;
        }
        
        .entity-item .toggle-btn {
            transition: all 0.3s ease;
            border: none;
            background: transparent;
        }
        
        .entity-item .toggle-btn:hover {
            background-color: #f8f9fa;
            transform: scale(1.1);
        }
        
        .entity-item.collapsed .toggle-btn i {
            transform: rotate(-90deg);
        }
        
        .entity-children {
            border-left: 2px solid #dee2e6;
            margin-left: 1rem;
            padding-left: 1rem;
            transition: all 0.3s ease-in-out;
        }
        
        .avatar {
            position: relative;
        }
        
        .avatar::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #28a745;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        .transition-transform {
            transition: transform 0.3s ease;
        }
        
        /* Animation pour l'apparition des éléments */
        .entity-item {
            animation: fadeInUp 0.3s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .entity-item .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .entity-item .btn-group {
                margin-top: 0.5rem;
                width: 100%;
            }
            
            .entity-children {
                margin-left: 0.5rem;
                padding-left: 0.5rem;
            }
        }
        
        /* Amélioration visuelle pour les niveaux profonds */
        .entity-item[data-level="0"] {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .entity-item[data-level="1"] {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        }
        
        .entity-item[data-level="2"] {
            background: linear-gradient(135deg, #fff 0%, #f1f3f4 100%);
        }
        
        /* Effet de survol amélioré */
        .entity-item:hover .avatar > div {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Style pour les entités sans enfants */
        .entity-item:not(:has(.entity-children)) {
            border-left: 3px solid #28a745;
        }
        
        /* Style pour les entités avec enfants */
        .entity-item:has(.entity-children) {
            border-left: 3px solid #007bff;
        }
        
        /* Indicateur visuel pour les entités actives */
        .entity-item.active {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        // Fonction pour basculer l'affichage des enfants
        function toggleEntity(entityId) {
            const entityItem = document.getElementById('entity-' + entityId);
            const childrenContainer = document.getElementById('children-' + entityId);
            const toggleButton = entityItem.querySelector('.toggle-btn i');
            
            if (entityItem && childrenContainer) {
                entityItem.classList.toggle('collapsed');
                
                if (entityItem.classList.contains('collapsed')) {
                    childrenContainer.style.display = 'none';
                    toggleButton.style.transform = 'rotate(-90deg)';
                } else {
                    childrenContainer.style.display = 'block';
                    toggleButton.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        // Fonction pour confirmer la suppression d'une entité
        function confirmDeleteEntity(entityId, entityName) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'entité "${entityName}" ?\n\nCette action est irréversible.`)) {
                // Créer un formulaire pour la suppression
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/entites/${entityId}`;
                
                // Token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Méthode DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Initialiser les tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Fonction pour mettre en évidence une entité spécifique
        function highlightEntity(entityId) {
            // Supprimer les anciennes highlights
            document.querySelectorAll('.entity-item.active').forEach(item => {
                item.classList.remove('active');
            });
            
            // Ajouter la highlight à l'entité ciblée
            const targetEntity = document.getElementById('entity-' + entityId);
            if (targetEntity) {
                targetEntity.classList.add('active');
                targetEntity.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        // Fonction pour rechercher dans l'arbre
        function searchInTree(searchTerm) {
            const entities = document.querySelectorAll('.entity-item');
            const term = searchTerm.toLowerCase();
            
            entities.forEach(entity => {
                const entityName = entity.querySelector('h6').textContent.toLowerCase();
                const entityCode = entity.querySelector('small').textContent.toLowerCase();
                
                if (entityName.includes(term) || entityCode.includes(term)) {
                    entity.style.display = 'block';
                    entity.classList.add('search-match');
                    
                    // Déplier les parents pour montrer les résultats
                    let parent = entity.closest('.entity-children');
                    while (parent) {
                        parent.style.display = 'block';
                        const parentEntity = parent.closest('.entity-item');
                        if (parentEntity) {
                            parentEntity.classList.remove('collapsed');
                        }
                        parent = parent.parentElement.closest('.entity-children');
                    }
                } else {
                    entity.classList.remove('search-match');
                    if (term === '') {
                        entity.style.display = 'block';
                    } else {
                        entity.style.display = 'none';
                    }
                }
            });
        }
        
        // Fonction pour développer/réduire tout l'arbre
        function toggleAllEntities(expand = true) {
            const entities = document.querySelectorAll('.entity-item');
            
            entities.forEach(entity => {
                if (expand) {
                    entity.classList.remove('collapsed');
                } else {
                    entity.classList.add('collapsed');
                }
                
                const childrenContainer = entity.querySelector('.entity-children');
                const toggleButton = entity.querySelector('.toggle-btn i');
                
                if (childrenContainer && toggleButton) {
                    if (expand) {
                        childrenContainer.style.display = 'block';
                        toggleButton.style.transform = 'rotate(0deg)';
                    } else {
                        childrenContainer.style.display = 'none';
                        toggleButton.style.transform = 'rotate(-90deg)';
                    }
                }
            });
        }
    </script>
    @endpush
@endonce