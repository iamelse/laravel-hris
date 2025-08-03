<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    public function index(Request $request): View
    {
        $reimbursements = Reimbursement::where('user_id', Auth::id())
            ->search(
                keyword: $request->keyword,
                columns: $this->_getAllowedFilterFields(),
            )
            ->sort(
                sort_by: $request->sort_by ?? 'created_at',
                sort_order: $request->sort_order ?? 'DESC',
            )
            ->paginate($this->_getPerPageLimit($request));

        return view('pages.employee.reimbursement.index', [
            'title'               => 'My Reimbursements',
            'reimbursements'      => $reimbursements,
            'allowedFilterFields' => $this->_getAllowedFilterFields(),
            'allowedSortFields'   => $this->_getAllowedSortFields(),
            'limits'              => $this->_getLimitOptions(),
        ]);
    }

    public function create(): View
    {
        return view('pages.employee.reimbursement.create', [
            'title' => 'Submit Reimbursement',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'proof_file'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->file('proof_file')->store('proofs', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        Reimbursement::create($data);

        return redirect()->route('employee.reimbursement.index')->with('success', 'Reimbursement submitted.');
    }

    public function edit(int $id): View
    {
        $reimbursement = Reimbursement::where('user_id', Auth::id())->findOrFail($id);

        return view('pages.employee.reimbursement.edit', [
            'title' => 'Edit Reimbursement',
            'reimbursement' => $reimbursement,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $reimbursement = Reimbursement::where('user_id', Auth::id())->findOrFail($id);

        if ($reimbursement->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reimbursements can be edited.');
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'proof_file'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->file('proof_file')->store('proofs', 'public');
        }

        $reimbursement->update($data);

        return redirect()->route('employee.reimbursement.index')->with('success', 'Reimbursement updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $reimbursement = Reimbursement::where('user_id', Auth::id())->findOrFail($id);

        if ($reimbursement->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reimbursements can be deleted.');
        }

        $reimbursement->delete();

        return redirect()->route('employee.reimbursement.index')->with('success', 'Reimbursement deleted.');
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
}