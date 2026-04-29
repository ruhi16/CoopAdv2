<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Active Assigned Loans</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Show:</span>
                <select class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>
        <div class="p-4">
            <div class="flex justify-between mb-4 gap-2">
                <div class="flex gap-2">
                    <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search loan..." wire:model="search">
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="member_filter">
                        <option value="">All Members</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">
                                <input type="checkbox" class="rounded" wire:click="toggleAll">
                            </th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Loan Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Current Balance</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">ROI</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">EMI</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($assignedLoans as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="checkbox" class="rounded" value="{{ $loan->id }}" wire:model="selectedLoans">
                                </td>
                                <td class="px-3 py-2">{{ $loop->iteration + ($assignedLoans->currentPage() - 1) * $assignedLoans->perPage() }}</td>
                                <td class="px-3 py-2">{{ $loan->member->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ number_format($loan->loan_amount, 2) }}</td>
                                <td class="px-3 py-2 font-medium">{{ number_format($loan->loan_current_balance, 2) }}</td>
                                <td class="px-3 py-2">{{ $loan->roi ?? 0 }}%</td>
                                <td class="px-3 py-2">
                                    @if($loan->is_emi_enabled)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">EMI: {{ number_format($loan->emi_amount, 2) }}</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">No EMI</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($loan->loan_current_balance <= 0)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Closed</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Active</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($loan->loan_current_balance > 0)
                                        <button class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" wire:click="openPaymentModal({{ $loan->id }})">Pay Now</button>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded">Paid Off</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-gray-500">No active loans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between items-center">
                @if(count($selectedLoans) > 0)
                    <div class="text-sm text-gray-600">
                        {{ count($selectedLoans) }} loan(s) selected
                        <button class="ml-2 px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" wire:click="openMultiPaymentModal">Collect Payment</button>
                    </div>
                @else
                    <div></div>
                @endif


                
                {{ $assignedLoans->links() }}
                
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Payment History</h3>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Loan</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Amount Paid</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Payment Date</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Method</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Balance Before</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                                <td class="px-3 py-2">{{ $payment->loanAssign->member->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $payment->loanAssign->name ?? '-' }}</td>
                                <td class="px-3 py-2 font-medium text-green-600">{{ number_format($payment->payment_total_amount, 2) }}</td>
                                <td class="px-3 py-2">{{ $payment->payment_date }}</td>
                                <td class="px-3 py-2">{{ $payment->payment_method }}</td>
                                <td class="px-3 py-2">{{ number_format($payment->principal_balance_amount_before_payment, 2) }}</td>
                                <td class="px-3 py-2">{{ number_format($payment->principal_balance_amount_after_payment, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">Record Payment</h3>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    @if($selectedLoan)
                        <div class="mb-4 p-3 bg-gray-50 rounded">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">Member</p>
                                    <p class="text-sm font-medium">{{ $selectedLoan->member->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Loan Scheme</p>
                                    <p class="text-sm font-medium">{{ $selectedLoan->loanScheme->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Loan Amount</p>
                                    <p class="text-sm font-medium">{{ number_format($selectedLoan->loan_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Current Balance</p>
                                    <p class="text-sm font-medium text-red-600">{{ number_format($selectedLoan->loan_current_balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">ROI</p>
                                    <p class="text-sm font-medium">{{ $selectedLoan->roi ?? 0 }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Last Payment</p>
                                    <p class="text-sm font-medium">{{ $lastPayment->payment_date ?? $selectedLoan->loan_assigned_date }}</p>
                                </div>
                            </div>
                        </div>

                        @if($selectedLoan->is_emi_enabled && $nextEmi)
                            <div class="mb-4 p-3 bg-blue-50 rounded border border-blue-200">
                                <p class="text-sm font-medium text-blue-700">Next Scheduled EMI</p>
                                <p class="text-xs text-blue-600">EMI #{{ $nextEmi->emi_schedule_index }} - Due: {{ $nextEmi->emi_due_date }}</p>
                            </div>
                        @else
                            @if($pendingInterest > 0 || $pendingFine > 0)
                                <div class="mb-4 p-3 bg-yellow-50 rounded border border-yellow-200">
                                    <p class="text-sm font-medium text-yellow-700">Pending Charges</p>
                                    <div class="flex justify-between text-xs mt-1">
                                        <span>Interest:</span>
                                        <span>{{ number_format($pendingInterest, 2) }}</span>
                                    </div>
                                    @if($pendingFine > 0)
                                        <div class="flex justify-between text-xs">
                                            <span>Fine:</span>
                                            <span>{{ number_format($pendingFine, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    @endif

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Amount <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_total_amount">
                        @error('payment_total_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_date">
                        @error('payment_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_method">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" rows="2" placeholder="Enter remarks"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal">Cancel</button>
                        <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="store">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isMultiPaymentOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-green-600">
                    <h3 class="text-lg font-semibold text-white">Bulk Payment Collection</h3>
                    <button class="text-white hover:text-gray-200" wire:click="closeMultiPaymentModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <p class="text-sm font-medium">Total Collection: {{ number_format($grandTotal, 2) }}</p>
                    </div>

                    <div class="overflow-x-auto mb-4">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Member</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Loan</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Balance</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Principal</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Interest</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Other</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($paymentItems as $index => $item)
                                    <tr>
                                        <td class="px-3 py-2">{{ $item['member_name'] }}</td>
                                        <td class="px-3 py-2">{{ $item['loan_name'] }}</td>
                                        <td class="px-3 py-2">{{ number_format($item['current_balance'], 2) }}</td>
                                        <td class="px-3 py-2">
                                            <input type="number" step="0.01" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm" wire:model="paymentItems.{{ $index }}.principal" wire:change="updatePaymentItemTotal({{ $index }})">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" step="0.01" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm" wire:model="paymentItems.{{ $index }}.interest" wire:change="updatePaymentItemTotal({{ $index }})">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" step="0.01" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm" wire:model="paymentItems.{{ $index }}.other" wire:change="updatePaymentItemTotal({{ $index }})">
                                        </td>
                                        <td class="px-3 py-2 font-medium">{{ number_format($item['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="6" class="px-3 py-2 text-right font-medium">Grand Total</td>
                                    <td class="px-3 py-2 font-bold text-green-600">{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" wire:model="payment_date">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm" wire:model="payment_method">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="upi">UPI</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" wire:model="remarks" placeholder="Enter remarks">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeMultiPaymentModal">Cancel</button>
                        <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="storeMultiPayment">Submit All Payments</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>