@extends('layouts.admin')

@section('title', 'Boîtes Peu Occupées')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-chart-pie me-2"></i>
            Boîtes Peu Occupées
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box-open me-2"></i>
                    Boîtes avec faible taux d'occupation
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette liste montre les boîtes contenant moins de 30% de dossiers par rapport à leur capacité estimée.
                    Vous pouvez optimiser l'espace en consolidant ces boîtes.
                </div>
                
                @if($boites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Boîte</th>
                                    <th>Localisation</th>
                                    <th>Occupation</th>
                                    <th>Dossiers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boites as $boite)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="boite-icon bg-primary">
                                                        <i class="fas fa-archive"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $boite->numero }}</h6>
                                                    <small class="text-muted">{{ $boite->reference }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($boite->position)
                                                <a href="{{ route('admin.positions.show', $boite->position) }}">
                                                    {{ $boite->position->full_path }}
                                                </a>
                                            @else
                                                <span class="text-muted">Non localisée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $boite->occupation_percentage < 10 ? 'danger' : 'warning' }}" 
                                                         style="width: {{ $boite->occupation_percentage }}%"></div>
                                                </div>
                                                <small>{{ $boite->occupation_percentage }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $boite->dossiers_count }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.boites.show', $boite) }}" 
                                                   class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.boites.edit', $boite) }}" 
                                                   class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-success" 
                                                        onclick="showConsolidateModal({{ $boite->id }})" title="Consolider">
                                                    <i class="fas fa-compress-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($boites->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $boites->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">Aucune boîte peu occupée</h5>
                        <p class="text-muted">Toutes vos boîtes sont suffisamment remplies.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de consolidation -->
<div class="modal fade" id="consolidateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Consolider une Boîte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="consolidateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point de consolider les dossiers de cette boîte avec une autre boîte.</p>
                    
                    <div class="mb-3">
                        <label for="target_boite_id" class="form-label">Boîte de destination</label>
                        <select class="form-select" id="target_boite_id" name="target_boite_id" required>
                            <option value="">Sélectionner une boîte</option>
                            @foreach($all_boites as $target)
                                <option value="{{ $target->id }}">
                                    {{ $target->numero }} ({{ $target->position ? $target->position->full_path : 'Non localisée' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="delete_empty" name="delete_empty">
                        <label class="form-check-label" for="delete_empty">
                            Supprimer la boîte source si vide après consolidation
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer la consolidation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Afficher le modal de consolidation
    function showConsolidateModal(boiteId) {
        const modal = new bootstrap.Modal(document.getElementById('consolidateModal'));
        const form = document.getElementById('consolidateForm');
        form.action = `/admin/boites/${boiteId}/consolidate`;
        modal.show();
    }
</script>
@endpush

@push('styles')
<style>
    .boite-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: white;
        font-size: 1.2rem;
    }

    .progress {
        height: 8px;
        background-color: #e9ecef;
    }
</style>
@endpush