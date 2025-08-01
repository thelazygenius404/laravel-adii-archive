{{-- resources/views/admin/salles/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modifier la Salle - ' . $salle->nom)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier la Salle - {{ $salle->nom }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.salles.show', $salle) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>
                Voir détails
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
                    Informations de la Salle
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.salles.update', $salle) }}" method="POST" id="salleForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom', $salle->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="organisme_id" class="form-label">Organisme <span class="text-danger">*</span></label>
                            <select class="form-select @error('organisme_id') is-invalid @enderror" 
                                    id="organisme_id" name="organisme_id" required>
                                <option value="">Sélectionner un organisme</option>
                                @foreach($organismes as $organisme)
                                    <option value="{{ $organisme->id }}" {{ old('organisme_id', $salle->organisme_id) == $organisme->id ? 'selected' : '' }}>
                                        {{ $organisme->nom_org }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organisme_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $salle->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Localisation -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control @error('adresse') is-invalid @enderror" 
                                   id="adresse" name="adresse" value="{{ old('adresse', $salle->adresse) }}">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="batiment" class="form-label">Bâtiment</label>
                            <input type="text" class="form-control @error('batiment') is-invalid @enderror" 
                                   id="batiment" name="batiment" value="{{ old('batiment', $salle->batiment) }}">
                            @error('batiment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="etage" class="form-label">Étage</label>
                            <input type="text" class="form-control @error('etage') is-invalid @enderror" 
                                   id="etage" name="etage" value="{{ old('etage', $salle->etage) }}">
                            @error('etage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Capacité -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacite_max" class="form-label">Capacité maximale <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('capacite_max') is-invalid @enderror" 
                                   id="capacite_max" name="capacite_max" value="{{ old('capacite_max', $salle->capacite_max) }}" 
                                   min="1" required>
                            @error('capacite_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="capacite_actuelle" class="form-label">Capacité actuelle</label>
                            <input type="number" class="form-control" 
                                   id="capacite_actuelle" name="capacite_actuelle" 
                                   value="{{ $salle->capacite_actuelle }}" readonly>
                            <small class="form-text text-muted">Cette valeur est calculée automatiquement</small>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="actif" name="actif" 
                               {{ old('actif', $salle->actif ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">
                            Salle active
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">
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
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations Actuelles
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Date de création</small>
                    <div>{{ $salle->created_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Dernière modification</small>
                    <div>{{ $salle->updated_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Utilisation actuelle</small>
                    <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 100px;">
                            <div class="progress-bar bg-{{ $salle->utilisation_percentage < 50 ? 'success' : ($salle->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $salle->utilisation_percentage }}%"></div>
                        </div>
                        <span>{{ number_format($salle->utilisation_percentage, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Structure actuelle -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2"></i>
                    Structure Actuelle
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="row">
                        <div class="col-4">
                            <h5 class="text-primary">{{ $salle->travees()->count() }}</h5>
                            <small class="text-muted">Travées</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-info">{{ $salle->tablettes()->count() }}</h5>
                            <small class="text-muted">Tablettes</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-success">{{ $salle->positions()->count() }}</h5>
                            <small class="text-muted">Positions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.salles.show', $salle) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-2"></i>
                        Voir les détails
                    </a>
                    <a href="{{ route('admin.stockage.hierarchy') }}?salle={{ $salle->id }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-sitemap me-2"></i>
                        Vue hiérarchique
                    </a>
                    <a href="{{ route('admin.travees.create') }}?salle_id={{ $salle->id }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une travée
                    </a>
                    <button class="btn btn-outline-warning btn-sm" onclick="updateCapacity()">
                        <i class="fas fa-sync me-2"></i>
                        Recalculer la capacité
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validation du formulaire
    document.getElementById('salleForm').addEventListener('submit', function(e) {
        const capaciteMax = parseInt(document.getElementById('capacite_max').value);
        const capaciteActuelle = parseInt(document.getElementById('capacite_actuelle').value);
        
        if (capaciteMax < capaciteActuelle) {
            e.preventDefault();
            alert('La capacité maximale ne peut pas être inférieure à la capacité actuelle.');
            return false;
        }
    });

    // Recalculer la capacité
    function updateCapacity() {
        fetch(`{{ route('admin.salles.update-capacity', $salle) }}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('capacite_actuelle').value = data.capacite_actuelle;
                location.reload();
            } else {
                alert('Erreur lors du recalcul de la capacité');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du recalcul de la capacité');
        });
    }
</script>
@endpush

@push('styles')
<style>
    .progress {
        height: 8px;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    
    .card-body h5 {
        font-size: 1.5rem;
        font-weight: 600;
    }
</style>
@endpush