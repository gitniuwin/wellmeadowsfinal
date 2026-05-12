@extends('layouts.app')

@section('title', 'Edit ' . $ward->name)
@section('page-title', 'Edit Ward')

@section('topbar-action')
    <a href="{{ route('wards.show', $ward) }}" class="btn-secondary">← Back</a>
@endsection

@section('content')
<div class="form-card" style="max-width:640px">
    <h3 class="form-card-title">Edit Ward — {{ $ward->name }}</h3>
    <form method="POST" action="{{ route('wards.update', $ward) }}">
        @csrf @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Ward Name <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name', $ward->name) }}"
                       class="form-input @error('name') is-error @enderror" required>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Ward Type <span class="required">*</span></label>
                <select name="type" class="form-select" required>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $ward->type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity', $ward->capacity) }}"
                       min="{{ $ward->beds->where('status','occupied')->count() }}" max="200"
                       class="form-input" required>
                <p class="form-hint">Minimum {{ $ward->beds->where('status','occupied')->count() }} (occupied beds)</p>
            </div>
            <div class="form-group">
                <label class="form-label">Floor</label>
                <input type="text" name="floor" value="{{ old('floor', $ward->floor) }}" class="form-input">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" rows="3">{{ old('description', $ward->description) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('wards.show', $ward) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>

    {{-- Danger zone --}}
    <div class="danger-zone">
        <h4 class="danger-title">Deactivate Ward</h4>
        <p class="danger-desc">Deactivating a ward hides it from the system. All occupied beds must be released first.</p>
        <form method="POST" action="{{ route('wards.destroy', $ward) }}"
              onsubmit="return confirm('Deactivate this ward? This cannot be undone easily.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger"
                    @if($ward->beds->where('status','occupied')->count() > 0) disabled title="Release all occupied beds first" @endif>
                Deactivate Ward
            </button>
        </form>
    </div>
</div>
@endsection
