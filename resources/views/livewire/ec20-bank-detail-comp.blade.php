<div>
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            {{ $bank_id ? 'Edit Bank' : 'Create Bank' }}
        </h2>

        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Bank Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                    <input type="text" id="name" wire:model.defer="name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Bank Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <input type="text" id="description" wire:model.defer="description"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="my-6">

            <h3 class="text-md font-medium text-gray-900 mb-2">Bank Details</h3>

            @foreach($details as $index => $detail)
                <div class="grid grid-cols-12 gap-4 items-center mb-4 p-3 border rounded-md" wire:key="detail-{{ $index }}">
                    <!-- Detail Name -->
                    <div class="col-span-4">
                        <label for="detail_name_{{ $index }}" class="block text-sm font-medium text-gray-700">Detail Name</label>
                        <input type="text" id="detail_name_{{ $index }}" wire:model.defer="details.{{ $index }}.name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        @error('details.'.$index.'.name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Detail Description -->
                    <div class="col-span-4">
                        <label for="detail_description_{{ $index }}" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" id="detail_description_{{ $index }}" wire:model.defer="details.{{ $index }}.description"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    
                    <!-- Detail Status -->
                    <div class="col-span-3">
                        <label for="detail_status_{{ $index }}" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="detail_status_{{ $index }}" wire:model.defer="details.{{ $index }}.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                            <option value="running">Running</option>
                            <option value="completed">Completed</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="suspended">Suspended</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <!-- Remove Button -->
                    <div class="col-span-1">
                        <button type="button" wire:click="removeDetail({{ $index }})"
                                class="mt-5 inline-flex items-center justify-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-between items-center mt-4">
                <button type="button" wire:click="addDetail"
                        class="inline-flex items-center px-4 py-2 border border-dashed border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Add Detail
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Bank
                </button>
            </div>
        </form>
    </div>
</div>
