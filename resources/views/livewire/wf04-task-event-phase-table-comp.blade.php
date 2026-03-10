<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Task Event Phase Tables</h3>
            <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">Create Table Operation</button>
        </div>
        <div class="p-4">
            <div class="flex justify-between mb-4 gap-2">
                <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Model Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Description</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">School ID</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Remarks</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Created At</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($tableOperations as $operation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($tableOperations->currentPage() - 1) * $tableOperations->perPage() }}</td>
                                <td class="px-3 py-2 font-medium">{{ $operation->name }}</td>
                                <td class="px-3 py-2">{{ $operation->model_name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $operation->description ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $operation->school_id ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($operation->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $operation->remarks ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $operation->created_at->format('d-m-Y') }}</td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $operation->id }})">Edit</button>
                                    @if ($confirmingDelete === $operation->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $operation->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600" wire:click="confirmDelete({{ $operation->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-gray-500">No table operations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tableOperations->links() }}
            </div>
        </div>
    </div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">{{ $table_operation_id ? 'Edit' : 'Create' }} Table Operation</h3>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Enter operation name">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model Name <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="model_name">
                            <option value="">Select Model</option>
                            @foreach($availableModels as $model)
                                <option value="{{ $model }}">{{ $model }}</option>
                            @endforeach
                        </select>
                        @error('model_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" rows="3" placeholder="Enter description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">School ID</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="school_id" placeholder="Enter school ID">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" rows="2" placeholder="Enter remarks"></textarea>
                    </div>
                    <div class="mb-3 flex items-center">
                        <input type="checkbox" class="mr-2" wire:model="is_active" id="is_active">
                        <label for="is_active" class="text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal">Cancel</button>
                        <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store">{{ $table_operation_id ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
