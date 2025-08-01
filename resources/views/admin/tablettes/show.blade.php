@extends('layouts.admin')

@section('title', 'Détails de la Tablette')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-tablet-alt me-2"></i>
            Détails de la Tablette : {{ $tablette->nom }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.tablettes.edit', $tablette) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            @if($tablette->positions->count() == 0)
                <button class="btn btn-outline-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash me-2"></i>
                    Supprimer
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
                            <label class="form-label">Nom de la tablette</label>
                            <div class="form-control-plaintext">{{ $tablette->nom }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Travée</label>
                            <div class="form-control-plaintext">
                                {{ $tablette->travee->nom }}
                                <a href="{{ route('admin.travees.show', $tablette->travee) }}" class="btn btn-sm btn-outline-info ms-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Salle</label>
                            <div class="form-control-plaintext">
                                {{ $tablette->travee->salle->nom }}
                                <a href="{{ route('admin.salles.show', $tablette->travee->salle) }}" class="btn btn-sm btn-outline-info ms-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Organisme</label>
                            <div class="form-control-plaintext">
                                {{ $tablette->travee->salle->organisme->nom_org }}
                                <a href="{{ route('admin.organismes.show', $tablette->travee->salle->organisme) }}" class="btn btn-sm btn-outline-info ms-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Chemin complet</label>
                    <div class="form-control-plaintext">
                        {{ $tablette->travee->salle->organisme->nom_org }} > 
                        {{ $tablette->travee->salle->nom }} > 
                        {{ $tablette->travee->nom }} > 
                        {{ $tablette->nom }}
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Date de création</label>
                            <div class="form-control-plaintext">{{ $tablette->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Dernière modification</label>
                            <div class="form-control-plaintext">{{ $tablette->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistiques d'Occupation
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h2 class="text-primary">{{ $stats['total_positions'] }}</h2>
                            <p class="text-muted">Positions totales</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h2 class="text-success">{{ $stats['positions_occupees'] }}</h2>
                            <p class="text-muted">Positions occupées</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h2 class="text-warning">{{ $stats['positions_libres'] }}</h2>
                            <p class="text-muted">Positions libres</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Taux d'occupation: {{ $stats['utilisation_percentage'] }}%</h6>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $stats['utilisation_percentage'] < 50 ? 'success' : ($stats['utilisation_percentage'] < 80 ? 'warning' : 'danger') }}" 
                             style="width: {{ $stats['utilisation_percentage'] }}%" role="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
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
                    <a href="{{ route('admin.positions.create', ['tablette_id' => $tablette->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une position
                    </a>
                    
                    <a href="{{ route('admin.positions.index', ['tablette_id' => $tablette->id]) }}" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>
                        Voir toutes les positions
                    </a>
                    
                    <button class="btn btn-info" onclick="showPositionsMap()">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Plan des positions
                    </button>
                    
                    <button class="btn btn-outline-secondary" onclick="showQrCode()">
                        <i class="fas fa-qrcode me-2"></i>
                        Générer QR Code
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Dernières positions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Dernières Positions
                </h5>
            </div>
            <div class="card-body">
                @if($tablette->positions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($tablette->positions->sortByDesc('updated_at')->take(5) as $position)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $position->nom }}</h6>
                                        <small class="text-muted">
                                            {{ $position->updated_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div>
                                        @if($position->vide)
                                            <span class="badge bg-warning">Libre</span>
                                        @else
                                            <span class="badge bg-success">Occupée</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.positions.index', ['tablette_id' => $tablette->id]) }}" class="btn btn-sm btn-outline-primary">
                            Voir toutes les positions
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune position dans cette tablette</p>
                        <a href="{{ route('admin.positions.create', ['tablette_id' => $tablette->id]) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Ajouter une position
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Informations techniques -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Informations Techniques
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">ID:</small>
                    <div class="font-monospace">{{ $tablette->id }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Slug:</small>
                    <div class="font-monospace">{{ Str::slug($tablette->nom) }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Chemin API:</small>
                    <div class="font-monospace">/api/tablettes/{{ $tablette->id }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la tablette <strong>{{ $tablette->nom }}</strong> ?</p>
                @if($tablette->positions->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cette tablette contient {{ $tablette->positions->count() }} position(s) et ne peut pas être supprimée.
                    </div>
                @else
                    <p class="text-danger"><small>Cette action est irréversible.</small></p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                @if($tablette->positions->count() == 0)
                    <form action="{{ route('admin.tablettes.destroy', $tablette) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal du plan des positions -->
<div class="modal fade" id="positionsMapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    Plan des Positions - {{ $tablette->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($tablette->positions->count() > 0)
                    <div class="row">
                        @foreach($tablette->positions->sortBy('nom') as $position)
                            <div class="col-md-3 mb-3">
                                <div class="card border-{{ $position->vide ? 'warning' : 'success' }}">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-map-marker-alt {{ $position->vide ? 'text-warning' : 'text-success' }} fa-2x mb-2"></i>
                                        <h6 class="card-title mb-1">{{ $position->nom }}</h6>
                                        <span class="badge bg-{{ $position->vide ? 'warning' : 'success' }}">
                                            {{ $position->vide ? 'Libre' : 'Occupée' }}
                                        </span>
                                        @if(!$position->vide && $position->boite)
                                            <br><small class="text-muted mt-1">{{ $position->boite->numero }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune position dans cette tablette</p>
                        <a href="{{ route('admin.positions.create', ['tablette_id' => $tablette->id]) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Ajouter une position
                        </a>
                    </div>
                @endif
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
                    QR Code - {{ $tablette->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" class="mb-3"></div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="qrCodeUrl" 
                           value="{{ route('admin.tablettes.show', $tablette) }}" readonly>
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
    // Confirmation de suppression
    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    // Afficher le plan des positions
    function showPositionsMap() {
        const modal = new bootstrap.Modal(document.getElementById('positionsMapModal'));
        modal.show();
    }
    
    // Afficher le QR Code
    function showQrCode() {
        const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
        modal.show();
        
        // Générer le QR Code
        new QRCode(document.getElementById("qrCodeContainer"), {
            text: "{{ route('admin.tablettes.show', $tablette) }}",
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
        link.download = 'qr-code-tablette-{{ $tablette->id }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
</script>
@endpush

@push('styles')
<style>
    .stat-card {
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    .stat-card h2 {
        margin-bottom: 5px;
        font-weight: 700;
    }
    
    .stat-card p {
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    
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
</style>
@endpush