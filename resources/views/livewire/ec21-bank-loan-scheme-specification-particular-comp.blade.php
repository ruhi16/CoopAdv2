<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Bank Loan Schema Particulars</h3>
            <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">+ Create Particular</button>
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
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Description</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Optional</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Finalized</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($particulars as $particular)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($particulars->currentPage() - 1) * $particulars->perPage() }}</td>
                                <td class="px-3 py-2">{{ $particular->name }}</td>
                                <td class="px-3 py-2">{{ $particular->description ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($particular->is_optional)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($particular->is_finalized)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @switch($particular->status)
                                        @case('published')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Published</span>
                                            @break
                                        @case('archived')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Archived</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Draft</span>
                                    @endswitch
                                </td>
                                <td class="px-3 py-2">
                                    @if($particular->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $particular->id }})">Edit</button>
                                    @if ($confirmingDelete === $particular->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $particular->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $particular->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">No particulars found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $particulars->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="font-semibold text-gray-700">{{ $particular_id ? 'Edit Particular' : 'Create Particular' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Enter name">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" placeholder="Enter description" rows="2"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_optional" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_optional">
                                <label for="is_optional" class="ml-2 text-sm text-gray-700">Optional</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="is_finalized" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_finalized">
                                <label for="is_finalized" class="ml-2 text-sm text-gray-700">Finalized</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="status">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-5">
                                <input type="checkbox" id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_active">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Enter remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $particular_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
