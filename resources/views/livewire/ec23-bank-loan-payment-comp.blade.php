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
            <h3 class="text-lg font-semibold text-gray-700">Bank Loan Payments</h3>
            @if(count($selectedLoans) > 0)
                <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" wire:click="openPaymentModal()">
                    Pay Selected ({{ count($selectedLoans) }})
                </button>
            @endif
        </div>
        <div class="p-4">
            <div class="flex justify-between mb-4 gap-2">
                <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600 w-10">
                                <input type="checkbox" class="w-4 h-4" 
                                    @if(count($borrowedLoans) > 0 && count($selectedLoans) == count($borrowedLoans->pluck('id'))) checked @endif
                                    wire:click="selectAll()">
                            </th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Scheme</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Loan Amount</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Paid</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Balance</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Installment</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($borrowedLoans as $loan)
                            <tr class="hover:bg-gray-50 {{ in_array($loan->id, $selectedLoans) ? 'bg-blue-50' : '' }}">
                                <td class="px-3 py-2">
                                    <input type="checkbox" class="w-4 h-4" 
                                        {{ in_array($loan->id, $selectedLoans) ? 'checked' : '' }}
                                        wire:click="toggleLoanSelection({{ $loan->id }})">
                                </td>
                                <td class="px-3 py-2">{{ $loop->iteration + ($borrowedLoans->currentPage() - 1) * $borrowedLoans->perPage() }}</td>
                                <td class="px-3 py-2">{{ $loan->name }}</td>
                                <td class="px-3 py-2">{{ $loan->loanScheme->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ number_format($loan->loan_borrowed_amount, 2) }}</td>
                                <td class="px-3 py-2">{{ number_format($loan->total_paid, 2) }}</td>
                                <td class="px-3 py-2 font-medium {{ $loan->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($loan->balance, 2) }}
                                </td>
                                <td class="px-3 py-2">{{ number_format($loan->installment_amount, 2) }} x {{ $loan->no_of_installments }}</td>
                                <td class="px-3 py-2">
                                    @switch($loan->status)
                                        @case('running')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Running</span>
                                            @break
                                        @case('completed')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Completed</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 text-gray-700 bg-gray-100 text-xs rounded">{{ $loan->status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-gray-500">No pending loans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $borrowedLoans->links() }}
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
                        {{-- Selected Loans Summary --}}
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Selected Loans</label>
                            <div class="overflow-x-auto border border-gray-200 rounded">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-medium text-gray-600">Loan Name</th>
                                            <th class="px-2 py-1 text-right font-medium text-gray-600">Balance</th>
                                            <th class="px-2 py-1 text-right font-medium text-gray-600">EMI</th>
                                            <th class="px-2 py-1 text-right font-medium text-gray-600">Pay Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($loanDetails as $loan)
                                            <tr>
                                                <td class="px-2 py-1">{{ $loan['name'] }}</td>
                                                <td class="px-2 py-1 text-right">{{ number_format($loan['balance'], 2) }}</td>
                                                <td class="px-2 py-1 text-right">{{ number_format($loan['installment_amount'], 2) }}</td>
                                                <td class="px-2 py-1 text-right font-medium">
                                                    {{ number_format($calculatedPayment[$loan['id']]['pay_amount'] ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Payment Details --}}
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
