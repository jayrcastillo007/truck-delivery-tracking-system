@extends('partials.header')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')

<section x-data="user()" class="mt-4">

    {{-- PAGE TITLE --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Users</h1>
        
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        {{-- Search group (fixed width like picture) --}}
        <div class="w-full lg:w-[560px]">
            <div class="flex">
                <div class="relative flex-1">

                    <input id="userSearch"
                        type="text"
                        class="w-full rounded-xl rounded-r-none border border-gray-300 bg-white pl-3 pr-3 py-4 text-sm text-gray-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        placeholder="Search"
                    >
                </div>

                <button type="button"
                    id="btnUserSearch"
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
            Add User
        </button>

    </div>
        

    <div class="overflow-x-auto bg-white rounded shadow">
        <table id="userTable" class="w-full text-left text-gray-700">
            <thead class="uppercase bg-sky-600 text-white ">
                <tr>
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-6">Email / Username</th>
                    <th class="py-3 px-6">Role</th>
                    <th class="py-3 px-6">Action</th>
                </tr>
            </thead>

            <tbody>
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

    <!-- Add User Modal -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="open = false"
        @keydown.escape.window="open = false"
    >
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <form @submit.prevent="submit($event.target)">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-bold">Name</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2">
                    <template x-if="errors.name">
                        <p class="text-red-500 text-xs" x-text="errors.name[0]"></p>
                    </template>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-bold">Email</label>
                    <input type="email" name="email" class="w-full border rounded px-3 py-2">
                    <template x-if="errors.email">
                        <p class="text-red-500 text-xs" x-text="errors.email[0]"></p>
                    </template>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-sm font-bold">Password</label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="block text-sm font-bold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button"
                            class="bg-gray-300 px-4 py-2 rounded"
                            @click="open = false">
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
