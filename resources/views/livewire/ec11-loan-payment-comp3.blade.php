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

            @foreach($loansByScheme as $schemeName => $schemeGroup)
                @php
                    $theme = $schemeGroup['theme'];
                    $loans = $schemeGroup['loans'];
                @endphp
                <div class="mb-6 rounded-lg overflow-hidden border {{ $theme['border'] }}">
                    <div class="{{ $theme['header'] }} px-4 py-2 flex justify-between items-center">
                        <h4 class="text-sm font-semibold text-white">{{ $schemeName }} ({{ count($loans) }} loan{{ count($loans) > 1 ? 's' : '' }})</h4>
                        <span class="px-2 py-0.5 bg-white bg-opacity-20 text-white text-xs rounded">{{ $theme['label'] }} Theme</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="{{ $theme['bg'] }} border-b {{ $theme['border'] }}">
                                <tr>
                                    <th class="px-2 py-2 text-left font-medium text-gray-600 w-8">
                                        <input type="checkbox" class="w-4 h-4" wire:click="selectAll()">
                                    </th>
                                    <th class="px-2 py-2 text-left font-medium text-gray-600">No.</th>
                                    <th class="px-2 py-2 text-left font-medium text-gray-600">Member</th>
                                    <th class="px-2 py-2 text-right font-medium text-gray-600">Loan Amount</th>
                                    <th class="px-2 py-2 text-right font-medium text-gray-600">Total Paid</th>
                                    <th class="px-2 py-2 text-right font-medium text-gray-600">Balance</th>
                                    <th class="px-2 py-2 text-right font-medium text-gray-600">EMI</th>
                                    <th class="px-2 py-2 text-left font-medium text-gray-600">Status</th>
                                    <th class="px-2 py-2 text-left font-medium text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($loans as $loan)
                                    <tr class="hover:bg-gray-50 {{ in_array($loan['id'], $selectedLoans) ? $theme['bg'] : '' }}">
                                        <td class="px-2 py-2">
                                            <input type="checkbox" class="w-4 h-4"
                                                {{ in_array($loan['id'], $selectedLoans) ? 'checked' : '' }}
                                                wire:click="toggleLoanSelection({{ $loan['id'] }})">
                                        </td>
                                        <td class="px-2 py-2">{{ $loop->iteration }}</td>
                                        <td class="px-2 py-2">
                                            <div class="font-medium">{{ $loan['member_name'] }}</div>
                                            <div class="text-gray-500">ID: {{ $loan['member_id'] }} | Loan ID: {{ $loan['id'] }}</div>
                                            <div class="text-gray-500">Released: {{ $loan['loan_released_date'] ? date('d-m-Y', strtotime($loan['loan_released_date'])) : '-' }}</div>
                                        </td>
                                        <td class="px-2 py-2 text-right">{{ number_format($loan['loan_amount'], 2) }}</td>
                                        <td class="px-2 py-2 text-right text-green-600">{{ number_format($loan['total_paid'], 2) }}</td>
                                        <td class="px-2 py-2 text-right font-medium {{ $loan['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($loan['balance'], 2) }}
                                        </td>
                                        <td class="px-2 py-2 text-right">{{ number_format($loan['monthly_emi'], 2) }}</td>
                                        <td class="px-2 py-2">
                                            @switch($loan['status'])
                                                @case('running')
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Running</span>
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

                                    <tr class="{{ $theme['bg'] }}">
                                        <td colspan="2"></td>
                                        <td colspan="7" class="px-2 py-2">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <h5 class="font-semibold {{ $theme['text'] }} mb-1">Current Dues</h5>
                                                    <div class="text-xs space-y-0.5">
                                                        <div><span class="text-gray-500">ROI:</span> <span class="font-medium">{{ number_format($loan['roi'], 2) }}%</span></div>
                                                        <div><span class="text-gray-500">Days since last payment:</span> <span class="font-medium {{ $loan['days_since_payment'] > 30 ? 'text-red-600' : 'text-gray-800' }}">{{ $loan['days_since_payment'] }} days</span></div>
                                                        <div><span class="text-gray-500">Monthly Interest:</span> <span class="font-medium">{{ number_format($loan['monthly_interest'], 2) }}</span></div>
                                                        <div><span class="text-gray-500">Other Dues:</span> <span class="font-medium">{{ number_format($loan['other_dues'], 2) }}</span></div>
                                                        <div class="border-t border-gray-300 pt-0.5"><span class="text-gray-500">Total Monthly Due:</span> <span class="font-bold {{ $theme['text'] }}">{{ number_format($loan['total_monthly_due'], 2) }}</span></div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <h5 class="font-semibold {{ $theme['text'] }} mb-1">Last Payment</h5>
                                                    @if($loan['last_payment_amount'])
                                                        <div class="text-xs space-y-0.5">
                                                            <div><span class="text-gray-500">Amount:</span> <span class="font-medium">{{ number_format($loan['last_payment_amount'], 2) }}</span></div>
                                                            <div><span class="text-gray-500">Date:</span> <span class="font-medium">{{ date('d-m-Y', strtotime($loan['last_payment_date'])) }}</span></div>
                                                            <div><span class="text-gray-500">Days ago:</span> <span class="font-medium">{{ $loan['days_since_payment'] }} days</span></div>
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-red-600">No payment history</div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <h5 class="font-semibold {{ $theme['text'] }} mb-1">Scheme Details</h5>
                                                    <div class="text-xs space-y-0.5">
                                                        @foreach(array_slice($loan['scheme_details'], 0, 3) as $detail)
                                                            <div><span class="text-gray-500">{{ $detail['name'] }}:</span> <span class="font-medium">{{ number_format($detail['value'], 2) }}</span></div>
                                                        @endforeach
                                                        @if(count($loan['scheme_details']) > 3)
                                                            <div class="text-gray-500">+{{ count($loan['scheme_details']) - 3 }} more...</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @if($loan['payment_history'] && count($loan['payment_history']) > 0)
                                                <div class="mt-2 pt-1 border-t {{ $theme['border'] }}">
                                                    <h5 class="font-semibold {{ $theme['text'] }} mb-1 text-xs">Previous Payments</h5>
                                                    <div class="flex gap-3 flex-wrap">
                                                        @foreach($loan['payment_history'] as $payment)
                                                            <div class="bg-white px-2 py-1 rounded border border-gray-200 text-xs">
                                                                <div class="font-medium">{{ date('d-m-Y', strtotime($payment['payment_date'])) }}</div>
                                                                <div class="{{ $theme['text'] }} font-bold">{{ number_format($payment['payment_total_amount'], 2) }}</div>
                                                                <div class="text-gray-500">{{ \Carbon\Carbon::parse($payment['payment_date'])->diffInDays(\Carbon\Carbon::now()) }} days ago</div>
                                                                @if(isset($payment['details']) && count($payment['details']) > 0)
                                                                    <div class="mt-0.5 text-gray-500">
                                                                        @foreach($payment['details'] as $pd)
                                                                            <span class="mr-1">{{ $pd['remarks'] }}: {{ number_format($pd['amount'], 2) }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            @if(empty($loansByScheme))
                <div class="px-3 py-4 text-center text-gray-500">No loans found with active payments.</div>
            @endif
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-6xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">Process Payment</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    @if($confirmingPayment)
                        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded text-yellow-800">
                            <h4 class="font-bold mb-2">Confirm Payment</h4>
                            <p>Are you sure you want to process the payment of <strong>{{ number_format($payment_amount, 2) }}</strong> for {{ count($selectedLoans) }} loan(s)?</p>
                            <div class="mt-4 flex gap-2">
                                <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" wire:click="store()">Yes, Process Payment</button>
                                <button class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600" wire:click="cancelConfirmation()">Cancel</button>
                            </div>
                        </div>
                    @endif

                    <form>
                        <div class="grid grid-cols-3 gap-3 mb-4">
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
                                <label class="block text-xs font-medium text-gray-600 mb-1">Total Payment Amount</label>
                                <input type="text" class="w-full px-2 py-1.5 bg-gray-100 border border-gray-300 rounded text-sm font-bold" value="{{ number_format($payment_amount, 2) }}" readonly>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Selected Loans Details</label>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-2 text-left font-medium text-gray-600">Member</th>
                                            <th class="px-2 py-2 text-left font-medium text-gray-600">Loan Scheme</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-600">ROI %</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-600">Balance</th>
                                            <th class="px-2 py-2 text-center font-medium text-gray-600">Days</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-600">Interest</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-600">Others</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-600 w-32">Principal</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-600">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($calculatedPayments as $loanId => $calc)
                                            <tr>
                                                <td class="px-2 py-2">{{ $calc['member_name'] }}</td>
                                                <td class="px-2 py-2">{{ $calc['scheme_name'] }}</td>
                                                <td class="px-2 py-2 text-right">{{ number_format($calc['roi'], 2) }}%</td>
                                                <td class="px-2 py-2 text-right font-medium">{{ number_format($calc['balance'], 2) }}</td>
                                                <td class="px-2 py-2 text-center">{{ $calc['days'] }}</td>
                                                <td class="px-2 py-2 text-right text-blue-600">{{ number_format($calc['interest'], 2) }}</td>
                                                <td class="px-2 py-2 text-right text-gray-600">{{ number_format($calc['others'], 2) }}</td>
                                                <td class="px-2 py-2 text-right">
                                                    <input type="number" step="0.01"
                                                        class="w-full px-2 py-1 border border-blue-300 rounded text-right text-xs focus:outline-none focus:border-blue-500 bg-blue-50"
                                                        wire:model.lazy="principal_amounts.{{ $loanId }}"
                                                        placeholder="0.00">
                                                </td>
                                                <td class="px-2 py-2 text-right font-bold text-green-700">{{ number_format($calc['total'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 font-bold">
                                        <tr>
                                            <td colspan="8" class="px-2 py-2 text-right">Grand Total:</td>
                                            <td class="px-2 py-2 text-right text-lg text-green-800">{{ number_format($payment_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
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
                    @if(!$confirmingPayment)
                        <button class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="store()">Submit Payment</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
