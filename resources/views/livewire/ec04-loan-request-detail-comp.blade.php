<div class="p-4">
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Loan Request Details</h3>
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
                    <select class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_request_id">
                        <option value="">All Loan Requests</option>
                        @foreach($loanRequests as $req)
                            <option value="{{ $req->id }}">{{ $req->member->name ?? 'N/A' }} - {{ $req->id }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">Add Loan Detail</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">ID</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Loan Request</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Feature</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Value</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Condition</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($details as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2">{{ $detail->id }}</td>
                                <td class="px-2 py-2">{{ $detail->loanRequest->member->name ?? '-' }} ({{ $detail->loan_request_id }})</td>
                                <td class="px-2 py-2">{{ $detail->loan_scheme_feature_name }}</td>
                                <td class="px-2 py-2">{{ $detail->loan_scheme_feature_value }}</td>
                                <td class="px-2 py-2">{{ $detail->loan_scheme_feature_condition }}</td>
                                <td class="px-2 py-2">
                                    @if($detail->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $detail->id }})">Edit</button>
                                    @if ($confirmingDelete === $detail->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $detail->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $detail->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-2 py-4 text-center text-gray-500">No loan details found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $details->links() }}
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">{{ $detail_id ? 'Edit Loan Detail' : 'Add Loan Detail' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Request <span class="text-red-500">*</span></label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="loan_request_id">
                                    <option value="">Select Loan Request</option>
                                    @foreach($loanRequests as $req)
                                        <option value="{{ $req->id }}">{{ $req->member->name ?? 'N/A' }} - {{ $req->id }}</option>
                                    @endforeach
                                </select>
                                @error('loan_request_id') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        
                        @if(count($selectedSchemeDetails) > 0)
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-600 mb-2">Scheme Details (Auto-populated from Loan Scheme)</label>
                            <div class="border border-gray-200 rounded max-h-60 overflow-y-auto">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Feature</th>
                                            <th class="px-2 py-1 text-left">Value</th>
                                            <th class="px-2 py-1 text-left">Condition</th>
                                            <th class="px-2 py-1 text-center">Active</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedSchemeDetails as $index => $detail)
                                            <tr class="border-t border-gray-100">
                                                <td class="px-2 py-1">{{ $detail['loan_scheme_feature_name'] }}</td>
                                                <td class="px-2 py-1">
                                                    <input type="text" class="w-full px-1 py-0.5 border border-gray-300 rounded text-xs" wire:model="selectedSchemeDetails.{{ $index }}.loan_scheme_feature_value">
                                                </td>
                                                <td class="px-2 py-1">{{ $detail['loan_scheme_feature_condition'] }}</td>
                                                <td class="px-2 py-1 text-center">
                                                    <input type="checkbox" class="w-3 h-3" wire:model="selectedSchemeDetails.{{ $index }}.is_active">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                            <textarea class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="remarks" placeholder="Remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $detail_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
