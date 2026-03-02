@extends('partials.header')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Update User</h1>
            <p class="text-sm text-gray-500 mt-1">
                Modify user account information.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ url('/users') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">User Information</h2>
        </div>

        <form action="/user/update/{{ $users->id }}" method="POST" class="p-6">
            @method('PUT')
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <!-- Name -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $users->name) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="Enter full name"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email / Username</label>
                    <input
                        type="text"
                        name="email"
                        value="{{ old('email', $users->email) }}"
                        class="w-full rounded-lg border px-3 py-2 text-gray-900 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 @enderror"
                        placeholder="Enter email address"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-500">
                    Ensure the information is correct before saving changes.
                </p>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ url('/users') }}"
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