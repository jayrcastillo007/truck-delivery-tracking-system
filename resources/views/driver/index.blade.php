@extends('partials.header')

@section('title', 'Drivers')
@section('page-title', 'Drivers')

@section('content')

<section x-data="driver()" class="mt-4">

    {{-- PAGE TITLE --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Drivers</h1>
        
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6  pt-2">
        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-2 self-stretch rounded-full bg-sky-600"></div>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold !text-sky-600 leading-none">{{$totalDrivers}}</div>
                <div>
                    <div class="text-base font-bold text-gray-800">Total Drivers</div>
                    <div class="text-sm text-gray-500">All registered drivers</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-2 self-stretch rounded-full bg-emerald-600"></div>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold text-emerald-600 leading-none">{{$onDelivery}}</div>
                <div>
                    <div class="text-base font-bold text-gray-800">On Delivery</div>
                    <div class="text-sm text-gray-500">Currently delivering</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-2 self-stretch rounded-full bg-amber-500"></div>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold text-amber-600 leading-none">{{$driverLeave}}</div>
                <div>
                    <div class="text-base font-bold text-gray-800">Restday / Leave</div>
                    <div class="text-sm text-gray-500">Currently unavailable</div>
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

                    <input id="driverSearch"
                        type="text"
                        class="w-full rounded-xl rounded-r-none border border-gray-300 bg-white pl-3 pr-3 py-4 text-sm text-gray-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        placeholder="Search"
                    >
                </div>

                <button type="button"
                    id="btnDriverSearch"
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
            Add Driver
        </button>

    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table id="driverTable" class="w-full text-left text-gray-700">
            <thead class="uppercase bg-sky-600 text-white ">
                <tr>
                    <th class="py-3 px-6">First Name</th>
                    <th class="py-3 px-6">Last Name</th>
                    <th class="py-3 px-6">Address</th>
                    <th class="py-3 px-6">License no.</th>
                    <th class="py-3 px-6">Phone no.</th>
                    <th class="py-3 px-6">Status</th>
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

    <!-- Add Driver Modal -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="open = false"
        @keydown.escape.window="open = false"
        >
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">
                    Add New Driver
                </h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit($event.target)" class="p-6 space-y-6">
                @csrf

                <!-- Account Information -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">
                        Account Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                            <template x-if="errors.email">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.email[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                            <template x-if="errors.password">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.password[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Driver Information -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">
                        Driver Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name"
                                class="w-full border rounded-lg px-3 py-2">
                            <template x-if="errors.first_name">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.first_name[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name"
                                class="w-full border rounded-lg px-3 py-2">
                            <template x-if="errors.last_name">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.last_name[0]"></p>
                            </template>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="address"
                                class="w-full border rounded-lg px-3 py-2">
                            <template x-if="errors.address">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.address[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">License Number</label>
                            <input type="text" name="license_number"
                                class="w-full border rounded-lg px-3 py-2">
                            <template x-if="errors.license_number">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.license_number[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone"
                                class="w-full border rounded-lg px-3 py-2">
                            <template x-if="errors.phone">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.phone[0]"></p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button"
                            class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300"
                            @click="open = false">
                        Cancel
                    </button>

                    <button type="submit"
                            :disabled="loading"
                            class="px-5 py-2 rounded-lg bg-sky-600 text-white hover:bg-sky-700">
                        <span x-show="!loading">Save Driver</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


</section>

@endsection
