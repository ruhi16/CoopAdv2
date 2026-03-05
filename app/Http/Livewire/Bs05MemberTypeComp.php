<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MemberType;
use Livewire\WithPagination;

class Bs05MemberTypeComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name, $description, $order_index, $member_type_id;
    public $isOpen = 0;
    public $search = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:member_types,name,' . $this->member_type_id,
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
        ];
    }

    public function render()
    {
        $memberTypes = MemberType::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.bs05-member-type-comp', compact('memberTypes'));
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
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->member_type_id = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        MemberType::updateOrCreate(['id' => $this->member_type_id], $validated);

        session()->flash('message', $this->member_type_id ? 'Member Type Updated Successfully.' : 'Member Type Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $memberType = MemberType::findOrFail($id);
        $this->member_type_id = $id;
        $this->name = $memberType->name;
        $this->description = $memberType->description;
        $this->order_index = $memberType->order_index;

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
        MemberType::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Member Type Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
