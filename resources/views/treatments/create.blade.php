@extends('layouts.app')
@section('page-title', 'Record Treatment')

@section('content')
<div style="padding: 28px 36px;">
  <div style="background: white; border: 1px solid #C8D9EE; border-radius: 12px; padding: 28px; max-width: 600px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 6px; color: #1a2640;">Record New Treatment</h2>
    <p style="font-size: 13px; color: #6B7E9F; margin-bottom: 20px;">Document patient treatment and diagnosis.</p>

    <form method="POST" action="{{ route('treatments.store') }}">
      @csrf
      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Patient</label>
          <select name="patient_id" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            <option value="">Select patient</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">{{ $p->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Doctor</label>
          <select name="doctor_id" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;">
            <option value="">Select doctor</option>
            @foreach($doctors as $d)
              <option value="{{ $d->id }}">Dr. {{ $d->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Diagnosis</label>
          <textarea name="diagnosis" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; resize: vertical;" rows="3" placeholder="Medical diagnosis..."></textarea>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Treatment</label>
          <textarea name="treatment" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%; resize: vertical;" rows="3" placeholder="Treatment details..."></textarea>
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Procedure (if applicable)</label>
          <input type="text" name="procedure" style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
        </div>

        <div>
          <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1a2640; font-weight: 500;">Date</label>
          <input type="date" name="treatment_date" required style="border: 1.5px solid #C8D9EE; border-radius: 7px; padding: 8px 12px; font-size: 13px; width: 100%;" />
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
          <a href="{{ route('treatments.index') }}" style="padding: 9px 18px; border: 1.5px solid #C8D9EE; border-radius: 8px; background: white; color: #6B7E9F; text-decoration: none; font-size: 13px; font-weight: 500;">Cancel</a>
          <button type="submit" style="padding: 9px 18px; border: none; border-radius: 8px; background: #1B2D5B; color: white; font-size: 13px; font-weight: 500; cursor: pointer;">Record</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
