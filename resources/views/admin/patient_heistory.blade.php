@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-md-12 px-0">
            <div class="card border-0 rounded-0">
                <div class="card-header bg-success text-white rounded-0 py-2 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Appointment History') }}</h5>
                </div>
                <div class="card-body p-3">
                    @if($appointments->isEmpty())
                    <p class="text-muted">No appointment history available for this patient.</p>
                    @else
                    @foreach($appointments as $appointment)
                    <div class="appointment-card card shadow-sm mb-3">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span><strong>Appointment:</strong> {{ $appointment->appointment_date->format('F j, Y') }}</span>
                            <small class="text-muted">{{ $appointment->doctor->name }} ({{ $appointment->doctor->department->name ?? 'N/A' }})</small>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>Procedure:</strong> {{ $appointment->procedure_name }}</p>
                                    <p><strong>Total:</strong> {{ $appointment->total_amount }}</p>
                                    <p><strong>Additional:</strong> {{ $appointment->amount }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h6>Medicines</h6>
                                    <ul class="list-unstyled small mb-0">
                                        @forelse($appointment->medicines as $medicine)
                                        <li>
                                            <strong>{{ $medicine->name }} - {{ $medicine->size }}</strong>
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    @if($medicine->pivot->days)
                                                    <span class="badge bg-primary">{{$medicine->pivot->days}} دن</span>
                                                    @endif
                                                </li>
                                                <li class="list-inline-item">
                                                    @if($medicine->pivot->meal_timing == 'before')
                                                    <span class="badge bg-primary">کھانے سے پہلے</span>
                                                    @elseif($medicine->pivot->meal_timing == 'after')
                                                    <span class="badge bg-primary">کھانے کے بعد</span>
                                                    @endif
                                                </li>

                                                <li class="list-inline-item">
                                                    @if($medicine->pivot->morning)
                                                    <span class="badge bg-primary">صبح</span>
                                                    @endif
                                                    @if($medicine->pivot->afternoon)
                                                    <span class="badge bg-primary">دوپہر</span>
                                                    @endif
                                                    @if($medicine->pivot->evening)
                                                    <span class="badge bg-primary">شام</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </li>
                                        @empty
                                        <li class="text-muted">None</li>
                                        @endforelse
                                    </ul>

                                </div>
                                <div class="col-md-3">
                                    <h6>Lab Tests</h6>
                                    <ul class="list-unstyled small mb-0">
                                        @forelse($appointment->labTests as $labTest)
                                        <li>{{ $labTest->name }}</li>
                                        @empty
                                        <li class="text-muted">None</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <h6>Clinic Notes</h6>
                                    @if( $appointment->clinicNotes)
                                    <ul class="list-unstyled small mb-0">
                                        <li><strong>DM:</strong> {{ $appointment->clinicNotes->dm ? 'Yes' : 'No' }}</li>
                                        <li><strong>BP:</strong> {{ $appointment->clinicNotes->bp ?? 'N/A' }}</li>
                                        <li><strong>PC:</strong> {{ $appointment->clinicNotes->pc ?? 'N/A' }}</li>
                                        <li><strong>Diagnosis:</strong> {{ $appointment->clinicNotes->diagnosis ?? 'N/A' }}</li>
                                        <li><strong>Temp:</strong> {{ $appointment->clinicNotes->temperature ?? 'N/A' }}</li>
                                    </ul>
                                    @else
                                    <p class="text-muted">No clinic notes available.</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <h6>Instructions</h6>
                                    <ul class="list-unstyled small mb-0">
                                        @forelse($appointment->instructions as $instruction)
                                        <li>{{ $instruction->instruction }}</li>
                                        @empty
                                        <li class="text-muted">None</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted p-1">
                            <small>Appointment ID: {{ $appointment->id }}</small>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection