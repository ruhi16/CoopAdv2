<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec17ShfundBankMasterDb;
use App\Models\Ec17ShfundBankSpecification;
use App\Models\Ec20Bank;
use App\Models\Ec05LoanAssign;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec17ShfundBankMasterDbComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $master_db_id = null;
    public $bank_id = '';
    public $loan_assign_id = '';
    public $name = '';
    public $description = '';
    public $bank_loan_previous_balnce = '';
    public $bank_share_previous_balnce = '';
    public $bank_share_operational_amount = '';
    public $bank_share_operational_type = '';
    public $bank_share_operational_date = '';
    public $bank_share_current_balnce = '';
    public $status = 'draft';
    public $is_finalized = true;
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $banks = [];
    public $loanAssigns = [];
    public $selectedSpecs = [];
    public $specParticular = '';
    public $specParticularValue = '';

    protected function rules()
    {
        return [
            'bank_id' => 'nullable|integer|min:0',
            'loan_assign_id' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'bank_loan_previous_balnce' => 'nullable|numeric',
            'bank_share_previous_balnce' => 'nullable|numeric',
            'bank_share_operational_amount' => 'nullable|numeric|min:0',
            'bank_share_operational_type' => 'nullable|in:deposit,withdrawal',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function render()
    {
        $masterDbs = Ec17ShfundBankMasterDb::with(['bank', 'loanAssign'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec17-shfund-bank-master-db-comp', compact('masterDbs'));
    }

    public function mount()
    {
        $this->banks = Ec20Bank::where('is_active', true)->orderBy('name')->get();
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
        $this->bank_id = '';
        $this->loan_assign_id = '';
        $this->name = '';
        $this->description = '';
        $this->bank_loan_previous_balnce = '';
        $this->bank_share_previous_balnce = '';
        $this->bank_share_operational_amount = '';
        $this->bank_share_operational_type = '';
        $this->bank_share_operational_date = '';
        $this->bank_share_current_balnce = '';
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

        $masterDb = Ec17ShfundBankMasterDb::updateOrCreate(['id' => $this->master_db_id], array_merge($validated, [
            'bank_id' => $this->bank_id ?: null,
            'loan_assign_id' => $this->loan_assign_id ?: null,
            'bank_loan_previous_balnce' => floatval($this->bank_loan_previous_balnce),
            'bank_share_previous_balnce' => floatval($this->bank_share_previous_balnce),
            'bank_share_operational_amount' => floatval($this->bank_share_operational_amount),
            'bank_share_operational_type' => $this->bank_share_operational_type,
            'bank_share_operational_date' => $this->bank_share_operational_date ?: now(),
            'bank_share_current_balnce' => floatval($this->bank_share_current_balnce),
            'is_finalized' => $this->is_finalized,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        Ec17ShfundBankSpecification::where('shfund_bank_master_db_id', $masterDb->id)->delete();

        foreach ($this->selectedSpecs as $spec) {
            Ec17ShfundBankSpecification::create([
                'shfund_bank_master_db_id' => $masterDb->id,
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
        $masterDb = Ec17ShfundBankMasterDb::with('specifications')->findOrFail($id);
        
        $this->master_db_id = $id;
        $this->bank_id = $masterDb->bank_id ?? '';
        $this->loan_assign_id = $masterDb->loan_assign_id ?? '';
        $this->name = $masterDb->name;
        $this->description = $masterDb->description ?? '';
        $this->bank_loan_previous_balnce = $masterDb->bank_loan_previous_balnce;
        $this->bank_share_previous_balnce = $masterDb->bank_share_previous_balnce;
        $this->bank_share_operational_amount = $masterDb->bank_share_operational_amount;
        $this->bank_share_operational_type = $masterDb->bank_share_operational_type;
        $this->bank_share_operational_date = $masterDb->bank_share_operational_date ? date('Y-m-d', strtotime($masterDb->bank_share_operational_date)) : '';
        $this->bank_share_current_balnce = $masterDb->bank_share_current_balnce;
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
        Ec17ShfundBankMasterDb::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Record Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
