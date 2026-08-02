<nav x-data="{ open: false }" class="border-b border-gray-100 bg-blue-950 dark:bg-gray-800 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-full px-4 mx-3 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <div class="flex items-center shrink-0 active:scale-90">
                    <a href="{{ route('home') }}" class="route_pop_up">
                        <x-application-logo class="block w-auto text-white fill-current h-9 dark:text-gray-200" />
                    </a>
                </div>
            </div>

            <!-- Center: Navigation Links -->
            <div class="hidden md:flex md:items-center md:justify-center md:flex-1">
            </div>

            <!-- Right: Auth / Login + Hamburger -->
            <div class="flex items-center">
                @auth
                    <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                        <!-- Icon Notifikasi Bell (Revisi Pak Dahliar) -->
                        <x-dropdown align="right" width="80">
                            <x-slot name="trigger">
                                <button class="relative p-2 text-gray-300 hover:text-white focus:outline-none transition duration-150 active:scale-95">
                                    <!-- Heroicon Bell -->
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    
                                    <!-- Badge Indicator (Kondisional: jika ada unread notification) -->
                                    {{-- @if (Auth::user()->unreadNotifications->count() > 0) --}}
                                        <span class="absolute top-1 right-1 flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                        </span>
                                    {{-- @endif --}}
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Notifikasi DUPAK') }}
                                </div>

                                <div class="max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                                    <!-- Render list notifikasi kamu di sini -->
                                    {{-- @forelse (Auth::user()->notifications as $notification)
                                        <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ $notification->data['message'] ?? '' }}</p>
                                            <span class="text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                        </a>
                                    @empty --}}
                                        <div class="px-4 py-3 text-xs text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada notifikasi baru
                                        </div>
                                    {{-- @endforelse --}}
                                </div>
                            </x-slot>
                        </x-dropdown>

                        <!-- User Profile Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex active:scale-95 items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                    <div>{{ Auth::user()->nama_lengkap ?? "Dev" }}</div>

                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if (Auth::user()->is_admin ?? false)
                                    <x-dropdown-link :href="route('admin.dashboard')">
                                        {{ __('Admin Panel') }}
                                    </x-dropdown-link>
                                @endif

                                <x-dropdown-link :href="route('profile.personal-info', ['idUser' => Auth::user()->id])">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 text-sm leading-5 text-left text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 focus:outline-none">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden text-white sm:flex sm:items-center sm:ms-6">
                        <x-nav-link :href="route('login')">
                            {{ __('Login') }}
                        </x-nav-link>
                    </div>
                @endauth

                <!-- Hamburger (Mobile) -->
                <div class="flex items-center -me-2 md:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="text-base font-medium text-white">{{ Auth::user()->nama_lengkap }}</div>
                    <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email_institusi }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if (Auth::user()->is_admin ?? false)
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            <p class="text-white clear-p">{{ __('Admin Panel') }}</p>
                        </x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="route('profile.personal-info', ['idUser' => Auth::user()->id])">
                        <p class="text-white clear-p">{{ __('Profile') }}</p>
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}" class="block w-full">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-base font-medium text-left text-white transition duration-150 ease-in-out hover:bg-blue-900">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 text-white border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>