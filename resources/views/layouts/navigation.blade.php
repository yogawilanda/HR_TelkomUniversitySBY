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
                {{-- <div class="space-x-8 text-white md:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Beranda
                    </x-nav-link>

                    <x-nav-link :href="url('/presensi')" :active="request()->is('presensi*')">
                        PDK
                    </x-nav-link>

                    <x-nav-link :href="url('/manage')" :active="request()->is('pencatatan*')">
                        Pengelolaan Data
                    </x-nav-link>

                    
                    <x-nav-link :href="url('/dupak/dashboard')" :active="request()->is('bantuan*')">
                        Dupak
                    </x-nav-link>
                    
                    @auth
                    <!-- <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link> -->
                    @endauth
                </div> --}}
            </div>

            <!-- Right: Auth / Login + Hamburger -->
            <div class="flex items-center">
                @auth
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex active:scale-95 items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                    <div>{{ Auth::user()->nama_lengkap ?? "Dev" }}</div>


                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if (Auth::user()->is_admin)
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
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="w-full px-4 py-2 text-sm leading-5 text-left text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
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

                <!-- Hamburger -->
                <div class="flex items-center -me-2 md:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        {{-- <div class="pt-2 pb-3 space-y-1 text-white">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                <p class="text-white ">Berandaaaa</p>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/presensi')" :active="request()->is('presensi*')">
                <p class="text-white clear-p">Presensi</p>

            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/manage')" :active="request()->is('pencatatan*')">
                <p class="text-white clear-p">Pengelolaan Data</p>

            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/bantuan')" :active="request()->is('bantuan*')">
                <p class="text-white clear-p">Bantuan</p>

            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <p class="text-white clear-p">{{ __('Dashboard') }}</p>

                </x-responsive-nav-link>
            @endauth
        </div> --}}

        <!-- Responsive Settings Options -->
        @auth
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->nama_lengkap }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email_institusi }}</div>
                </div>                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.personal-info', ['idUser' => Auth::user()->id])">
                        {{-- {{ __('Profile') }} --}}
                        <p class="text-white clear-p">{{ __('Profile') }}</p>

                    </x-responsive-nav-link>
                    <div class="mt-3 space-y-1">
                        @if (Auth::user()->is_admin)
                            <x-responsive-nav-link :href="route('admin.dashboard')">
                                <p class="text-white clear-p">{{ __('Admin Panel') }}</p>
                            </x-responsive-nav-link>
                        @endif

                        <x-responsive-nav-link :href="route('profile.personal-info', ['idUser' => Auth::user()->id])">
                            {{-- {{ __('Profile') }} --}}
                            <p class="text-white clear-p">{{ __('Profile') }}</p>

                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}" class="block w-full">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-base font-medium text-white transition duration-150 ease-in-out hover:bg-blue-900">
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
            </div>
        @endauth
</nav>

