<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MemberDb;
use App\Models\MemberType;
use Livewire\WithPagination;

class Bs06MemberDbComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $member_db_id;
    public $member_type_id = '';
    public $name = '';
    public $name_short = '';
    public $father_name = '';
    public $school_designation = '';
    public $email = '';
    public $phone = '';
    public $mobile = '';
    public $address = '';
    public $dob = '';
    public $doj = '';
    public $dor = '';
    public $gender = '';
    public $nationality = 'Indian';
    public $religion = '';
    public $marital_status = '';
    public $blood_group = '';
    public $pan_no = '';
    public $aadhar_no = '';
    public $voter_id_no = '';
    public $account_bank = '';
    public $account_branch = '';
    public $account_no = '';
    public $account_ifsc = '';
    public $account_micr = '';
    public $account_customer_id = '';
    public $account_holder_name = '';
    public $is_default = false;
    public $is_active = true;

    public $isOpen = 0;
    public $search = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'member_type_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'name_short' => 'nullable|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'school_designation' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'dob' => 'nullable|date',
            'doj' => 'nullable|date',
            'dor' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'nationality' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'pan_no' => 'nullable|string|max:10',
            'aadhar_no' => 'nullable|string|max:12',
            'voter_id_no' => 'nullable|string|max:20',
            'account_bank' => 'nullable|string|max:255',
            'account_branch' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'account_ifsc' => 'nullable|string|max:11',
            'account_micr' => 'nullable|string|max:10',
            'account_customer_id' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function render()
    {
        $memberTypes = MemberType::orderBy('name')->get();

        $memberDbs = MemberDb::withoutGlobalScopes()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('name_short', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('mobile', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.bs06-member-db-comp', compact('memberDbs', 'memberTypes'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->member_db_id = '';
        $this->member_type_id = '';
        $this->name = '';
        $this->name_short = '';
        $this->father_name = '';
        $this->school_designation = '';
        $this->email = '';
        $this->phone = '';
        $this->mobile = '';
        $this->address = '';
        $this->dob = '';
        $this->doj = '';
        $this->dor = '';
        $this->gender = '';
        $this->nationality = 'Indian';
        $this->religion = '';
        $this->marital_status = '';
        $this->blood_group = '';
        $this->pan_no = '';
        $this->aadhar_no = '';
        $this->voter_id_no = '';
        $this->account_bank = '';
        $this->account_branch = '';
        $this->account_no = '';
        $this->account_ifsc = '';
        $this->account_micr = '';
        $this->account_customer_id = '';
        $this->account_holder_name = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        MemberDb::withoutGlobalScopes()->updateOrCreate(['id' => $this->member_db_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        session()->flash('message', $this->member_db_id ? 'Member Updated Successfully.' : 'Member Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $memberDb = MemberDb::withoutGlobalScopes()->findOrFail($id);
        $this->member_db_id = $id;
        $this->member_type_id = $memberDb->member_type_id;
        $this->name = $memberDb->name;
        $this->name_short = $memberDb->name_short;
        $this->father_name = $memberDb->father_name;
        $this->school_designation = $memberDb->school_designation;
        $this->email = $memberDb->email;
        $this->phone = $memberDb->phone;
        $this->mobile = $memberDb->mobile;
        $this->address = $memberDb->address;
        $this->dob = $memberDb->dob;
        $this->doj = $memberDb->doj;
        $this->dor = $memberDb->dor;
        $this->gender = $memberDb->gender;
        $this->nationality = $memberDb->nationality;
        $this->religion = $memberDb->religion;
        $this->marital_status = $memberDb->marital_status;
        $this->blood_group = $memberDb->blood_group;
        $this->pan_no = $memberDb->pan_no;
        $this->aadhar_no = $memberDb->aadhar_no;
        $this->voter_id_no = $memberDb->voter_id_no;
        $this->account_bank = $memberDb->account_bank;
        $this->account_branch = $memberDb->account_branch;
        $this->account_no = $memberDb->account_no;
        $this->account_ifsc = $memberDb->account_ifsc;
        $this->account_micr = $memberDb->account_micr;
        $this->account_customer_id = $memberDb->account_customer_id;
        $this->account_holder_name = $memberDb->account_holder_name;
        $this->is_default = $memberDb->is_default;
        $this->is_active = $memberDb->is_active;

        $this->resetValidation();
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = null;
    }

    public function delete($id)
    {
        MemberDb::withoutGlobalScopes()->find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Member Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
