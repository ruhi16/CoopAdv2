<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec02LoanSchemeDetail;
use App\Models\Ec01LoanScheme;
use App\Models\Ec02LoanSchemeFeature;
use Livewire\WithPagination;

class Ec02LoanSchemeDetailComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $detail_id;
    public $loan_scheme_id = '';
    public $loan_scheme_feature_id = '';
    public $loan_scheme_feature_name = '';
    public $loan_scheme_feature_value = '';
    public $loan_scheme_feature_condition = '';
    public $name = '';
    public $description = '';
    public $order_index = '';
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
            'loan_scheme_feature_id' => 'nullable|integer',
            'loan_scheme_feature_name' => 'nullable|string|max:255',
            'loan_scheme_feature_value' => 'nullable|numeric',
            'loan_scheme_feature_condition' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $loanSchemes = Ec01LoanScheme::orderBy('name')->get();
        $loanSchemeFeatures = Ec02LoanSchemeFeature::orderBy('name')->get();

        $details = Ec02LoanSchemeDetail::with(['loanScheme', 'loanSchemeFeature'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('loan_scheme_feature_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->loan_scheme_id, function ($query) {
                $query->where('loan_scheme_id', $this->loan_scheme_id);
            })
            ->orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec02-loan-scheme-detail-comp', compact('details', 'loanSchemes', 'loanSchemeFeatures'));
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
        $this->detail_id = '';
        $this->loan_scheme_id = '';
        $this->loan_scheme_feature_id = '';
        $this->loan_scheme_feature_name = '';
        $this->loan_scheme_feature_value = '';
        $this->loan_scheme_feature_condition = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Ec02LoanSchemeDetail::updateOrCreate(['id' => $this->detail_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        session()->flash('message', $this->detail_id ? 'Detail Updated Successfully.' : 'Detail Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $detail = Ec02LoanSchemeDetail::findOrFail($id);
        $this->detail_id = $id;
        $this->loan_scheme_id = $detail->loan_scheme_id;
        $this->loan_scheme_feature_id = $detail->loan_scheme_feature_id;
        $this->loan_scheme_feature_name = $detail->loan_scheme_feature_name;
        $this->loan_scheme_feature_value = $detail->loan_scheme_feature_value;
        $this->loan_scheme_feature_condition = $detail->loan_scheme_feature_condition;
        $this->name = $detail->name;
        $this->description = $detail->description;
        $this->order_index = $detail->order_index;
        $this->is_default = $detail->is_default;
        $this->is_active = $detail->is_active;
        $this->remarks = $detail->remarks;
        $this->status = $detail->status;

        $this->resetValidation();
        $this->openModal();
    }

    public function updatedLoanSchemeFeatureId($value)
    {
        if ($value) {
            $feature = Ec02LoanSchemeFeature::find($value);
            if ($feature) {
                $this->loan_scheme_feature_name = $feature->name;
            }
        }
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
        Ec02LoanSchemeDetail::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Detail Deleted Successfully.');
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
