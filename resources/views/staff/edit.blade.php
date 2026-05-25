@extends('layouts.app')
@section('page-title', 'Edit Staff')

@section('content')
<div style="padding: 28px 36px;">
  <div style="background: white; border: 1px solid #C8D9EE; border-radius: 12px; padding: 28px; max-width: 600px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 6px; color: #1a2640;">Edit Staff Member</h2>
    <p style="font-size: 13px; color: #6B7E9F; margin-bottom: 20px;">Update staff information below.</p>

    <form method="POST" action="{{ route('staff.update', $staff->id) }}">
      @csrf @method('PUT')
      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">First Name</label>
            <input type="text" name="first_name" value="{{ $staff->first_name }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
          </div>
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Last Name</label>
            <input type="text" name="last_name" value="{{ $staff->last_name }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
          </div>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Email</label>
          <input type="email" name="email" value="{{ $staff->email }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Phone</label>
          <input type="text" name="phone" value="{{ $staff->phone }}" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Role</label>
            <select name="role" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
              <option value="Doctor" {{ $staff->role == 'Doctor' ? 'selected' : '' }}>Doctor</option>
              <option value="Nurse" {{ $staff->role == 'Nurse' ? 'selected' : '' }}>Nurse</option>
              <option value="Admin" {{ $staff->role == 'Admin' ? 'selected' : '' }}>Admin</option>
              <option value="Ward Manager" {{ $staff->role == 'Ward Manager' ? 'selected' : '' }}>Ward Manager</option>
            </select>
          </div>
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Status</label>
            <select name="status" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
              <option value="Active" {{ $staff->status == 'Active' ? 'selected' : '' }}>Active</option>
              <option value="On Leave" {{ $staff->status == 'On Leave' ? 'selected' : '' }}>On Leave</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Department</label>
            <select name="department" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
              <option value="Emergency" {{ $staff->department == 'Emergency' ? 'selected' : '' }}>Emergency</option>
              <option value="Cardiology" {{ $staff->department == 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
              <option value="Pediatrics" {{ $staff->department == 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
              <option value="Orthopedics" {{ $staff->department == 'Orthopedics' ? 'selected' : '' }}>Orthopedics</option>
              <option value="Neurology" {{ $staff->department == 'Neurology' ? 'selected' : '' }}>Neurology</option>
              <option value="General Medicine" {{ $staff->department == 'General Medicine' ? 'selected' : '' }}>General Medicine</option>
              <option value="Administration" {{ $staff->department == 'Administration' ? 'selected' : '' }}>Administration</option>
            </select>
          </div>
          <div>
            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Ward</label>
            <select name="ward" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
              <option value="">None</option>
              <option value="Ward A" {{ $staff->ward == 'Ward A' ? 'selected' : '' }}>Ward A</option>
              <option value="Ward B" {{ $staff->ward == 'Ward B' ? 'selected' : '' }}>Ward B</option>
              <option value="Ward C" {{ $staff->ward == 'Ward C' ? 'selected' : '' }}>Ward C</option>
              <option value="ICU" {{ $staff->ward == 'ICU' ? 'selected' : '' }}>ICU</option>
            </select>
          </div>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Shift</label>
          <select name="shift" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            <option value="AM" {{ $staff->shift == 'AM' ? 'selected' : '' }}>AM</option>
            <option value="PM" {{ $staff->shift == 'PM' ? 'selected' : '' }}>PM</option>
            <option value="Night" {{ $staff->shift == 'Night' ? 'selected' : '' }}>Night</option>
          </select>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
          <a href="{{ route('staff.index') }}" style="padding: 9px 18px; border: 1.5px solid #C8D9EE; border-radius: 8px; background: white; color: #6B7E9F; text-decoration: none; font-size: 13px; font-weight: 500;">Cancel</a>
          <button type="submit" style="padding: 9px 18px; border: none; border-radius: 8px; background: #1B2D5B; color: white; font-size: 13px; font-weight: 500; cursor: pointer;">Update Staff</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
