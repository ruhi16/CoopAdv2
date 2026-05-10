<div class="p-4">
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Loan Requests</h3>
        </div>
        <div class="p-4">
            @if (session()->has('message'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="flex justify-between mb-4 gap-2">
                <div class="flex gap-2">
                    <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-48 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="member_id">
                        <option value="">All Members</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_scheme_id">
                        <option value="">All Schemes</option>
                        @foreach($loanSchemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                        @endforeach
                    </select>
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="status">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">Create Loan Request</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-indigo-200 divide-y divide-gray-400">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            {{-- <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th> --}}
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Scheme</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Details</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            {{-- <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th> --}}
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y  divide-gray-400">
                        @forelse ($loanRequests as $request)
                            <tr class="hover:bg-gray-50 cursor-pointer">
                                <td class="px-3 py-2">{{ $loop->iteration + ($loanRequests->currentPage() - 1) * $loanRequests->perPage() }}</td>
                                {{-- <td class="px-3 py-2">{{ $request->name }}xx</td> --}}
                                <td class="px-3 py-2">{{ $request->member->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $request->loanScheme->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ number_format($request->loan_amount, 2) }}</td>
                                <td class="px-3 py-2">
                                    {{-- @if($request->is_loan_assigned) --}}
                                        @forelse ($request->loanRequestDetails as $detail)
                                            {{ $detail->loan_scheme_feature_name }}: {{ $detail->loan_scheme_feature_value }} %<br/>
                                        
                                        @empty
                                            {{ '-' }}
                                        @endforelse
                                    {{-- @endif --}}
                                </td>
                                {{-- <td class="px-3 py-2">
                                    @if($request->status == 'Approved')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Approved</span>
                                    @elseif($request->status == 'Rejected')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Rejected</span>
                                    @else
                                        @if($request->is_loan_assigned == false)
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Pending</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Assigned</span>
                                    
                                        @endif
                                    @endif
                                </td> --}}
                                <td class="px-3 py-2">
                                    @if($request->is_loan_assigned)
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded">Assigned</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($request->is_loan_assigned)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">No Actions Required</span>
                                    @else
                                        <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-200 mr-1" wire:click="edit({{ $request->id }})" @if($request->is_loan_assigned) disabled @endif>Edit</button>
                                        @if ($confirmingDelete === $request->id)
                                            <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $request->id }})" @if($request->is_loan_assigned) disabled @endif>Confirm</button>
                                            <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()" @if($request->is_loan_assigned) disabled @endif>Cancel</button>
                                        @else
                                            <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $request->id }})" @if($request->is_loan_assigned) disabled @endif>Delete</button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">No loan requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $loanRequests->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">{{ $loan_request_id ? 'Edit Loan Request' : 'Create Loan Request' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Member <span class="text-red-500">*</span></label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="member_id">
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                                @error('member_id') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Scheme</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_scheme_id">
                                    <option value="">Select Scheme</option>
                                    @foreach($loanSchemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Amount</label>
                                <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_amount" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">No of Years</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="no_of_years">
                                    <option value="">Select Years</option>
                                    <option value="1">1 Year</option>
                                    <option value="2">2 Years</option>
                                    <option value="3">3 Years</option>
                                    <option value="4">4 Years</option>
                                    <option value="5">5 Years</option>
                                    <option value="6">6 Years</option>
                                    <option value="7">7 Years</option>
                                    <option value="8">8 Years</option>
                                    <option value="9">9 Years</option>
                                    <option value="10">10 Years</option>
                                    <option value="11">11 Years</option>
                                    <option value="12">12 Years</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-5">
                                <input type="checkbox" id="emi_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" wire:model="emi_active">
                                <label for="emi_active" class="ml-2 text-sm text-gray-700">EMI Active</label>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="description" placeholder="Description" rows="2"></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Remarks" rows="2"></textarea>
                        </div>

                        @if(count($schemeDetails) > 0)
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Loan Scheme Details</label>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Name</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Feature</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Type</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Value Type</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Value</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Mandate</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Condition</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Order</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Optional</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Default</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($schemeDetails as $detail)
                                            <tr>
                                                <td class="px-2 py-1">{{ $detail['name'] ?? '-' }}</td>
                                                <td class="px-2 py-1">{{ $detail['loan_scheme_feature_name'] ?? '-' }}</td>
                                                <td class="px-2 py-1">{{ ucfirst($detail['loan_scheme_feature_type'] ?? '-') }}</td>
                                                <td class="px-2 py-1">{{ ucfirst($detail['loan_scheme_feature_value_type'] ?? '-') }}</td>
                                                <td class="px-2 py-1">{{ $detail['loan_scheme_feature_value'] ?? '-' }}</td>
                                                <td class="px-2 py-1">{{ ucfirst($detail['loan_scheme_feature_mandate'] ?? '-') }}</td>
                                                <td class="px-2 py-1">{{ ucfirst($detail['loan_scheme_feature_condition'] ?? '-') }}</td>
                                                <td class="px-2 py-1">{{ $detail['order_index'] ?? 0 }}</td>
                                                <td class="px-2 py-1">
                                                    @if($detail['is_optional'])
                                                        <span class="px-1 py-0.5 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                                    @else
                                                        <span class="px-1 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">No</span>
                                                    @endif
                                                </td>
                                                <td class="px-2 py-1">
                                                    @if($detail['is_default'])
                                                        <span class="px-1 py-0.5 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                                    @else
                                                        <span class="px-1 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        @if($emi_active && count($calculatedEmis) > 0)
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">EMI Schedule</label>
                            <div class="overflow-x-auto border border-gray-200 rounded max-h-48">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">EMI No.</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Principal Amt</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Balance Amt</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Interest Amt</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($calculatedEmis as $emi)
                                            <tr>
                                                <td class="px-2 py-1">{{ $emi['emi_no'] }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['principal_amount'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['balance_amount'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['interest_amount'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['total'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $loan_request_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
