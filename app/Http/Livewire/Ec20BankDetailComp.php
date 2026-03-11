<?php

namespace App\Http\Livewire;

use App\Models\Ec20Bank;
use App\Models\Ec20BankDetail;
use Livewire\Component;

class Ec20BankDetailComp extends Component
{
    public $bank_id;
    public $name;
    public $description;
    public $status = 'draft';
    public $details = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'details.*.name' => 'required|string|max:255',
            'details.*.description' => 'nullable|string',
            'details.*.status' => 'required|in:running,completed,upcoming,suspended,cancelled,archived',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $bank = Ec20Bank::with('details')->findOrFail($id);
            $this->bank_id = $bank->id;
            $this->name = $bank->name;
            $this->description = $bank->description;
            $this->status = $bank->status;
            $this->details = $bank->details->toArray();
        } else {
            $this->addDetail();
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'name' => '',
            'description' => '',
            'status' => 'running'
        ];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function save()
    {
        $this->validate();

        $bank = Ec20Bank::updateOrCreate(
            ['id' => $this->bank_id],
            [
                'name' => $this->name,
                'description' => $this->description,
                'status' => $this->status,
                'user_id' => auth()->id(),
            ]
        );

        $bank->details()->delete();

        foreach ($this->details as $detail) {
            $bank->details()->create([
                'name' => $detail['name'],
                'description' => $detail['description'],
                'status' => $detail['status'],
                'user_id' => auth()->id(),
            ]);
        }

        session()->flash('message', 'Bank information saved successfully.');
        return redirect()->to('/banks'); // Or a relevant route
    }

    public function render()
    {
        return view('livewire.ec20-bank-detail-comp');
    }
}
