{{-- Settings - System & Display Preferences --}}
@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="page-content">
       

        <form method="POST" action="{{ route('settings.update') }}" class="patient-form">
            @csrf

            <div class="card" style="margin-bottom: 20px;">
                <h3>Display Preferences</h3>

                <div class="form-group">
                    <label for="dark_mode">Color Theme</label>
                    <select id="dark_mode" name="dark_mode" class="form-control">
                        <option value="0" @selected(!(auth()->user()->dark_mode ?? false))>Light Mode</option>
                        <option value="1" @selected(auth()->user()->dark_mode ?? false)>Dark Mode</option>
                    </select>
                </div>

                <p style="font-size: 13px; color: #7f8c8d;">Dark mode applies to this account only and updates the colors of
                    the dashboard, side navigation, and content areas.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
@endsection
