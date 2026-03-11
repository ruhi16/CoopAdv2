<?php

namespace App\Http\Livewire;

use App\Models\Ec21BankLoanSchemaParticular;
use App\Models\Ec21BankLoanScheme;
use App\Models\Ec21BankLoanSchemeSpecification;
use Livewire\Component;

class Ec21BankLoanSchemeComp extends Component
{
    public $scheme_id;
    public $name;
    public $description;
    public $bank_id;
    public $effected_on;
    public $status = 'running';

    public $specifications = [];
    public $allParticulars = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'bank_id' => 'required|integer|exists:ec20_banks,id',
            'effected_on' => 'required|date',
            'status' => 'required|in:running,completed,upcoming,suspended,cancelled',
            'specifications.*.name' => 'required|string|max:255',
            'specifications.*.description' => 'nullable|string',
            'specifications.*.bank_loan_schema_particular_id' => 'required|integer|exists:ec21_bank_loan_schema_particulars,id',
            'specifications.*.bank_loan_schema_particular_value' => 'required|numeric',
            'specifications.*.is_percent_on_current_balance' => 'required|boolean',
            'specifications.*.is_regular' => 'required|boolean',
            'specifications.*.effected_on' => 'required|date',
        ];
    }

    public function mount($id = null)
    {
        $this->allParticulars = Ec21BankLoanSchemaParticular::where('is_active', true)->get();

        if ($id) {
            $scheme = Ec21BankLoanScheme::with('specifications')->findOrFail($id);
            $this->scheme_id = $scheme->id;
            $this->name = $scheme->name;
            $this->description = $scheme->description;
            $this->bank_id = $scheme->bank_id;
            $this->effected_on = $scheme->effected_on;
            $this->status = $scheme->status;
            $this->specifications = $scheme->specifications->toArray();
        } else {
            $this->addSpecification();
        }
    }

    public function addSpecification()
    {
        $this->specifications[] = [
            'name' => '',
            'description' => '',
            'bank_loan_schema_particular_id' => $this->allParticulars->first()->id ?? null,
            'bank_loan_schema_particular_value' => 0,
            'is_percent_on_current_balance' => true,
            'is_regular' => true,
            'effected_on' => now()->format('Y-m-d'),
            'status' => 'running',
        ];
    }

    public function removeSpecification($index)
    {
        unset($this->specifications[$index]);
        $this->specifications = array_values($this->specifications);
    }

    public function save()
    {
        $this->validate();

        $scheme = Ec21BankLoanScheme::updateOrCreate(
            ['id' => $this->scheme_id],
            [
                'name' => $this->name,
                'description' => $this->description,
                'bank_id' => $this->bank_id,
                'effected_on' => $this->effected_on,
                'status' => $this->status,
                'user_id' => auth()->id(),
            ]
        );

        $scheme->specifications()->delete();

        foreach ($this->specifications as $spec) {
            $scheme->specifications()->create($spec + ['user_id' => auth()->id()]);
        }

        session()->flash('message', 'Bank loan scheme saved successfully.');
        return redirect()->to('/bank-loan-schemes'); // Or a relevant route
    }

    public function render()
    {
        return view('livewire.ec21-bank-loan-scheme-comp');
    }
}
