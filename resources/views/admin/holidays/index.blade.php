<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Pengaturan Hari Libur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- FORM TAMBAH LIBUR --}}
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-neutral-800 p-6 rounded-2xl shadow-sm border border-neutral-100 dark:border-neutral-700">
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 mb-4">Tambah Hari Libur</h3>
                        
                        <form action="{{ route('admin.holidays.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <x-input-label for="title" :value="__('Nama Libur')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required placeholder="Contoh: HUT RI ke-80" />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="date" :value="__('Tanggal')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Keterangan (Opsional)')" />
                                <textarea id="description" name="description" class="block mt-1 w-full border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3"></textarea>
                            </div>

                            <x-primary-button class="w-full justify-center">
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>

                {{-- DAFTAR LIST LIBUR --}}
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-neutral-800 p-6 rounded-2xl shadow-sm border border-neutral-100 dark:border-neutral-700">
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 mb-4">Daftar Hari Libur</h3>

                        @if(session('success'))
                            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                                <thead class="bg-neutral-50 dark:bg-neutral-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-neutral-500 uppercase">Tanggal</th>
                                        <th class="px-4 py-3 text-left font-medium text-neutral-500 uppercase">Nama Libur</th>
                                        <th class="px-4 py-3 text-center font-medium text-neutral-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                    @forelse($holidays as $holiday)
                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/30">
                                            <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300 font-mono">
                                                {{ $holiday->date->format('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-neutral-800 dark:text-neutral-200 font-semibold">
                                                {{ $holiday->title }}
                                                @if($holiday->description)
                                                    <div class="text-xs text-neutral-500 font-normal">{{Str::limit($holiday->description, 30)}}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <form action="{{ route('admin.holidays.destroy', $holiday->id) }}" method="POST" onsubmit="return confirm('Hapus libur ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold uppercase tracking-wider">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-neutral-400 italic">
                                                Belum ada data hari libur.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $holidays->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>