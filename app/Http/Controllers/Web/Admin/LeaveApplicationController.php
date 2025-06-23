<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveApplicationController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        $allowedFilterFields = ['reason', 'status'];
        $allowedSortFields = ['start_date', 'end_date', 'created_at', 'updated_at'];
        $limits = [10, 25, 50, 100];

        // Default status to 'pending' if not provided
        $status = $request->input('status', 'pending');

        // Base query
        $query = LeaveApplication::with(['user', 'approver'])
            ->search(
                keyword: $request->keyword,
                columns: $allowedFilterFields,
            )
            ->sort(
                sort_by: $request->sort_by ?? 'created_at',
                sort_order: $request->sort_order ?? 'DESC'
            )
            ->where('status', $status);

        $leaveApplications = $query->paginate($request->query('limit') ?? 10);

        return view('pages.admin.leave.index', [
            'title' => 'Leave Applications',
            'leaveApplications' => $leaveApplications,
            'allowedFilterFields' => $allowedFilterFields,
            'allowedSortFields' => $allowedSortFields,
            'limits' => $limits,
        ]);
    }

    public function approve(int $id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->id
        ]);

        return back()->with('success', 'Leave application approved.');
    }

    public function reject(int $id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->id
        ]);

        return back()->with('success', 'Leave application rejected.');
    }

}
