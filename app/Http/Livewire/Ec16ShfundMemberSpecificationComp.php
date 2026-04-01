<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec16ShfundMemberSpecification;
use App\Models\Ec16ShfundMemberMasterDb;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec16ShfundMemberSpecificationComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $spec_id = null;
    public $shfund_member_master_db_id = '';
    public $name = '';
    public $description = '';
    public $particular = '';
    public $particular_value = '';
    public $effected_on = '';
    public $status = 'draft';
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $memberMasterDbs = [];

    protected function rules()
    {
        return [
            'shfund_member_master_db_id' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'particular' => 'nullable|string|max:255',
            'particular_value' => 'nullable|numeric|min:0',
            'effected_on' => 'nullable|date',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function render()
    {
        $specs = Ec16ShfundMemberSpecification::with(['memberMasterDb'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('particular', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec16-shfund-member-specification-comp', compact('specs'));
    }

    public function mount()
    {
        $this->memberMasterDbs = Ec16ShfundMemberMasterDb::where('is_active', true)->orderBy('name')->get();
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
        $this->spec_id = null;
        $this->shfund_member_master_db_id = '';
        $this->name = '';
        $this->description = '';
        $this->particular = '';
        $this->particular_value = '';
        $this->effected_on = '';
        $this->status = 'draft';
        $this->is_active = true;
        $this->remarks = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        Ec16ShfundMemberSpecification::updateOrCreate(['id' => $this->spec_id], array_merge($validated, [
            'name' => $this->name ?: $this->particular,
            'particular_value' => floatval($this->particular_value),
            'effected_on' => $this->effected_on ?: date('Y-m-d'),
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        session()->flash('message', $this->spec_id ? 'Specification Updated Successfully.' : 'Specification Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $spec = Ec16ShfundMemberSpecification::findOrFail($id);
        
        $this->spec_id = $id;
        $this->shfund_member_master_db_id = $spec->shfund_member_master_db_id ?? '';
        $this->name = $spec->name;
        $this->description = $spec->description ?? '';
        $this->particular = $spec->particular ?? '';
        $this->particular_value = $spec->particular_value;
        $this->effected_on = $spec->effected_on ? date('Y-m-d', strtotime($spec->effected_on)) : '';
        $this->status = $spec->status;
        $this->is_active = $spec->is_active;
        $this->remarks = $spec->remarks ?? '';

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
        Ec16ShfundMemberSpecification::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Specification Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
