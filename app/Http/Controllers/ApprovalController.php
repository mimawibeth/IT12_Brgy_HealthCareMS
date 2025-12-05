<?php

namespace App\Http\Controllers;

use App\Models\FinancialAssistanceRequest;
use App\Models\MedicalSuppliesRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Show approval dashboard based on user role
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            // Superadmin sees only requests approved by admin
            $financialRequests = FinancialAssistanceRequest::where('status', 'approved_by_admin')
                ->with(['requestor', 'admin'])
                ->orderByDesc('admin_reviewed_at')
                ->paginate(10);

            $medicalRequests = MedicalSuppliesRequest::where('status', 'approved_by_admin')
                ->with(['requestor', 'admin'])
                ->orderByDesc('admin_reviewed_at')
                ->paginate(10);

            return view('approvals.index', compact('financialRequests', 'medicalRequests'));
        } elseif ($user->role === 'admin') {
            // Admin sees pending requests and their decisions
            $financialRequests = FinancialAssistanceRequest::whereIn('status', ['pending', 'approved_by_admin', 'rejected_by_admin'])
                ->with(['requestor', 'admin', 'superadmin'])
                ->orderByDesc('submitted_at')
                ->paginate(10);

            $medicalRequests = MedicalSuppliesRequest::whereIn('status', ['pending', 'approved_by_admin', 'rejected_by_admin'])
                ->with(['requestor', 'admin', 'superadmin'])
                ->orderByDesc('submitted_at')
                ->paginate(10);

            return view('approvals.index', compact('financialRequests', 'medicalRequests'));
        } else {
            // BHW users see their own requests and status
            $financialRequests = FinancialAssistanceRequest::where('user_id', $user->id)
                ->with(['admin', 'superadmin'])
                ->orderByDesc('submitted_at')
                ->paginate(10);

            $medicalRequests = MedicalSuppliesRequest::where('user_id', $user->id)
                ->with(['admin', 'superadmin'])
                ->orderByDesc('submitted_at')
                ->paginate(10);

            return view('approvals.index', compact('financialRequests', 'medicalRequests'));
        }
    }

    /**
     * Show create financial assistance request form
     */
    public function createFinancial(): View
    {
        return view('approvals.financial-assistance');
    }

    /**
     * Store a new financial assistance request
     */
    public function storeFinancial(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:2000',
        ]);

        FinancialAssistanceRequest::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('approvals.index')
            ->with('success', 'Financial assistance request submitted successfully.');
    }

    /**
     * Show create medical supplies request form
     */
    public function createMedical(): View
    {
        return view('approvals.medical-supplies');
    }

    /**
     * Store a new medical supplies request
     */
    public function storeMedical(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        MedicalSuppliesRequest::create([
            'user_id' => auth()->id(),
            'item_name' => $validated['item_name'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('approvals.index')
            ->with('success', 'Medical supplies request submitted successfully.');
    }

    /**
     * Admin approves request (forwards to superadmin)
     */
    public function adminApprove(Request $request, $type, $id)
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $model = $type === 'financial' ? FinancialAssistanceRequest::class : MedicalSuppliesRequest::class;
        $approvalRequest = $model::findOrFail($id);

        if (!$approvalRequest->isPending()) {
            return response()->json(['error' => 'Request already reviewed'], 400);
        }

        $approvalRequest->update([
            'status' => 'approved_by_admin',
            'admin_id' => $user->id,
            'admin_reviewed_at' => now(),
            'admin_notes' => $request->input('notes'),
        ]);

        return response()->json(['message' => 'Request forwarded to superadmin']);
    }

    /**
     * Admin rejects request
     */
    public function adminReject(Request $request, $type, $id)
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $model = $type === 'financial' ? FinancialAssistanceRequest::class : MedicalSuppliesRequest::class;
        $approvalRequest = $model::findOrFail($id);

        if (!$approvalRequest->isPending()) {
            return response()->json(['error' => 'Request already reviewed'], 400);
        }

        $approvalRequest->update([
            'status' => 'rejected_by_admin',
            'admin_id' => $user->id,
            'admin_reviewed_at' => now(),
            'admin_notes' => $request->input('notes'),
        ]);

        return response()->json(['message' => 'Request rejected']);
    }

    /**
     * Superadmin approves request
     */
    public function superadminApprove(Request $request, $type, $id)
    {
        $user = auth()->user();

        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $model = $type === 'financial' ? FinancialAssistanceRequest::class : MedicalSuppliesRequest::class;
        $approvalRequest = $model::findOrFail($id);

        if (!$approvalRequest->isAwaitingSuperadminReview()) {
            return response()->json(['error' => 'Request is not pending superadmin review'], 400);
        }

        $approvalRequest->update([
            'status' => 'approved_by_superadmin',
            'superadmin_id' => $user->id,
            'superadmin_reviewed_at' => now(),
            'superadmin_notes' => $request->input('notes'),
        ]);

        return response()->json(['message' => 'Request approved']);
    }

    /**
     * Superadmin rejects request
     */
    public function superadminReject(Request $request, $type, $id)
    {
        $user = auth()->user();

        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $model = $type === 'financial' ? FinancialAssistanceRequest::class : MedicalSuppliesRequest::class;
        $approvalRequest = $model::findOrFail($id);

        if (!$approvalRequest->isAwaitingSuperadminReview()) {
            return response()->json(['error' => 'Request is not pending superadmin review'], 400);
        }

        $approvalRequest->update([
            'status' => 'rejected_by_superadmin',
            'superadmin_id' => $user->id,
            'superadmin_reviewed_at' => now(),
            'superadmin_notes' => $request->input('notes'),
        ]);

        return response()->json(['message' => 'Request rejected']);
    }

    /**
     * Show request details in modal
     */
    public function show($type, $id)
    {
        $model = $type === 'financial' ? FinancialAssistanceRequest::class : MedicalSuppliesRequest::class;
        $approvalRequest = $model::with(['requestor', 'admin', 'superadmin'])->findOrFail($id);

        return response()->json($approvalRequest);
    }
}
