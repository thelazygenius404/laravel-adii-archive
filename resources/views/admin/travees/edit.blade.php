{{-- resources/views/admin/travees/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modifier la Travée - ' . $travee->nom)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier la Travée - {{ $travee->nom }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.travees.show', $travee) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>
                Voir détails
            </a>
            <a href="{{ route('admin.travees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations de la Travée
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.travees.update', $travee) }}" method="POST" id="traveeForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la travée <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom', $travee->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Exemple: Travée A, T01, etc.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="salle_id" class="form-label">Salle <span class="text-danger">*</span></label>
                            <select class="form-select @error('salle_id') is-invalid @enderror" 
                                    id="salle_id" name="salle_id" required>
                                <option value="">Sélectionner une salle</option>
                                @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}" 
                                            {{ old('salle_id', $travee->salle_id) == $salle->id ? 'selected' : '' }}
                                            data-organisme="{{ $salle->organisme->nom_org }}">
                                        {{ $salle->nom }} ({{ $salle->organisme->nom_org }})
                                    </option>
                                @endforeach
                            </select>
                            @error('salle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $travee->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Description détaillée de la travée</small>
                    </div>

                    <!-- Coordonnées physiques optionnelles -->
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Coordonnées Physiques (Optionnel)
                    </h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="zone" class="form-label">Zone</label>
                            <input type="text" class="form-control @error('zone') is-invalid @enderror" 
                                   id="zone" name="zone" value="{{ old('zone', $travee->zone) }}" maxlength="10">
                            @error('zone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: Z1, Zone A</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="rangee" class="form-label">Rangée</label>
                            <input type="text" class="form-control @error('rangee') is-invalid @enderror" 
                                   id="rangee" name="rangee" value="{{ old('rangee', $travee->rangee) }}" maxlength="10">
                            @error('rangee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: R1, R2</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="code_barre" class="form-label">Code-barres</label>
                            <input type="text" class="form-control @error('code_barre') is-invalid @enderror" 
                                   id="code_barre" name="code_barre" value="{{ old('code_barre', $travee->code_barre) }}" maxlength="50">
                            @error('code_barre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Code unique pour la travée</small>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $travee->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Informations complémentaires sur la travée</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('admin.travees.show', $travee) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Informations actuelles -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations actuelles
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Nom actuel :</label>
                    <div class="fw-bold">{{ $travee->nom }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Salle actuelle :</label>
                    <div class="fw-bold">{{ $travee->salle->nom }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Organisme :</label>
                    <div><span class="badge bg-primary">{{ $travee->salle->organisme->nom_org }}</span></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Créée le :</label>
                    <div>{{ $travee->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="mb-0">
                    <label class="text-muted">Modifiée le :</label>
                    <div>{{ $travee->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-info">{{ $travee->tablettes_count ?? 0 }}</h4>
                        <small class="text-muted">Tablettes</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $travee->positions_count ?? 0 }}</h4>
                        <small class="text-muted">Positions</small>
                    </div>
                </div>
                <hr>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Utilisation</span>
                        <span>{{ number_format($travee->utilisation_percentage ?? 0, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-{{ ($travee->utilisation_percentage ?? 0) < 50 ? 'success' : (($travee->utilisation_percentage ?? 0) < 80 ? 'warning' : 'danger') }}" 
                             style="width: {{ $travee->utilisation_percentage ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avertissements -->
        @if($travee->tablettes_count > 0)
        <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Attention
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning alert-sm mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette travée contient <strong>{{ $travee->tablettes_count }} tablette(s)</strong>. 
                    Le changement de salle déplacera toute la structure.
                </div>
            </div>
        </div>
        @endif

        <!-- Actions rapides -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.tablettes.create') }}?travee_id={{ $travee->id }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une tablette
                    </a>
                    <a href="{{ route('admin.stockage.hierarchy') }}?travee={{ $travee->id }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-sitemap me-2"></i>
                        Vue hiérarchique
                    </a>
                    <button class="btn btn-outline-success btn-sm" onclick="exportStructure()">
                        <i class="fas fa-download me-2"></i>
                        Exporter structure
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion du changement de salle
    document.getElementById('salle_id').addEventListener('change', function() {
        const currentSalleId = {{ $travee->salle_id }};
        if (this.value && this.value != currentSalleId) {
            const selectedOption = this.options[this.selectedIndex];
            showChangeWarning(selectedOption.dataset.organisme);
        }
    });

    function showChangeWarning(organismeNom) {
        if ({{ $travee->tablettes_count ?? 0 }} > 0) {
            const confirmed = confirm(
                `Attention ! Cette travée contient {{ $travee->tablettes_count ?? 0 }} tablette(s). ` +
                `Le changement vers "${organismeNom}" déplacera toute la structure. ` +
                `Êtes-vous sûr de vouloir continuer ?`
            );
            
            if (!confirmed) {
                document.getElementById('salle_id').value = {{ $travee->salle_id }};
            }
        }
    }

    // Exporter la structure
    function exportStructure() {
        window.location.href = `{{ route('admin.stockage.export') }}?travee_id={{ $travee->id }}`;
    }

    // Validation du formulaire
    document.getElementById('traveeForm').addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        const salleId = document.getElementById('salle_id').value;
        
        if (!nom || !salleId) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
        
        // Confirmation si changement de salle avec contenu
        const currentSalleId = {{ $travee->salle_id }};
        if (salleId != currentSalleId && {{ $travee->tablettes_count ?? 0 }} > 0) {
            const confirmed = confirm(
                'Êtes-vous sûr de vouloir déplacer cette travée et tout son contenu vers la nouvelle salle ?'
            );
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .card-body h4 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .progress {
        height: 6px;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    .form-text {
        font-size: 0.8rem;
    }

    .fw-bold {
        font-weight: 600;
    }

    .text-muted {
        font-size: 0.875rem;
    }
</style>
@endpush