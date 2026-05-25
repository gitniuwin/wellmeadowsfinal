@extends('layouts.app')
@section('page-title', 'Appointment Details')

@section('content')
<div style="padding: 28px 36px;">
  <div style="background: white; border: 1px solid #C8D9EE; border-radius: 12px; padding: 28px; max-width: 600px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
      <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; color: #1a2640;">Appointment Details</h2>
      <a href="{{ route('appointments.index') }}" style="color: #5B9BD5; text-decoration: none; font-size: 13px; font-weight: 500;">← Back</a>
    </div>

    <div style="display: grid; gap: 14px;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7E9F; font-weight: 500;">Patient</label>
          <p style="font-size: 14px; color: #1a2640; font-weight: 500; margin-top: 4px;">{{ $appointment->patient->full_name }}</p>
        </div>
        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7E9F; font-weight: 500;">Doctor</label>
          <p style="font-size: 14px; color: #1a2640; font-weight: 500; margin-top: 4px;">Dr. {{ $appointment->doctor->full_name }}</p>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7E9F; font-weight: 500;">Appointment Type</label>
          <p style="font-size: 14px; color: #1a2640; font-weight: 500; margin-top: 4px;">{{ $appointment->type }}</p>
        </div>
        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7E9F; font-weight: 500;">Status</label>
          <p style="font-size: 14px; color: #1a2640; font-weight: 500; margin-top: 4px;">
            <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500;
              {{ $appointment->status == 'scheduled' ? 'background: #FFF3E0; color: #A06000;' : '' }}
              {{ $appointment->status == 'completed' ? 'background: #E3F7EF; color: #1B7A54;' : '' }}
              {{ $appointment->status == 'cancelled' ? 'background: #FDEDEC; color: #C0392B;' : '' }}
            ">
              {{ ucfirst($appointment->status) }}
            </span>
          </p>
        </div>
      </div>

      <div>
        <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7E9F; font-weight: 500;">Appointment Date & Time</label>
        <p style="font-size: 14px; color: #1a2640; font-weight: 500; margin-top: 4px;">{{ $appointment->appointment_date->format('F d, Y \a\t g:i A') }}</p>
      </div>

      @if($appointment->notes)
      <div>
        <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7E9F; font-weight: 500;">Notes</label>
        <p style="font-size: 13px; color: #1a2640; margin-top: 4px; line-height: 1.6;">{{ $appointment->notes }}</p>
      </div>
      @endif

      <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #C8D9EE;">
        <a href="{{ route('appointments.edit', $appointment->id) }}" style="padding: 8px 16px; background: #5B9BD5; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500;">Edit</a>
        <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" style="display: inline;">
          @csrf @method('DELETE')
          <button type="submit" onclick="return confirm('Delete this appointment?')" style="padding: 8px 16px; background: #D94F4F; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;">Delete</button>
        </form>
        <a href="{{ route('appointments.index') }}" style="padding: 8px 16px; background: white; color: #6B7E9F; border: 1px solid #C8D9EE; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500; margin-left: auto;">Back</a>
      </div>
    </div>
  </div>
</div>
@endsection
