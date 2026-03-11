<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Bank Loan Schemes</h3>
            <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">+ Create Scheme</button>
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
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Bank</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Effected On</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Finalized</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($schemes as $scheme)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($schemes->currentPage() - 1) * $schemes->perPage() }}</td>
                                <td class="px-3 py-2">{{ $scheme->name }}</td>
                                <td class="px-3 py-2">{{ $scheme->bank->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $scheme->effected_on ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($scheme->is_finalized)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @switch($scheme->status)
                                        @case('running')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Running</span>
                                            @break
                                        @case('completed')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Completed</span>
                                            @break
                                        @case('upcoming')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Upcoming</span>
                                            @break
                                        @case('suspended')
                                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Suspended</span>
                                            @break
                                        @case('cancelled')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Cancelled</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $scheme->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-3 py-2">
                                    @if($scheme->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $scheme->id }})">Edit</button>
                                    @if ($confirmingDelete === $scheme->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $scheme->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $scheme->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">No schemes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $schemes->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">{{ $scheme_id ? 'Edit Scheme' : 'Create Scheme' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Enter scheme name">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Bank <span class="text-red-500">*</span></label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_id') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Effected On</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="effected_on">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="status">
                                    <option value="running">Running</option>
                                    <option value="completed">Completed</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" placeholder="Enter description" rows="2"></textarea>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mt-3">
                            <div class="flex items-center mt-5">
                                <input type="checkbox" id="is_finalized" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_finalized">
                                <label for="is_finalized" class="ml-2 text-sm text-gray-700">Finalized</label>
                            </div>
                            <div class="flex items-center mt-5">
                                <input type="checkbox" id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_active">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Enter remarks" rows="2"></textarea>
                        </div>

                        <div class="mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-semibold text-gray-700">Specifications</label>
                                <button type="button" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" wire:click="addSpecification()">+ Add</button>
                            </div>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Name</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Particular</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Value</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">% on Balance</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Regular</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Effected On</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($specifications as $index => $spec)
                                            <tr>
                                                <td class="px-2 py-1">
                                                    <input type="text" class="w-full px-1 py-1 border border-gray-300 rounded text-xs" wire:model="specifications.{{ $index }}.name" placeholder="Name">
                                                </td>
                                                <td class="px-2 py-1">
                                                    <select class="w-full px-1 py-1 border border-gray-300 rounded text-xs" wire:model="specifications.{{ $index }}.bank_loan_schema_particular_id">
                                                        <option value="">Select</option>
                                                        @foreach($allParticulars as $particular)
                                                            <option value="{{ $particular->id }}">{{ $particular->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-2 py-1">
                                                    <input type="number" step="0.01" class="w-20 px-1 py-1 border border-gray-300 rounded text-xs" wire:model="specifications.{{ $index }}.bank_loan_schema_particular_value" placeholder="0.00">
                                                </td>
                                                <td class="px-2 py-1 text-center">
                                                    <input type="checkbox" class="w-3 h-3" wire:model="specifications.{{ $index }}.is_percent_on_current_balance">
                                                </td>
                                                <td class="px-2 py-1 text-center">
                                                    <input type="checkbox" class="w-3 h-3" wire:model="specifications.{{ $index }}.is_regular">
                                                </td>
                                                <td class="px-2 py-1">
                                                    <input type="date" class="w-full px-1 py-1 border border-gray-300 rounded text-xs" wire:model="specifications.{{ $index }}.effected_on">
                                                </td>
                                                <td class="px-2 py-1 text-center">
                                                    @if(count($specifications) > 1)
                                                        <button type="button" class="text-red-600 hover:text-red-800" wire:click="removeSpecification({{ $index }})">&times;</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $scheme_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
