<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec21BankLoanSchemaParticular;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec21BankLoanSchemeSpecificationParticularComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $particular_id = null;
    public $name = '';
    public $description = '';
    public $is_optional = false;
    public $status = 'draft';
    public $is_finalized = true;
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function render()
    {
        $particulars = Ec21BankLoanSchemaParticular::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec21-bank-loan-scheme-specification-particular-comp', compact('particulars'));
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
        $this->particular_id = null;
        $this->name = '';
        $this->description = '';
        $this->is_optional = false;
        $this->status = 'draft';
        $this->is_finalized = true;
        $this->is_active = true;
        $this->remarks = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        Ec21BankLoanSchemaParticular::updateOrCreate(['id' => $this->particular_id], array_merge($validated, [
            'is_optional' => $this->is_optional,
            'is_finalized' => $this->is_finalized,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        session()->flash('message', $this->particular_id ? 'Particular Updated Successfully.' : 'Particular Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $particular = Ec21BankLoanSchemaParticular::findOrFail($id);
        $this->particular_id = $id;
        $this->name = $particular->name;
        $this->description = $particular->description;
        $this->is_optional = $particular->is_optional;
        $this->status = $particular->status;
        $this->is_finalized = $particular->is_finalized;
        $this->is_active = $particular->is_active;
        $this->remarks = $particular->remarks;

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
        Ec21BankLoanSchemaParticular::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Particular Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
