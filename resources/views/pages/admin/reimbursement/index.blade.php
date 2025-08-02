@extends('layouts.admin.app')

@section('content')
<main>
   <div class="max-w-full px-4 md:px-6">

    <!-- Header Section -->
    <div class="px-6 py-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Reimburse Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage employee reimbursement requests</p>
            </div>

            <!-- Status Counts -->
            <div class="grid w-full grid-cols-3 gap-4 sm:w-auto">
                <div class="p-3 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 dark:border-yellow-800">
                    <p class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Pending</p>
                    <p class="text-lg font-semibold text-yellow-800 dark:text-yellow-100">{{ $statusCounts['pending'] ?? 0 }}</p>
                </div>
                <div class="p-3 border border-green-200 rounded-lg bg-green-50 dark:bg-green-900/30 dark:border-green-800">
                    <p class="text-xs font-medium text-green-700 dark:text-green-300">Approved</p>
                    <p class="text-lg font-semibold text-green-800 dark:text-green-100">{{ $statusCounts['approved'] ?? 0 }}</p>
                </div>
                <div class="p-3 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/30 dark:border-red-800">
                    <p class="text-xs font-medium text-red-700 dark:text-red-300">Rejected</p>
                    <p class="text-lg font-semibold text-red-800 dark:text-red-100">{{ $statusCounts['rejected'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="px-5 border-gray-100 dark:border-gray-800 sm:px-6">
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-end sm:justify-end sm:px-6">
                
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex items-center gap-2">                         

                        <!-- Reset Filter Button -->
                        <a href="{{ route('admin.leave.index') }}"
                            class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-gray-400 bg-gray-100 text-gray-700 font-medium transition-all hover:bg-gray-200 hover:border-gray-500 focus:ring focus:ring-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="text-lg bx bx-reset"></i>
                            Reset Filter
                        </a>
                        
                        <!-- Filter Modal need to adjust the sort-->
                        <div x-data="{ open: false, selectedField: '{{ request()->query('filter') ? array_key_first(request('filter')) : '' }}' }">                            
                            <!-- Filter Button -->
                            <button @click.prevent="open = true"
                                class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-purple-500 bg-purple-600 text-white font-medium transition-all hover:bg-purple-700 hover:border-purple-600 focus:ring focus:ring-purple-300 dark:bg-purple-700 dark:border-purple-600 dark:hover:bg-purple-800">
                                <i class="text-lg bx bx-filter"></i>
                                Filter
                            </button>

                            <!-- Modal -->
                            <div x-cloak x-show="open" @keydown.escape.window="open = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div @click.away="open = false"
                                    class="w-1/2 p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800">
                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Filter Options</h2>

                                    <!-- Form -->
                                    <form method="GET" action="{{ route('admin.reimbursement.index') }}">
                                        <!-- Limit Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Limit
                                            </label>
                                            <select name="limit"
                                                class="w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                @foreach ($limits as $limit)
                                                    <option value="{{ $limit }}" {{ request('limit', 10) == $limit ? 'selected' : '' }}>
                                                        {{ $limit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Filter by Keyword -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Keyword
                                            </label>
                                            <input type="text" name="keyword" 
                                                value="{{ request('keyword', '') }}"
                                                class="w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring focus:ring-blue-500 focus:outline-none">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                Anything that match in: {{ implode(', ', $allowedFilterFields) }}
                                            </span>
                                        </div>

                                        <!-- Sort Field Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Sort By
                                            </label>
                                            <select name="sort_by"
                                                class="w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                @foreach ($allowedSortFields as $field)
                                                    <option value="{{ $field }}" {{ request('sort_by') === $field ? 'selected' : '' }}>
                                                        {{ ucfirst($field) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Status Filter -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Status
                                            </label>
                                            <select name="status"
                                                class="w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                <option value="">All</option>
                                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>

                                        <!-- Sort Field Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Sort Order
                                            </label>
                                            <select name="sort_order"
                                                class="w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                <option value="ASC" {{ request('sort_order', 'ASC') === 'ASC' ? 'selected' : '' }}>
                                                    Ascending
                                                </option>
                                                <option value="DESC" {{ request('sort_order', 'ASC') === 'DESC' ? 'selected' : '' }}>
                                                    Descending
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="flex justify-end gap-3 mt-6">
                                            <button type="button" @click="open = false"
                                                class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                                Apply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>            
                </div>
            </div>

            <div class="min-h-[500px] custom-scrollbar max-w-full overflow-x-auto px-5 sm:px-6">
                <table class="min-w-full table-auto">
                    <thead class="border-gray-200 border-y dark:border-gray-800 dark:bg-gray-900">
                        <tr class="text-sm text-left text-gray-600 dark:text-gray-300">
                            <th class="w-20 px-4 py-3 font-medium">No.</th>
                            <th class="px-4 py-3 font-medium">Employee</th>
                            <th class="px-4 py-3 font-medium">Title</th>
                            <th class="px-4 py-3 font-medium">Amount</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Created At</th>
                            <th class="px-4 py-3 font-medium">Updated At</th>
                            <th class="px-4 py-3 font-medium text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 dark:text-gray-400">
                        @forelse ($reimbursements as $item)
                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $item->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->title }}</td>
                                <td class="px-4 py-3">{{ $item->amount_formatted }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                                        {{ match($item->status) {
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-yellow-100 text-yellow-700'
                                        } }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $item->formatted_created_at }}</td>
                                <td class="px-4 py-3">{{ $item->formatted_updated_at }}</td>
                                <td class="relative px-4 py-3">
                                    <div 
                                        x-data="{
                                            openActionModal: false,
                                            actionType: '',
                                            openDetailModal: false,
                                            userName: @js($item->user->name)
                                        }" 
                                        class="inline-flex justify-center space-x-1"
                                    >
                                        <!-- Detail Button -->
                                        <button 
                                            @click="openDetailModal = true" 
                                            class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400" 
                                            title="View Details"
                                        >
                                            <i class="bx bx-info-circle bx-sm"></i>
                                        </button>

                                        <!-- Approve Button -->
                                        <button 
                                            @click="openActionModal = true; actionType = 'approve'" 
                                            :disabled="'{{ $item->status }}' !== 'pending'"
                                            :class="{
                                                'text-green-600 hover:text-green-800 dark:hover:text-green-400': '{{ $item->status }}' === 'pending',
                                                'text-gray-400 cursor-not-allowed': '{{ $item->status }}' !== 'pending'
                                            }"
                                            title="Approve"
                                        >
                                            <i class="bx bx-check-circle bx-sm"></i>
                                        </button>

                                        <!-- Reject Button -->
                                        <button 
                                            @click="openActionModal = true; actionType = 'reject'" 
                                            :disabled="'{{ $item->status }}' !== 'pending'"
                                            :class="{
                                                'text-red-600 hover:text-red-800 dark:hover:text-red-400': '{{ $item->status }}' === 'pending',
                                                'text-gray-400 cursor-not-allowed': '{{ $item->status }}' !== 'pending'
                                            }"
                                            title="Reject"
                                        >
                                            <i class="bx bx-x-circle bx-sm"></i>
                                        </button>

                                        <!-- Confirm Action Modal -->
                                        <div 
                                            x-show="openActionModal" 
                                            x-cloak 
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                        >
                                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-[400px]">
                                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Confirm Action</h2>
                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    Are you sure you want to 
                                                    <span class="font-semibold" x-text="actionType"></span> 
                                                    reimbursement from 
                                                    <span class="font-semibold" x-text="userName"></span>?
                                                </p>

                                                <div class="flex justify-end mt-4 space-x-3">
                                                    <button 
                                                        @click="openActionModal = false" 
                                                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                                    >
                                                        Cancel
                                                    </button>

                                                    <template x-if="actionType === 'approve'">
                                                        <form method="POST" action="{{ route('admin.reimbursement.approve', $item->id) }}">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                                                                Approve
                                                            </button>
                                                        </form>
                                                    </template>

                                                    <template x-if="actionType === 'reject'">
                                                        <form method="POST" action="{{ route('admin.reimbursement.reject', $item->id) }}">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Modal -->
                                        <div 
                                            x-show="openDetailModal" 
                                            x-cloak 
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                        >
                                            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md mx-4 max-h-[80vh] overflow-y-auto">
                                                <!-- Header -->
                                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                        <i class="mr-1 bx bx-receipt"></i>
                                                        Reimbursement Details
                                                    </h2>
                                                    <button @click="openDetailModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                                        <i class="text-xl bx bx-x"></i>
                                                    </button>
                                                </div>

                                                <!-- Body -->
                                                <div class="px-6 py-4 space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-28">Employee:</span>
                                                        <span>{{ $item->user->name }}</span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-28">Title:</span>
                                                        <span>{{ $item->title }}</span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-28">Amount:</span>
                                                        <span>{{ $item->amount_formatted }}</span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-28">Status:</span>
                                                        <span>
                                                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded 
                                                                {{ match($item->status) {
                                                                    'approved' => 'bg-green-100 text-green-700',
                                                                    'rejected' => 'bg-red-100 text-red-700',
                                                                    default => 'bg-yellow-100 text-yellow-700'
                                                                } }}">
                                                                {{ ucfirst($item->status) }}
                                                            </span>
                                                        </span>
                                                    </div>

                                                    @if ($item->description)
                                                        <div class="flex items-start space-x-2">
                                                            <span class="font-medium w-28">Description:</span>
                                                            <span class="whitespace-pre-line">{{ $item->description }}</span>
                                                        </div>
                                                    @endif

                                                    @if ($item->proof_file)
                                                        <div class="flex items-start space-x-2">
                                                            <span class="font-medium w-28">Proof:</span>
                                                            <a href="{{ asset('storage/' . $item->proof_file) }}" target="_blank" class="text-blue-600 hover:underline">
                                                                View File
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-28">Submitted:</span>
                                                        <span>{{ $item->created_at->format('d M Y, H:i') }}</span>
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                                    <button 
                                                        @click="openDetailModal = false" 
                                                        class="px-4 py-2 text-white bg-gray-600 rounded-lg hover:bg-gray-700"
                                                    >
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-400">No reimbursement data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="{{ !$reimbursements->previousPageUrl() && !$reimbursements->nextPageUrl() ? '' : 'border-t border-gray-200 px-6 py-4 dark:border-gray-800' }}">
                <div class="flex items-center justify-between">
                    <!-- Previous Button -->
                    @if ($reimbursements->previousPageUrl())
                        <a href="{{ $reimbursements->appends(request()->query())->previousPageUrl() }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 transition bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                            <span class="hidden sm:inline">Previous</span>
                        </a>
                    @else
                        <div class="w-[96px]"></div>
                    @endif
            
                    <!-- Pagination Links - Always Centered -->
                    <div class="flex justify-center flex-1">
                        {{ $reimbursements->appends(request()->query())->links() }}
                    </div>
            
                    <!-- Next Button -->
                    @if ($reimbursements->nextPageUrl())
                        <a href="{{ $reimbursements->appends(request()->query())->nextPageUrl() }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 transition bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                            <span class="hidden sm:inline">Next</span>
                        </a>
                    @else
                        <div class="w-[96px]"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
   </div>
</main>
@endsection

@section('bottom-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        @endif

        @if(session('error'))
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
        @endif
    });
</script>
@endsection