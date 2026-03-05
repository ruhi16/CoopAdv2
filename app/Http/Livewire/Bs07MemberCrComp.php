<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Member;
use App\Models\MemberDb;
use Livewire\WithPagination;

class Bs07MemberCrComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $member_id;
    public $member_db_id = '';
    public $name = '';
    public $description = '';
    public $order_index = '';
    public $join_date = '';
    public $exit_date = '';
    public $financial_year = '';
    public $is_default = false;
    public $is_active = true;
    public $remarks = '';
    public $status = '';

    public $isOpen = 0;
    public $search = '';
    public $confirmingDelete = null;
    public $availableFinancialYears = [];

    protected function rules()
    {
        return [
            'member_db_id' => 'nullable|integer',
            'name' => 'required|string|max:255|unique:members,name,' . $this->member_id,
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'join_date' => 'nullable|date',
            'exit_date' => 'nullable|date',
            'financial_year' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function mount()
    {
        $this->loadFinancialYears();
    }

    public function loadFinancialYears()
    {
        $years = MemberDb::getAvailableFinancialYears();
        $this->availableFinancialYears = $years->toArray();
        
        if (empty($this->availableFinancialYears)) {
            $currentYear = date('Y');
            $currentMonth = date('m');
            if ($currentMonth >= 4) {
                $this->financial_year = $currentYear . '-' . ($currentYear + 1);
            } else {
                $this->financial_year = ($currentYear - 1) . '-' . $currentYear;
            }
        }
    }

    public function render()
    {
        $memberDbs = MemberDb::withoutGlobalScopes()
            ->orderBy('name')
            ->get();

        $members = Member::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('financial_year', 'like', '%' . $this->search . '%');
            })
            ->when($this->financial_year, function ($query) {
                $query->where('financial_year', $this->financial_year);
            })
            ->orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.bs07-member-cr-comp', compact('members', 'memberDbs'));
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
        $this->member_id = '';
        $this->member_db_id = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->join_date = '';
        $this->exit_date = '';
        $this->loadFinancialYears();
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Member::updateOrCreate(['id' => $this->member_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        session()->flash('message', $this->member_id ? 'Member Updated Successfully.' : 'Member Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $member = Member::findOrFail($id);
        $this->member_id = $id;
        $this->member_db_id = $member->member_db_id;
        $this->name = $member->name;
        $this->description = $member->description;
        $this->order_index = $member->order_index;
        $this->join_date = $member->join_date;
        $this->exit_date = $member->exit_date;
        $this->financial_year = $member->financial_year;
        $this->is_default = $member->is_default;
        $this->is_active = $member->is_active;
        $this->remarks = $member->remarks;
        $this->status = $member->status;

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
        Member::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Member Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedMemberDbId($value)
    {
        if ($value) {
            $memberDb = MemberDb::withoutGlobalScopes()->find($value);
            if ($memberDb && $memberDb->doj) {
                $year = date('Y', strtotime($memberDb->doj));
                $month = date('m', strtotime($memberDb->doj));
                if ($month >= 4) {
                    $this->financial_year = $year . '-' . ($year + 1);
                } else {
                    $this->financial_year = ($year - 1) . '-' . $year;
                }
            }
        }
    }
}
