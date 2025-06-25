@php
    $minDate = \Carbon\Carbon::now()->toDateString();
@endphp

@extends('layouts.employee.app')

@section('content')
<main>
    <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Leave Application</h1>
            <p class="text-gray-600 dark:text-gray-400">Update your leave request below.</p>
        </div>

        <!-- Leave Application Form -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('employee.leave.update', $leave->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Leave Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="start_date" 
                        name="start_date" 
                        min="{{ $minDate }}"
                        value="{{ old('start_date', \Carbon\Carbon::parse($leave->start_date)->toDateString()) }}"
                        class="mt-1 block w-full rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 
                            border 
                            @error('start_date') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror"
                        required
                    >
                    @error('start_date')
                        <p class="text-xs text-red-600 mt-1">*{{ $message }}</p>
                    @enderror
                </div>

                <!-- Leave End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="end_date" 
                        name="end_date" 
                        min="{{ $minDate }}"
                        value="{{ old('end_date', \Carbon\Carbon::parse($leave->end_date)->toDateString()) }}"
                        class="mt-1 block w-full rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 
                            border 
                            @error('end_date') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror"
                        required
                    >
                    @error('end_date')
                        <p class="text-xs text-red-600 mt-1">*{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="reason" 
                        name="reason" 
                        rows="4" 
                        class="mt-1 block w-full rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 
                            border 
                            @error('reason') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror"
                        required
                    >{{ old('reason', $leave->reason) }}</textarea>
                    @error('reason')
                        <p class="text-xs text-red-600 mt-1">*{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end gap-3">
                    <a 
                        href="{{ route('employee.leave.index') }}" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection