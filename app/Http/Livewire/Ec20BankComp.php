<?php

namespace App\Http\Livewire;

use App\Models\Ec20Bank;
use Livewire\Component;
use Livewire\WithPagination;

class Ec20BankComp extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $banks = Ec20Bank::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.ec20-bank-comp', [
            'banks' => $banks,
        ]);
    }

    public function delete($id)
    {
        $bank = Ec20Bank::find($id);
        if ($bank) {
            $bank->delete();
        }
    }
}
