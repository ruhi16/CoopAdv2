<div class="p-4">
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Loan Assign Management</h3>
        </div>
        <div class="p-4">
            @if (session()->has('message'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="flex justify-between mb-4 gap-2">
                <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">+ New Loan Assign</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Loan Scheme</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">EMI</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">ROI</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($loanAssigns as $assign)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($loanAssigns->currentPage() - 1) * $loanAssigns->perPage() }}</td>
                                <td class="px-3 py-2">{{ $assign->member->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2">{{ $assign->loanScheme->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2">{{ number_format($assign->loan_amount, 2) }}</td>
                                <td class="px-3 py-2">{{ $assign->no_of_emi }} x {{ number_format($assign->emi_amount, 2) }}</td>
                                <td class="px-3 py-2">{{ $assign->roi ? $assign->roi . '%' : 'N/A' }}</td>
                                <td class="px-3 py-2">
                                    @switch($assign->status)
                                        @case('pending')
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Pending</span>
                                            @break
                                        @case('approved')
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Rejected</span>
                                            @break
                                        @case('active')
                                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">Active</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $assign->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $assign->id }})">Edit</button>
                                    @if ($confirmingDelete === $assign->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $assign->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $assign->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">No loan assigns found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $loanAssigns->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="font-semibold text-gray-700">{{ $editId ? 'Edit Loan Assign' : 'New Loan Assign' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Member <span class="text-red-500">*</span></label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="selectedMemberId">
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedMemberId') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Scheme <span class="text-red-500">*</span></label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="selectedLoanSchemeId">
                                    <option value="">Select Scheme</option>
                                    @foreach($loanSchemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedLoanSchemeId') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        @if(count($loanRequests) > 0)
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Link Loan Request</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="selectedLoanRequestId">
                                <option value="">Select Request (Optional)</option>
                                @foreach($loanRequests as $request)
                                    <option value="{{ $request->id }}">Loan #{{ $request->id }} - {{ number_format($request->loan_amount, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Amount <span class="text-red-500">*</span></label>
                                <input type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loanAmount" placeholder="0.00" step="0.01">
                                @error('loanAmount') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Rate of Interest (%) <span class="text-red-500">*</span></label>
                                <input type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="roi" placeholder="e.g., 12.5" step="0.01">
                                @error('roi') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">No. of EMI <span class="text-red-500">*</span></label>
                                <input type="number" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="noOfEmi" placeholder="e.g., 12">
                                @error('noOfEmi') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">EMI Amount</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-200 rounded text-sm bg-gray-50" wire:model="emiAmount" readonly placeholder="Auto calculated">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Assigned Date <span class="text-red-500">*</span></label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loanAssignedDate">
                                @error('loanAssignedDate') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <label class="inline-flex items-center text-xs">
                                        <input type="radio" class="form-radio text-blue-600" wire:model="status" value="pending">
                                        <span class="ml-1">Pending</span>
                                    </label>
                                    <label class="inline-flex items-center text-xs">
                                        <input type="radio" class="form-radio text-green-600" wire:model="status" value="approved">
                                        <span class="ml-1">Approved</span>
                                    </label>
                                    <label class="inline-flex items-center text-xs">
                                        <input type="radio" class="form-radio text-red-600" wire:model="status" value="rejected">
                                        <span class="ml-1">Rejected</span>
                                    </label>
                                    <label class="inline-flex items-center text-xs">
                                        <input type="radio" class="form-radio text-blue-600" wire:model="status" value="active">
                                        <span class="ml-1">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Released Date</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loanReleasedDate">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Closed Date</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loanClosedDate">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <label class="inline-flex items-center text-xs">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600" wire:model="isEmiEnabled">
                                <span class="ml-1">EMI Enabled</span>
                            </label>
                            <label class="inline-flex items-center text-xs">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600" wire:model="isDefault">
                                <span class="ml-1">Set as Default</span>
                            </label>
                            <label class="inline-flex items-center text-xs">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600" wire:model="isActive">
                                <span class="ml-1">Active</span>
                            </label>
                        </div>

                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $editId ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
