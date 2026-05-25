@extends('layouts.app')
@section('page-title', 'Edit Appointment')

@section('content')
<div style="padding: 28px 36px;">
  <div style="background: white; border: 1px solid #C8D9EE; border-radius: 12px; padding: 28px; max-width: 600px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 6px; color: #1a2640;">Edit Appointment</h2>

    <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
      @csrf @method('PUT')
      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Patient</label>
          <select name="patient_id" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            @foreach($patients as $p)
              <option value="{{ $p->id }}" {{ $appointment->patient_id == $p->id ? 'selected' : '' }}>{{ $p->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Doctor</label>
          <select name="doctor_id" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            @foreach($doctors as $d)
              <option value="{{ $d->id }}" {{ $appointment->doctor_id == $d->id ? 'selected' : '' }}>Dr. {{ $d->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Appointment Type</label>
          <select name="type" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            <option value="Consultation" {{ $appointment->type == 'Consultation' ? 'selected' : '' }}>Consultation</option>
            <option value="Follow-up" {{ $appointment->type == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
            <option value="Surgery" {{ $appointment->type == 'Surgery' ? 'selected' : '' }}>Surgery</option>
            <option value="Check-up" {{ $appointment->type == 'Check-up' ? 'selected' : '' }}>Check-up</option>
          </select>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Date & Time</label>
          <input type="datetime-local" name="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d\TH:i') }}" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Status</label>
          <select name="status" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            <option value="scheduled" {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Notes</label>
          <textarea name="notes" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; resize: vertical;" rows="3">{{ $appointment->notes }}</textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
          <a href="{{ route('appointments.index') }}" style="padding: 9px 18px; border: 1.5px solid #C8D9EE; border-radius: 8px; background: white; color: #6B7E9F; text-decoration: none; font-size: 13px; font-weight: 500;">Cancel</a>
          <button type="submit" style="padding: 9px 18px; border: none; border-radius: 8px; background: #1B2D5B; color: white; font-size: 13px; font-weight: 500; cursor: pointer;">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
