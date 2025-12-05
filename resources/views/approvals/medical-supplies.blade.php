@extends('layouts.app')

@section('title', 'Request Medical Supplies')
@section('page-title', 'Request Medical Supplies')

@section('content')
    <div class="page-content">
        <div class="form-container" style="max-width: 600px; margin: 0 auto;">
            <div class="form-header">
                <h1>Request Medical Supplies</h1>
                <p class="text-muted">Submit a request for medical supplies. Your request will be reviewed by the administrator.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('approvals.medical.store') }}" method="POST" class="form">
                @csrf

                <div class="form-group">
                    <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="item_name" id="item_name" class="form-control @error('item_name') is-invalid @enderror" 
                           placeholder="e.g., First Aid Kit, Antiseptic Solution..." required value="{{ old('item_name') }}">
                    @error('item_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                           min="1" placeholder="Number of units" required value="{{ old('quantity') }}">
                    @error('quantity')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" 
                              rows="4" placeholder="Describe the reason for requesting these medical supplies..." required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Additional Details</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Optional: Specifications, urgency, etc...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Submit Request
                    </button>
                    <a href="{{ route('approvals.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
@endsection