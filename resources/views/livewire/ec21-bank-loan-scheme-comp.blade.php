<div>
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            {{ $scheme_id ? 'Edit Bank Loan Scheme' : 'Create Bank Loan Scheme' }}
        </h2>

        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save">
            {{-- Main Scheme Details --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Scheme Name</label>
                    <input type="text" id="name" wire:model.defer="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="bank_id" class="block text-sm font-medium text-gray-700">Bank</label>
                    {{-- This should ideally be a searchable dropdown component --}}
                    <input type="number" id="bank_id" wire:model.defer="bank_id" placeholder="Enter Bank ID" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('bank_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="effected_on" class="block text-sm font-medium text-gray-700">Effected On</label>
                    <input type="date" id="effected_on" wire:model.defer="effected_on" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('effected_on') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-3">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" wire:model.defer="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"></textarea>
                    @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="my-6">

            {{-- Specifications Section --}}
            <h3 class="text-md font-medium text-gray-900 mb-2">Scheme Specifications</h3>
            
            @foreach($specifications as $index => $spec)
                <div class="grid grid-cols-12 gap-x-4 gap-y-2 mb-4 p-3 border rounded-md items-start" wire:key="spec-{{ $index }}">
                    <div class="col-span-12">
                        <input type="text" wire:model.defer="specifications.{{ $index }}.name" placeholder="Specification Name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        @error('specifications.'.$index.'.name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-4">
                        <label class="block text-sm font-medium text-gray-700">Particular</label>
                        <select wire:model.defer="specifications.{{ $index }}.bank_loan_schema_particular_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                            @foreach($allParticulars as $particular)
                                <option value="{{ $particular->id }}">{{ $particular->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Value</label>
                        <input type="number" step="0.01" wire:model.defer="specifications.{{ $index }}.bank_loan_schema_particular_value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        @error('specifications.'.$index.'.bank_loan_schema_particular_value') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Effected On</label>
                        <input type="date" wire:model.defer="specifications.{{ $index }}.effected_on" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div class="col-span-3 flex space-x-4 items-center mt-6">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.defer="specifications.{{ $index }}.is_percent_on_current_balance" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-600">% on Balance</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.defer="specifications.{{ $index }}.is_regular" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-600">Is Regular</span>
                        </label>
                    </div>
                    <div class="col-span-1 flex justify-end items-center mt-5">
                        <button type="button" wire:click="removeSpecification({{ $index }})"
                                class="inline-flex items-center justify-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach

            {{-- Action Buttons --}}
            <div class="flex justify-between items-center mt-6">
                <button type="button" wire:click="addSpecification"
                        class="inline-flex items-center px-4 py-2 border border-dashed border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Add Specification
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Save Scheme
                </button>
            </div>
        </form>
    </div>
</div>
