<div class="p-4">
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Loan Payment Collection</h3>
            @if(count($selectedLoans) > 0)
                <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="openPaymentModal()">
                    Pay Selected ({{ count($selectedLoans) }})
                </button>
            @endif
        </div>
        <div class="p-4">
            <div class="flex justify-between mb-4 gap-2">
                <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search member or loan..." wire:model="search">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-medium text-gray-600 w-8">
                                <input type="checkbox" class="w-4 h-4" wire:click="selectAll()">
                            </th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Member</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Loan Name</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Scheme</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Loan Amount</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">ROI %</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Paid</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Balance</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">EMI</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Interest</th>
                            <th class="px-2 py-2 text-right font-medium text-gray-600">Total Due</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Last Payment</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($loanData as $index => $loan)
                            <tr class="hover:bg-gray-50 {{ in_array($loan['id'], $selectedLoans) ? 'bg-blue-50' : '' }}">
                                <td class="px-2 py-2">
                                    <input type="checkbox" class="w-4 h-4" 
                                        {{ in_array($loan['id'], $selectedLoans) ? 'checked' : '' }}
                                        wire:click="toggleLoanSelection({{ $loan['id'] }})">
                                </td>
                                <td class="px-2 py-2">{{ $index + 1 }}</td>
                                <td class="px-2 py-2">{{ $loan['member_name'] }}</td>
                                <td class="px-2 py-2">{{ $loan['name'] }}</td>
                                <td class="px-2 py-2">{{ $loan['scheme_name'] }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($loan['loan_amount'], 2) }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($loan['roi'], 2) }}%</td>
                                <td class="px-2 py-2 text-right text-green-600">{{ number_format($loan['total_paid'], 2) }}</td>
                                <td class="px-2 py-2 text-right font-medium {{ $loan['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($loan['balance'], 2) }}
                                </td>
                                <td class="px-2 py-2 text-right">{{ number_format($loan['monthly_emi'], 2) }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($loan['monthly_interest'], 2) }}</td>
                                <td class="px-2 py-2 text-right font-medium text-blue-600">{{ number_format($loan['total_monthly_due'], 2) }}</td>
                                <td class="px-2 py-2">
                                    @if($loan['last_payment_amount'])
                                        <span class="text-xs">
                                            {{ number_format($loan['last_payment_amount'], 2) }}
                                            <br><span class="text-gray-500">{{ date('d-m-Y', strtotime($loan['last_payment_date'])) }}</span>
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    @switch($loan['status'])
                                        @case('running')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Running</span>
                                            @break
                                        @case('closed')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Closed</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">{{ $loan['status'] }}</span>
                                    @endswitch
                                </td>
                                <td class="px-2 py-2">
                                    <button class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" wire:click="toggleLoanSelection({{ $loan['id'] }})">
                                        {{ in_array($loan['id'], $selectedLoans) ? 'Selected' : 'Select' }}
                                    </button>
                                </td>
                            </tr>
                            @if($loan['scheme_details'] && count($loan['scheme_details']) > 0)
                            <tr class="bg-gray-50">
                                <td colspan="3"></td>
                                <td colspan="10" class="px-2 py-2">
                                    <div class="text-xs text-gray-600">
                                        <strong>Scheme Details:</strong>
                                        @foreach($loan['scheme_details'] as $detail)
                                            <span class="mr-3">{{ $detail['name'] ?? '-' }}: {{ number_format($detail['value'] ?? 0, 2) }}</span>
                                        @endforeach
                                    </div>
                                    @if($loan['payment_history'] && count($loan['payment_history']) > 0)
                                    <div class="text-xs text-gray-600 mt-1">
                                        <strong>Payment History:</strong>
                                        @foreach($loan['payment_history'] as $payment)
                                            <span class="mr-2">
                                                {{ date('d-m-Y', strtotime($payment->payment_date)) }}: 
                                                {{ number_format($payment->payment_total_amount, 2) }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </td>
                                <td colspan="1"></td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="14" class="px-3 py-4 text-center text-gray-500">No loans found with active payments.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">Process Payment</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Selected Loans Summary</label>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Loan Name</th>
                                            <th class="px-2 py-1 text-right font-medium text-gray-600">Balance</th>
                                            <th class="px-2 py-1 text-right font-medium text-gray-600">Monthly Due</th>
                                            <th class="px-2 py-1 text-right font-medium text-gray-600">Pay Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($selectedLoans as $loanId)
                                            @php
                                                $loan = collect($loanData)->firstWhere('id', $loanId);
                                            @endphp
                                            @if($loan)
                                            <tr>
                                                <td class="px-2 py-1">{{ $loan['name'] }}</td>
                                                <td class="px-2 py-1 text-right">{{ number_format($loan['balance'], 2) }}</td>
                                                <td class="px-2 py-1 text-right">{{ number_format($loan['total_monthly_due'], 2) }}</td>
                                                <td class="px-2 py-1 text-right font-medium">
                                                    {{ number_format(($calculatedPayments[$loanId]['pay_amount'] ?? 0), 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Payment Date <span class="text-red-500">*</span></label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_date">
                                @error('payment_date') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Payment Amount <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_amount" placeholder="0.00">
                                @error('payment_amount') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="upi">UPI</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Enter remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="store()">Process Payment</button>
                </div>
            </div>
        </div>
    @endif
</div>