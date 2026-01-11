<nav x-data="{ open: false }" class="bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- 1. MENU UMUM (Semua User) --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('my-attendance.index')" :active="request()->routeIs('my-attendance.index')">
                        {{ __('Absensi Saya') }}
                    </x-nav-link>

                    {{-- 2. MENU PENGATURAN / MASTER DATA --}}
                    {{-- Hanya untuk Superadmin & SysAdmin --}}
                    @can('manage-master-data')
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-neutral-600 dark:text-neutral-300 bg-white dark:bg-neutral-900 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/60 dark:focus:ring-indigo-500/40 transition ease-in-out duration-150">
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
                                <x-dropdown-link :href="route('admin.holidays.index')" :active="request()->routeIs('admin.holidays.index')">
                                    {{ __('Setting Hari Libur') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endcan

                    {{-- 3. MENU LAPORAN --}}
                    {{-- Untuk Superadmin, SysAdmin, DAN Unit Admin --}}
                    @can('access-admin-panel')
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-neutral-600 dark:text-neutral-300 bg-white dark:bg-neutral-900 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/60 dark:focus:ring-indigo-500/40 transition ease-in-out duration-150">
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
                                    {{ __('Laporan Absensi (Harian)') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.rekap-absensi.index')" :active="request()->routeIs('admin.rekap-absensi.*')">
                                    {{ __('Rekap Matriks (Bulanan)') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endcan

                    {{-- 4. MENU KALENDER (Semua User) --}}
                    {{-- Controller sudah memfilter data berdasarkan unit kerja --}}
                    <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.index')">
                        {{ __('Kalender Cuti') }}
                    </x-nav-link>

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-neutral-500 dark:text-neutral-400 bg-white dark:bg-neutral-800 hover:text-neutral-700 dark:hover:text-neutral-300 focus:outline-none transition ease-in-out duration-150">
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

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-neutral-400 dark:text-neutral-500 hover:text-neutral-500 dark:hover:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-900 focus:outline-none focus:bg-neutral-100 dark:focus:bg-neutral-900 focus:text-neutral-500 dark:focus:text-neutral-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('my-attendance.index')" :active="request()->routeIs('my-attendance.index')">
                {{ __('Absensi Saya') }}
            </x-responsive-nav-link>

            {{-- Mobile: Pengaturan --}}
            @can('manage-master-data')
            <div class="pt-2 pb-1 border-t border-neutral-200 dark:border-neutral-700 mt-2">
                <div class="px-4 py-2 text-xs font-semibold text-neutral-400 uppercase">Pengaturan</div>
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
                <x-responsive-nav-link :href="route('admin.holidays.index')" :active="request()->routeIs('admin.holidays.index')">
                    {{ __('Setting Hari Libur') }}
                </x-responsive-nav-link>
            </div>
            @endcan

            {{-- Mobile: Laporan --}}
            @can('access-admin-panel')
            <div class="pt-2 pb-1 border-t border-neutral-200 dark:border-neutral-700 mt-2">
                <div class="px-4 py-2 text-xs font-semibold text-neutral-400 uppercase">Laporan</div>
                <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                    {{ __('Laporan Cuti') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.attendance.report')" :active="request()->routeIs('admin.attendance.report.*')">
                    {{ __('Laporan Absensi (Harian)') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.rekap-absensi.index')" :active="request()->routeIs('admin.rekap-absensi.*')">
                    {{ __('Rekap Matriks (Bulanan)') }}
                </x-responsive-nav-link>
            </div>
            @endcan

            <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.index')">
                {{ __('Kalender Cuti') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-neutral-200 dark:border-neutral-800">
            <div class="px-4">
                <div class="font-medium text-base text-neutral-800 dark:text-neutral-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-neutral-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>