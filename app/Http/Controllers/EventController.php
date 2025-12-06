<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    /**
     * Display the event calendar
     */
    public function index(): View
    {
        $events = Event::with('creator')
            ->orderBy('start_date')
            ->get();

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Only superadmin and admin can create events
        if (!in_array($user->role ?? '', ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        return view('events.create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Only superadmin and admin can create events
        if (!in_array($user->role ?? '', ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->start_time && $value <= $request->start_time) {
                        $fail('The end time must be after the start time.');
                    }
                },
            ],
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? $validated['start_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'color' => $validated['color'] ?? '#4a90a4',
            'created_by' => $user->id,
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event): View
    {
        $user = auth()->user();
        
        // Only superadmin and admin can edit events
        if (!in_array($user->role ?? '', ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $user = auth()->user();
        
        // Only superadmin and admin can update events
        if (!in_array($user->role ?? '', ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->start_time && $value <= $request->start_time) {
                        $fail('The end time must be after the start time.');
                    }
                },
            ],
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? $validated['start_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'color' => $validated['color'] ?? '#4a90a4',
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        $user = auth()->user();
        
        // Only superadmin and admin can delete events
        if (!in_array($user->role ?? '', ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        $event->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Event deleted successfully.']);
        }

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Get events as JSON for calendar
     */
    public function getEvents(Request $request)
    {
        $events = Event::with('creator')
            ->orderBy('start_date')
            ->get()
            ->map(function ($event) {
                $start = $event->start_date->format('Y-m-d');
                if ($event->start_time) {
                    $start .= 'T' . date('H:i:s', strtotime($event->start_time));
                }
                
                $endDate = $event->end_date ?? $event->start_date;
                $end = $endDate->format('Y-m-d');
                if ($event->end_time) {
                    $end .= 'T' . date('H:i:s', strtotime($event->end_time));
                } else {
                    // If no end time, add one day for all-day events
                    $end = $endDate->copy()->addDay()->format('Y-m-d');
                }
                
                // Format time for display
                $startTimeFormatted = $event->start_time ? date('g:i A', strtotime($event->start_time)) : null;
                $endTimeFormatted = $event->end_time ? date('g:i A', strtotime($event->end_time)) : null;
                
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $start,
                    'end' => $end,
                    'location' => $event->location,
                    'backgroundColor' => $event->color,
                    'borderColor' => $event->color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'location' => $event->location,
                        'description' => $event->description,
                        'created_by' => $event->creator->name ?? 'Unknown',
                        'start_time' => $startTimeFormatted,
                        'end_time' => $endTimeFormatted,
                    ],
                ];
            });

        return response()->json($events);
    }
}
