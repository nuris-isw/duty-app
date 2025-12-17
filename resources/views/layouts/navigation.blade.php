<nav x-data="{ open: false }" 
     class="bg-white dark:bg-neutral-900 
            border-b border-neutral-200 dark:border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Logo + Menu Utama -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto text-brand" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('my-attendance.index')" :active="request()->routeIs('my-attendance.index')">
                        {{ __('Absensi') }}
                    </x-nav-link>
                    
                    @can('is-admin')
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 
                                               border border-transparent 
                                               text-sm leading-4 font-medium rounded-md 
                                               text-neutral-600 dark:text-neutral-300 
                                               bg-white dark:bg-neutral-900 
                                               hover:text-brand dark:hover:text-brand-light 
                                               hover:bg-neutral-50 dark:hover:bg-neutral-800 
                                               focus:outline-none 
                                               focus:ring-2 focus:ring-brand/60 dark:focus:ring-brand/40 
                                               transition ease-in-out duration-150">
                                    <div>Pengaturan</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    {{ __('Manajemen User') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.unit-kerja.index')" :active="request()->routeIs('admin.unit-kerja.*')">
                                    {{ __('Manajemen Unit Kerja') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.jabatan.index')" :active="request()->routeIs('admin.jabatan.*')">
                                    {{ __('Manajemen Jabatan') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.leave-types.index')" :active="request()->routeIs('admin.leave-types.*')">
                                    {{ __('Manajemen Jenis Cuti') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.attendance.mapping')" :active="request()->routeIs('admin.attendance.mapping.*')">
                                    {{ __('Mapping Absensi') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 
                                               border border-transparent 
                                               text-sm leading-4 font-medium rounded-md 
                                               text-neutral-600 dark:text-neutral-300 
                                               bg-white dark:bg-neutral-900 
                                               hover:text-brand dark:hover:text-brand-light 
                                               hover:bg-neutral-50 dark:hover:bg-neutral-800 
                                               focus:outline-none 
                                               focus:ring-2 focus:ring-brand/60 dark:focus:ring-brand/40 
                                               transition ease-in-out duration-150">
                                    <div>Laporan</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                                    {{ __('Laporan Cuti') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.attendance.report')" :active="request()->routeIs('admin.attendance.report.*')">
                                    {{ __('Laporan Absensi') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.index')">
                        {{ __('Kalender Cuti') }}
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Dropdown Profil -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 
                                       border border-transparent 
                                       text-sm leading-4 font-medium rounded-md 
                                       text-neutral-600 dark:text-neutral-300 
                                       bg-white dark:bg-neutral-900 
                                       hover:text-brand dark:hover:text-brand-light 
                                       hover:bg-neutral-50 dark:hover:bg-neutral-800 
                                       focus:outline-none 
                                       focus:ring-2 focus:ring-brand/60 dark:focus:ring-brand/40 
                                       transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Menu (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" 
                        class="inline-flex items-center justify-center p-2 rounded-md 
                               text-neutral-500 dark:text-neutral-400 
                               hover:text-neutral-700 dark:hover:text-neutral-200 
                               hover:bg-neutral-100 dark:hover:bg-neutral-800 
                               focus:outline-none 
                               focus:ring-2 focus:ring-brand/60 dark:focus:ring-brand/40 
                               transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" 
                              class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" 
                              class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('my-attendance.index')" :active="request()->routeIs('my-attendance.index')">
                {{ __('Absensi') }}
            </x-responsive-nav-link>

            @can('is-admin')
                <div class="pt-2 pb-1 border-t border-neutral-200 dark:border-neutral-700">
                    <div class="px-4 mt-2 mb-1">
                        <div class="font-medium text-base text-neutral-800 dark:text-neutral-200">Pengaturan</div>
                    </div>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Manajemen User') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.unit-kerja.index')" :active="request()->routeIs('admin.unit-kerja.*')">
                        {{ __('Manajemen Unit Kerja') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.jabatan.index')" :active="request()->routeIs('admin.jabatan.*')">
                        {{ __('Manajemen Jabatan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.leave-types.index')" :active="request()->routeIs('admin.leave-types.*')">
                        {{ __('Manajemen Jenis Cuti') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.attendance.mapping')" :active="request()->routeIs('admin.attendance.mapping.*')">
                        {{ __('Mapping Absensi') }}
                    </x-responsive-nav-link>
                </div>
                <div class="pt-2 pb-1 border-t border-neutral-200 dark:border-neutral-700">
                     <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                        {{ __('Laporan Cuti') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.attendance.report')" :active="request()->routeIs('admin.attendance.report.*')">
                        {{ __('Laporan Absensi') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.index')">
                        {{ __('Kalender Cuti') }}
                    </x-responsive-nav-link>
                </div>
            @endcan
        </div>

        <div class="pt-4 pb-1 border-t border-neutral-200 dark:border-neutral-700">
            <div class="px-4">
                <div class="font-medium text-base text-neutral-800 dark:text-neutral-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-neutral-500 dark:text-neutral-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
