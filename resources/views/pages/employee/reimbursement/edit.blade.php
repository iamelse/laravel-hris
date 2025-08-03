@extends('layouts.employee.app')

@section('content')
<main>
    <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Reimbursement</h1>
            <p class="text-gray-600 dark:text-gray-400">Update your reimbursement submission below.</p>
        </div>

        <!-- Reimbursement Form -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('employee.reimbursement.update', $reimbursement->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title"
                        value="{{ old('title', $reimbursement->title) }}"
                        placeholder="E.g. Transportation Fee, Office Supplies, etc."
                        class="mt-1 block w-full rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 
                            border 
                            @error('title') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror"
                        required
                    >
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">*{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount"
                        value="{{ old('amount', $reimbursement->amount) }}"
                        placeholder="Enter amount in IDR (e.g. 150000)"
                        step="any"
                        min="0"
                        class="mt-1 block w-full rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 
                            border 
                            @error('amount') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror"
                        required
                    >
                    @error('amount')
                        <p class="mt-1 text-xs text-red-600">*{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4" 
                        placeholder="Provide a brief explanation for this reimbursement (e.g. 'Grab receipt to client meeting')"
                        class="mt-1 block w-full rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 
                            border 
                            @error('description') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror"
                        required
                    >{{ old('description', $reimbursement->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">*{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end gap-3">
                    <a 
                        href="{{ route('employee.reimbursement.index') }}" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection