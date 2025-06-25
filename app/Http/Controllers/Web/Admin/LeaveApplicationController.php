<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveApplicationController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        $query = $this->_buildLeaveApplicationQuery($request);

        $leaveApplications = $query->paginate($this->_getPerPageLimit($request));

        $statusCounts = $this->_getStatusCounts();

        return view('pages.admin.leave.index', [
            'title'               => 'Leave Applications',
            'leaveApplications'   => $leaveApplications,
            'allowedFilterFields' => $this->_getAllowedFilterFields(),
            'allowedSortFields'   => $this->_getAllowedSortFields(),
            'limits'              => $this->_getLimitOptions(),
            'statusCounts'        => $statusCounts,
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

    // ────────────────────────────
    // PRIVATE METHODS (with _ prefix)
    // ────────────────────────────

    private function _getAllowedFilterFields(): array
    {
        return ['reason'];
    }

    private function _getAllowedSortFields(): array
    {
        return ['start_date', 'end_date', 'created_at', 'updated_at'];
    }

    private function _getLimitOptions(): array
    {
        return [10, 25, 50, 100];
    }

    private function _getPerPageLimit(Request $request): int
    {
        return in_array((int) $request->query('limit'), $this->_getLimitOptions())
            ? (int) $request->query('limit')
            : 10;
    }

    private function _getStatusCounts(): array
    {
        return LeaveApplication::select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    private function _buildLeaveApplicationQuery(Request $request): Builder
    {
        $query = LeaveApplication::with(['user', 'approver'])
            ->search(
                keyword: $request->keyword,
                columns: $this->_getAllowedFilterFields(),
            )
            ->sort(
                sort_by: $request->sort_by ?? 'created_at',
                sort_order: $request->sort_order ?? 'DESC',
            );

        $this->_applyStatusFilter($query, $request);

        return $query;
    }

    private function _applyStatusFilter(Builder $query, Request $request): void
    {
        $statusParam = $request->query('status');

        if (! $request->has('status')) {
            $query->where('status', 'pending');
        } elseif ($request->filled('status')) {
            $query->where('status', $statusParam);
        }
    }
}