<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Unassigned Loan Requests</h3>
        </div>
        <div class="p-4">
            <div class="flex justify-between mb-4 gap-2">
                <div class="flex gap-2">
                    <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-48 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="status">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">ROI</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Scheme</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Years</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($unassignedLoans as $loan)
                            <tr class="hover:bg-gray-50">
                                {{-- <td class="px-3 py-2">{{ $loop->iteration + ($unassignedLoans->currentPage() - 1) * $unassignedLoans->perPage() }}</td> --}}
                                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2">{{ $loan['member']['name'] ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    {{ $loan['roi'] ?? '-' }}%
                                    @if($loan['loan_request_details'])
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Pending</span>
                                    @endif
                                </td>
                                {{-- <td class="px-3 py-2">{{ json_encode($loan['loan_request_details']) ?? '-' }}</td> --}}
                                {{-- <td class="px-3 py-2">{{ $loan['id'] ?? '-' }}</td> --}}
                                <td class="px-3 py-2">{{ $loan['loan_scheme']['name'] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ number_format($loan['loan_amount'], 2) }}</td>
                                <td class="px-3 py-2">{{ $loan['no_of_years'] ?? 0 }}</td>
                                <td class="px-3 py-2">
                                    {{-- {{ $loan->roi ?? '-' }} --}}
                                    @if($loan['status'] == 'Approved')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Approved</span>
                                    @elseif($loan['status'] == 'Rejected')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Rejected</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Pending</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($loan['is_active'])
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($loan['no_of_years'] > 1 && $loan['no_of_years'] <= 5)
                                        <button class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700" wire:click="openAssignModal({{ $loan['id'] }})">Loan Assign</button>
                                    @else
                                        <button class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" wire:click="openAssignModal({{ $loan->id }})">Loan Assign</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-gray-500">No unassigned loan requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{-- {{ $unassignedLoans->links() }} --}}
            </div>
        </div>
    </div>

    @if(count($assignedLoans) > 0)
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Previously Assigned Loans</h3>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Sl/LID</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Id Member</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">LID Scheme</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Loan Amount</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">ROI</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Tenure</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">EMI Amt</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Total EMI</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Current Bal</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Assign Date</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($assignedLoans as $assign)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $loop->iteration }} / {{ $assign['id'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="text-sm text-gray-500">{{ $assign['member']['id'] ?? '-' }}</span>
                                        {{ $assign['member']['name'] ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $assign['loan_id'] ?? '-' }} :
                                        {{ $assign['loan_scheme']['name'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ number_format($assign['loan_amount'], 2) }}</td>
                                    <td class="px-3 py-2">
                                        {{-- {{ json_encode($assign, true) ?? '-' }}% --}}
                                        {{ $assign['roi'] ?? 0 }}%
                                    </td>
                                    <td class="px-3 py-2">
                                        
                                            {{ $assign['is_emi_enabled'] ? str($assign['no_of_emi'] ?? 0 ).'mo': '-' }}
                                        
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $assign['is_emi_enabled'] ? (number_format($assign['emi_amount'], 2)) : '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $assign['is_emi_enabled'] ? number_format(($assign['emi_amount'] ?? 0) * ($assign['no_of_emi'] ?? 0), 2) : '-' }}</td>
                                    <td class="px-3 py-2">{{ number_format($assign['loan_current_balance'], 2) }}</td>
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($assign['loan_assigned_date'])->format('d-m-Y') ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if($assign['status'] == 'Assigned')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Assigned</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $assign['status'] ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <button class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700" wire:click="openDetailModal({{ $assign['id'] }})">View Details</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">Assign Loan</h3>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    @if($selectedLoanRequest)
                        <div class="mb-4 p-3 bg-gray-50 rounded">
                            <p class="text-sm"><strong>Member:</strong> {{ $selectedLoanRequest->member->name ?? '-' }}</p>
                            <p class="text-sm"><strong>Scheme:</strong> {{ $selectedLoanRequest->loanScheme->name ?? '-' }}</p>
                            <p class="text-sm"><strong>Loan Amount:</strong> {{ number_format($selectedLoanRequest->loan_amount, 2) }}</p>
                            <p class="text-sm"><strong>Duration:</strong> {{ $selectedLoanRequest->no_of_years ?? 0 }} years</p>
                        </div>

                        @if(count($loanRequestDetails) > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Loan Scheme Features</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Feature</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Value</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Condition</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($loanRequestDetails as $detail)
                                                <tr>
                                                    <td class="px-2 py-1">{{ $detail['loan_scheme_feature_name'] ?? '-' }}</td>
                                                    <td class="px-2 py-1">{{ $detail['loan_scheme_feature_value'] ?? '-' }}</td>
                                                    <td class="px-2 py-1">{{ $detail['loan_scheme_feature_condition'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loan Assigned Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_assigned_date">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loan Released Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_released_date">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loan Amount</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_amount">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Balance</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_current_balance">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ROI (%)</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="roi">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. of Years</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="no_of_years">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. of EMI</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="no_of_emi">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">EMI Amount</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="emi_amount">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">First EMI Due Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="first_emi_due_date">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Next EMI Due Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="next_emi_due_date">
                        </div>
                        <div class="mb-3 flex items-center mt-6">
                            <input type="checkbox" class="mr-2" wire:model="is_emi_enabled" id="is_emi_enabled">
                            <label for="is_emi_enabled" class="text-sm text-gray-700">EMI Enabled</label>
                        </div>
                        <div class="mb-3 flex items-center mt-6">
                            <input type="checkbox" class="mr-2" wire:model="is_active" id="is_active">
                            <label for="is_active" class="text-sm text-gray-700">Active</label>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" rows="2"></textarea>
                        </div>
                    </div>

                    @if($is_emi_enabled && count($calculatedEmis) > 0)
                        <div class="mb-4 mt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">EMI Schedule</h4>
                            <div class="overflow-x-auto max-h-48">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-100 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">EMI No.</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Principal</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Interest</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Total</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Balance After</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($calculatedEmis as $emi)
                                            <tr>
                                                <td class="px-2 py-1">{{ $emi['emi_no'] }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['emi_principal'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['emi_interest'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['emi_total'], 2) }}</td>
                                                <td class="px-2 py-1">{{ number_format($emi['balance_after'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 mt-4">
                        <button class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal">Cancel</button>
                        <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store">Assign Loan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isDetailOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-indigo-600">
                    <h3 class="text-lg font-semibold text-white">Loan Details - {{ $selectedLoanAssign['name'] ?? '' }}</h3>
                    <button class="text-white hover:text-gray-200" wire:click="closeDetailModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    @if($selectedLoanAssign)
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="p-3 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Member Information</h4>
                                <p class="text-sm"><strong>Name:</strong> {{ $selectedLoanAssign['member']['name'] ?? '-' }}</p>
                                <p class="text-sm"><strong>Scheme:</strong> {{ $selectedLoanAssign['loan_scheme']['name'] ?? '-' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Loan Information</h4>
                                <p class="text-sm"><strong>Loan Amount:</strong> {{ number_format($selectedLoanAssign['loan_amount'], 2) }}</p>
                                <p class="text-sm"><strong>Current Balance:</strong> {{ number_format($selectedLoanAssign['loan_current_balance'], 2) }}</p>
                                <p class="text-sm"><strong>ROI:</strong> {{ $selectedLoanAssign['roi'] ?? 0 }}%</p>
                                <p class="text-sm"><strong>Tenure:</strong> {{ $selectedLoanAssign['no_of_emi'] ?? 0 }} months</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Dates</h4>
                                <p class="text-sm"><strong>Assigned Date:</strong> {{ $selectedLoanAssign['loan_assigned_date'] ?? '-' }}</p>
                                <p class="text-sm"><strong>Released Date:</strong> {{ $selectedLoanAssign['loan_released_date'] ?? '-' }}</p>
                                <p class="text-sm"><strong>First EMI Due:</strong> {{ $selectedLoanAssign['first_emi_due_date'] ?? '-' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">EMI Details</h4>
                                <p class="text-sm"><strong>EMI Enabled:</strong> {{ $selectedLoanAssign['is_emi_enabled'] ? 'Yes' : 'No' }}</p>
                                <p class="text-sm"><strong>EMI Amount:</strong> {{ number_format($selectedLoanAssign['emi_amount'], 2) }}</p>
                                <p class="text-sm"><strong>Total EMI Amount:</strong> {{ number_format(($selectedLoanAssign['emi_amount'] ?? 0) * ($selectedLoanAssign['no_of_emi'] ?? 0), 2) }}</p>
                                <p class="text-sm"><strong>Status:</strong> {{ $selectedLoanAssign['status'] ?? '-' }}</p>
                            </div>
                        </div>

                        @if(count($selectedLoanAssignDetails) > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Loan Scheme Features</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Feature Name</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Value</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Condition</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($selectedLoanAssignDetails as $detail)
                                                <tr>
                                                    <td class="px-2 py-1">{{ $detail['loan_scheme_detail_feature_name'] ?? '-' }}</td>
                                                    <td class="px-2 py-1">{{ $detail['loan_scheme_detail_feature_value'] ?? '-' }}</td>
                                                    <td class="px-2 py-1">{{ $detail['loan_scheme_detail_feature_condition'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if(count($selectedEmiSchedules) > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">EMI Schedule</h4>
                                <div class="overflow-x-auto max-h-64">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-100 sticky top-0">
                                            <tr>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">EMI No.</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Due Date</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Principal</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Interest</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Total</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Balance After</th>
                                                <th class="px-2 py-1 text-left font-medium text-gray-600">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($selectedEmiSchedules as $emi)
                                                <tr>
                                                    <td class="px-2 py-1">{{ $emi['emi_schedule_index'] }}</td>
                                                    <td class="px-2 py-1">{{ $emi['emi_due_date'] }}</td>
                                                    <td class="px-2 py-1">{{ number_format($emi['principal_emi_amount'], 2) }}</td>
                                                    <td class="px-2 py-1">{{ number_format($emi['interest_emi_amount'], 2) }}</td>
                                                    <td class="px-2 py-1">{{ number_format($emi['total_emi_amount'], 2) }}</td>
                                                    <td class="px-2 py-1">{{ number_format($emi['principal_balance_amount_after_emi'], 2) }}</td>
                                                    <td class="px-2 py-1">
                                                        @if($emi['status'] == 'Paid')
                                                            <span class="px-1 py-0.5 bg-green-100 text-green-700 text-xs rounded">Paid</span>
                                                        @else
                                                            <span class="px-1 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="flex justify-end gap-2 mt-4">
                        <button class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeDetailModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
