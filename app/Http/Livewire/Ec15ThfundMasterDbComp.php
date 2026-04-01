<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec15ThfundMasterDb;
use App\Models\Ec15ThfundSpecification;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec15ThfundMasterDbComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $master_db_id = null;
    public $member_id = '';
    public $name = '';
    public $description = '';
    public $thfund_operational_amount = '';
    public $thfund_operational_type = '';
    public $thfund_operational_date = '';
    public $thfund_current_balance = '';
    public $start_at = '';
    public $end_at = '';
    public $no_of_months = '';
    public $status = 'draft';
    public $is_finalized = true;
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $members = [];
    public $selectedSpecs = [];
    public $specParticular = '';
    public $specParticularValue = '';

    protected function rules()
    {
        return [
            'member_id' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'thfund_operational_amount' => 'nullable|numeric|min:0',
            'thfund_operational_type' => 'nullable|in:deposit,withdrawal',
            'thfund_current_balance' => 'nullable|numeric',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'no_of_months' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function render()
    {
        $masterDbs = Ec15ThfundMasterDb::with(['member'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec15-thfund-master-db-comp', compact('masterDbs'));
    }

    public function mount()
    {
        $this->members = Member::where('is_active', true)->orderBy('name')->get();
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
        $this->name = '';
        $this->description = '';
        $this->thfund_operational_amount = '';
        $this->thfund_operational_type = '';
        $this->thfund_operational_date = '';
        $this->thfund_current_balance = '';
        $this->start_at = '';
        $this->end_at = '';
        $this->no_of_months = '';
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

        $masterDb = Ec15ThfundMasterDb::updateOrCreate(['id' => $this->master_db_id], array_merge($validated, [
            'member_id' => $this->member_id ?: null,
            'thfund_operational_amount' => floatval($this->thfund_operational_amount),
            'thfund_operational_type' => $this->thfund_operational_type,
            'thfund_operational_date' => $this->thfund_operational_date ?: date('Y-m-d'),
            'thfund_current_balance' => floatval($this->thfund_current_balance),
            'is_finalized' => $this->is_finalized,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        Ec15ThfundSpecification::where('thfund_master_db_id', $masterDb->id)->delete();

        foreach ($this->selectedSpecs as $spec) {
            Ec15ThfundSpecification::create([
                'thfund_master_db_id' => $masterDb->id,
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
        $masterDb = Ec15ThfundMasterDb::with('specifications')->findOrFail($id);
        
        $this->master_db_id = $id;
        $this->member_id = $masterDb->member_id ?? '';
        $this->name = $masterDb->name;
        $this->description = $masterDb->description ?? '';
        $this->thfund_operational_amount = $masterDb->thfund_operational_amount;
        $this->thfund_operational_type = $masterDb->thfund_operational_type;
        $this->thfund_operational_date = $masterDb->thfund_operational_date ? date('Y-m-d', strtotime($masterDb->thfund_operational_date)) : '';
        $this->thfund_current_balance = $masterDb->thfund_current_balance;
        $this->start_at = $masterDb->start_at ? date('Y-m-d', strtotime($masterDb->start_at)) : '';
        $this->end_at = $masterDb->end_at ? date('Y-m-d', strtotime($masterDb->end_at)) : '';
        $this->no_of_months = $masterDb->no_of_months;
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
        Ec15ThfundMasterDb::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Record Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
