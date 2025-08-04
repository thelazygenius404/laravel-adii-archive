{{-- resources/views/admin/travees/index.blade.php - VERSION CORRIGÉE --}}
@extends('layouts.admin')

@section('title', 'Gestion des Travées')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-layer-group me-2"></i>
            Gestion des Travées
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour 
            </a>
            <a href="{{ route('admin.travees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Travée
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportTravees()">
                        <i class="fas fa-download me-2"></i>Exporter la liste
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="showBulkActions()">
                        <i class="fas fa-tasks me-2"></i>Actions groupées
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nom de la travée...">
                    </div>
                    <div class="col-md-4">
                        <label for="salle_id" class="form-label">Salle</label>
                        <select class="form-select" id="salle_id" name="salle_id">
                            <option value="">Toutes les salles</option>
                            @foreach($salles as $salle)
                                <option value="{{ $salle->id }}" {{ request('salle_id') == $salle->id ? 'selected' : '' }}>
                                    {{ $salle->nom }} ({{ $salle->organisme->nom_org }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Par page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Liste des travées -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des Travées
                    <span class="badge bg-primary ms-2">{{ $travees->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($travees->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Travée</th>
                                    <th>Salle</th>
                                    <th>Organisme</th>
                                    <th>Tablettes</th>
                                    <th>Positions totales</th>
                                    <th>Utilisation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($travees as $travee)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input travee-checkbox" value="{{ $travee->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="travee-icon bg-success text-white rounded">
                                                        <i class="fas fa-layer-group"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $travee->nom }}</h6>
                                                    <small class="text-muted">ID: {{ $travee->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $travee->salle->nom }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $travee->salle->organisme->nom_org }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $travee->tablettes_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $travee->positions_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 80px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $travee->utilisation_percentage < 50 ? 'success' : ($travee->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $travee->utilisation_percentage ?? 0 }}%"></div>
                                                </div>
                                                <small>{{ number_format($travee->utilisation_percentage ?? 0, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.travees.show', $travee) }}" 
                                                   class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.travees.edit', $travee) }}" 
                                                   class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="deleteTravee({{ $travee->id }})" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($travees->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $travees->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune travée trouvée</h5>
                        <p class="text-muted">Commencez par créer votre première travée.</p>
                        <a href="{{ route('admin.travees.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer une travée
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Variables globales pour le debug
    let selectedTravees = [];

    // CORRECTION 1: Fonction de mise à jour de la sélection
    function updateSelectedTravees() {
        selectedTravees = Array.from(document.querySelectorAll('.travee-checkbox:checked')).map(cb => cb.value);
        console.log('Travées sélectionnées:', selectedTravees);
    }

    // CORRECTION 2: Sélection multiple avec mise à jour
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.travee-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedTravees();
    });

    // CORRECTION 3: Écouteur sur les checkboxes individuelles
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('travee-checkbox')) {
            updateSelectedTravees();
        }
    });

    // Supprimer une travée
    function deleteTravee(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette travée ? Cette action supprimera également toutes les tablettes et positions associées.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.travees.index') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // CORRECTION 4: Actions groupées complètement refaites
    function showBulkActions() {
        updateSelectedTravees(); // S'assurer que la sélection est à jour
        
        if (selectedTravees.length === 0) {
            alert('Veuillez sélectionner au moins une travée.');
            return;
        }
        
        // Supprimer l'ancien modal s'il existe
        const existingModal = document.getElementById('bulkActionsModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // CORRECTION 5: Modal correct avec gestionnaires d'événements
        const modalHtml = `
            <div class="modal fade" id="bulkActionsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Actions Groupées sur les Travées</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>${selectedTravees.length}</strong> travée(s) sélectionnée(s)
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Action à effectuer :</label>
                                <select class="form-select" id="bulkActionSelect">
                                    <option value="">Choisir une action...</option>
                                    <option value="export">📤 Exporter la sélection</option>
                                    <option value="delete">🗑️ Supprimer (travées vides uniquement)</option>
                                    <option value="move">📦 Déplacer vers une autre salle</option>
                                    <option value="optimize">⚡ Optimiser l'organisation</option>
                                </select>
                            </div>
                            <div id="moveOptions" style="display: none;">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <label class="form-label">Nouvelle salle :</label>
                                        <select class="form-select" id="newSalleId">
                                            <option value="">Sélectionner une salle</option>
                                            @foreach($salles as $salle)
                                                <option value="{{ $salle->id }}">{{ $salle->nom }} ({{ $salle->organisme->nom_org }})</option>
                                            @endforeach
                                        </select>
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <small>⚠️ Cette action déplacera toutes les tablettes associées.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" id="executeBulkBtn" disabled>
                                <i class="fas fa-cog me-2"></i>Exécuter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // CORRECTION 6: Attacher les événements APRÈS la création du modal
        const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
        
        // Gérer l'affichage des options de déplacement
        document.getElementById('bulkActionSelect').addEventListener('change', function() {
            const moveOptions = document.getElementById('moveOptions');
            const executeBtn = document.getElementById('executeBulkBtn');
            
            moveOptions.style.display = this.value === 'move' ? 'block' : 'none';
            executeBtn.disabled = !this.value;
        });
        
        // CORRECTION 7: Attacher l'événement au bouton Exécuter
        document.getElementById('executeBulkBtn').addEventListener('click', function() {
            executeTraveesBulkAction();
        });
        
        modal.show();
    }

    // CORRECTION 8: Fonction d'exécution corrigée
    function executeTraveesBulkAction() {
    const action = document.getElementById('bulkActionSelect').value;
    const newSalleId = document.getElementById('newSalleId') ? document.getElementById('newSalleId').value : null;
    
    if (!action) {
        alert('Veuillez sélectionner une action.');
        return;
    }
    
    if (action === 'move' && !newSalleId) {
        alert('Veuillez sélectionner une salle de destination.');
        return;
    }
    
    // Désactiver le bouton pendant traitement
    const executeBtn = document.getElementById('executeBulkBtn');
    executeBtn.disabled = true;
    executeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
    
    const formData = new FormData();
    formData.append('action', action);
    formData.append('travee_ids', JSON.stringify(selectedTravees));
    if (newSalleId) {
        formData.append('new_salle_id', newSalleId);
    }
    
    // DEBUG DÉTAILLÉ
    console.log('=== DEBUG REQUÊTE ===');
    console.log('Action:', action);
    console.log('Travées sélectionnées:', selectedTravees);
    console.log('Nouvelle salle ID:', newSalleId);
    console.log('FormData entries:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    fetch('{{ route("admin.travees.bulk-action") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('=== RÉPONSE SERVEUR ===');
        console.log('Status:', response.status);
        console.log('Headers:', response.headers);
        
        // IMPORTANT : Lire la réponse même en cas d'erreur 422
        return response.text().then(text => {
            console.log('Réponse brute:', text);
            
            try {
                const data = JSON.parse(text);
                console.log('Données JSON:', data);
                
                if (response.ok) {
                    if (data.success) {
                        alert(data.message);
                        if (action !== 'export') {
                            window.location.reload();
                        }
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                } else {
                    // ERREUR 422 - Afficher les détails de validation
                    if (data.errors) {
                        console.log('Erreurs de validation:', data.errors);
                        let errorMessage = 'Erreurs de validation:\n';
                        for (const [field, messages] of Object.entries(data.errors)) {
                            errorMessage += `- ${field}: ${messages.join(', ')}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert('Erreur de validation: ' + (data.message || 'Erreur inconnue'));
                    }
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                alert('Réponse invalide du serveur: ' + text);
            }
        });
    })
    .catch(error => {
        console.error('=== ERREUR RÉSEAU ===');
        console.error('Erreur:', error);
        alert('Erreur réseau: ' + error.message);
    })
    .finally(() => {
        // Réactiver le bouton
        executeBtn.disabled = false;
        executeBtn.innerHTML = '<i class="fas fa-cog me-2"></i>Exécuter';
        
        // Fermer le modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal'));
        if (modal) {
            modal.hide();
        }
    });
}
    // Exporter
    function exportTravees() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', '1');
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    // Recherche en temps réel
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                this.form.submit();
            }
        }, 500);
    });

    // CORRECTION 9: Debug des routes
    console.log('Route bulk-action:', '{{ route("admin.travees.bulk-action") }}');
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
</script>
@endpush

@push('styles')
<style>
    .travee-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.2rem;
    }

    .progress {
        background-color: #e9ecef;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
@endpush