<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec21BankLoanScheme;
use App\Models\Ec21BankLoanSchemeSpecification;
use App\Models\Ec21BankLoanSchemaParticular;
use App\Models\Ec20Bank;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec21BankLoanSchemeComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $scheme_id = null;
    public $name = '';
    public $description = '';
    public $bank_id = '';
    public $effected_on = '';
    public $status = 'running';
    public $is_finalized = true;
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $specifications = [];
    public $allParticulars = [];
    public $banks = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'bank_id' => 'required|integer|min:1',
            'effected_on' => 'nullable|date',
            'status' => 'required|in:running,completed,upcoming,suspended,cancelled',
        ];
    }

    public function render()
    {
        $schemes = Ec21BankLoanScheme::with(['bank'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec21-bank-loan-scheme-comp', compact('schemes'));
    }

    public function mount()
    {
        $this->banks = Ec20Bank::where('is_active', true)->orderBy('name')->get();
        $this->allParticulars = Ec21BankLoanSchemaParticular::where('is_active', true)->orderBy('name')->get();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->addSpecification();
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
        $this->scheme_id = null;
        $this->name = '';
        $this->description = '';
        $this->bank_id = '';
        $this->effected_on = '';
        $this->status = 'running';
        $this->is_finalized = true;
        $this->is_active = true;
        $this->remarks = '';
        $this->specifications = [];
        $this->confirmingDelete = null;
    }

    public function addSpecification()
    {
        $this->specifications[] = [
            'name' => '',
            'description' => '',
            'bank_loan_schema_particular_id' => '',
            'bank_loan_schema_particular_value' => '',
            'is_percent_on_current_balance' => true,
            'is_regular' => true,
            'effected_on' => date('Y-m-d'),
            'status' => 'running',
        ];
    }

    public function removeSpecification($index)
    {
        unset($this->specifications[$index]);
        $this->specifications = array_values($this->specifications);
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        $scheme = Ec21BankLoanScheme::updateOrCreate(['id' => $this->scheme_id], array_merge($validated, [
            'effected_on' => $this->effected_on,
            'is_finalized' => $this->is_finalized,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        Ec21BankLoanSchemeSpecification::where('bank_loan_scheme_id', $scheme->id)->delete();

        foreach ($this->specifications as $spec) {
            if (!empty($spec['bank_loan_schema_particular_id'])) {
                Ec21BankLoanSchemeSpecification::create([
                    'bank_loan_scheme_id' => $scheme->id,
                    'name' => $spec['name'],
                    'description' => $spec['description'] ?? '',
                    'bank_loan_schema_particular_id' => $spec['bank_loan_schema_particular_id'],
                    'bank_loan_schema_particular_value' => $spec['bank_loan_schema_particular_value'] ?? 0,
                    'is_percent_on_current_balance' => $spec['is_percent_on_current_balance'] ?? true,
                    'is_regular' => $spec['is_regular'] ?? true,
                    'effected_on' => $spec['effected_on'] ?? date('Y-m-d'),
                    'status' => $spec['status'] ?? 'running',
                    'user_id' => $userId,
                ]);
            }
        }

        session()->flash('message', $this->scheme_id ? 'Scheme Updated Successfully.' : 'Scheme Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $scheme = Ec21BankLoanScheme::with('specifications')->findOrFail($id);
        
        $this->scheme_id = $id;
        $this->name = $scheme->name;
        $this->description = $scheme->description;
        $this->bank_id = $scheme->bank_id;
        $this->effected_on = $scheme->effected_on;
        $this->status = $scheme->status;
        $this->is_finalized = $scheme->is_finalized;
        $this->is_active = $scheme->is_active;
        $this->remarks = $scheme->remarks;

        $this->specifications = $scheme->specifications->toArray();
        if (empty($this->specifications)) {
            $this->addSpecification();
        }

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
        Ec21BankLoanScheme::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Scheme Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
