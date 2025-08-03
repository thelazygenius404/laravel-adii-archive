{{-- resources/views/admin/tablettes/index.blade.php - VERSION CORRIGÉE --}}
@extends('layouts.admin')

@section('title', 'Gestion des Tablettes')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-table me-2"></i>
            Gestion des Tablettes
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.tablettes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Tablette
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportData()">
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
                               value="{{ request('search') }}" placeholder="Nom de tablette...">
                    </div>
                    <div class="col-md-3">
                        <label for="travee_id" class="form-label">Travée</label>
                        <select class="form-select" id="travee_id" name="travee_id">
                            <option value="">Toutes les travées</option>
                            @foreach($travees as $travee)
                                <option value="{{ $travee->id }}" {{ request('travee_id') == $travee->id ? 'selected' : '' }}>
                                    {{ $travee->nom }} ({{ $travee->salle->nom }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Par page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-table text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $tablettes->total() }}</h3>
                <p class="text-muted mb-0">Tablettes Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $tablettes->sum('positions_count') }}</h3>
                <p class="text-muted mb-0">Positions Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-percentage text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ number_format($tablettes->where('positions_count', '>', 0)->avg('utilisation_percentage') ?? 0, 1) }}%</h3>
                <p class="text-muted mb-0">Utilisation Moyenne</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-archive text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ $travees->count() }}</h3>
                <p class="text-muted mb-0">Travées Connectées</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste des tablettes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des Tablettes
                    <span class="badge bg-primary ms-2">{{ $tablettes->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($tablettes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Tablette</th>
                                    <th>Localisation</th>
                                    <th>Positions</th>
                                    <th>Utilisation</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tablettes as $tablette)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input tablette-checkbox" value="{{ $tablette->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="tablette-icon bg-{{ $tablette->utilisation_percentage > 80 ? 'danger' : ($tablette->utilisation_percentage > 50 ? 'warning' : 'success') }} text-white">
                                                        <i class="fas fa-table"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $tablette->nom }}</h6>
                                                    <small class="text-muted">ID: {{ $tablette->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $tablette->travee->salle->nom }}</strong>
                                                <br><small class="text-muted">{{ $tablette->travee->salle->organisme->nom_org }}</small>
                                                <br><small class="text-muted">{{ $tablette->travee->nom }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $tablette->positions_count }}</span>
                                            @if($tablette->positions_count > 0)
                                                <br><small class="text-muted">{{ $tablette->positions_occupees ?? 0 }} occupées</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tablette->positions_count > 0)
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $tablette->utilisation_percentage < 50 ? 'success' : ($tablette->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $tablette->utilisation_percentage }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($tablette->utilisation_percentage, 1) }}%</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tablette->positions_count == 0)
                                                <span class="badge bg-secondary">Vide</span>
                                            @elseif($tablette->utilisation_percentage >= 90)
                                                <span class="badge bg-danger">Pleine</span>
                                            @elseif($tablette->utilisation_percentage >= 70)
                                                <span class="badge bg-warning">Occupée</span>
                                            @else
                                                <span class="badge bg-success">Disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.tablettes.show', $tablette) }}" 
                                                   class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tablettes.edit', $tablette) }}" 
                                                   class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($tablette->positions_count == 0)
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="deleteTablette({{ $tablette->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-secondary" disabled title="Contient des positions">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($tablettes->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tablettes->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-table fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune tablette trouvée</h5>
                        <p class="text-muted">Commencez par créer votre première tablette.</p>
                        <a href="{{ route('admin.tablettes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer une tablette
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
    let selectedTablettes = [];

    // CORRECTION 1: Fonction de mise à jour de la sélection
    function updateSelectedTablettes() {
        selectedTablettes = Array.from(document.querySelectorAll('.tablette-checkbox:checked')).map(cb => cb.value);
        console.log('Tablettes sélectionnées:', selectedTablettes);
    }

    // CORRECTION 2: Sélection multiple avec mise à jour
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.tablette-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedTablettes();
    });

    // CORRECTION 3: Écouteur sur les checkboxes individuelles
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('tablette-checkbox')) {
            updateSelectedTablettes();
        }
    });

    // Supprimer une tablette
    function deleteTablette(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette tablette ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.tablettes.index') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Exporter les données
    function exportData() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.tablettes.export') }}?${params.toString()}`;
    }

    // CORRECTION 4: Actions groupées complètement refaites
    function showBulkActions() {
        updateSelectedTablettes(); // S'assurer que la sélection est à jour
        
        if (selectedTablettes.length === 0) {
            alert('Veuillez sélectionner au moins une tablette.');
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
                            <h5 class="modal-title">Actions Groupées sur les Tablettes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>${selectedTablettes.length}</strong> tablette(s) sélectionnée(s)
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Action à effectuer :</label>
                                <select class="form-select" id="bulkActionSelect">
                                    <option value="">Choisir une action...</option>
                                    <option value="export">📤 Exporter la sélection</option>
                                    <option value="delete">🗑️ Supprimer (tablettes vides uniquement)</option>
                                    <option value="move">📦 Déplacer vers une autre travée</option>
                                    <option value="optimize">⚡ Optimiser l'organisation</option>
                                </select>
                            </div>
                            <div id="moveOptions" style="display: none;">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <label class="form-label">Nouvelle travée :</label>
                                        <select class="form-select" id="newTraveeId">
                                            <option value="">Sélectionner une travée</option>
                                            @foreach($travees as $travee)
                                                <option value="{{ $travee->id }}">{{ $travee->nom }} ({{ $travee->salle->nom }})</option>
                                            @endforeach
                                        </select>
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <small>⚠️ Cette action déplacera les tablettes vers la nouvelle travée.</small>
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
            executeTablettesBulkAction();
        });
        
        modal.show();
    }

    // CORRECTION 8: Fonction d'exécution corrigée
    function executeTablettesBulkAction() {
        const action = document.getElementById('bulkActionSelect').value;
        const newTraveeId = document.getElementById('newTraveeId') ? document.getElementById('newTraveeId').value : null;
        
        if (!action) {
            alert('Veuillez sélectionner une action.');
            return;
        }
        
        if (action === 'move' && !newTraveeId) {
            alert('Veuillez sélectionner une travée de destination.');
            return;
        }
        
        // Désactiver le bouton pendant traitement
        const executeBtn = document.getElementById('executeBulkBtn');
        executeBtn.disabled = true;
        executeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
        
        const formData = new FormData();
        formData.append('action', action);
        formData.append('tablette_ids', JSON.stringify(selectedTablettes));
        if (newTraveeId) {
            formData.append('new_travee_id', newTraveeId);
        }
        
        // DEBUG DÉTAILLÉ
        console.log('=== DEBUG REQUÊTE TABLETTES ===');
        console.log('Action:', action);
        console.log('Tablettes sélectionnées:', selectedTablettes);
        console.log('Nouvelle travée ID:', newTraveeId);
        console.log('FormData entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        fetch('{{ route("admin.tablettes.bulk-action") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('=== RÉPONSE SERVEUR TABLETTES ===');
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
                            
                            // Afficher les résultats détaillés si disponibles (pour optimize)
                            if (data.results && Array.isArray(data.results)) {
                                let detailsHtml = '<div class="mt-3"><h6>Détails de l\'optimisation:</h6><ul>';
                                data.results.forEach(result => {
                                    detailsHtml += `<li><strong>${result.tablette}</strong>: ${result.positions_total} positions, ${result.utilisation}% d'utilisation (${result.efficacite})</li>`;
                                });
                                detailsHtml += '</ul></div>';
                                document.querySelector('#bulkActionsModal .modal-body').insertAdjacentHTML('beforeend', detailsHtml);
                                
                                // Ne pas recharger automatiquement pour permettre de voir les résultats
                                setTimeout(() => {
                                    if (confirm('Voulez-vous recharger la page pour voir les modifications ?')) {
                                        window.location.reload();
                                    }
                                }, 2000);
                            } else if (action !== 'export') {
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
            console.error('=== ERREUR RÉSEAU TABLETTES ===');
            console.error('Erreur:', error);
            alert('Erreur réseau: ' + error.message);
        })
        .finally(() => {
            // Réactiver le bouton
            executeBtn.disabled = false;
            executeBtn.innerHTML = '<i class="fas fa-cog me-2"></i>Exécuter';
            
            // Fermer le modal si pas d'erreur ou si export
            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal'));
            if (modal && action === 'export') {
                modal.hide();
            }
        });
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
    console.log('Route bulk-action:', '{{ route("admin.tablettes.bulk-action") }}');
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
</script>
@endpush

@push('styles')
<style>
    .tablette-icon {
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

    .alert {
        border-radius: 8px;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
</style>
@endpush