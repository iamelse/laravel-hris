<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        $query = $this->_buildReimbursementQuery($request);

        $reimbursements = $query->paginate($this->_getPerPageLimit($request));

        $statusCounts = $this->_getStatusCounts();

        return view('pages.admin.reimbursement.index', [
            'title'               => 'Reimbursement Requests',
            'reimbursements'      => $reimbursements,
            'allowedFilterFields' => $this->_getAllowedFilterFields(),
            'allowedSortFields'   => $this->_getAllowedSortFields(),
            'limits'              => $this->_getLimitOptions(),
            'statusCounts'        => $statusCounts,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $reimbursement = Reimbursement::findOrFail($id);
        $reimbursement->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'Reimbursement request approved.');
    }

    public function reject(int $id): RedirectResponse
    {
        $reimbursement = Reimbursement::findOrFail($id);
        $reimbursement->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'Reimbursement request rejected.');
    }

    // ────────────────────────────
    // PRIVATE METHODS
    // ────────────────────────────

    private function _getAllowedFilterFields(): array
    {
        return ['title', 'description'];
    }

    private function _getAllowedSortFields(): array
    {
        return ['amount', 'created_at', 'updated_at'];
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
        return Reimbursement::select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    private function _buildReimbursementQuery(Request $request): Builder
    {
        $query = Reimbursement::with('user')
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