<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            {{ __('Formulir Pengajuan Izin / Cuti') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                
                <form method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="leave_type_id" :value="__('Jenis Pengajuan')" />
                        <select name="leave_type_id" id="leave_type_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" selected disabled>-- Pilih Jenis Cuti --</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->nama_cuti }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="document-upload" class="mt-4" style="display: none;">
                        <x-input-label for="dokumen_pendukung" :value="__('Dokumen Pendukung (Surat Sakit, dll.)')" />
                        <input id="dokumen_pendukung" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm p-2" type="file" name="dokumen_pendukung" />
                        <x-input-error :messages="$errors->get('dokumen_pendukung')" class="mt-2" />
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
       
                    <div id="reason-field" class="mt-4">
                        <x-input-label for="reason" :value="__('Alasan')" />
                        <textarea name="reason" id="reason" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Kirim Pengajuan') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('leave_type_id').addEventListener('change', function () {
            const documentUploadDiv = document.getElementById('document-upload');
            const documentInput = document.getElementById('dokumen_pendukung');
            const reasonDiv = document.getElementById('reason-field');
            const reasonInput = document.getElementById('reason');
            
            const selectedOption = this.options[this.selectedIndex];
            const isSickLeave = selectedOption.text.trim() === 'Izin Sakit';

            if (isSickLeave) {
                documentUploadDiv.style.display = 'block';
                documentInput.setAttribute('required', 'required');
                
                reasonDiv.style.display = 'none';
                reasonInput.removeAttribute('required');
            } else {
                documentUploadDiv.style.display = 'none';
                documentInput.removeAttribute('required');

                reasonDiv.style.display = 'block';
                reasonInput.setAttribute('required', 'required');
            }
        });
    </script>
</x-app-layout>