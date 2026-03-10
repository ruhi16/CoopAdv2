<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf02TaskEvent;
use App\Models\Wf01TaskCategory;
use Livewire\WithPagination;

class Wf02TaskEventComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $task_event_id = null;
    public $task_category_id = '';
    public $name = '';
    public $description = '';
    public $school_id = '';
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'task_category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $taskCategories = Wf01TaskCategory::where('is_active', true)->orderBy('name')->get();
        
        $taskEvents = Wf02TaskEvent::with('taskCategory')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->task_category_id, function ($query) {
                $query->where('task_category_id', $this->task_category_id);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf02-task-event-comp', compact('taskEvents', 'taskCategories'));
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
        $this->task_event_id = null;
        $this->task_category_id = '';
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

        Wf02TaskEvent::updateOrCreate(['id' => $this->task_event_id], array_merge($validated, [
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->task_event_id ? 'Task Event Updated Successfully.' : 'Task Event Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $taskEvent = Wf02TaskEvent::findOrFail($id);
        $this->task_event_id = $id;
        $this->task_category_id = $taskEvent->task_category_id;
        $this->name = $taskEvent->name;
        $this->description = $taskEvent->description;
        $this->school_id = $taskEvent->school_id;
        $this->is_active = $taskEvent->is_active;
        $this->remarks = $taskEvent->remarks;
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
        Wf02TaskEvent::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Task Event Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
