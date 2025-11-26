@extends('layouts.app')

@section('title', 'Edit Family Planning Record')
@section('page-title', 'Edit FP Record')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing FP Record #{{ $record->record_no ?? ('FP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT)) }}</h2>
            <form class="patient-form" method="POST" action="{{ route('health-programs.family-planning-update', $record) }}">
                @csrf
                @method('PUT')

                <div class="form-section section-patient-info">
                    <h3 class="section-header"><span class="section-indicator"></span>Client Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fp_client_name">Client Name</label>
                            <input type="text" id="fp_client_name" name="fp_client_name" class="form-control" value="{{ old('fp_client_name', $record->client_name) }}">
                        </div>
                        <div class="form-group">
                            <label for="fp_type">Client Type</label>
                            <select id="fp_type" name="fp_type" class="form-control">
                                <option value="">Select</option>
                                <option value="new" @selected(old('fp_type', $record->client_type) === 'new')>New Acceptor</option>
                                <option value="current" @selected(old('fp_type', $record->client_type) === 'current')>Current User</option>
                                <option value="changing" @selected(old('fp_type', $record->client_type) === 'changing')>Changing Method</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fp_reason">Reason</label>
                            @php($reasons = (array) ($record->reason ?? []))
                            <select id="fp_reason" class="form-control" disabled>
                                <option>{{ implode(', ', $reasons) ?: '—' }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section section-assessment">
                    <h3 class="section-header"><span class="section-indicator"></span>Assessment Notes</h3>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="fp_exam_findings">Physical Examination Findings / Notes</label>
                            <textarea id="fp_exam_findings" name="fp_exam_findings" class="form-control" rows="4">{{ old('fp_exam_findings', $record->exam_findings) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="{{ route('health-programs.family-planning-view') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

