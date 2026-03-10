<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf01TaskCategory;
use Livewire\WithPagination;

class Wf01TaskCategoryComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $task_category_id = null;
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
        $taskCategories = Wf01TaskCategory::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf01-task-category-comp', compact('taskCategories'));
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
        $this->task_category_id = null;
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

        Wf01TaskCategory::updateOrCreate(['id' => $this->task_category_id], array_merge($validated, [
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->task_category_id ? 'Task Category Updated Successfully.' : 'Task Category Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $taskCategory = Wf01TaskCategory::findOrFail($id);
        $this->task_category_id = $id;
        $this->name = $taskCategory->name;
        $this->description = $taskCategory->description;
        $this->school_id = $taskCategory->school_id;
        $this->is_active = $taskCategory->is_active;
        $this->remarks = $taskCategory->remarks;
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
        Wf01TaskCategory::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Task Category Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
