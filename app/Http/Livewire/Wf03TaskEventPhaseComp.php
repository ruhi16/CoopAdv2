<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf03TaskEventPhase;
use Livewire\WithPagination;

class Wf03TaskEventPhaseComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $task_event_phase_id = null;
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
        $taskEventPhases = Wf03TaskEventPhase::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf03-task-event-phase-comp', compact('taskEventPhases'));
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
        $this->task_event_phase_id = null;
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

        Wf03TaskEventPhase::updateOrCreate(['id' => $this->task_event_phase_id], array_merge($validated, [
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->task_event_phase_id ? 'Task Event Phase Updated Successfully.' : 'Task Event Phase Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $taskEventPhase = Wf03TaskEventPhase::findOrFail($id);
        $this->task_event_phase_id = $id;
        $this->name = $taskEventPhase->name;
        $this->description = $taskEventPhase->description;
        $this->school_id = $taskEventPhase->school_id;
        $this->is_active = $taskEventPhase->is_active;
        $this->remarks = $taskEventPhase->remarks;
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
        Wf03TaskEventPhase::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Task Event Phase Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
