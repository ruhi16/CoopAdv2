<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\School;
use Livewire\WithPagination;

class BsSchoolComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name, $code, $address, $phone, $email, $school_id;
    public $isOpen = 0;
    public $search = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:schools,code,' . $this->school_id,
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:schools,email,' . $this->school_id,
        ];
    }

    public function render()
    {
        $schools = School::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.bs-school-comp', compact('schools'));
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
        $this->code = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->school_id = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        School::updateOrCreate(['id' => $this->school_id], $validated);

        session()->flash('message', $this->school_id ? 'School Updated Successfully.' : 'School Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $school = School::findOrFail($id);
        $this->school_id = $id;
        $this->name = $school->name;
        $this->code = $school->code;
        $this->address = $school->address;
        $this->phone = $school->phone;
        $this->email = $school->email;

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
        School::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'School Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
