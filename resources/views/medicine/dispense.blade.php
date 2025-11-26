{{-- Dispense Medicine --}}
@extends('layouts.app')

@section('title', 'Dispense Medicine')
@section('page-title', 'Dispense Medicine')

@section('content')
    <div class="page-content">
        <div class="content-header">
            <h2>Dispense Medicine</h2>
        </div>

        <form method="POST" action="{{ route('medicine.dispense.store') }}" class="patient-form" style="margin-bottom: 30px;">
            @csrf

            <div class="form-group">
                <label for="medicine_id">Medicine *</label>
                <select id="medicine_id" name="medicine_id" class="form-control" required>
                    <option value="">-- Select Medicine --</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(old('medicine_id') == $medicine->id)>
                            {{ $medicine->name }} ({{ $medicine->quantity_on_hand }} {{ $medicine->unit }} in stock)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity to Dispense *</label>
                <input type="number" id="quantity" name="quantity" class="form-control" min="1" required
                    value="{{ old('quantity', 1) }}">
            </div>

            <div class="form-group">
                <label for="dispensed_to">Dispensed To (Patient Name / ID)</label>
                <input type="text" id="dispensed_to" name="dispensed_to" class="form-control"
                    value="{{ old('dispensed_to') }}">
            </div>

            <div class="form-group">
                <label for="reference_no">Reference No. (ITR / Program)</label>
                <input type="text" id="reference_no" name="reference_no" class="form-control"
                    value="{{ old('reference_no') }}">
            </div>

            <div class="form-group">
                <label for="dispensed_at">Dispensed Date</label>
                <input type="date" id="dispensed_at" name="dispensed_at" class="form-control"
                    value="{{ old('dispensed_at') }}">
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('medicine.index') }}" class="btn btn-secondary">Back to Inventory</a>
                <button type="submit" class="btn btn-primary">Dispense</button>
            </div>
        </form>

        <div class="table-container">
            <h3 style="padding: 0 0 10px;">Recent Dispensed Medicines</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Dispensed To</th>
                        <th>Reference No.</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispenses as $dispense)
                        <tr>
                            <td>{{ optional($dispense->dispensed_at)->format('M d, Y') ?? $dispense->created_at->format('M d, Y') }}</td>
                            <td>{{ $dispense->medicine->name ?? 'N/A' }}</td>
                            <td>{{ $dispense->quantity }} {{ $dispense->medicine->unit ?? '' }}</td>
                            <td>{{ $dispense->dispensed_to }}</td>
                            <td>{{ $dispense->reference_no }}</td>
                            <td>{{ $dispense->remarks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">No dispenses recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
