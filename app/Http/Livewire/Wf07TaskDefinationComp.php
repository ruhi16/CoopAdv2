<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf07TaskDefination;
use App\Models\Wf01TaskCategory;
use App\Models\Wf02TaskEvent;
use Livewire\WithPagination;

class Wf07TaskDefinationComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $task_category_filter = '';
    public $isOpen = 0;
    public $task_defination_id = null;
    public $name = '';
    public $description = '';
    public $task_category_id = '';
    public $task_event_id = '';
    public $created_by = '';
    public $approved_by = '';
    public $school_id = '';
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'task_category_id' => 'required|integer',
            'task_event_id' => 'required|integer',
            'description' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $taskCategories = Wf01TaskCategory::where('is_active', true)->orderBy('name')->get();
        
        $taskEvents = Wf02TaskEvent::where('is_active', true)
            ->when($this->task_category_id, function ($query) {
                $query->where('task_category_id', $this->task_category_id);
            })
            ->orderBy('name')
            ->get();

        $taskDefinations = Wf07TaskDefination::with(['taskCategory', 'taskEvent'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->task_category_filter, function ($query) {
                $query->where('task_category_id', $this->task_category_filter);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf07-task-defination-comp', compact('taskDefinations', 'taskCategories', 'taskEvents'));
    }

    public function updatedTaskCategoryId($value)
    {
        $this->task_event_id = '';
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
        $this->task_defination_id = null;
        $this->name = '';
        $this->description = '';
        $this->task_category_id = '';
        $this->task_event_id = '';
        $this->created_by = '';
        $this->approved_by = '';
        $this->school_id = '';
        $this->is_active = true;
        $this->remarks = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Wf07TaskDefination::updateOrCreate(['id' => $this->task_defination_id], array_merge($validated, [
            'created_by' => $this->created_by ?: 0,
            'approved_by' => $this->approved_by ?: 0,
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->task_defination_id ? 'Task Definition Updated Successfully.' : 'Task Definition Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $taskDefination = Wf07TaskDefination::findOrFail($id);
        $this->task_defination_id = $id;
        $this->name = $taskDefination->name;
        $this->description = $taskDefination->description;
        $this->task_category_id = $taskDefination->task_category_id;
        $this->task_event_id = $taskDefination->task_event_id;
        $this->created_by = $taskDefination->created_by;
        $this->approved_by = $taskDefination->approved_by;
        $this->school_id = $taskDefination->school_id;
        $this->is_active = $taskDefination->is_active;
        $this->remarks = $taskDefination->remarks;
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
        Wf07TaskDefination::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Task Definition Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
