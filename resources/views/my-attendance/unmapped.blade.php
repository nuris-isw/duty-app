<!-- Ini akan muncul jika pegawai login, tapi Admin belum melakukan "Mapping User" untuk pegawai -->
 <x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-neutral-800 leading-tight">
            {{ __('Absensi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded shadow">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong class="font-bold">Akun Anda belum terhubung.</strong><br>
                            Data sidik jari Anda belum dipasangkan dengan akun ini. Silakan hubungi Admin/HRD untuk melakukan konfigurasi Mapping Absensi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>