{{-- resources/views/admin/positions/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Détails de la Position - ' . $position->nom)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-map-marker-alt me-2"></i>{{ $position->nom }}
            <span class="badge bg-{{ $position->vide ? 'warning' : 'success' }} ms-2">
                {{ $position->vide ? 'Libre' : 'Occupée' }}
            </span>
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('admin.positions.edit', $position) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nom de la position</label>
                            <div class="form-control-plaintext">{{ $position->nom }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <div class="form-control-plaintext">
                                @if($position->vide)
                                    <span class="badge bg-warning">Libre</span>
                                @else
                                    <span class="badge bg-success">Occupée</span>
                                @endif
                            </div>
                        </div>
                        @if($position->niveau)
                            <div class="mb-3">
                                <label class="form-label">Niveau</label>
                                <div class="form-control-plaintext">{{ $position->niveau }}</div>
                            </div>
                        @endif
                        @if($position->colonne)
                            <div class="mb-3">
                                <label class="form-label">Colonne</label>
                                <div class="form-control-plaintext">{{ $position->colonne }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tablette</label>
                            <div class="form-control-plaintext">
                                {{ $position->tablette->nom }}
                                <a href="{{ route('admin.tablettes.show', $position->tablette) }}" class="btn btn-sm btn-outline-info ms-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        @if($position->rangee)
                            <div class="mb-3">
                                <label class="form-label">Rangée</label>
                                <div class="form-control-plaintext">{{ $position->rangee }}</div>
                            </div>
                        @endif
                        @if($position->code_barre)
                            <div class="mb-3">
                                <label class="form-label">Code-barres</label>
                                <div class="form-control-plaintext">{{ $position->code_barre }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Chemin complet -->
                <div class="mb-3">
                    <label class="form-label">Chemin complet</label>
                    <div class="form-control-plaintext">
                        <small class="text-muted">
                            {{ $position->tablette->travee->salle->organisme->nom_org }}
                            → {{ $position->tablette->travee->salle->nom }}
                            → {{ $position->tablette->travee->nom }}
                            → {{ $position->tablette->nom }}
                            → {{ $position->nom }}
                        </small>
                    </div>
                </div>

                <!-- Métadonnées -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Créé le</label>
                            <div class="form-control-plaintext">{{ $position->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Modifié le</label>
                            <div class="form-control-plaintext">{{ $position->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides & Boîte associée -->
    <div class="col-lg-4">
        <!-- Boîte associée -->
        @if(!$position->vide && $position->boite)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-box me-2"></i>Boîte Associée</h6>
                </div>
                <div class="card-body text-center">
                    <h5 class="mb-1"><a href="{{ route('admin.boites.show', $position->boite) }}">{{ $position->boite->nom }}</a></h5>
                    <p class="text-muted mb-2">{{ $position->boite->dossiers->count() }} dossier(s)</p>
                    <a href="{{ route('admin.boites.show', $position->boite) }}" class="btn btn-info btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>Voir boîte
                    </a>
                </div>
            </div>
        @endif

        <!-- Actions rapides -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Actions Rapides</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($position->vide)
                        <a href="{{ route('admin.boites.create', ['position_id' => $position->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Ajouter une boîte
                        </a>
                    @else
                        <button class="btn btn-warning" onclick="libererPosition()">
                            <i class="fas fa-box-open me-2"></i>Libérer la position
                        </button>
                    @endif
                    <button class="btn btn-info" onclick="showQrCode()">
                        <i class="fas fa-qrcode me-2"></i>Générer QR Code
                    </button>
                    <a href="{{ route('admin.tablettes.show', $position->tablette) }}" class="btn btn-outline-primary">
                        <i class="fas fa-tablet-alt me-2"></i>Voir la tablette
                    </a>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-qrcode me-2"></i>QR Code</h6>
            </div>
            <div class="card-body text-center">
                <div id="qrcode" class="d-inline-block mb-3"></div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="qrCodeUrl" value="{{ route('admin.positions.show', $position) }}" readonly>
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyQrCodeUrl()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <button class="btn btn-primary btn-sm" onclick="downloadQrCode()">
                    <i class="fas fa-download me-2"></i>Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale de confirmation pour libérer -->
<div class="modal fade" id="libererModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="libererForm" method="POST" action="{{ route('admin.positions.toggle', $position) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Libérer la position ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir libérer la position <strong>{{ $position->nom }}</strong> ?</p>
                    <p class="text-danger"><small>Cette action détachera la boîte actuelle.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Libérer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code - {{ $position->nom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcodeModal"></div>
                <p class="mt-3"><small>Scannez pour accéder à cette position</small></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="downloadQrCode()">Télécharger</button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour suppression (si besoin) -->
@if($position->vide)
    <form id="deleteForm" action="{{ route('admin.positions.destroy', $position) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode.js/lib/qrcode.min.js"></script>
<script>
// Générer le QR code
function generateQrCode(containerId, url) {
    new QRCode(document.getElementById(containerId), {
        text: url,
        width: 128,
        height: 128
    });
}

// Afficher le QR code dans la modale
function showQrCode() {
    const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    document.getElementById('qrcodeModal').innerHTML = '';
    generateQrCode('qrcodeModal', "{{ route('admin.positions.show', $position) }}");
    modal.show();
}

// Copier l'URL du QR code
function copyQrCodeUrl() {
    const input = document.getElementById('qrCodeUrl');
    input.select();
    document.execCommand('copy');
    alert('URL copiée !');
}

// Télécharger le QR code
function downloadQrCode() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const pngUrl = canvas.toDataURL('image/png');
        const downloadLink = document.createElement('a');
        downloadLink.href = pngUrl;
        downloadLink.download = 'qrcode-position-{{ $position->nom }}.png';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
}

// Libérer la position
function libererPosition() {
    const modal = new bootstrap.Modal(document.getElementById('libererModal'));
    modal.show();
}

// Générer le QR code principal
document.addEventListener('DOMContentLoaded', function () {
    generateQrCode('qrcode', "{{ route('admin.positions.show', $position) }}");
});
</script>
@endpush