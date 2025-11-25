@extends('layouts.app')

@section('title', 'Edit NIP Record')
@section('page-title', 'Edit Immunization Entry')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing NIP Record #{{ $id }}</h2>
            <form class="patient-form">
                <div class="form-section section-patient-info">
                    <h3 class="section-header"><span class="section-indicator"></span>Child Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_child_name">Child Name</label>
                            <input type="text" id="edit_child_name" class="form-control" value="Baby Liam Cruz">
                        </div>
                        <div class="form-group">
                            <label for="edit_birth_date">Birth Date</label>
                            <input type="date" id="edit_birth_date" class="form-control" value="2025-01-18">
                        </div>
                        <div class="form-group">
                            <label for="edit_mother">Mother</label>
                            <input type="text" id="edit_mother" class="form-control" value="Jasmine Cruz">
                        </div>
                    </div>
                </div>

                <div class="form-section section-assessment">
                    <h3 class="section-header"><span class="section-indicator"></span>Visit Update</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_visit_age">Age in months</label>
                            <input type="text" id="edit_visit_age" class="form-control" value="2">
                        </div>
                        <div class="form-group">
                            <label for="edit_visit_vaccine">Vaccine</label>
                            <input type="text" id="edit_visit_vaccine" class="form-control" value="Penta 1">
                        </div>
                        <div class="form-group">
                            <label for="edit_visit_notes">Notes</label>
                            <textarea id="edit_visit_notes" class="form-control" rows="3">No adverse reaction.</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-primary">Update Record</button>
                    <a href="{{ route('health-programs.nip-view') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

