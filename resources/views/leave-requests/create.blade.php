<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Formulir Pengajuan Izin / Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                
                <form 
                    method="POST" 
                    action="{{ route('leave-requests.store') }}" 
                    enctype="multipart/form-data"
                    x-data="{ selectedLeaveTypeName: '{{ old('leave_type_id') ? App\Models\LeaveType::find(old('leave_type_id'))->nama_cuti : '' }}' }"
                >
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Jenis Pengajuan --}}
                        <div class="md:col-span-2">
                            <x-input-label for="leave_type_id" :value="__('Jenis Pengajuan')" />
                            <x-select-input 
                                name="leave_type_id" 
                                id="leave_type_id" 
                                class="block mt-1 w-full" 
                                required
                                x-on:change="selectedLeaveTypeName = $event.target.options[$event.target.selectedIndex].text.trim()"
                            >
                                <option value="">-- Pilih Jenis Cuti --</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('leave_type_id') == $type->id)>{{ $type->nama_cuti }}</option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Tanggal Mulai & Selesai --}}
                        <div>
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>
                        
                        {{-- Dokumen Pendukung (muncul kondisional) --}}
                        <div x-show="selectedLeaveTypeName === 'Izin Sakit'" x-transition class="md:col-span-2">
                            <x-input-label for="dokumen_pendukung" :value="__('Dokumen Pendukung (Surat Sakit, dll.)')" />
                            <input 
                                id="dokumen_pendukung" 
                                class="block w-full text-sm rounded-md shadow-sm cursor-pointer
                                    border border-neutral-300 bg-neutral-50 text-neutral-600
                                    dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400
                                    focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand
                                    file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                    file:text-sm file:font-semibold file:bg-brand file:text-white
                                    hover:file:bg-brand-dark dark:hover:file:bg-brand-dark" 
                                type="file" 
                                name="dokumen_pendukung"
                                x-bind:required="selectedLeaveTypeName === 'Izin Sakit'"
                            />
                            <x-input-error :messages="$errors->get('dokumen_pendukung')" class="mt-2" />
                        </div>
                        
                        {{-- Alasan (muncul kondisional) --}}
                        <div x-show="selectedLeaveTypeName !== 'Izin Sakit' && selectedLeaveTypeName !== ''" x-transition class="md:col-span-2">
                            <x-input-label for="reason" :value="__('Alasan')" />
                            <x-textarea-input 
                                name="reason" 
                                id="reason" 
                                rows="4" 
                                class="block mt-1 w-full" 
                                x-bind:required="selectedLeaveTypeName !== 'Izin Sakit' && selectedLeaveTypeName !== ''"
                            >{{ old('reason') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <a href="{{ route('dashboard') }}">
                            <x-secondary-button type="button">{{ __('Batal') }}</x-secondary-button>
                        </a>
                        <x-primary-button>
                            {{ __('Kirim Pengajuan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>