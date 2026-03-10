<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf04TaskEventPhaseTableOperationStatus;
use Livewire\WithPagination;

class Wf04TaskEventPhaseTableOperationStatusComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $operation_status_id = null;
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
        $operationStatuses = Wf04TaskEventPhaseTableOperationStatus::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf04-task-event-phase-table-operation-status-comp', compact('operationStatuses'));
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
        $this->operation_status_id = null;
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

        Wf04TaskEventPhaseTableOperationStatus::updateOrCreate(['id' => $this->operation_status_id], array_merge($validated, [
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->operation_status_id ? 'Operation Status Updated Successfully.' : 'Operation Status Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $operationStatus = Wf04TaskEventPhaseTableOperationStatus::findOrFail($id);
        $this->operation_status_id = $id;
        $this->name = $operationStatus->name;
        $this->description = $operationStatus->description;
        $this->school_id = $operationStatus->school_id;
        $this->is_active = $operationStatus->is_active;
        $this->remarks = $operationStatus->remarks;
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
        Wf04TaskEventPhaseTableOperationStatus::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Operation Status Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
