@extends('components.layouts_driver')

@section('title', 'Driver Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">

    <!-- Top Bar -->
    <div class="sticky top-0 z-20 bg-white border-b">
        <div class="px-4 py-3 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Welcome back</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ auth()->user()->name ?? 'Driver' }}
                </p>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <button class="text-sm text-gray-600 hover:text-gray-900">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="p-4 space-y-4">

        <!-- Status Strip -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Today</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ now()->format('l, M d, Y') }}
                    </p>
                </div>

                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    Online
                </span>
            </div>
        </div>

        @if(!$transaction)
            <!-- No Assigned Trip -->
            <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
                <div class="text-4xl mb-2">🚚</div>
                <p class="text-lg font-semibold text-gray-800">No assigned trip</p>
                <p class="text-sm text-gray-600 mt-1">
                    You don’t have any active booking right now. Please wait for assignment.
                </p>

                <div class="mt-4">
                    <button
                        type="button"
                        onclick="window.location.reload()"
                        class="w-full bg-sky-600 hover:bg-sky-700 text-white font-semibold py-2 rounded-lg"
                    >
                        Refresh
                    </button>
                </div>
            </div>

        @else
            <!-- Assigned Trip Card -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-4 border-b">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Assigned Trip</p>
                            <p class="text-base font-semibold text-gray-800">
                                {{ $transaction->transaction_code }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                Customer: <span class="font-medium">{{ $transaction->customer_name }}</span>
                            </p>
                        </div>

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($transaction->status === 'in_transit') bg-blue-100 text-blue-700
                            @elseif($transaction->status === 'scheduled') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-700 @endif
                        ">
                            {{ ucfirst(str_replace('_',' ', $transaction->status)) }}
                        </span>
                    </div>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Pickup / Dropoff -->
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <div class="mt-1 h-8 w-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold">
                                P
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Pickup</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $transaction->pickup_location }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="mt-1 h-8 w-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold">
                                D
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Drop-off</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $transaction->dropoff_location }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle / Driver quick info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3 border">
                            <p class="text-xs text-gray-500">Vehicle</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $transaction->vehicle->vehicle_type ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-600">
                                Plate: {{ $transaction->vehicle->plate_number ?? '—' }}
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3 border">
                            <p class="text-xs text-gray-500">Driver</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $transaction->driver ? ($transaction->driver->first_name . ' ' . $transaction->driver->last_name) : 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-600">
                                {{ $transaction->driver->phone ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid gap-3">
                        <a
                            href="{{ url('/driver/trip/' . $transaction->id) }}"
                            class="text-center bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2 rounded-lg"
                        >
                            View Trip
                        </a>

                        <!-- @if($transaction->status === 'scheduled')
                            <form method="POST" action="{{ url('/driver/trip/' . $transaction->id . '/start') }}">
                                @csrf
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg">
                                    Start
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-600 font-semibold py-2 rounded-lg cursor-not-allowed">
                                Started
                            </button>
                        @endif -->
                    </div>

                </div>
            </div>
        @endif

        <!-- Footer hint -->
        <div class="text-center text-xs text-gray-500 pt-2">
            Tip: Keep your browser open during the trip for best tracking accuracy.
        </div>
    </div>
</div>
@endsection
