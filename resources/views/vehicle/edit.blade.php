@extends('partials.header')

@section('title', 'Edit Vehicle')
@section('page-title', 'Edit Vehicle')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Update Vehicle</h1>
            <p class="text-sm text-gray-500 mt-1">
                Update vehicle details and status information.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ url('/vehicle') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Vehicle Information</h2>

                <!-- Optional badge -->
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                    {{ $vehicles->status === 'available' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $vehicles->status === 'in_use' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $vehicles->status === 'maintenance' ? 'bg-amber-100 text-amber-700' : '' }}
                ">
                    {{ strtoupper(str_replace('_', ' ', $vehicles->status)) }}
                </span>
            </div>
        </div>

        <form action="/update_vehicle/{{ $vehicles->id }}" method="POST" class="p-6">
            @method('PUT')
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <!-- Vehicle Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                    <input
                        type="text"
                        name="vehicle_type"
                        value="{{ old('vehicle_type', $vehicles->vehicle_type) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('vehicle_type') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., Truck, Van"
                    >
                    @error('vehicle_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plate Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plate Number</label>
                    <input
                        type="text"
                        name="plate_number"
                        value="{{ old('plate_number', $vehicles->plate_number) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('plate_number') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., ABC-1234"
                    >
                    @error('plate_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                    <input
                        type="number"
                        name="capacity"
                        value="{{ old('capacity', $vehicles->capacity) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('capacity') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="e.g., 1000"
                    >
                    @error('capacity')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        name="status"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               border-gray-300"
                    >
                        <option value="available" {{ old('status', $vehicles->status) === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ old('status', $vehicles->status) === 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="maintenance" {{ old('status', $vehicles->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Choose the current availability of the vehicle.</p>
                </div>

            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-500">
                    Make sure the information is correct before saving.
                </p>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ url('/vehicle') }}"
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