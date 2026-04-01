<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec16ShfundMemberMasterDb;
use App\Models\Ec16ShfundMemberSpecification;
use App\Models\Member;
use App\Models\Ec05LoanAssign;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec16ShfundMemberMasterDbComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $master_db_id = null;
    public $member_id = '';
    public $loan_assign_id = '';
    public $name = '';
    public $description = '';
    public $share_operational_amount = '';
    public $share_operational_type = '';
    public $share_operational_date = '';
    public $share_current_balance = '';
    public $status = 'draft';
    public $is_finalized = true;
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $members = [];
    public $loanAssigns = [];
    public $selectedSpecs = [];
    public $specParticular = '';
    public $specParticularValue = '';

    protected function rules()
    {
        return [
            'member_id' => 'nullable|integer|min:0',
            'loan_assign_id' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'share_operational_amount' => 'nullable|numeric|min:0',
            'share_operational_type' => 'nullable|in:deposit,withdrawal',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function render()
    {
        $masterDbs = Ec16ShfundMemberMasterDb::with(['member', 'loanAssign'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec16-shfund-member-master-db-comp', compact('masterDbs'));
    }

    public function mount()
    {
        $this->members = Member::where('is_active', true)->orderBy('name')->get();
        $this->loanAssigns = Ec05LoanAssign::where('is_active', true)->orderBy('name')->get();
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
        $this->master_db_id = null;
        $this->member_id = '';
        $this->loan_assign_id = '';
        $this->name = '';
        $this->description = '';
        $this->share_operational_amount = '';
        $this->share_operational_type = '';
        $this->share_operational_date = '';
        $this->share_current_balance = '';
        $this->status = 'draft';
        $this->is_finalized = true;
        $this->is_active = true;
        $this->remarks = '';
        $this->selectedSpecs = [];
        $this->specParticular = '';
        $this->specParticularValue = '';
        $this->confirmingDelete = null;
    }

    public function addSpecification()
    {
        if (!empty($this->specParticular)) {
            $this->selectedSpecs[] = [
                'particular' => $this->specParticular,
                'particular_value' => floatval($this->specParticularValue),
                'effected_on' => date('Y-m-d'),
                'status' => 'draft',
            ];
            $this->specParticular = '';
            $this->specParticularValue = '';
        }
    }

    public function removeSpecification($index)
    {
        if (isset($this->selectedSpecs[$index])) {
            unset($this->selectedSpecs[$index]);
            $this->selectedSpecs = array_values($this->selectedSpecs);
        }
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        $masterDb = Ec16ShfundMemberMasterDb::updateOrCreate(['id' => $this->master_db_id], array_merge($validated, [
            'member_id' => $this->member_id ?: null,
            'loan_assign_id' => $this->loan_assign_id ?: null,
            'share_operational_amount' => floatval($this->share_operational_amount),
            'share_operational_type' => $this->share_operational_type,
            'share_operational_date' => $this->share_operational_date ?: now(),
            'share_current_balance' => floatval($this->share_current_balance),
            'is_finalized' => $this->is_finalized,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        Ec16ShfundMemberSpecification::where('shfund_member_master_db_id', $masterDb->id)->delete();

        foreach ($this->selectedSpecs as $spec) {
            Ec16ShfundMemberSpecification::create([
                'shfund_member_master_db_id' => $masterDb->id,
                'name' => $spec['particular'] ?? 'Specification',
                'particular' => $spec['particular'] ?? '',
                'particular_value' => floatval($spec['particular_value'] ?? 0),
                'effected_on' => $spec['effected_on'] ?? date('Y-m-d'),
                'status' => $spec['status'] ?? 'draft',
                'user_id' => $userId,
            ]);
        }

        session()->flash('message', $this->master_db_id ? 'Record Updated Successfully.' : 'Record Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $masterDb = Ec16ShfundMemberMasterDb::with('specifications')->findOrFail($id);
        
        $this->master_db_id = $id;
        $this->member_id = $masterDb->member_id ?? '';
        $this->loan_assign_id = $masterDb->loan_assign_id ?? '';
        $this->name = $masterDb->name;
        $this->description = $masterDb->description ?? '';
        $this->share_operational_amount = $masterDb->share_operational_amount;
        $this->share_operational_type = $masterDb->share_operational_type;
        $this->share_operational_date = $masterDb->share_operational_date ? date('Y-m-d', strtotime($masterDb->share_operational_date)) : '';
        $this->share_current_balance = $masterDb->share_current_balance;
        $this->status = $masterDb->status;
        $this->is_finalized = $masterDb->is_finalized;
        $this->is_active = $masterDb->is_active;
        $this->remarks = $masterDb->remarks ?? '';

        $this->selectedSpecs = $masterDb->specifications->map(function ($spec) {
            return [
                'particular' => $spec->particular ?? $spec->name,
                'particular_value' => $spec->particular_value,
                'effected_on' => $spec->effected_on ? date('Y-m-d', strtotime($spec->effected_on)) : date('Y-m-d'),
                'status' => $spec->status,
            ];
        })->toArray();

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
        Ec16ShfundMemberMasterDb::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Record Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
