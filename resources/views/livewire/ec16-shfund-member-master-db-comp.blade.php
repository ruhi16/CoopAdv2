<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">SHFUND Member Master</h3>
            <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">+ Create Record</button>
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
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Operational Type</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Current Balance</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Finalized</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($masterDbs as $masterDb)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($masterDbs->currentPage() - 1) * $masterDbs->perPage() }}</td>
                                <td class="px-3 py-2">{{ $masterDb->name }}</td>
                                <td class="px-3 py-2">{{ $masterDb->member->name ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($masterDb->share_operational_type)
                                        <span class="px-2 py-1 text-xs rounded {{ $masterDb->share_operational_type === 'deposit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($masterDb->share_operational_type) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $masterDb->share_operational_amount ? number_format($masterDb->share_operational_amount, 2) : '-' }}</td>
                                <td class="px-3 py-2">{{ $masterDb->share_current_balance ? number_format($masterDb->share_current_balance, 2) : '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($masterDb->is_finalized)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @switch($masterDb->status)
                                        @case('draft')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Draft</span>
                                            @break
                                        @case('published')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Published</span>
                                            @break
                                        @case('archived')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Archived</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $masterDb->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-3 py-2">
                                    @if($masterDb->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $masterDb->id }})">Edit</button>
                                    @if ($confirmingDelete === $masterDb->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $masterDb->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $masterDb->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-3 py-4 text-center text-gray-500">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $masterDbs->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">{{ $master_db_id ? 'Edit Record' : 'Create Record' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Enter name">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Member</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="member_id">
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Assignment</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_assign_id">
                                    <option value="">Select Loan Assignment</option>
                                    @foreach($loanAssigns as $loanAssign)
                                        <option value="{{ $loanAssign->id }}">{{ $loanAssign->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" placeholder="Enter description">
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Operational Type</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="share_operational_type">
                                    <option value="">Select Type</option>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdrawal</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Operational Amount</label>
                                <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="share_operational_amount" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Operational Date</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="share_operational_date">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Current Balance</label>
                                <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="share_current_balance" placeholder="0.00">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="status">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-center mt-5">
                                <input type="checkbox" id="is_finalized" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_finalized">
                                <label for="is_finalized" class="ml-2 text-sm text-gray-700">Finalized</label>
                            </div>
                            <div class="flex items-center justify-center mt-5">
                                <input type="checkbox" id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_active">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Enter remarks" rows="2"></textarea>
                        </div>

                        @if(count($selectedSpecs) > 0)
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Specifications</label>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Particular</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Value</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Effected On</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Status</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600 w-20">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($selectedSpecs as $index => $spec)
                                            <tr>
                                                <td class="px-2 py-1">{{ $spec['particular'] ?? '-' }}</td>
                                                <td class="px-2 py-1">{{ number_format($spec['particular_value'] ?? 0, 2) }}</td>
                                                <td class="px-2 py-1">{{ $spec['effected_on'] ?? '-' }}</td>
                                                <td class="px-2 py-1">{{ $spec['status'] ?? 'draft' }}</td>
                                                <td class="px-2 py-1">
                                                    <button type="button" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="removeSpecification({{ $index }})">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4 p-3 bg-gray-50 rounded">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Add Specification</label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="specParticular" placeholder="Particular name">
                                </div>
                                <div>
                                    <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="specParticularValue" placeholder="Value">
                                </div>
                                <div>
                                    <button type="button" class="w-full px-2 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="addSpecification()">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $master_db_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
