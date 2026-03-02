@extends('partials.header')

@section('title', 'Edit Driver')
@section('page-title', 'Edit Driver')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Update Driver</h1>
            <p class="text-sm text-gray-500 mt-1">
                Update driver personal information and contact details.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ url('/driver') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Driver Information</h2>
        </div>

        <form action="/update_driver/{{ $drivers->id }}" method="POST" class="p-6">
            @method('PUT')
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <!-- First Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name', $drivers->first_name) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('first_name') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., Juan"
                    >
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name', $drivers->last_name) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('last_name') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., Dela Cruz"
                    >
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address (full width) -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input
                        type="text"
                        name="address"
                        value="{{ old('address', $drivers->address) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('address') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., Brgy. ___, City, Province"
                    >
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License Number</label>
                    <input
                        type="text"
                        name="license_number"
                        value="{{ old('license_number', $drivers->license_number) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('license_number') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., N01-12-345678"
                    >
                    @error('license_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $drivers->phone) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('phone') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., 09XXXXXXXXX"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-500">
                    Make sure details are correct before saving.
                </p>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ url('/driver') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 rounded-lg bg-sky-600 text-white font-semibold
                                   hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition">
                        Save Changes
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection