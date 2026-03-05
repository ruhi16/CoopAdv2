<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec02LoanSchemeFeature;
use App\Models\Ec01LoanScheme;
use Livewire\WithPagination;

class Ec02LoanSchemeFeatureComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $feature_id;
    public $loan_scheme_id = '';
    public $name = '';
    public $description = '';
    public $order_index = '';
    public $feature_type = '';
    public $feature_value_type = '';
    public $is_default = false;
    public $is_active = true;
    public $remarks = '';
    public $status = '';

    public $isOpen = 0;
    public $search = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'loan_scheme_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'feature_type' => 'nullable|string|max:100',
            'feature_value_type' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $loanSchemes = Ec01LoanScheme::orderBy('name')->get();

        $features = Ec02LoanSchemeFeature::with('loanScheme')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('feature_type', 'like', '%' . $this->search . '%');
            })
            ->when($this->loan_scheme_id, function ($query) {
                $query->where('loan_scheme_id', $this->loan_scheme_id);
            })
            ->orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec02-loan-scheme-feature-comp', compact('features', 'loanSchemes'));
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
        $this->feature_id = '';
        $this->loan_scheme_id = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->feature_type = '';
        $this->feature_value_type = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Ec02LoanSchemeFeature::updateOrCreate(['id' => $this->feature_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        session()->flash('message', $this->feature_id ? 'Feature Updated Successfully.' : 'Feature Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $feature = Ec02LoanSchemeFeature::findOrFail($id);
        $this->feature_id = $id;
        $this->loan_scheme_id = $feature->loan_scheme_id;
        $this->name = $feature->name;
        $this->description = $feature->description;
        $this->order_index = $feature->order_index;
        $this->feature_type = $feature->feature_type;
        $this->feature_value_type = $feature->feature_value_type;
        $this->is_default = $feature->is_default;
        $this->is_active = $feature->is_active;
        $this->remarks = $feature->remarks;
        $this->status = $feature->status;

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
        Ec02LoanSchemeFeature::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Feature Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedLoanSchemeId()
    {
        $this->resetPage();
    }
}
