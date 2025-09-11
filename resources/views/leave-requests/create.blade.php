<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Formulir Pengajuan Izin / Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('leave-requests.store') }}">
                        @csrf

                        <div class="mt-4">
                            <x-input-label for="leave_type" :value="__('Jenis Pengajuan')" />
                            <select name="leave_type" id="leave_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="reason" :value="__('Alasan')" />
                            <textarea name="reason" id="reason" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Kirim Pengajuan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>