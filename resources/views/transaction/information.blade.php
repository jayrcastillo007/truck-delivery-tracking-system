@extends('partials.header')

@section('title', 'Transaction info')
@section('page-title', 'Transaction info')

@section('content')

<section x-data="transaction()" class="mt-4" >
    

    <div class="flex justify-between items-center mb-4 text-gray-700">
        <div
            x-show="successMessage"
            class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow"
            x-text="successMessage"
        ></div>
        <h1 class="text-2xl font-bold">Transaction information</h1>

       
    </div>

    {{-- MAP --}}
    <div id="map" class="w-full h-96 rounded shadow mb-6"></div>

    <div class="space-y-6">

        <!-- TRANSACTION SUMMARY -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                Transaction Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <p class="text-sm text-gray-500">Transaction Code</p>
                    <p class="font-medium">{{ $transaction->transaction_code }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Customer</p>
                    <p class="font-medium">{{ $transaction->customer_name }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Pickup Location</p>
                    <p class="font-medium">{{ $transaction->pickup_location }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Drop-off Location</p>
                    <p class="font-medium">{{ $transaction->dropoff_location }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-block px-3 py-1 text-sm rounded-full
                        @if($transaction->status === 'in_transit') bg-blue-100 text-blue-700
                        @elseif($transaction->status === 'scheduled') bg-yellow-100 text-yellow-700
                        @else bg-gray-200 text-gray-700 @endif">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>

            @if($transaction->signature_path)
                <div class="bg-white rounded-xl shadow p-6 mt-6">
                    <h2 class="text-lg font-semibold mb-4">Proof of Delivery</h2>

                    <img src="{{ asset('storage/'.$transaction->signature_path) }}"
                        class="w-full max-w-md rounded-lg border">

                    <p class="text-sm text-gray-600 mt-2">
                        Receiver: {{ $transaction->receiver_name }}
                    </p>

                    <p class="text-sm text-gray-600">
                        Delivered at: {{ $transaction->completed_at }}
                    </p>

                    {{-- Show Confirm button only if status is delivered --}}
                    @if($transaction->status === 'delivered')
                        <form method="POST" action="{{ url('/done-trip/'.$transaction->id) }}" class="mt-4">
                            @csrf
                            @method('PUT')

                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold">
                                Confirm
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- VEHICLE + DRIVER -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- VEHICLE CARD -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    Vehicle Information
                </h2>

                <div class="space-y-3 text-gray-700">
                    <div>
                        <p class="text-sm text-gray-500">Vehicle Type</p>
                        <p class="font-medium">{{ $transaction->vehicle->vehicle_type }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Plate Number</p>
                        <p class="font-medium">{{ $transaction->vehicle->plate_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Capacity</p>
                        <p class="font-medium">{{ $transaction->vehicle->capacity }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                            {{ ucfirst($transaction->vehicle->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- DRIVER CARD -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    Driver Information
                </h2>

                <div class="space-y-3 text-gray-700">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-medium">
                            {{ $transaction->driver->first_name }} {{ $transaction->driver->last_name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">License Number</p>
                        <p class="font-medium">{{ $transaction->driver->license_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium">{{ $transaction->driver->phone }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700">
                            {{ ucfirst($transaction->driver->status) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    

</section>

<script>

    const transactionId = @json($transaction->id);
    const transactionStatus = @json($transaction->status);

    const pickupLat = @json($transaction->pickup_lat);
    const pickupLng = @json($transaction->pickup_long);
    const dropLat   = @json($transaction->dropoff_lat);
    const dropLng   = @json($transaction->dropoff_long);

    const map = L.map('map').setView([pickupLat, pickupLng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    // Markers
    L.marker([pickupLat, pickupLng]).addTo(map).bindPopup('Pickup');
    L.marker([dropLat, dropLng]).addTo(map).bindPopup('Drop-off');

    // Fetch real route
    fetch(`https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${dropLng},${dropLat}?overview=full&geometries=geojson`)
        .then(res => res.json())
        .then(data => {
            const routeCoords = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);

            const routeLine = L.polyline(routeCoords, {
                weight: 4
            }).addTo(map);

            map.fitBounds(routeLine.getBounds());
        });


    /////////////////////////// LIVE LOCATION //////////////////////////////////////

        let driverMarker = null;

        async function fetchLatestDriverLocation() {
            try {
                const res = await fetch(`/admin/transactions/${transactionId}/latest-location`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) return;

                const data = await res.json();

                if (!data.found) return;

                const lat = data.lat;
                const lng = data.lng;

                if (!driverMarker) {
                    driverMarker = L.marker([lat, lng]).addTo(map).bindPopup('Driver (Live)');
                    // optional: open popup once
                    // driverMarker.openPopup();
                } else {
                    driverMarker.setLatLng([lat, lng]);
                }

            } catch (e) {
                console.log('Tracking fetch error', e);
            }
        }   

        if (transactionStatus === 'in_transit') {
            fetchLatestDriverLocation();
            setInterval(fetchLatestDriverLocation, 3000);
        }


</script>


@endsection
