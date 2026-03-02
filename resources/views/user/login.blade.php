<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen">

    <!-- Background -->
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('img/lastBackground.jpg') }}" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/55"></div>
    </div>

    <!-- Center -->
    <main class="min-h-screen flex items-center justify-center px-4">
        <section class="w-full max-w-md rounded-2xl bg-white/15 backdrop-blur-xl shadow-2xl border border-white/15 p-8">

            <!-- Title -->
            <div class="text-center mb-7 text-white">
                <h1 class="text-3xl font-extrabold tracking-wide">Tracking System</h1>
                <p class="text-white/80 text-sm mt-2">Sign in to continue</p>
            </div>

            <form action="/login/process" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-white mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        class="w-full rounded-xl bg-white text-gray-900 placeholder:text-gray-400
                               border border-gray-200 px-4 py-2.5
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ show:false }">
                    <label class="block text-sm font-semibold text-white mb-1">Password</label>

                    <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            placeholder="Enter your password"
                            class="w-full rounded-xl bg-white text-gray-900 placeholder:text-gray-400
                                   border border-gray-200 px-4 py-2.5 pr-12
                                   focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                            required
                        >

                        <!-- Eye button INSIDE the input -->
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-gray-800"
                            aria-label="Toggle password visibility"
                        >
                            <!-- Eye -->
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5
                                         c4.477 0 8.268 2.943 9.542 7
                                         -1.274 4.057-5.065 7-9.542 7
                                         -4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>

                            <!-- Eye slash -->
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19
                                         c-4.478 0-8.268-2.943-9.543-7
                                         a9.956 9.956 0 012.293-3.95
                                         m3.014-2.482A9.956 9.956 0 0112 5
                                         c4.478 0 8.268 2.943 9.543 7
                                         a9.957 9.957 0 01-4.132 5.411M15 12
                                         a3 3 0 01-3 3m0 0a3 3 0 01-3-3m3 3l6 6M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5
                           shadow-lg shadow-sky-600/30 transition"
                >
                    Login
                </button>
            </form>

            <!-- Forgot password only -->
            <div class="mt-5 text-center">
                <div x-data="{ fpOpen:false }">
                    <button
                        type="button"
                        class="text-sm text-white/90 hover:text-white underline"
                        @click="fpOpen = true"
                    >
                        Forgot password?
                    </button>

                    <!-- Modal -->
                    <div
                        x-show="fpOpen"
                        x-cloak
                        class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4"
                        @click.self="fpOpen = false"
                        @keydown.escape.window="fpOpen = false"
                    >
                        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Reset password</h2>
                                    <p class="text-sm text-gray-500">Enter your email to receive a reset link.</p>
                                </div>
                                <button type="button" class="text-gray-500 hover:text-gray-800" @click="fpOpen = false">✕</button>
                            </div>

                            @if (session('status'))
                                <div class="mb-3 rounded-lg bg-green-50 p-3 text-sm text-green-700">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form action="{{ route('password.email') }}" method="POST" class="space-y-3">
                                @csrf

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5
                                           focus:outline-none focus:ring-2 focus:ring-sky-500 text-gray-900"
                                    placeholder="you@example.com"
                                    required
                                >

                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="flex justify-end gap-2 pt-2">
                                    <button type="button"
                                            class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50"
                                            @click="fpOpen = false">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                            class="px-4 py-2 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">
                                        Send link
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>

</body>
</html>