@extends('partials.header')

@section('title', 'Vehicle')
@section('page-title', 'Vehicle')

@section('content')
<section x-data="vehicle()" x-init="init()" class="mt-4">

    {{-- PAGE TITLE --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Vehicles</h1>
        
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6  pt-2">
        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-2 self-stretch rounded-full bg-sky-600"></div>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold !text-sky-600 leading-none">{{$totalVehicles}}</div>
                <div>
                    <div class="text-base font-bold text-gray-800">Total Vehicles</div>
                    <div class="text-sm text-gray-500">All registered vehicles</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-2 self-stretch rounded-full bg-emerald-600"></div>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold text-emerald-600 leading-none">{{$inUseVehicles}}</div>
                <div>
                    <div class="text-base font-bold text-gray-800">In Use</div>
                    <div class="text-sm text-gray-500">Currently assigned</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-2 self-stretch rounded-full bg-amber-500"></div>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold text-amber-600 leading-none">{{$availableVehicles}}</div>
                <div>
                    <div class="text-base font-bold text-gray-800">Available</div>
                    <div class="text-sm text-gray-500">Currently idle</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH LEFT + ADD BUTTON RIGHT (LIKE YOUR 2ND PIC) --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        {{-- Search group (fixed width like picture) --}}
        <div class="w-full lg:w-[560px]">
            <div class="flex">
                <div class="relative flex-1">

                    <input id="vehicleSearch"
                        type="text"
                        class="w-full rounded-xl rounded-r-none border border-gray-300 bg-white pl-3 pr-3 py-4 text-sm text-gray-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        placeholder="Search"
                    >
                </div>

                <button type="button"
                    id="btnVehicleSearch"
                    class="shrink-0 w-28 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl rounded-l-none px-6"
                >
                    Search
                </button>
            </div>
        </div>

        {{-- Add button (right side) --}}
        <button
            type="button"
            class="w-full lg:w-auto bg-sky-600 hover:bg-sky-500 text-white font-bold px-8 py-3 rounded-xl shadow"
            @click="open = true"
        >
            Add Vehicle
        </button>

    </div>




    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">

        <div class="overflow-x-auto">
            <table id="vehicleTable" class="w-full text-left text-gray-700">
                <thead class="uppercase bg-sky-600 text-white ">
                    <tr>
                        <th class="px-6 py-3">Vehicle Type</th>
                        <th class="px-6 py-3">Plate No.</th>
                        <th class="px-6 py-3">Capacity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y"></tbody>
                
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
    </div>

    {{-- MODAL (your existing) --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="open = false"
        @keydown.escape.window="open = false"
        >
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
            <form @submit.prevent="submit($event.target)">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-bold">Vehicle Type</label>
                    <input type="text" name="vehicle_type" class="w-full border rounded-xl px-3 py-2">
                    <template x-if="errors.vehicle_type">
                        <p class="text-red-500 text-xs" x-text="errors.vehicle_type[0]"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold">Plate no.</label>
                    <input type="text" name="plate_number" class="w-full border rounded-xl px-3 py-2">
                    <template x-if="errors.plate_number">
                        <p class="text-red-500 text-xs" x-text="errors.plate_number[0]"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold">Capacity</label>
                    <input type="number" name="capacity" class="w-full border rounded-xl px-3 py-2">
                    <template x-if="errors.capacity">
                        <p class="text-red-500 text-xs" x-text="errors.capacity[0]"></p>
                    </template>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="bg-gray-200 px-4 py-2 rounded-xl" @click="open = false">
                        Close
                    </button>

                    <button type="submit" :disabled="loading" class="bg-sky-600 text-white px-4 py-2 rounded-xl font-bold">
                        <span x-show="!loading">Save</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
@endsection




