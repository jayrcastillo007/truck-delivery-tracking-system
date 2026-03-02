@extends('components.layouts_driver')

@section('title', 'Driver Trip')

@section('content')

<section x-data="transaction()" class="min-h-screen pb-24">

    {{-- Success toast --}}
    <div
        x-show="successMessage"
        x-transition
        class="fixed top-4 right-4 z-50 bg-green-600 text-white px-4 py-2 rounded-lg shadow"
        x-text="successMessage"
    ></div>

    {{-- Sticky Header (Mobile First) --}}
    <header class="sticky top-0 z-40 bg-gray-900/95 backdrop-blur border-b border-white/10">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="min-w-0">
                <h1 class="text-white text-base font-semibold leading-tight truncate">
                    Transaction Info
                </h1>
                <p class="text-gray-300 text-xs truncate">
                    {{ $transaction->transaction_code }}
                </p>
            </div>

            {{-- Status chip --}}
            <span class="shrink-0 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                @if($transaction->status === 'in_transit') bg-blue-100 text-blue-700
                @elseif($transaction->status === 'scheduled') bg-yellow-100 text-yellow-700
                @else bg-gray-200 text-gray-700 @endif">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
    </header>

    <div class="px-4 mt-4 space-y-4">

        {{-- MAP (mobile height smaller, desktop larger) --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">

            {{-- TOP: Instruction card (NOT overlay) --}}
            <div class="p-3 border-b">
                <div class="bg-white rounded-2xl shadow p-3 border border-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500">Next</p>
                            <p id="navInstruction" class="text-sm font-semibold text-gray-900 truncate">
                                Ready to navigate
                            </p>
                            <p id="navSub" class="text-xs text-gray-600 mt-0.5">—</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p id="navETA" class="text-sm font-semibold text-gray-900">-- min</p>
                            <p id="navDist" class="text-xs text-gray-500">-- km</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAP --}}
            <div id="map" class="w-full h-64 sm:h-80 md:h-96"></div>

            {{-- BOTTOM: Controls (Mobile-first) --}}
            <div class="p-3 border-t">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-3 space-y-3">

                    {{-- Top row: chips (wrap on mobile) --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                            Speed: <span id="navSpeed" class="ml-1 font-semibold">0</span> km/h
                        </span>

                        <button id="recenterBtn" type="button"
                            class="inline-flex items-center rounded-full border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 active:scale-[0.99]">
                            Recenter
                        </button>

                        <button id="followBtn" type="button"
                            class="inline-flex items-center rounded-full border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 active:scale-[0.99]">
                            Follow: ON
                        </button>
                    </div>

                    {{-- Action area --}}
                    @if($transaction->status === 'scheduled')
                        <button id="startTripBtn" type="button"
                            class="w-full rounded-xl bg-green-600 text-white px-4 py-3 text-sm font-semibold active:scale-[0.99] transition">
                            Start Trip
                        </button>

                    @elseif($transaction->status === 'in_transit')
                        <form id="signatureForm" method="POST" action="{{ url('/done-trip/'.$transaction->id) }}"
                            class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold text-gray-700">
                                    Receiver Name
                                </label>
                                <input type="text"
                                    name="receiver_name"
                                    required
                                    class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-gray-700">
                                        Receiver Signature
                                    </label>
                                    <button type="button" id="clearSignature"
                                        class="text-xs font-semibold text-gray-600 underline underline-offset-2">
                                        Clear
                                    </button>
                                </div>

                                <div class="mt-2 rounded-2xl border border-gray-200 bg-white overflow-hidden">
                                    <canvas id="signaturePad" class="w-full h-40"></canvas>
                                </div>

                                <input type="hidden" name="signature" id="signatureInput">
                                <p class="mt-1 text-[11px] text-gray-500">
                                    Ask the receiver to sign using finger.
                                </p>
                            </div>

                            <button type="submit"
                                class="w-full rounded-xl bg-orange-500 text-white py-3 text-sm font-semibold active:scale-[0.99] transition">
                                Confirm Delivery
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        </div>


        {{-- TRANSACTION DETAILS --}}
        <div class="bg-white rounded-2xl shadow p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-800">Transaction Details</h2>
            </div>

            <div class="space-y-3 text-gray-700">
                <div class="grid grid-cols-1 gap-3">
                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Customer</p>
                        <p class="text-sm font-medium">{{ $transaction->customer_name }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Pickup Location</p>
                        <p class="text-sm font-medium break-words">{{ $transaction->pickup_location }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Drop-off Location</p>
                        <p class="text-sm font-medium break-words">{{ $transaction->dropoff_location }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- VEHICLE + DRIVER (stack on mobile, 2 columns on md+) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- VEHICLE --}}
            <div class="bg-white rounded-2xl shadow p-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Vehicle</h2>

                <div class="space-y-3">
                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Vehicle Type</p>
                        <p class="text-sm font-medium">{{ $transaction->vehicle->vehicle_type }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Plate Number</p>
                        <p class="text-sm font-medium">{{ $transaction->vehicle->plate_number }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Capacity</p>
                        <p class="text-sm font-medium">{{ $transaction->vehicle->capacity }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="text-sm font-medium">{{ ucfirst($transaction->vehicle->status) }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            {{ ucfirst($transaction->vehicle->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- DRIVER --}}
            <div class="bg-white rounded-2xl shadow p-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Driver</h2>

                <div class="space-y-3">
                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Name</p>
                        <p class="text-sm font-medium">
                            {{ $transaction->driver->first_name }} {{ $transaction->driver->last_name }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">License Number</p>
                        <p class="text-sm font-medium">{{ $transaction->driver->license_number }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">Phone</p>
                        <p class="text-sm font-medium">{{ $transaction->driver->phone }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-3 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="text-sm font-medium">{{ ucfirst($transaction->driver->status) }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
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

    let routeBounds = null;
    let followMode = true;
    let driverMarker = null;
    let watchId = null;
    let lastSentAt = 0;

    const elInstruction = document.getElementById('navInstruction');
    const elSub = document.getElementById('navSub');
    const elETA = document.getElementById('navETA');
    const elDist = document.getElementById('navDist');
    const elSpeed = document.getElementById('navSpeed');

    const followBtn = document.getElementById('followBtn');
    const recenterBtn = document.getElementById('recenterBtn');

    const pickupLat = @json($transaction->pickup_lat);
    const pickupLng = @json($transaction->pickup_long);
    const dropLat   = @json($transaction->dropoff_lat);
    const dropLng   = @json($transaction->dropoff_long);

    const map = L.map('map').setView([pickupLat, pickupLng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    L.marker([pickupLat, pickupLng]).addTo(map).bindPopup('Pickup');
    L.marker([dropLat, dropLng]).addTo(map).bindPopup('Drop-off');

    // let routeBounds = null;

    fetch(`https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${dropLng},${dropLat}?overview=full&geometries=geojson`)
        .then(res => res.json())
        .then(data => {
            const route = data.routes[0];
            const routeCoords = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
            const routeLine = L.polyline(routeCoords, { weight: 4 }).addTo(map);

            routeBounds = routeLine.getBounds();

            map.fitBounds(routeLine.getBounds(), { padding: [20, 20] });

            // ETA & Distance
            setNavSummary(route.distance, route.duration);
        });


    // Follow toggle
    followBtn?.addEventListener('click', () => {
        followMode = !followMode;
        followBtn.textContent = `Follow: ${followMode ? 'ON' : 'OFF'}`;
    });

    // Recenter button (back to route bounds or driver)
    recenterBtn?.addEventListener('click', () => {
        if (driverMarker && followMode) {
            const p = driverMarker.getLatLng();
            map.flyTo([p.lat, p.lng], 17, { animate: true });
            return;
        }
        if (routeBounds) map.fitBounds(routeBounds, { padding: [20, 20] });
    });


    document.getElementById('startTripBtn')?.addEventListener('click', async () => {

        // call backend to update status
        await fetch(`/driver/trips/${transactionId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            alert('Failed to start trip');
            return;
        }

        // zoom into route for "navigation"
        if (routeBounds) map.fitBounds(routeBounds, { padding: [10, 10], maxZoom: 16 });

        // start tracking immediately
        // startTracking();

        window.location.reload();

        if (transactionStatus === 'in_transit') {
            startTracking();
        }

    });
    
    function setNavSummary(distanceMeters, durationSeconds) {
        const km = (distanceMeters / 1000);
        const mins = Math.max(1, Math.round(durationSeconds / 60));

        if (elETA) elETA.textContent = `${mins} min`;
        if (elDist) elDist.textContent = `${km.toFixed(1)} km`;

        // Simple instruction text (OSRM doesn't provide turn-by-turn without extra steps)
        if (elInstruction) elInstruction.textContent = "Head to drop-off";
        if (elSub) elSub.textContent = "Following the route…";
    }

    function startTracking() {
        if (!navigator.geolocation) {
            alert('Geolocation not supported');
            return;
        }
        if (watchId !== null) return;

        watchId = navigator.geolocation.watchPosition(async (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            // Speed (m/s -> km/h)
            const kph = pos.coords.speed ? (pos.coords.speed * 3.6) : 0;
            if (elSpeed) elSpeed.textContent = `${Math.round(kph)}`;

            if (!driverMarker) {
            driverMarker = L.marker([lat, lng]).addTo(map).bindPopup('Driver');
            } else {
            driverMarker.setLatLng([lat, lng]);
            }

            // Navigation view: follow driver
            if (followMode) {
            map.flyTo([lat, lng], 17, { animate: true });
            }

            // Send to backend every 5 seconds
            const now = Date.now();
            if (now - lastSentAt < 5000) return;
            lastSentAt = now;

            await fetch(`/driver/trips/${transactionId}/location`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    lat, lng,
                    accuracy: pos.coords.accuracy ?? null,
                    speed: pos.coords.speed ?? null,
                }),
            });
        }, (err) => {
            console.log('GPS error:', err);
        }, {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 10000,
        });
    }

    // ✅ AUTO-RESUME when page opens again
    window.addEventListener('load', () => {
        if (transactionStatus === 'in_transit') {
            startTracking();
        }
    });

    ///////////// SIGNATURE  ///////////////////

    document.addEventListener('DOMContentLoaded', function () {

        const canvas = document.getElementById('signaturePad');
        if (!canvas) return;

        const signaturePad = new SignaturePad(canvas);

        document.getElementById('clearSignature').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.getElementById('signatureForm').addEventListener('submit', function (e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert("Please provide a signature.");
                return;
            }

            const signatureData = signaturePad.toDataURL();
            document.getElementById('signatureInput').value = signatureData;
        });

    });

</script>

@endsection
