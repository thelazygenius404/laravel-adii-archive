@extends('layouts.admin')

@section('title', 'Détails de la Boîte')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-archive me-2"></i>
            Détails de la Boîte : {{ $boite->numero }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.boites.edit', $boite) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            @if($boite->detruite)
                <button class="btn btn-outline-success" onclick="restoreBoite()">
                    <i class="fas fa-trash-restore me-2"></i>
                    Restaurer
                </button>
            @else
                <button class="btn btn-outline-danger" onclick="confirmDestruction()">
                    <i class="fas fa-trash me-2"></i>
                    Détruire
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Numéro</label>
                            <div class="form-control-plaintext">{{ $boite->numero }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Code Thématique</label>
                            <div class="form-control-plaintext">{{ $boite->code_thematique ?? '-' }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Code Topographique</label>
                            <div class="form-control-plaintext">{{ $boite->code_topo ?? '-' }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <div class="form-control-plaintext">
                                @if($boite->detruite)
                                    <span class="badge bg-secondary">Détruite</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Capacité</label>
                            <div class="form-control-plaintext">{{ $boite->capacite }} dossiers</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dossiers présents</label>
                            <div class="form-control-plaintext">{{ $boite->nbr_dossiers }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Taux d'occupation</label>
                    <div class="d-flex align-items-center">
                        <div class="progress me-3" style="width: 200px; height: 10px;">
                            <div class="progress-bar bg-{{ $boite->utilization_color }}" 
                                 style="width: {{ $boite->utilisation_percentage }}%"></div>
                        </div>
                        <span>{{ $boite->utilisation_percentage }}%</span>
                    </div>
                </div>
                
                @if($boite->description)
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <div class="form-control-plaintext">{{ $boite->description }}</div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Localisation -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Localisation
                </h5>
            </div>
            <div class="card-body">
                @if($boite->position)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <div class="form-control-plaintext">
                                    <a href="{{ route('admin.positions.show', $boite->position) }}">
                                        {{ $boite->position->nom }}
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tablette</label>
                                <div class="form-control-plaintext">
                                    <a href="{{ route('admin.tablettes.show', $boite->position->tablette) }}">
                                        {{ $boite->position->tablette->nom }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Travée</label>
                                <div class="form-control-plaintext">
                                    <a href="{{ route('admin.travees.show', $boite->position->tablette->travee) }}">
                                        {{ $boite->position->tablette->travee->nom }}
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Salle</label>
                                <div class="form-control-plaintext">
                                    <a href="{{ route('admin.salles.show', $boite->position->tablette->travee->salle) }}">
                                        {{ $boite->position->tablette->travee->salle->nom }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chemin complet</label>
                        <div class="form-control-plaintext">
                            {{ $boite->full_location }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Cette boîte n'est actuellement localisée dans aucune position</p>
                        <a href="{{ route('admin.boites.edit', $boite) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Assigner une position
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Dossiers -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-folder me-2"></i>
                    Dossiers Contenus
                </h5>
            </div>
            <div class="card-body">
                @if($boite->dossiers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Titre</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boite->dossiers as $dossier)
                                    <tr>
                                        <td>{{ $dossier->reference }}</td>
                                        <td>{{ Str::limit($dossier->titre, 30) }}</td>
                                        <td>
                                            @if($dossier->statut === 'elimine')
                                                <span class="badge bg-secondary">Éliminé</span>
                                            @else
                                                <span class="badge bg-success">Actif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.dossiers.show', $dossier) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.dossiers.index', ['boite_id' => $boite->id]) }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les dossiers
                        </a>
                        @if(!$boite->detruite && $boite->hasSpace())
                            <a href="{{ route('admin.dossiers.create', ['boite_id' => $boite->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Ajouter un dossier
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Cette boîte ne contient actuellement aucun dossier</p>
                        @if(!$boite->detruite)
                            <a href="{{ route('admin.dossiers.create', ['boite_id' => $boite->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Ajouter un dossier
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recommandations -->
        @if(!empty($boite->recommended_actions))
            <div class="card mt-4 border-{{ $boite->detruite ? 'secondary' : ($boite->utilisation_percentage < 30 ? 'warning' : ($boite->utilisation_percentage > 95 ? 'danger' : 'success')) }}">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Recommandations
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($boite->recommended_actions as $action)
                            <li>{{ $action }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Actions rapides -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($boite->position)
                        <a href="{{ route('admin.positions.show', $boite->position) }}" class="btn btn-info">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Voir la position
                        </a>
                    @else
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-search me-2"></i>
                            Trouver une position
                        </a>
                    @endif
                    
                    @if(!$boite->detruite && $boite->hasSpace())
                        <a href="{{ route('admin.dossiers.create', ['boite_id' => $boite->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Ajouter un dossier
                        </a>
                    @endif
                    
                    <button class="btn btn-outline-primary" onclick="showQrCode()">
                        <i class="fas fa-qrcode me-2"></i>
                        Générer QR Code
                    </button>
                    
                    @if($boite->detruite)
                        <button class="btn btn-success" onclick="restoreBoite()">
                            <i class="fas fa-trash-restore me-2"></i>
                            Restaurer la boîte
                        </button>
                    @else
                        <button class="btn btn-outline-danger" onclick="confirmDestruction()">
                            <i class="fas fa-trash me-2"></i>
                            Détruire la boîte
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h2 class="text-primary">{{ $boite->nbr_dossiers }}</h2>
                    <p class="text-muted">Dossiers dans cette boîte</p>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <span class="text-muted">Capacité restante :</span>
                    <span class="fw-bold">{{ $boite->capacite_restante }} dossiers</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Score d'efficacité :</span>
                    <span class="fw-bold">{{ $boite->efficiency_score }}/100</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Âge de la boîte :</span>
                    <span class="fw-bold">{{ $boite->age_in_days }} jours</span>
                </div>
                
                @if($boite->position)
                    <hr>
                    
                    <div class="mb-2">
                        <span class="text-muted">Position :</span>
                        <span class="fw-bold">{{ $boite->position->nom }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Tablette :</span>
                        <span class="fw-bold">{{ $boite->position->tablette->nom }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Travée :</span>
                        <span class="fw-bold">{{ $boite->position->tablette->travee->nom }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Codes et identifiants -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-barcode me-2"></i>
                    Codes et Identifiants
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="text-muted">Code-barres :</span>
                    <div class="font-monospace">{{ $boite->generateBarcode() }}</div>
                </div>
                <div class="mb-2">
                    <span class="text-muted">ID :</span>
                    <div class="font-monospace">{{ $boite->id }}</div>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Créée le :</span>
                    <div class="font-monospace">{{ $boite->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Dernière activité :</span>
                    <div class="font-monospace">{{ $boite->last_activity->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de destruction -->
<div class="modal fade" id="destructionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la destruction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir marquer cette boîte comme détruite ?</p>
                <p class="text-danger"><small>Cette action marquera également tous les dossiers contenus comme éliminés.</small></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette action est réversible mais libérera la position actuelle.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="destructionForm" method="POST" action="{{ route('admin.boites.destroy-box', $boite) }}" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('destructionForm').submit()">
                    Confirmer la destruction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de restauration -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la restauration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir restaurer cette boîte ?</p>
                <p class="text-success"><small>Cette action restaurera également tous les dossiers contenus.</small></p>
                @if($boite->position && !$boite->position->vide)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        La position précédente est maintenant occupée. Vous devrez assigner une nouvelle position.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="restoreForm" method="POST" action="{{ route('admin.boites.restore-box', $boite) }}" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button" class="btn btn-success" onclick="document.getElementById('restoreForm').submit()">
                    Confirmer la restauration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>
                    QR Code - {{ $boite->numero }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" class="mb-3"></div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="qrCodeUrl" 
                           value="{{ route('admin.boites.show', $boite) }}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyQrCodeUrl()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <button class="btn btn-primary" onclick="downloadQrCode()">
                    <i class="fas fa-download me-2"></i>
                    Télécharger QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    // Confirmer la destruction
    function confirmDestruction() {
        const modal = new bootstrap.Modal(document.getElementById('destructionModal'));
        modal.show();
    }
    
    // Restaurer la boîte
    function restoreBoite() {
        const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
        modal.show();
    }
    
    // Afficher le QR Code
    function showQrCode() {
        const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
        modal.show();
        
        // Générer le QR Code
        new QRCode(document.getElementById("qrCodeContainer"), {
            text: "{{ $boite->qr_code_data }}",
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }
    
    // Copier l'URL du QR Code
    function copyQrCodeUrl() {
        const input = document.getElementById('qrCodeUrl');
        input.select();
        document.execCommand('copy');
        alert('URL copiée dans le presse-papier');
    }
    
    // Télécharger le QR Code
    function downloadQrCode() {
        const canvas = document.querySelector('#qrCodeContainer canvas');
        const link = document.createElement('a');
        link.download = 'qr-code-boite-{{ $boite->id }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
</script>
@endpush

@push('styles')
<style>
    .font-monospace {
        font-family: monospace;
        font-size: 0.85rem;
        word-break: break-all;
    }
    
    #qrCodeContainer {
        display: inline-block;
        padding: 20px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    
    .progress {
        height: 10px;
        background-color: #e9ecef;
    }
    
    .bg-gray {
        background-color: #6c757d;
    }
</style>
@endpush