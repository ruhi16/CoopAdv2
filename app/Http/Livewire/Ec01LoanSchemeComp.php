<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec01LoanScheme;
use Livewire\WithPagination;

class Ec01LoanSchemeComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $loan_scheme_id;
    public $name = '';
    public $description = '';
    public $order_index = '';
    public $with_effect_from = '';
    public $with_effect_to = '';
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
            'name' => 'required|string|max:255|unique:ec01_loan_schemes,name,' . $this->loan_scheme_id,
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'with_effect_from' => 'nullable|date',
            'with_effect_to' => 'nullable|date|after:with_effect_from',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $loanSchemes = Ec01LoanScheme::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec01-loan-scheme-comp', compact('loanSchemes'));
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
        $this->loan_scheme_id = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->with_effect_from = '';
        $this->with_effect_to = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Ec01LoanScheme::updateOrCreate(['id' => $this->loan_scheme_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        session()->flash('message', $this->loan_scheme_id ? 'Loan Scheme Updated Successfully.' : 'Loan Scheme Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $loanScheme = Ec01LoanScheme::findOrFail($id);
        $this->loan_scheme_id = $id;
        $this->name = $loanScheme->name;
        $this->description = $loanScheme->description;
        $this->order_index = $loanScheme->order_index;
        $this->with_effect_from = $loanScheme->with_effect_from;
        $this->with_effect_to = $loanScheme->with_effect_to;
        $this->is_default = $loanScheme->is_default;
        $this->is_active = $loanScheme->is_active;
        $this->remarks = $loanScheme->remarks;
        $this->status = $loanScheme->status;

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
        Ec01LoanScheme::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Loan Scheme Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
