<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Employee\Leave\StoreLeaveRequest;
use App\Http\Requests\Web\Employee\Leave\UpdateLeaveRequest;
use App\Models\LeaveApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeaveApplicationController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        $allowedFilterFields = ['reason', 'status'];
        $allowedSortFields = ['start_date', 'end_date', 'created_at', 'updated_at'];
        $limits = [10, 25, 50, 100];

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

            ->where('user_id', Auth::user()->id);

        $leaveApplications = $query->paginate($request->query('limit') ?? 10);

        return view('pages.employee.leave.index', [
            'title' => 'Leave Applications',
            'leaveApplications' => $leaveApplications,
            'allowedFilterFields' => $allowedFilterFields,
            'allowedSortFields' => $allowedSortFields,
            'limits' => $limits,
        ]);
    }

    public function create(): View
    {
        return view('pages.employee.leave.create', [
            'title' => 'Apply for Leave'
        ]);
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        try {
            // Simpan data cuti
            LeaveApplication::create([
                'user_id' => Auth::user()->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'status'     => 'pending'
            ]);

            return redirect()
                    ->route('employee.leave.index')
                    ->with('success', 'Leave application submitted successfully.');
        } catch (\Throwable $e) {
            // Log errornya
            Log::error('Failed to submit leave application', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withInput()->with('error', 'An error occurred while submitting your leave application.');
        }
    }

    public function edit(LeaveApplication $leave): View | RedirectResponse
    {
        if ($leave->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya izinkan edit jika masih pending
        if ($leave->status !== 'pending') {
            return redirect()
                ->route('employee.leave.index')
                ->with('error', 'Only pending leave applications can be edited.');
        }

        return view('pages.employee.leave.edit', [
            'title' => 'Edit Leave Application',
            'leave' => $leave,
        ]);
    }

    public function update(UpdateLeaveRequest $request, LeaveApplication $leave): RedirectResponse
    {
        try {
            if ($leave->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            if ($leave->status !== 'pending') {
                return redirect()
                    ->route('employee.leave.index')
                    ->with('error', 'Only pending leave applications can be updated.');
            }

            $leave->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
            ]);

            return redirect()
                ->route('employee.leave.index')
                ->with('success', 'Leave application updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to update leave application', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'leave_id' => $leave->id,
                'request' => $request->all(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating your leave application.');
        }
    }

    public function destroy(LeaveApplication $leave): RedirectResponse
    {
        try {
            // Cek apakah user yang login adalah pemilik leave
            if ($leave->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            // Pastikan hanya status "pending" yang bisa dihapus
            if ($leave->status !== 'pending') {
                return redirect()
                    ->back()
                    ->with('error', 'Only pending leave applications can be deleted.');
            }

            $leave->delete();

            return redirect()
                ->route('employee.leave.index')
                ->with('success', 'Leave application deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete leave application', [
                'error' => $e->getMessage(),
                'leave_id' => $leave->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'An error occurred while deleting the leave application.');
        }
    }
}
