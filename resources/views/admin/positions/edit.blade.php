{{-- resources/views/admin/positions/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Modifier la Position - ' . $position->nom)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title"><i class="fas fa-edit me-2"></i>Modifier la Position</h1>
        <div class="btn-group">
            <a href="{{ route('admin.positions.show', $position) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>Voir
            </a>
            <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Annuler
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.positions.update', $position) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $position->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tablette_id" class="form-label">Tablette <span class="text-danger">*</span></label>
                            <select class="form-select @error('tablette_id') is-invalid @enderror" id="tablette_id" name="tablette_id" required>
                                @foreach($tablettes as $tablette)
                                    <option value="{{ $tablette->id }}" {{ $position->tablette_id == $tablette->id ? 'selected' : '' }}>
                                        {{ $tablette->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tablette_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <input type="text" class="form-control" id="niveau" name="niveau" value="{{ old('niveau', $position->niveau) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="colonne" class="form-label">Colonne</label>
                            <input type="text" class="form-control" id="colonne" name="colonne" value="{{ old('colonne', $position->colonne) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="rangee" class="form-label">Rangée</label>
                            <input type="text" class="form-control" id="rangee" name="rangee" value="{{ old('rangee', $position->rangee) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="code_barre" class="form-label">Code-barres</label>
                            <input type="text" class="form-control" id="code_barre" name="code_barre" value="{{ old('code_barre', $position->code_barre) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $position->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h6>
            </div>
            <div class="card-body">
                <p><strong>Statut actuel :</strong>
                    <span class="badge bg-{{ $position->vide ? 'success' : 'warning' }}">
                        {{ $position->vide ? 'Libre' : 'Occupée' }}
                    </span>
                </p>
                <p><small>Modifié le : {{ $position->updated_at->format('d/m/Y H:i') }}</small></p>
            </div>
        </div>
    </div>
</div>
@endsection