@php
    use App\Enums\PermissionEnum;
@endphp

@extends('layouts.admin.app')

@section('content')
<!-- ===== Main Content Start ===== -->
<main>
   <div class="p-4 mx-auto max-w-screen-2xl md:p-6">

    <!-- Header Section -->
    <div class="px-6 py-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Leaves Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage leave request data</p>
            </div>

            <!-- Status Counts -->
            <div class="grid grid-cols-3 gap-4 w-full sm:w-auto">
                <!-- Pending -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                    <p class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Pending</p>
                    <p class="text-lg font-semibold text-yellow-800 dark:text-yellow-100">{{ $statusCounts['pending'] ?? 0 }}</p>
                </div>

                <!-- Approved -->
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-3">
                    <p class="text-xs font-medium text-green-700 dark:text-green-300">Approved</p>
                    <p class="text-lg font-semibold text-green-800 dark:text-green-100">{{ $statusCounts['approved'] ?? 0 }}</p>
                </div>

                <!-- Rejected -->
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-3">
                    <p class="text-xs font-medium text-red-700 dark:text-red-300">Rejected</p>
                    <p class="text-lg font-semibold text-red-800 dark:text-red-100">{{ $statusCounts['rejected'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="border-gray-100 p-5 dark:border-gray-800 sm:p-6" x-data="{ selected: [] }">
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex flex-col gap-2 px-5 sm:flex-row sm:items-end sm:justify-end sm:px-6">
                
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex items-center gap-2">                         

                        <!-- Reset Filter Button -->
                        <a href="{{ route('admin.leave.index') }}"
                            class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-gray-400 bg-gray-100 text-gray-700 font-medium transition-all hover:bg-gray-200 hover:border-gray-500 focus:ring focus:ring-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="bx bx-reset text-lg"></i>
                            Reset Filter
                        </a>
                        
                        <!-- Filter Modal need to adjust the sort-->
                        <div x-data="{ open: false, selectedField: '{{ request()->query('filter') ? array_key_first(request('filter')) : '' }}' }">                            
                            <!-- Filter Button -->
                            <button @click.prevent="open = true"
                                class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-purple-500 bg-purple-600 text-white font-medium transition-all hover:bg-purple-700 hover:border-purple-600 focus:ring focus:ring-purple-300 dark:bg-purple-700 dark:border-purple-600 dark:hover:bg-purple-800">
                                <i class="bx bx-filter text-lg"></i>
                                Filter
                            </button>

                            <!-- Modal -->
                            <div x-cloak x-show="open" @keydown.escape.window="open = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div @click.away="open = false"
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-1/2">
                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Filter Options</h2>

                                    <!-- Form -->
                                    <form method="GET" action="{{ route('admin.leave.index') }}">
                                        <!-- Limit Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Limit
                                            </label>
                                            <select name="limit"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
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
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg 
                                                    bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 
                                                    focus:ring focus:ring-blue-500 focus:outline-none">
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
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
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
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
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
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                <option value="ASC" {{ request('sort_order', 'ASC') === 'ASC' ? 'selected' : '' }}>
                                                    Ascending
                                                </option>
                                                <option value="DESC" {{ request('sort_order', 'ASC') === 'DESC' ? 'selected' : '' }}>
                                                    Descending
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" @click="open = false"
                                                class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
                    <thead class="border-y border-gray-200 dark:border-gray-800 dark:bg-gray-900">
                        <tr class="text-left text-gray-600 dark:text-gray-300 text-sm">
                            <th class="w-20 px-4 py-3 font-medium">No.</th>
                            <th class="px-4 py-3 font-medium">Employee</th>
                            <th class="px-4 py-3 font-medium">Position</th>
                            <th class="px-4 py-3 font-medium">Leave Period</th>
                            <th class="px-4 py-3 font-medium">Reason</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 dark:text-gray-400">
                        @forelse ($leaveApplications as $application)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <td class="w-20 px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $application->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $application->user->job_title ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $application->formatted_leave_period }}</td>
                                <td class="px-4 py-3">
                                    <div class="line-clamp-2">{{ $application->reason }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                                        {{ match($application->status) {
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-yellow-100 text-yellow-700'
                                        } }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $application->formatted_created_at }}</td>
                                <td class="px-4 py-3">{{ $application->formatted_updated_at }}</td>
                                <td class="px-4 py-3 relative">
                                    <div 
                                        x-data="{
                                            openActionModal: false,
                                            actionType: '',
                                            openDetailModal: false,
                                            userName: @js($application->user->name)
                                        }" 
                                        class="inline-flex space-x-1 justify-center"
                                    >
                                        <!-- Details Button -->
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
                                            :disabled="'{{ $application->status }}' !== 'pending'"
                                            :class="{
                                                'text-green-600 hover:text-green-800 dark:hover:text-green-400': '{{ $application->status }}' === 'pending',
                                                'text-gray-400 cursor-not-allowed': '{{ $application->status }}' !== 'pending'
                                            }"
                                            title="Approve"
                                        >
                                            <i class="bx bx-check-circle bx-sm"></i>
                                        </button>

                                        <!-- Reject Button -->
                                        <button 
                                            @click="openActionModal = true; actionType = 'reject'" 
                                            :disabled="'{{ $application->status }}' !== 'pending'"
                                            :class="{
                                                'text-red-600 hover:text-red-800 dark:hover:text-red-400': '{{ $application->status }}' === 'pending',
                                                'text-gray-400 cursor-not-allowed': '{{ $application->status }}' !== 'pending'
                                            }"
                                            title="Reject"
                                        >
                                            <i class="bx bx-x-circle bx-sm"></i>
                                        </button>

                                        <!-- Approve/Reject Modal -->
                                        <div 
                                            x-show="openActionModal" 
                                            x-cloak 
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                        >
                                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-[400px]">
                                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Confirm Action</h2>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                                    Are you sure you want to 
                                                    <span class="font-semibold" x-text="actionType"></span> 
                                                    leave request from 
                                                    <span class="font-semibold" x-text="userName"></span>?
                                                </p>

                                                <div class="flex justify-end space-x-3 mt-4">
                                                    <button 
                                                        @click="openActionModal = false" 
                                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                                    >
                                                        Cancel
                                                    </button>

                                                    <template x-if="actionType === 'approve'">
                                                        <form method="POST" action="{{ route('admin.leave.approve', $application->id) }}">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                                Approve
                                                            </button>
                                                        </form>
                                                    </template>

                                                    <template x-if="actionType === 'reject'">
                                                        <form method="POST" action="{{ route('admin.leave.reject', $application->id) }}">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
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
                                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                        <i class="bx bx-file mr-1"></i>
                                                        Leave Application Details
                                                    </h2>
                                                    <button @click="openDetailModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                                        <i class="bx bx-x text-xl"></i>
                                                    </button>
                                                </div>

                                                <!-- Body -->
                                                <div class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 space-y-4">
                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-24">Employee:</span>
                                                        <span>{{ $application->user->name }}</span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-24">Period:</span>
                                                        <span>{{ $application->formatted_leave_period }}</span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-24">Status:</span>
                                                        <span>
                                                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded 
                                                                {{ match($application->status) {
                                                                    'approved' => 'bg-green-100 text-green-700',
                                                                    'rejected' => 'bg-red-100 text-red-700',
                                                                    default => 'bg-yellow-100 text-yellow-700'
                                                                } }}">
                                                                {{ ucfirst($application->status) }}
                                                            </span>
                                                        </span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-24">Reason:</span>
                                                        <span class="whitespace-pre-line">{{ $application->reason }}</span>
                                                    </div>

                                                    <div class="flex items-start space-x-2">
                                                        <span class="font-medium w-24">Submitted:</span>
                                                        <span>{{ $application->created_at->format('d M Y, H:i') }}</span>
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                                                    <button 
                                                        @click="openDetailModal = false" 
                                                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
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
                                <td colspan="10" class="text-center py-4 text-gray-400">No leave applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>            
                     
            <div class="{{ !$leaveApplications->previousPageUrl() && !$leaveApplications->nextPageUrl() ? '' : 'border-t border-gray-200 px-6 py-4 dark:border-gray-800' }}">
                <div class="flex items-center justify-between">
                    <!-- Previous Button -->
                    @if ($leaveApplications->previousPageUrl())
                        <a href="{{ $leaveApplications->appends(request()->query())->previousPageUrl() }}" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow-sm hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition">
                            <span class="hidden sm:inline">Previous</span>
                        </a>
                    @else
                        <div class="w-[96px]"></div>
                    @endif
            
                    <!-- Pagination Links - Always Centered -->
                    <div class="flex justify-center flex-1">
                        {{ $leaveApplications->appends(request()->query())->links() }}
                    </div>
            
                    <!-- Next Button -->
                    @if ($leaveApplications->nextPageUrl())
                        <a href="{{ $leaveApplications->appends(request()->query())->nextPageUrl() }}" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow-sm hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition">
                            <span class="hidden sm:inline">Next</span>
                        </a>
                    @else
                        <div class="w-[96px]"></div>
                    @endif
                </div>
            </div>                             
            
        </div>
        <!-- Table Five -->
    </div>
   </div>
</main>
<!-- ===== Main Content End ===== -->
@endsection

@section('bottom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 shadow-lg',
                        title: 'font-normal text-base text-gray-800 dark:text-gray-200'
                    }
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
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 shadow-lg',
                        title: 'font-normal text-base text-gray-800 dark:text-gray-200'
                    }
                });
            @endif
        });
    </script>
@endsection