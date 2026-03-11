<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Bank Loan Borrowed</h3>
            <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">+ Create Borrowed</button>
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
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Scheme</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Borrowed Date</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Installment</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Finalized</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($borroweds as $borrowed)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($borroweds->currentPage() - 1) * $borroweds->perPage() }}</td>
                                <td class="px-3 py-2">{{ $borrowed->name }}</td>
                                <td class="px-3 py-2">{{ $borrowed->loanScheme->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ number_format($borrowed->loan_borrowed_amount, 2) }}</td>
                                <td class="px-3 py-2">{{ $borrowed->loan_borrowed_date ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $borrowed->installment_amount ?? '-' }} x {{ $borrowed->no_of_installments ?? 0 }}</td>
                                <td class="px-3 py-2">
                                    @if($borrowed->is_finalized)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @switch($borrowed->status)
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
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $borrowed->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-3 py-2">
                                    @if($borrowed->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $borrowed->id }})">Edit</button>
                                    @if ($confirmingDelete === $borrowed->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $borrowed->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $borrowed->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-3 py-4 text-center text-gray-500">No borrowed records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $borroweds->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">{{ $borrowed_id ? 'Edit Borrowed' : 'Create Borrowed' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        {{-- First: Bank Loan Scheme Selection --}}
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Bank Loan Scheme <span class="text-red-500">*</span></label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="bank_loan_scheme_id">
                                <option value="">Select Scheme</option>
                                @foreach($loanSchemes as $scheme)
                                    <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                @endforeach
                            </select>
                            @error('bank_loan_scheme_id') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        @if(count($selectedSpecs) > 0)
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Scheme Specifications</label>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Name</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Value</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">% on Balance</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Regular</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($selectedSpecs as $spec)
                                            <tr>
                                                <td class="px-2 py-1">{{ $spec['name'] ?? '-' }}</td>
                                                <td class="px-2 py-1">{{ $spec['bank_loan_schema_particular_value'] ?? 0 }}</td>
                                                <td class="px-2 py-1">{{ ($spec['is_percent_on_current_balance'] ?? true) ? 'Yes' : 'No' }}</td>
                                                <td class="px-2 py-1">{{ ($spec['is_regular'] ?? false) ? 'Yes' : 'No' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        {{-- Second Line: Name, Amount, ROI, Years --}}
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Enter name">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Borrowed Amount</label>
                                <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_borrowed_amount" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">ROI (%)</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-200 rounded text-sm bg-gray-50" wire:model="interestRate" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Years</label>
                                <input type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="no_of_installments" placeholder="Years">
                            </div>
                        </div>

                        {{-- Third Line: EMI Table --}}
                        @if(count($calculatedEmis) > 0)
                        <div class="mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-semibold text-gray-700">EMI Schedule</label>
                                <span class="text-xs text-gray-500">Total: {{ number_format(array_sum(array_column($calculatedEmis, 'total')), 2) }}</span>
                            </div>
                            <div class="overflow-x-auto border border-gray-200 rounded max-h-40">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">EMI No.</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Principal</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Interest</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Total</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($calculatedEmis as $emi)
                                            <tr>
                                                <td class="px-2 py-1">{{ $emi['emi_no'] }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['principal_amount'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['interest_amount'], 2) }}</td>
                                                <td class="px-2 py-1 font-medium">{{ number_format($emi['total'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['balance_after'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        {{-- Fourth Line: Other Options --}}
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Borrowed Date</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_borrowed_date">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Installment Amount</label>
                                <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="installment_amount" placeholder="0.00">
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
                            <div class="flex items-center justify-center mt-5">
                                <input type="checkbox" id="is_finalized" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_finalized">
                                <label for="is_finalized" class="ml-2 text-sm text-gray-700">Finalized</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" placeholder="Enter description" rows="2"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                                <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Enter remarks" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center mt-3">
                            <input type="checkbox" id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="is_active">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $borrowed_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
