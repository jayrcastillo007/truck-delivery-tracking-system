@php
    $nav = [
        [
            'label' => 'Dashboard',
            'href'  => '/admin/dashboard',   // change if your dashboard route is different
            'match' => 'admin/dashboard*',
            'icon'  => 'home',
        ],
        [
            'label' => 'Vehicles',
            'href'  => '/vehicle',
            'match' => 'vehicle*',
            'icon'  => 'truck',
        ],
        [
            'label' => 'Drivers',
            'href'  => '/driver',
            'match' => 'driver*',
            'icon'  => 'user',
        ],
        [
            'label' => 'Transactions',
            'href'  => '/transaction',
            'match' => 'transaction*',
            'icon'  => 'clipboard',
        ],
        [
            'label' => 'Users',
            'href'  => '/users',             // change if your users route is different
            'match' => 'users*',
            'icon'  => 'users',
        ],
    ];

    $isActive = fn($match) => request()->is($match);
@endphp

<aside class="w-64 h-screen bg-slate-900 text-slate-100 fixed left-0 top-0 flex flex-col border-r border-white/10">

    {{-- Brand --}}
    <div class="h-16 px-5 flex items-center border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div class="leading-tight">
                <p class="font-semibold">Tracking System</p>
                <p class="text-xs text-slate-400">Admin Panel</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4">
        <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">
            Main Menu
        </p>

        <ul class="space-y-1">
            @foreach($nav as $item)
                @php $active = $isActive($item['match']); @endphp

                <li>
                    <a href="{{ $item['href'] }}"
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                       {{ $active ? 'bg-white/10 text-white shadow-sm' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}">
                        
                        {{-- Icon container --}}
                        <span class="w-9 h-9 rounded-xl flex items-center justify-center transition
                            {{ $active ? 'bg-white/10' : 'bg-white/0 group-hover:bg-white/5' }}">
                            
                            @if($item['icon'] === 'home')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 12l9-9 9 9M9 21V9h6v12" />
                                </svg>
                            @elseif($item['icon'] === 'truck')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 17a2 2 0 104 0m-4 0a2 2 0 114 0M3 17V6a1 1 0 011-1h10a1 1 0 011 1v11m0 0h4l2-3V9h-6" />
                                </svg>
                            @elseif($item['icon'] === 'user')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M6 20a6 6 0 0112 0" />
                                </svg>
                            @elseif($item['icon'] === 'clipboard')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 5h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 3h6v4H9V3z" />
                                </svg>
                            @elseif($item['icon'] === 'users')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                </svg>
                            @endif
                        </span>

                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t border-white/10 text-xs text-slate-400">
        <p>© {{ date('Y') }} Thesis Project</p>
    </div>
</aside>
