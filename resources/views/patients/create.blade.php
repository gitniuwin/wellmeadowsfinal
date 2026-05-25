@extends('layouts.app')
@section('page-title', 'Register Patient')

@section('content')
<div style="padding: 28px 36px;">
  <div style="background: white; border: 1px solid #C8D9EE; border-radius: 12px; padding: 28px; max-width: 600px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 6px; color: #1a2640;">Register New Patient</h2>
    <p style="font-size: 13px; color: #6B7E9F; margin-bottom: 20px;">Enter patient information to add them to the system.</p>

    <form method="POST" action="{{ route('patients.store') }}">
      @csrf
      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; outline: none;" />
            @error('first_name') <span style="color: #D94F4F; font-size: 11px;">{{ $message }}</span> @enderror
          </div>
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; outline: none;" />
            @error('last_name') <span style="color: #D94F4F; font-size: 11px;">{{ $message }}</span> @enderror
          </div>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Date of Birth</label>
          <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; outline: none;" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Gender</label>
            <select name="gender" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; outline: none;">
              <option value="">Select gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Contact Number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number') }}" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; outline: none;" />
          </div>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Address</label>
          <textarea name="address" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; outline: none; resize: vertical;" rows="3">{{ old('address') }}</textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
          <a href="{{ route('patients.index') }}" style="padding: 9px 18px; border: 1.5px solid #C8D9EE; border-radius: 8px; background: white; color: #6B7E9F; font-size: 13px; text-decoration: none; font-weight: 500; cursor: pointer;">Cancel</a>
          <button type="submit" style="padding: 9px 18px; border: none; border-radius: 8px; background: #1B2D5B; color: white; font-size: 13px; font-weight: 500; cursor: pointer;">Register Patient</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
