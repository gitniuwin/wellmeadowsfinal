@extends('layouts.app')
@section('page-title', 'Staff Assignments')

@section('content')
<div style="padding: 28px 36px;">
  <div style="background: white; border: 1px solid #C8D9EE; border-radius: 12px; padding: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
      <h2 style="font-family: 'Playfair Display', serif; font-size: 20px; color: #1a2640;">Staff Assignments</h2>
      <a href="{{ route('staff-assignment.create') }}" style="padding: 8px 16px; background: #1B2D5B; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none;">+ New Assignment</a>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
      <thead>
        <tr style="border-bottom: 1px solid #C8D9EE; background: #F4F8FC;">
          <th style="text-align: left; padding: 10px; font-weight: 500; color: #6B7E9F; text-transform: uppercase; font-size: 11px;">Staff Member</th>
          <th style="text-align: left; padding: 10px; font-weight: 500; color: #6B7E9F; text-transform: uppercase; font-size: 11px;">Department</th>
          <th style="text-align: left; padding: 10px; font-weight: 500; color: #6B7E9F; text-transform: uppercase; font-size: 11px;">Ward</th>
          <th style="text-align: left; padding: 10px; font-weight: 500; color: #6B7E9F; text-transform: uppercase; font-size: 11px;">Date From</th>
          <th style="text-align: left; padding: 10px; font-weight: 500; color: #6B7E9F; text-transform: uppercase; font-size: 11px;">Date To</th>
          <th style="text-align: center; padding: 10px; font-weight: 500; color: #6B7E9F; text-transform: uppercase; font-size: 11px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($assignments as $assign)
        <tr style="border-bottom: 1px solid rgba(200,217,238,0.4);">
          <td style="padding: 12px;">{{ $assign->staff->full_name }}</td>
          <td style="padding: 12px;">{{ $assign->staff->department }}</td>
          <td style="padding: 12px;">{{ $assign->ward ?? '—' }}</td>
          <td style="padding: 12px;">{{ $assign->date_from->format('M d, Y') }}</td>
          <td style="padding: 12px;">{{ $assign->date_to?->format('M d, Y') ?? '—' }}</td>
          <td style="padding: 12px; text-align: center;">
            <form method="POST" action="{{ route('staff-assignment.destroy', $assign->id) }}" style="display: inline;">
              @csrf @method('DELETE')
              <button type="submit" style="color: #D94F4F; background: none; border: none; cursor: pointer; font-weight: 500; font-size: 12px;">Remove</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="padding: 30px; text-align: center; color: #6B7E9F;">No staff assignments yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
