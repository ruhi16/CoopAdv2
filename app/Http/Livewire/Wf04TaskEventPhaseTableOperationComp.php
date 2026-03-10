<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf04TaskEventPhaseTableOperation;
use Livewire\WithPagination;

class Wf04TaskEventPhaseTableOperationComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $table_operation_id = null;
    public $name = '';
    public $description = '';
    public $school_id = '';
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $tableOperations = Wf04TaskEventPhaseTableOperation::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf04-task-event-phase-table-operation-comp', compact('tableOperations'));
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
        $this->table_operation_id = null;
        $this->name = '';
        $this->description = '';
        $this->school_id = '';
        $this->is_active = true;
        $this->remarks = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Wf04TaskEventPhaseTableOperation::updateOrCreate(['id' => $this->table_operation_id], array_merge($validated, [
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->table_operation_id ? 'Table Operation Updated Successfully.' : 'Table Operation Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $tableOperation = Wf04TaskEventPhaseTableOperation::findOrFail($id);
        $this->table_operation_id = $id;
        $this->name = $tableOperation->name;
        $this->description = $tableOperation->description;
        $this->school_id = $tableOperation->school_id;
        $this->is_active = $tableOperation->is_active;
        $this->remarks = $tableOperation->remarks;
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
        Wf04TaskEventPhaseTableOperation::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Table Operation Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
