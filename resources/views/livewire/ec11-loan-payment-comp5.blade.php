<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <div class="flex gap-2 items-center">
            <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search member or loan..." wire:model="search">
            @if(count($selectedLoans) > 0)
                <span class="text-sm text-blue-600 font-medium">{{ count($selectedLoans) }} selected</span>
            @endif
        </div>
        @if(count($selectedLoans) > 0)
            <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="openPaymentModal()">
                Pay Selected ({{ count($selectedLoans) }})
            </button>
        @endif
    </div>

    @forelse($groupedLoans as $groupKey => $loanGroup)
        @php
            $parts = explode('_', $groupKey);
            $schemeName = $parts[0] ?? 'Uncategorized';
            $isEmi = ($parts[1] ?? '') === 'emi';
            $groupIds = collect($loanGroup)->pluck('id')->toArray();
            $allSelected = empty(array_diff($groupIds, $this->selectedLoans));
            $headerColor = $isEmi ? 'bg-indigo-600' : 'bg-emerald-600';
            $rowColor = $isEmi ? 'hover:bg-indigo-50' : 'hover:bg-emerald-50';
            $badgeColor = $isEmi ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700';
            $borderColor = $isEmi ? 'border-indigo-200' : 'border-emerald-200';
        @endphp

        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="{{ $headerColor }} px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <h3 class="text-white font-semibold text-sm">{{ $schemeName }}
                        <span class="ml-2 px-2 py-0.5 bg-white bg-opacity-20 rounded text-xs">
                            {{ $isEmi ? 'EMI Enabled' : 'Non-EMI' }}
                        </span>
                    </h3>
                    <span class="text-white text-xs opacity-75">({{ count($loanGroup) }} loans)</span>
                </div>
    
                <div class="flex gap-1">
                    @if(!$allSelected)
                        <button class="px-2 py-1 bg-white text-xs rounded hover:bg-gray-100" wire:click="selectAllInGroup('{{ $groupKey }}')">Select All</button>
                    @else
    
                        @if(count($selectedLoans) > 0)
                            <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="openPaymentModal()">
                                Pay Selected ({{ count($selectedLoans) }})
                            </button>
                        @endif
                        <button class="px-2 py-1 bg-white text-xs rounded hover:bg-gray-100" wire:click="deselectAllInGroup('{{ $groupKey }}')">Deselect All</button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto py-2">
                @if(count($selectedLoans) > 0)
                    <button class="px-4 py-2 bg-orange-600 text-white text-sm rounded hover:bg-orange-700" wire:click="openPaymentModal()">
                        Pay Selected ({{ count($selectedLoans) }})
                    </button>
                @endif
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left w-8">
                                <input type="checkbox" class="w-3.5 h-3.5" {{ $allSelected ? 'checked' : '' }} wire:click="selectAllInGroup('{{ $groupKey }}')">
                            </th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">#</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Loan Amt</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Balance</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">ROI %</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Days</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Interest</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Other</th>
                            @if($isEmi)
                            <th class="px-2 py-2 text-right font-medium text-gray-600">EMI Amt</th>
                            <th class="px-2 py-2 text-center font-medium text-gray-600">Pending</th>
                            @endif
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Last Payments</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($loanGroup as $index => $loan)
                            <tr class="{{ $rowColor }} {{ in_array($loan['id'], $selectedLoans) ? ($isEmi ? 'bg-indigo-100' : 'bg-emerald-100') : '' }}">
                                <td class="px-2 py-2">
                                    <input type="checkbox" class="w-3.5 h-3.5"
                                        {{ in_array($loan['id'], $selectedLoans) ? 'checked' : '' }}
                                        wire:click="toggleLoanSelection({{ $loan['id'] }})">
                                </td>
                                <td class="px-2 py-2 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-2 py-2">
                                    <span class="font-medium">{{ $loan['member_name'] }}</span>
                                    <span class="text-gray-400 ml-1">({{ $loan['member_id'] }})</span>
                                </td>
                                <td class="px-2 py-2 text-right">{{ number_format($loan['loan_amount'], 2) }}</td>
                                <td class="px-2 py-2 text-right font-medium {{ $loan['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($loan['balance'], 2) }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($loan['roi'], 2) }}%</td>
                                <td class="px-2 py-2 text-right">{{ $loan['days_since_last_payment'] }}</td>
                                <td class="px-2 py-2 text-right text-orange-600">{{ number_format($loan['due_interest'], 2) }}</td>
                                <td class="px-2 py-2 text-right text-gray-600">{{ number_format($loan['other_dues'], 2) }}</td>
                                @if($isEmi)
                                <td class="px-2 py-2 text-right">{{ number_format($loan['emi_amount'], 2) }}</td>
                                <td class="px-2 py-2 text-center">
                                    <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">{{ $loan['pending_emi_count'] }}</span>
                                </td>
                                @endif
                                <td class="px-2 py-2">
                                    @if(count($loan['last_payments']) > 0)
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-2 py-1 border border-gray-200">Date</th>
                                                <th class="px-2 py-1 border border-gray-200">Amount</th>
                                                <th class="px-2 py-1 border border-gray-200 text-left">Remarks</th>
                                            </tr>
                                        </thead>
                                        @foreach($loan['last_payments'] as $lp)
                                            <tbody>
                                            <tr class="text-xs">
                                                <td class="px-2 py-1 border border-gray-200">{{ \Carbon\Carbon::parse($lp['date'])->format('d/m') }}</td>
                                                <td class="px-2 py-1 border border-gray-200 text-right">{{ number_format($lp['amount'], 0) }}</td>
                                                <td class="px-2 py-1 border border-gray-200">
                                                    @foreach($lp['details'] as $ld)
                                                        <span class="text-gray-400 ml-1">{{Str::substr($ld['remarks'] ?? 'Detail', 0, 3) }}:</span>                                                    
                                                        <span class="text-gray-400 ml-1">{{ $ld['amount'] ?? 'X' }}</span>,
                                                    @endforeach
                                                </td>
                                            </tr>
                                            </tbody>
                                            {{-- <div class="text-xs leading-tight {{ $loop->index > 0 ? 'mt-0.5' : '' }}">
                                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($lp['date'])->format('d/m') }}:</span>
                                                <span class="font-medium">{{ number_format($lp['amount'], 0) }}</span>
                                                @foreach($lp['details'] as $ld)
                                                    <span class="text-gray-400 ml-1">({{ $ld['remarks'] ?? 'Detail' }})</span>
                                                @endforeach
                                            </div> --}}
                                        @endforeach
                                    </table>
                                    @else
                                        <span class="text-gray-400">No payments</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <span class="px-1.5 py-0.5 text-xs rounded {{ $badgeColor }}">
                                        {{ $loan['status'] ?? 'Active' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            No active loans found.
        </div>
    @endforelse

    

    @if ($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-6xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                <h5 class="font-semibold text-gray-700">Process Payment for {{ count($selectedLoans) }} Loan(s)</h5>
                <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                    <span class="text-xl">&times;</span>
                </button>
            </div>
            <div class="p-4">
                @if($confirmingPayment)
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <h4 class="font-bold text-yellow-800 mb-2">Confirm Payment</h4>
                    <p class="text-yellow-700 text-sm">Process payment of <strong>{{ number_format($payment_amount, 2) }}</strong> for {{ count($selectedLoans) }} loan(s)?</p>
                    <div class="mt-3 flex gap-2">
                        <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="store()">Yes, Process</button>
                        <button class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="cancelConfirmation()">Cancel</button>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-4 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Payment Date <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model.lazy="payment_date">
                        @error('payment_date') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label>
                        <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_method">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Total Amount</label>
                        <input type="text" class="w-full px-2 py-1.5 bg-gray-100 border border-gray-300 rounded text-sm font-bold" value="{{ number_format($payment_amount, 2) }}" readonly>
                    </div>
                    <div class="flex items-center mt-5">
                        @php $hasEmiLoan = false; @endphp
                        @foreach($calculatedPayments as $cp)
                            @if($cp['is_emi_enabled']) @php $hasEmiLoan = true; @endphp @endif
                        @endforeach
                        @if($hasEmiLoan)
                        <button class="px-3 py-1.5 text-xs rounded {{ $lumpsum_mode ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700' }}" wire:click="toggleLumpsum">
                            {{ $lumpsum_mode ? 'Lumpsum ON' : 'Lumpsum OFF' }}
                        </button>
                        @endif
                    </div>
                </div>

                @if($lumpsum_mode)
                <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded">
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Lumpsum Extra Amount</label>
                            <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-orange-300 rounded text-sm focus:outline-none focus:border-orange-500" wire:model.lazy="custom_emi_amount" placeholder="Extra amount">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">EMI Adjustment Mode</label>
                            <select class="w-full px-2 py-1.5 border border-orange-300 rounded text-sm focus:outline-none focus:border-orange-500" wire:model="emi_adjustment_mode">
                                <option value="reduce_no">Reduce EMI Count</option>
                                <option value="fix_amount">Fixed Amount, Vary Count</option>
                            </select>
                        </div>
                        <div class="flex items-end pb-1">
                            <span class="text-xs text-orange-700">Extra lumpsum will be distributed across principal of selected EMI loans</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="overflow-x-auto border border-gray-200 rounded">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left font-medium text-gray-600">Member</th>
                                <th class="px-2 py-2 text-left font-medium text-gray-600">Scheme</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600">Balance</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600">ROI %</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600 w-28">Interest</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600 w-28">Others</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600 w-28">Principal</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600 w-24">Total</th>
                                <th class="px-2 py-2 text-right font-medium text-gray-600">Balance After</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($calculatedPayments as $loanId => $calc)
                                <tr class="{{ $calc['is_emi_enabled'] ? 'bg-indigo-50' : 'bg-emerald-50' }}">
                                    <td class="px-2 py-2">
                                        <span class="font-medium">{{ $calc['member_name'] }}</span>
                                        @if($calc['is_emi_enabled'])
                                            <span class="ml-1 px-1 py-0.5 bg-indigo-100 text-indigo-600 text-xs rounded">EMI</span>
                                            @if($calc['pending_emi_count'])
                                                <span class="ml-1 text-gray-400">{{ $calc['pending_emi_count'] }} pending</span>
                                            @endif
                                        @else
                                            <span class="ml-1 px-1 py-0.5 bg-emerald-100 text-emerald-600 text-xs rounded">NON-EMI</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2">{{ $calc['scheme_name'] }}</td>
                                    <td class="px-2 py-2 text-right font-medium">{{ number_format($calc['balance'], 2) }}</td>
                                    <td class="px-2 py-2 text-right">{{ number_format($calc['roi'], 2) }}%</td>
                                    <td class="px-2 py-2 text-right">
                                        <input type="number" step="0.01"
                                            class="w-full px-1.5 py-1 border border-orange-300 rounded text-right text-xs focus:outline-none focus:border-orange-500 bg-orange-50"
                                            wire:model.lazy="payment_details.{{ $loanId }}.interest"
                                            placeholder="0.00">
                                    </td>
                                    <td class="px-2 py-2 text-right">
                                        <input type="number" step="0.01"
                                            class="w-full px-1.5 py-1 border border-gray-300 rounded text-right text-xs focus:outline-none focus:border-gray-500 bg-white"
                                            wire:model.lazy="payment_details.{{ $loanId }}.others"
                                            placeholder="0.00">
                                    </td>
                                    <td class="px-2 py-2 text-right">
                                        <input type="number" step="0.01"
                                            class="w-full px-1.5 py-1 border border-blue-300 rounded text-right text-xs focus:outline-none focus:border-blue-500 bg-blue-50 font-bold"
                                            wire:model.lazy="payment_details.{{ $loanId }}.principal"
                                            placeholder="0.00">
                                    </td>
                                    <td class="px-2 py-2 text-right font-bold text-green-700">{{ number_format($calc['total'], 2) }}</td>
                                    <td class="px-2 py-2 text-right text-gray-500">{{ number_format($calc['balance_after'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold">
                            <tr>
                                <td colspan="7" class="px-2 py-2 text-right text-sm">Grand Total:</td>
                                <td class="px-2 py-2 text-right text-green-800 text-sm">{{ number_format($payment_amount, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                    <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Remarks" rows="1"></textarea>
                </div>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                @if(!$confirmingPayment)
                    <button class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="store()">Submit Payment</button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
