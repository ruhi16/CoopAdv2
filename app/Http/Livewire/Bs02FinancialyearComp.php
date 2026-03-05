<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Financialyear;
use Livewire\WithPagination;

class Bs02FinancialyearComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $financialyear_id;
    public $name = '';
    public $description = '';
    public $order_index = '';
    public $start_date = '';
    public $end_date = '';
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
            'name' => 'required|string|max:255|unique:financialyears,name,' . $this->financialyear_id,
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $financialyears = Financialyear::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.bs02-financialyear-comp', compact('financialyears'));
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
        $this->financialyear_id = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Financialyear::updateOrCreate(['id' => $this->financialyear_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        session()->flash('message', $this->financialyear_id ? 'Financial Year Updated Successfully.' : 'Financial Year Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $financialyear = Financialyear::findOrFail($id);
        $this->financialyear_id = $id;
        $this->name = $financialyear->name;
        $this->description = $financialyear->description;
        $this->order_index = $financialyear->order_index;
        $this->start_date = $financialyear->start_date;
        $this->end_date = $financialyear->end_date;
        $this->is_default = $financialyear->is_default;
        $this->is_active = $financialyear->is_active;
        $this->remarks = $financialyear->remarks;
        $this->status = $financialyear->status;

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
        Financialyear::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Financial Year Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
