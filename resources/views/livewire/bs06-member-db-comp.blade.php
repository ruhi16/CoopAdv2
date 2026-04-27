<div class="p-4">
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Member Database</h3>
        </div>
        <div class="p-4">
            @if (session()->has('message'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="flex justify-between mb-4 gap-2">
                <input type="text" class="px-3 py-2 border border-gray-300 rounded text-sm w-64 focus:outline-none focus:border-blue-500" placeholder="Search..." wire:model="search">
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="create()">Create Member</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">No.</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Short Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Mobile</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Email</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">DOJ</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Active</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($memberDbs as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    {{ $loop->iteration + ($memberDbs->currentPage() - 1) * $memberDbs->perPage() }}                                    
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $member->name }}({{ $member->name_short }})<br/>
                                    <span class="text-sm text-gray-500">Mob: {{ $member->mobile }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $member->account_bank }}: {{ $member->account_no }}<br/>
                                    <span class="text-sm text-gray-500">Ifsc: {{ $member->account_ifsc ?: 'N/A' }}</span>
                                </td>
                                <td class="px-3 py-2"></td>
                                <td class="px-3 py-2"></td>
                                <td class="px-3 py-2"></td>
                                <td class="px-3 py-2">
                                    @if($member->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <button class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 mr-1" wire:click="edit({{ $member->id }})">Edit</button>
                                    @if ($confirmingDelete === $member->id)
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mr-1" wire:click="delete({{ $member->id }})">Confirm</button>
                                        <button class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" wire:click="cancelDelete()">Cancel</button>
                                    @else
                                        <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700" wire:click="confirmDelete({{ $member->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">No members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex items-right justify-center gap-2 bg-gray-250"> 
                <button class="px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300 disabled:opacity-50" 
                        wire:click="previousPage" 
                        {{ $memberDbs->onFirstPage() ? 'disabled' : '' }}>
                    Previous
                </button>
                
                {{-- <div class="flex-1 flex justify-center">
                    {{ $memberDbs->links() }}
                </div> --}}
                
                <button class="px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300 disabled:opacity-50" 
                        wire:click="nextPage" 
                        {{ $memberDbs->onLastPage() ? 'disabled' : '' }}>
                    Next
                </button>
            </div>
        </div>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <h5 class="font-semibold text-gray-700">{{ $member_db_id ? 'Edit Member' : 'Create Member' }}</h5>
                    <button class="text-gray-500 hover:text-gray-700" wire:click="closeModal()">
                        <span class="text-xl">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <form>
                        <div class="text-xs font-semibold text-gray-700 mb-2">Personal Information</div>
                        <div class="grid grid-cols-4 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name" placeholder="Full Name">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Short Name</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="name_short" placeholder="Short Name">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Father's Name</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="father_name" placeholder="Father Name">
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Gender</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="gender">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">DOB</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="dob">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Nationality</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="nationality" placeholder="Indian">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Religion</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="religion" placeholder="Religion">
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Marital Status</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="marital_status">
                                    <option value="">Select</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Blood Group</label>
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="blood_group">
                                    <option value="">Select</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mobile</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="mobile" placeholder="Mobile">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="phone" placeholder="Phone">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                                <input type="email" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="email" placeholder="Email">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="address" placeholder="Address">
                            </div>
                        </div>

                        <div class="text-xs font-semibold text-gray-700 mb-2 mt-4">Employment Details</div>
                        <div class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Designation</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="school_designation" placeholder="Designation">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Teacher type<span class="text-red-500">*</span></label>                                
                                <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="member_type_id">
                                    <option value="">Select</option>
                                    @foreach($memberTypes as $memberType)
                                        <option value="{{ $memberType->id }}">{{ $memberType->name }}</option>
                                    @endforeach
                                </select>
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date of Join</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="doj">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date of Relieve</label>
                                <input type="date" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="dor">
                            </div>
                        </div>

                        <div class="text-xs font-semibold text-gray-700 mb-2 mt-4">ID Proofs</div>
                        <div class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">PAN No.</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="pan_no" placeholder="PAN No.">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Aadhar No.</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="aadhar_no" placeholder="Aadhar No.">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Voter ID</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="voter_id_no" placeholder="Voter ID">
                            </div>
                        </div>
                        <div class="flex gap-4 mt-3">
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2" wire:model="is_active" id="is_active">
                                <label for="is_active" class="text-sm text-gray-700">Active</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2" wire:model="is_default" id="is_default">
                                <label for="is_default" class="text-sm text-gray-700">Default</label>
                            </div>
                        </div>

                        <div class="text-xs font-semibold text-gray-700 mb-2 mt-4">Bank Details</div>
                        <div class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Bank Name</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="account_bank" placeholder="Bank Name">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Branch</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="account_branch" placeholder="Branch">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account No.</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="account_no" placeholder="Account No.">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">IFSC Code</label>
                                <input type="text" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500" wire:model="account_ifsc" placeholder="IFSC Code">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0 bg-white">
                    <button class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600" wire:click="closeModal()">Close</button>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" wire:click="store()">{{ $member_db_id ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
