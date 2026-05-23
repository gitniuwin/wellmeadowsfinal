@extends('layouts.app')

@section('title', 'Add Ward')
@section('page-title', 'Add New Ward')

@section('topbar-action')
    <a href="{{ route('wards.index') }}" class="btn-secondary">← Back</a>
@endsection

@section('content')
<div class="form-card" style="max-width:640px">
    <h3 class="form-card-title">Ward Details</h3>
    <form method="POST" action="{{ route('wards.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Ward Name <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-input @error('name') is-error @enderror"
                       placeholder="e.g. General Ward A" required>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Ward Type <span class="required">*</span></label>
                <select name="type" class="form-select @error('type') is-error @enderror" required>
                    <option value="">— Select type —</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('type')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Capacity (no. of beds) <span class="required">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" max="200"
                       class="form-input @error('capacity') is-error @enderror"
                       placeholder="e.g. 20" required>
                @error('capacity')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Floor</label>
                <input type="text" name="floor" value="{{ old('floor') }}" class="form-input" placeholder="e.g. 2nd Floor">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Building</label>
                <input type="text" name="building" value="{{ old('building') }}" class="form-input" placeholder="e.g. Main Building">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" rows="3"
                      placeholder="Optional notes about this ward">{{ old('description') }}</textarea>
        </div>

        <p class="form-hint" style="margin-bottom:1rem">
            Beds will be auto-generated based on capacity when the ward is created.
        </p>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Create Ward</button>
            <a href="{{ route('wards.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
