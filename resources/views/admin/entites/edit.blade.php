@extends('layouts.admin')

@section('title', 'Modifier une Entité Productrice')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier une Entité Productrice
        </h1>
        <a href="{{ route('admin.entites.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informations de l'entité : {{ $entite->nom_entite }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.entites.update', $entite) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_entite" class="form-label">
                                    Nom de l'Entité <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nom_entite') is-invalid @enderror" 
                                       id="nom_entite" 
                                       name="nom_entite" 
                                       value="{{ old('nom_entite', $entite->nom_entite) }}" 
                                       placeholder="Ex: Direction des Opérations"
                                       required>
                                @error('nom_entite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Veuillez remplir ce champ.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code_entite" class="form-label">
                                    Code de l'Entité <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code_entite') is-invalid @enderror" 
                                       id="code_entite" 
                                       name="code_entite" 
                                       value="{{ old('code_entite', $entite->code_entite) }}" 
                                       placeholder="Ex: ADII-DOD"
                                       required>
                                @error('code_entite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Code unique pour identifier l'entité.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_organisme" class="form-label">
                                    Organisme <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('id_organisme') is-invalid @enderror" 
                                        id="id_organisme" 
                                        name="id_organisme" 
                                        required>
                                    <option value="">Sélectionner un organisme</option>
                                    @foreach($organismes as $organisme)
                                        <option value="{{ $organisme->id }}" {{ old('id_organisme', $entite->id_organisme) == $organisme->id ? 'selected' : '' }}>
                                            {{ $organisme->nom_org }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_organisme')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Organisme auquel appartient cette entité.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="entite_parent" class="form-label">
                                    Entité Parent
                                </label>
                                <select class="form-select @error('entite_parent') is-invalid @enderror" 
                                        id="entite_parent" 
                                        name="entite_parent">
                                    <option value="">Aucune (Entité racine)</option>
                                    @foreach($parentEntites as $parent)
                                        <option value="{{ $parent->id }}" 
                                                data-organisme="{{ $parent->id_organisme }}"
                                                {{ old('entite_parent', $entite->entite_parent) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->nom_entite }} ({{ $parent->organisme->nom_org }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('entite_parent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Entité parent dans la hiérarchie (optionnel).</div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Hierarchy Display -->
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-sitemap me-2"></i>
                                Hiérarchie Actuelle
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                @if($entite->parent)
                                    @php
                                        $hierarchy = [];
                                        $current = $entite->parent;
                                        while($current) {
                                            array_unshift($hierarchy, $current);
                                            $current = $current->parent;
                                        }
                                    @endphp
                                    
                                    @foreach($hierarchy as $ancestor)
                                        <span class="badge bg-secondary me-2">{{ $ancestor->nom_entite }}</span>
                                        <i class="fas fa-arrow-right me-2 text-muted"></i>
                                    @endforeach
                                @else
                                    <span class="badge bg-warning me-2">Racine</span>
                                    <i class="fas fa-arrow-right me-2 text-muted"></i>
                                @endif
                                
                                <span class="badge bg-primary">{{ $entite->nom_entite }}</span>
                                
                                @if($entite->children->count() > 0)
                                    <i class="fas fa-arrow-right me-2 ms-2 text-muted"></i>
                                    <small class="text-muted">
                                        {{ $entite->children->count() }} sous-entité(s)
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Warning for hierarchy changes -->
                    <div class="alert alert-warning" id="hierarchy-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Modifier la hiérarchie peut affecter les sous-entités existantes.
                    </div>

                    <!-- Information supplémentaire -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations Complémentaires
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date de création :</strong> {{ $entite->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Dernière modification :</strong> {{ $entite->updated_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Organisme actuel :</strong> 
                                        <span class="badge bg-primary">{{ $entite->organisme->nom_org }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Nombre de sous-entités :</strong> 
                                        <span class="badge bg-success">{{ $entite->children->count() }}</span>
                                    </p>
                                    <p><strong>Nombre d'utilisateurs :</strong> 
                                        <span class="badge bg-info">{{ $entite->users->count() }}</span>
                                    </p>
                                    <p><strong>Niveau hiérarchique :</strong> 
                                        @php
                                            $level = 0;
                                            $parent = $entite->parent;
                                            while ($parent) {
                                                $level++;
                                                $parent = $parent->parent;
                                            }
                                        @endphp
                                        <span class="badge bg-secondary">Niveau {{ $level + 1 }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-entities warning -->
                    @if($entite->children->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information :</strong> Cette entité a {{ $entite->children->count() }} sous-entité(s). 
                            Toute modification de l'organisme affectera également ces sous-entités.
                            <div class="mt-2">
                                <strong>Sous-entités :</strong>
                                @foreach($entite->children as $child)
                                    <span class="badge bg-light text-dark me-1">{{ $child->nom_entite }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Users warning -->
                    @if($entite->users->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-users me-2"></i>
                            <strong>Attention :</strong> Cette entité a {{ $entite->users->count() }} utilisateur(s) assigné(s). 
                            <div class="mt-2">
                                <strong>Utilisateurs :</strong>
                                @foreach($entite->users as $user)
                                    <span class="badge bg-light text-dark me-1">{{ $user->nom }} {{ $user->prenom }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.entites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const organismeSelect = document.getElementById('id_organisme');
        const parentSelect = document.getElementById('entite_parent');
        const hierarchyWarning = document.getElementById('hierarchy-warning');
        
        const originalOrganisme = '{{ $entite->id_organisme }}';
        const originalParent = '{{ $entite->entite_parent }}';

        // Filter parent entities based on selected organisme
        organismeSelect.addEventListener('change', function() {
            const selectedOrganisme = this.value;
            
            // Show warning if organisme changed
            if (selectedOrganisme !== originalOrganisme) {
                hierarchyWarning.style.display = 'block';
            } else {
                hierarchyWarning.style.display = 'none';
            }
            
            // Reset parent select
            Array.from(parentSelect.options).forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else {
                    const optionOrganisme = option.getAttribute('data-organisme');
                    option.style.display = optionOrganisme === selectedOrganisme ? 'block' : 'none';
                }
            });
            
            // Reset parent selection if incompatible
            if (parentSelect.value) {
                const selectedOption = parentSelect.options[parentSelect.selectedIndex];
                if (selectedOption.getAttribute('data-organisme') !== selectedOrganisme) {
                    parentSelect.value = '';
                }
            }
        });

        // Show warning when parent changes
        parentSelect.addEventListener('change', function() {
            if (this.value !== originalParent) {
                hierarchyWarning.style.display = 'block';
            } else if (organismeSelect.value === originalOrganisme) {
                hierarchyWarning.style.display = 'none';
            }
        });

        // Initialize on page load
        organismeSelect.dispatchEvent(new Event('change'));
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const nomEntite = document.getElementById('nom_entite').value.trim();
        const codeEntite = document.getElementById('code_entite').value.trim();
        const organisme = document.getElementById('id_organisme').value;
        
        if (!nomEntite || !codeEntite || !organisme) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
        
        // Check code format
        if (!/^[A-Z0-9\-_]+$/i.test(codeEntite)) {
            e.preventDefault();
            alert('Le code de l\'entité ne doit contenir que des lettres, chiffres, tirets et underscores.');
            return false;
        }

        // Confirm if hierarchy changes
        const organismeChanged = document.getElementById('id_organisme').value !== '{{ $entite->id_organisme }}';
        const parentChanged = document.getElementById('entite_parent').value !== '{{ $entite->entite_parent }}';
        
        if ((organismeChanged || parentChanged) && {{ $entite->children->count() > 0 ? 'true' : 'false' }}) {
            if (!confirm('Cette entité a des sous-entités. Êtes-vous sûr de vouloir modifier sa hiérarchie ?')) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush