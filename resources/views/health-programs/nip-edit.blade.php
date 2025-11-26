@extends('layouts.app')

@section('title', 'Edit NIP Record')
@section('page-title', 'Edit Immunization Entry')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing NIP Record #{{ $record->record_no ?? ('NIP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT)) }}</h2>
            <form class="patient-form" method="POST" action="{{ route('health-programs.nip-update', $record) }}">
                @csrf
                @method('PUT')

                <div class="form-section section-patient-info">
                    <h3 class="section-header"><span class="section-indicator"></span>Child Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="child_name">Child Name</label>
                            <input type="text" id="child_name" name="child_name" class="form-control" value="{{ old('child_name', $record->child_name) }}">
                        </div>
                        <div class="form-group">
                            <label for="dob">Birth Date</label>
                            <input type="date" id="dob" name="dob" class="form-control" value="{{ old('dob', optional($record->dob)->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label for="mother_name">Mother</label>
                            <input type="text" id="mother_name" name="mother_name" class="form-control" value="{{ old('mother_name', $record->mother_name) }}">
                        </div>
                    </div>
                </div>

                <div class="form-section section-assessment">
                    <h3 class="section-header"><span class="section-indicator"></span>Visit Update</h3>
                    <div class="form-row">
                        @php($firstVisit = $record->visits->first())
                        <div class="form-group">
                            <label for="edit_visit_age">Age in months</label>
                            <input type="text" id="edit_visit_age" name="edit_visit_age" class="form-control" value="{{ old('edit_visit_age', optional($firstVisit)->age_months) }}">
                        </div>
                        <div class="form-group">
                            <label for="edit_visit_vaccine">Vaccine</label>
                            <input type="text" id="edit_visit_vaccine" name="edit_visit_vaccine" class="form-control" value="{{ old('edit_visit_vaccine', optional($firstVisit)->vaccine) }}">
                        </div>
                        <div class="form-group">
                            <label for="edit_visit_notes">Notes</label>
                            <textarea id="edit_visit_notes" class="form-control" rows="3" disabled>No adverse reaction.</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="{{ route('health-programs.nip-view') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

