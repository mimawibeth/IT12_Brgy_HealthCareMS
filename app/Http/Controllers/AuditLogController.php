<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $query = AuditLog::with('user')->orderByDesc('created_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                    ->orWhere('ip_address', 'like', $term)
                    ->orWhere('module', 'like', $term)
                    ->orWhere('action', 'like', $term);
            });
        }

        $logs = $query->paginate(10)->withQueryString();

        $today = Carbon::today();
        $stats = [
            'totalToday' => AuditLog::whereDate('created_at', $today)->count(),
            'successToday' => AuditLog::whereDate('created_at', $today)->where('status', 'success')->count(),
            'failedToday' => AuditLog::whereDate('created_at', $today)->where('status', 'failed')->count(),
            'activeUsersToday' => AuditLog::whereDate('created_at', $today)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        $users = User::orderBy('name')->get();
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $modules = AuditLog::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('logs.audit', compact('logs', 'stats', 'users', 'actions', 'modules'));
    }
}
