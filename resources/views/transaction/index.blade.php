@extends('partials.header')

@section('title', 'Booking/Transaction')
@section('page-title', 'Booking/Transaction')

@section('content')

<section x-data="transaction()" class="mt-4" >
    

    <div class="flex justify-between items-center mb-4 text-gray-700">
        <div
            x-show="successMessage"
            class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow"
            x-text="successMessage"
        ></div>
        <h1 class="text-2xl font-bold">Booking/Transaction</h1>

    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        {{-- Search group (fixed width like picture) --}}
        <div class="w-full lg:w-[560px]">
            <div class="flex">
                <div class="relative flex-1">

                    <input id="transactionSearch"
                        type="text"
                        class="w-full rounded-xl rounded-r-none border border-gray-300 bg-white pl-3 pr-3 py-4 text-sm text-gray-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        placeholder="Search"
                    >
                </div>

                <button type="button"
                    id="btnTransactionSearch"
                    class="shrink-0 w-28 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl rounded-l-none px-6"
                >
                    Search
                </button>
            </div>
        </div>

        {{-- Create button (right side) --}}
        
        <button
            type="button"
            class="w-full lg:w-auto bg-sky-600 hover:bg-sky-500 text-white font-bold px-8 py-3 rounded-xl shadow"
            @click="activeModal = 'create';
            $nextTick(() => initPickupMap());
            $nextTick(() => initDropoffMap())
            "
        >
            Create Booking
        </button>

    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table id="transactionTable" class="w-full text-left text-gray-700">
            <thead class="uppercase bg-sky-600 text-white ">
                <tr>
                    <th class="py-3 px-6">Transaction code</th>
                    <th class="py-3 px-6">Customer</th>
                    <th class="py-3 px-6">Pickup location</th>
                    <th class="py-3 px-6">Drop-off location</th>
                    <th class="py-3 px-6">Cargo details</th>
                    <th class="py-3 px-6">Vehicle</th>
                    <th class="py-3 px-6">Driver</th>
                    <th class="py-3 px-6">status</th>
                    <th class="py-3 px-6">created by</th>
                    <th class="py-3 px-6"></th>
                </tr>
            </thead>

            <tbody class="divide-y">
                
            </tbody>
        </table>

        <div class="flex items-center justify-between p-4">
            <div class="text-sm text-gray-600" x-text="`Showing page ${meta.current_page} of ${meta.last_page} (Total: ${meta.total})`"></div>

            <div class="flex gap-2">
                <button class="px-3 py-2 rounded border bg-sky-600 hover:bg-sky-500 text-white"
                        :disabled="meta.current_page <= 1"
                        @click="goPage(meta.current_page - 1)">
                Prev
                </button>

                <button class="px-3 py-2 rounded border bg-sky-600 hover:bg-sky-500 text-white"
                        :disabled="meta.current_page >= meta.last_page"
                        @click="goPage(meta.current_page + 1)">
                Next
                </button>
            </div>
        </div>
    </div>

    <!-- Create Booking Modal -->
    <div
        x-show="activeModal === 'create'"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
        <div class="bg-white rounded-xl w-full max-w-5xl p-6 shadow-2xl">

            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-3 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Create Booking</h2>
                <button @click="activeModal = null" class="text-gray-400 hover:text-gray-700 text-2xl">
                    &times;
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitCreate($event.target)" class="space-y-6">
                @csrf

                <!-- BASIC INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Customer Name
                        </label>
                        <input
                            type="text"
                            name="customer_name"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500"
                        >
                        <template x-if="errors.customer_name">
                            <p class="text-red-500 text-xs mt-1" x-text="errors.customer_name[0]"></p>
                        </template>
                    </div>

                    <!-- Cargo Details -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Cargo Details
                        </label>
                        <input
                            type="text"
                            name="cargo_details"
                            placeholder="e.g. Furniture, Electronics"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500"
                        >
                        <template x-if="errors.cargo_details">
                            <p class="text-red-500 text-xs mt-1" x-text="errors.cargo_details[0]"></p>
                        </template>
                    </div>
                </div>

                <!-- LOCATIONS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- PICKUP -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            📍 Pickup Location
                        </h3>

                        <input
                            type="text"
                            x-model="pickupAddress"
                            placeholder="Enter pickup address"
                            class="w-full border rounded px-3 py-2 mb-2"
                        >

                        <button
                            type="button"
                            @click="geocodePickup()"
                            class="bg-sky-600 hover:bg-sky-700 text-white px-3 py-1 rounded text-sm"
                        >
                            Locate on map
                        </button>

                        <div id="pickupMap" class="h-56 mt-3 rounded border"></div>

                        <!-- hidden fields -->
                        <input type="hidden" name="pickup_location" :value="pickupAddress">
                        <input type="hidden" name="pickup_lat" x-model="pickupLat">
                        <input type="hidden" name="pickup_long" x-model="pickupLong">

                        <template x-if="errors.pickup_lat">
                            <p class="text-red-500 text-xs mt-1">
                                Please select a valid pickup location.
                            </p>
                        </template>
                    </div>

                    <!-- DROPOFF -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            🏁 Drop-off Location
                        </h3>

                        <input
                            type="text"
                            x-model="dropoffAddress"
                            placeholder="Enter drop-off address"
                            class="w-full border rounded px-3 py-2 mb-2"
                        >

                        <button
                            type="button"
                            @click="geocodeDropoff()"
                            class="bg-sky-600 hover:bg-sky-700 text-white px-3 py-1 rounded text-sm"
                        >
                            Locate on map
                        </button>

                        <div id="dropoffMap" class="h-56 mt-3 rounded border"></div>

                        <!-- hidden fields -->
                        <input type="hidden" name="dropoff_location" :value="dropoffAddress">
                        <input type="hidden" name="dropoff_lat" x-model="dropoffLat">
                        <input type="hidden" name="dropoff_long" x-model="dropoffLong">

                        <template x-if="errors.dropoff_lat">
                            <p class="text-red-500 text-xs mt-1">
                                Please select a valid drop-off location.
                            </p>
                        </template>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button
                        type="button"
                        @click="activeModal = null"
                        class="px-5 py-2 rounded bg-gray-200 hover:bg-gray-300"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-6 py-2 rounded bg-sky-600 hover:bg-sky-700 text-white font-semibold"
                    >
                        <span x-show="!loading">Create Booking</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Schedule Booking Modal -->
    <div
        x-show="activeModal === 'schedule'"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="open = false"
        @keydown.escape.window="open = false"
        >
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <form @submit.prevent="submitSchedule($event.target)">
                @csrf

                <input type="hidden" name="transaction_id" :value="selectedTransaction.id">
                <div class="mb-4">
                    <p><strong>Customer:</strong>
                        <span x-text="selectedTransaction.customer_name"></span>
                    </p>
                </div>

                <div class="mb-4">
                    <p><strong>Pickup:</strong>
                        <span x-text="selectedTransaction.pickup_location"></span>
                    </p>
                </div>

                <div class="mb-4">
                    <p><strong>Drop-off:</strong>
                        <span x-text="selectedTransaction.dropoff_location"></span>
                    </p>
                </div>

                <div class="mb-4">
                    <p><strong>Cargo Details:</strong>
                        <span x-text="selectedTransaction.cargo_details"></span>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold">Driver's</label>
                    <select
                        name="driver_id"
                        x-model="selectedDriver"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Select Driver</option>

                        <template x-for="driver in drivers" :key="driver.id">
                            <option :value="driver.id" :disabled="driver.status !== 'available'">
                                <span x-text="driver.first_name + ' ' + driver.last_name+ ' (' + driver.status + ')'"></span>
                            </option>
                        </template>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold">Vehicle's</label>
                    <select
                        name="vehicle_id"
                        x-model="selectedVehicle"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Select Vehicle</option>

                        <template x-for="vehicle in vehicles" :key="vehicle.id">
                            <option :value="vehicle.id" :disabled="vehicle.status !== 'available'">
                                <span x-text="vehicle.vehicle_type +' ('+ vehicle.status +')'"></span>
                            </option>
                        </template>
                    </select>
                </div>

               

                <div class="flex justify-end gap-2">
                    <button type="button"
                            class="bg-gray-300 px-4 py-2 rounded"
                            @click="activeModal = null">
                        Close
                    </button>

                    <button type="submit"
                            :disabled="loading"
                            class="bg-sky-600 text-white px-4 py-2 rounded">
                        <span x-show="!loading">Save</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>

@endsection
