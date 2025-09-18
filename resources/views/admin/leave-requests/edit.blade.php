<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            Edit Pengajuan Cuti: {{ $leaveRequest->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                <form method="POST" action="{{ route('admin.leave-requests.update', $leaveRequest) }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal Mulai --}}
                        <div>
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $leaveRequest->start_date)" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div>
                            <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $leaveRequest->end_date)" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>
                        
                        {{-- Status --}}
                        <div class="md:col-span-2">
                            <x-input-label for="status" :value="__('Status Pengajuan')" />
                            <x-select-input id="status" name="status" class="block mt-1 w-full">
                                <option value="pending" @selected(old('status', $leaveRequest->status) == 'pending')>Pending</option>
                                <option value="approved" @selected(old('status', $leaveRequest->status) == 'approved')>Approved</option>
                                <option value="rejected" @selected(old('status', $leaveRequest->status) == 'rejected')>Rejected</option>
                            </x-select-input>
                        </div>

                        {{-- Alasan --}}
                        <div class="md:col-span-2">
                            <x-input-label for="reason" :value="__('Alasan')" />
                            <x-textarea-input name="reason" id="reason" rows="4" class="block mt-1 w-full">{{ old('reason', $leaveRequest->reason) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 gap-4">
                        <a href="{{ route('admin.reports.index') }}">
                            <x-secondary-button type="button">{{ __('Batal') }}</x-secondary-button>
                        </a>
                        <x-primary-button>
                            {{ __('Update Pengajuan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>