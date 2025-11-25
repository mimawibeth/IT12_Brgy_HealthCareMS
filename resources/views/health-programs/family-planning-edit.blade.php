@extends('layouts.app')

@section('title', 'Edit Family Planning Record')
@section('page-title', 'Edit FP Record')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing FP Record #{{ $id }}</h2>
            <form class="patient-form">
                <div class="form-section section-patient-info">
                    <h3 class="section-header"><span class="section-indicator"></span>Client Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_fp_name">Client Name</label>
                            <input type="text" id="edit_fp_name" class="form-control" value="Jane Villanueva">
                        </div>
                        <div class="form-group">
                            <label for="edit_fp_type">Client Type</label>
                            <select id="edit_fp_type" class="form-control">
                                <option value="new" selected>New Acceptor</option>
                                <option value="current">Current User</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_fp_reason">Reason</label>
                            <select id="edit_fp_reason" class="form-control">
                                <option value="spacing" selected>Spacing</option>
                                <option value="limiting">Limiting</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section section-assessment">
                    <h3 class="section-header"><span class="section-indicator"></span>Assessment Notes</h3>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="edit_fp_notes">Notes</label>
                            <textarea id="edit_fp_notes" class="form-control" rows="4">Client opted for pills; BP within normal limits.</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-primary">Update Record</button>
                    <a href="{{ route('health-programs.family-planning-view') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

