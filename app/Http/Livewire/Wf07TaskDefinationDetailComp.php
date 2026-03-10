<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wf07TaskDefinationDetail;
use App\Models\Wf07TaskDefination;
use App\Models\Wf03TaskEventPhase;
use App\Models\Wf04TaskEventPhaseTable;
use App\Models\Wf04TaskEventPhaseTableOperation;
use Livewire\WithPagination;

class Wf07TaskDefinationDetailComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $task_defination_filter = '';
    public $isOpen = 0;
    public $detail_id = null;
    public $name = '';
    public $description = '';
    public $task_defination_id = '';
    public $task_event_sequence_no = '';
    public $task_event_phase_id = '';
    public $task_event_phase_table_id = '';
    public $task_event_phase_table_operation_id = '';
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
            'task_defination_id' => 'required|integer',
            'task_event_sequence_no' => 'required|integer',
            'task_event_phase_id' => 'required|integer',
            'task_event_phase_table_id' => 'required|integer',
            'task_event_phase_table_operation_id' => 'required|integer',
            'description' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $taskDefinations = Wf07TaskDefination::orderBy('name')->get();
        $taskEventPhases = Wf03TaskEventPhase::orderBy('name')->get();
        $taskEventPhaseTables = Wf04TaskEventPhaseTable::orderBy('name')->get();
        
        $taskEventPhaseTableOperations = Wf04TaskEventPhaseTableOperation::orderBy('name')->get();
        
        $details = Wf07TaskDefinationDetail::with(['taskDefination', 'taskEventPhase', 'taskEventPhaseTable', 'taskEventPhaseTableOperation'])
            ->when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->task_defination_filter, function ($query) {
                $query->where('task_defination_id', $this->task_defination_filter);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.wf07-task-defination-detail-comp', compact(
            'details', 'taskDefinations', 'taskEventPhases', 'taskEventPhaseTables', 'taskEventPhaseTableOperations'
        ));
    }

    public function updatedTaskEventPhaseTableId($value)
    {
        $this->task_event_phase_table_operation_id = '';
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
        $this->detail_id = null;
        $this->name = '';
        $this->description = '';
        $this->task_defination_id = '';
        $this->task_event_sequence_no = '';
        $this->task_event_phase_id = '';
        $this->task_event_phase_table_id = '';
        $this->task_event_phase_table_operation_id = '';
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

        Wf07TaskDefinationDetail::updateOrCreate(['id' => $this->detail_id], array_merge($validated, [
            'created_by' => $this->created_by ?: 0,
            'approved_by' => $this->approved_by ?: 0,
            'school_id' => $this->school_id ?: 0,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]));

        session()->flash('message', $this->detail_id ? 'Task Definition Detail Updated Successfully.' : 'Task Definition Detail Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $detail = Wf07TaskDefinationDetail::findOrFail($id);
        $this->detail_id = $id;
        $this->name = $detail->name;
        $this->description = $detail->description;
        $this->task_defination_id = $detail->task_defination_id;
        $this->task_event_sequence_no = $detail->task_event_sequence_no;
        $this->task_event_phase_id = $detail->task_event_phase_id;
        $this->task_event_phase_table_id = $detail->task_event_phase_table_id;
        $this->task_event_phase_table_operation_id = $detail->task_event_phase_table_operation_id;
        $this->created_by = $detail->created_by;
        $this->approved_by = $detail->approved_by;
        $this->school_id = $detail->school_id;
        $this->is_active = $detail->is_active;
        $this->remarks = $detail->remarks;
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
        Wf07TaskDefinationDetail::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Task Definition Detail Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
