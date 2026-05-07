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
    public $loan_scheme_feature_type = '';
    public $loan_scheme_feature_value_type = '';
    public $loan_scheme_feature_value = '';
    public $loan_scheme_feature_mandate = '';
    public $loan_scheme_feature_condition = '';
    public $name = '';
    public $description = '';
    public $order_index = '';
    public $is_optional = false;
    public $is_default = false;
    public $is_active = true;
    public $created_by = 0;
    public $approved_by = 0;
    public $school_id = 0;
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
            'loan_scheme_feature_type' => 'nullable|string|max:100',
            'loan_scheme_feature_value_type' => 'nullable|string|max:50',
            'loan_scheme_feature_value' => 'nullable|string|max:255',
            'loan_scheme_feature_mandate' => 'nullable|string|max:255',
            'loan_scheme_feature_condition' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'is_optional' => 'nullable|boolean',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
            'created_by' => 'nullable|integer',
            'approved_by' => 'nullable|integer',
            'school_id' => 'nullable|integer',
        ];
    }

    public function render()
    {
        $loanSchemes = Ec01LoanScheme::orderBy('name')->get();
        $loanSchemeFeatures = Ec02LoanSchemeFeature::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();

        $details = Ec02LoanSchemeDetail::with(['loanScheme', 'loanSchemeFeature', 'createdBy', 'approvedBy'])
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

        return view('livewire.ec02-loan-scheme-detail-comp', compact('details', 'loanSchemes', 'loanSchemeFeatures', 'users'));
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
        $this->loan_scheme_feature_type = '';
        $this->loan_scheme_feature_value_type = '';
        $this->loan_scheme_feature_value = '';
        $this->loan_scheme_feature_mandate = '';
        $this->loan_scheme_feature_condition = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->is_optional = false;
        $this->is_default = false;
        $this->is_active = true;
        $this->created_by = auth()->id() ?? 0;
        $this->approved_by = 0;
        $this->school_id = 0;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        if ($this->loan_scheme_id && $this->loan_scheme_feature_id) {
            $query = Ec02LoanSchemeDetail::where('loan_scheme_id', $this->loan_scheme_id)
                ->where('loan_scheme_feature_id', $this->loan_scheme_feature_id);

            if ($this->detail_id) {
                $query->where('id', '!=', $this->detail_id);
            }

            $query->where('is_active', true)
                ->update(['is_active' => false]);
        }

        Ec02LoanSchemeDetail::updateOrCreate(['id' => $this->detail_id], array_merge($validated, [
            'is_optional' => $this->is_optional,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by ?: (auth()->id() ?? 0),
            'approved_by' => $this->approved_by ?? 0,
            'school_id' => 0,
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
        $this->loan_scheme_feature_type = $detail->loan_scheme_feature_type;
        $this->loan_scheme_feature_value_type = $detail->loan_scheme_feature_value_type;
        $this->loan_scheme_feature_value = $detail->loan_scheme_feature_value;
        $this->loan_scheme_feature_mandate = $detail->loan_scheme_feature_mandate;
        $this->loan_scheme_feature_condition = $detail->loan_scheme_feature_condition;
        $this->name = $detail->name;
        $this->description = $detail->description;
        $this->order_index = $detail->order_index;
        $this->is_optional = $detail->is_optional;
        $this->is_default = $detail->is_default;
        $this->is_active = $detail->is_active;
        $this->created_by = $detail->created_by;
        $this->approved_by = $detail->approved_by;
        $this->school_id = $detail->school_id;
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
                $this->loan_scheme_feature_type = $feature->feature_type;
                $this->loan_scheme_feature_value_type = $feature->feature_value_type;
            }
        } else {
            $this->loan_scheme_feature_name = '';
            $this->loan_scheme_feature_type = '';
            $this->loan_scheme_feature_value_type = '';
        }
    }

    public function updatedLoanSchemeId($value)
    {
        $this->loan_scheme_feature_id = '';
        $this->loan_scheme_feature_name = '';
        $this->loan_scheme_feature_type = '';
        $this->loan_scheme_feature_value_type = '';
        $this->resetPage();
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
}
