<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Task Definition Details</h3>
            <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">Create Detail</button>
        </div>
        <div class="p-4">
            <div class="flex justify-between mb-4 gap-2">
                <div class="flex gap-2">
                    <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="task_defination_filter">
                        <option value="">All Definitions</option>
                        @foreach($taskDefinations as $def)
                            <option value="{{ $def->id }}">{{ $def->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Definition</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Seq No</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Phase</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Table</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Operation</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($details as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($details->currentPage() - 1) * $details->perPage() }}</td>
                                <td class="px-3 py-2 font-medium">{{ $detail->name }}</td>
                                <td class="px-3 py-2">{{ $detail->taskDefination->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $detail->task_event_sequence_no }}</td>
                                <td class="px-3 py-2">{{ $detail->taskEventPhase->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $detail->taskEventPhaseTable->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $detail->taskEventPhaseTableOperation->name ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($detail->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $detail->id }})">Edit</button>
                                    @if ($confirmingDelete === $detail->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $detail->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600" wire:click="confirmDelete({{ $detail->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-gray-500">No task definition details found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $details->links() }}
            </div>
        </div>
    </div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">{{ $detail_id ? 'Edit' : 'Create' }} Task Definition Detail</h3>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Enter name">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Definition <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="task_defination_id">
                            <option value="">Select Definition</option>
                            @foreach($taskDefinations as $def)
                                <option value="{{ $def->id }}">{{ $def->name }}</option>
                            @endforeach
                        </select>
                        @error('task_defination_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sequence No <span class="text-red-500">*</span></label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="task_event_sequence_no" placeholder="Enter sequence number">
                        @error('task_event_sequence_no') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Event Phase <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="task_event_phase_id">
                            <option value="">Select Phase</option>
                            @foreach($taskEventPhases as $phase)
                                <option value="{{ $phase->id }}">{{ $phase->name }}</option>
                            @endforeach
                        </select>
                        @error('task_event_phase_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Table <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="task_event_phase_table_id">
                            <option value="">Select Table</option>
                            @foreach($taskEventPhaseTables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                        @error('task_event_phase_table_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operation <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="task_event_phase_table_operation_id">
                            <option value="">Select Operation</option>
                            @foreach($taskEventPhaseTableOperations as $operation)
                                <option value="{{ $operation->id }}">{{ $operation->name }}</option>
                            @endforeach
                        </select>
                        @error('task_event_phase_table_operation_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" rows="2" placeholder="Enter description"></textarea>
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
                        <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store">{{ $detail_id ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
