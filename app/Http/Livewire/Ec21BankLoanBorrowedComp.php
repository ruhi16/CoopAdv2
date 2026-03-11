<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec21BankLoanBorrowed;
use App\Models\Ec21BankLoanBorrowedSpec;
use App\Models\Ec21BankLoanScheme;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec21BankLoanBorrowedComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $borrowed_id = null;
    public $bank_loan_scheme_id = '';
    public $name = '';
    public $description = '';
    public $loan_borrowed_amount = '';
    public $loan_borrowed_date = '';
    public $installment_amount = '';
    public $no_of_installments = '';
    public $status = 'running';
    public $is_finalized = true;
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $schemeDetails = [];
    public $loanSchemes = [];
    public $selectedSpecs = [];
    public $calculatedEmis = [];
    public $interestRate = 0;

    protected function rules()
    {
        return [
            'bank_loan_scheme_id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'loan_borrowed_amount' => 'nullable|numeric|min:0',
            'loan_borrowed_date' => 'nullable|date',
            'installment_amount' => 'nullable|numeric|min:0',
            'no_of_installments' => 'nullable|integer|min:0',
            'status' => 'required|in:running,completed,upcoming,suspended,cancelled',
        ];
    }

    public function render()
    {
        $borroweds = Ec21BankLoanBorrowed::with(['loanScheme'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec21-bank-loan-borrowed-comp', compact('borroweds'));
    }

    public function mount()
    {
        $this->loanSchemes = Ec21BankLoanScheme::where('is_active', true)->orderBy('name')->get();
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
        $this->borrowed_id = null;
        $this->bank_loan_scheme_id = '';
        $this->name = '';
        $this->description = '';
        $this->loan_borrowed_amount = '';
        $this->loan_borrowed_date = '';
        $this->installment_amount = '';
        $this->no_of_installments = '';
        $this->status = 'running';
        $this->is_finalized = true;
        $this->is_active = true;
        $this->remarks = '';
        $this->schemeDetails = [];
        $this->selectedSpecs = [];
        $this->calculatedEmis = [];
        $this->interestRate = 0;
        $this->confirmingDelete = null;
    }

    public function updatedBankLoanSchemeId($value)
    {
        if (!empty($value)) {
            $scheme = Ec21BankLoanScheme::with(['specifications', 'specifications.particular'])->find($value);
            if ($scheme) {
                $this->name = $scheme->name . ' - Borrowed';
                $this->description = $scheme->description;
                $this->schemeDetails = $scheme->specifications->toArray();
                
                $this->selectedSpecs = array_map(function ($spec) {
                    $specName = $spec['name'];
                    if (empty($specName) && isset($spec['particular']) && $spec['particular']) {
                        $specName = $spec['particular']['name'] ?? 'Specification ' . $spec['id'];
                    }
                    if (empty($specName)) {
                        $specName = 'Specification ' . ($spec['id'] ?? '');
                    }
                    return [
                        'name' => $specName,
                        'description' => $spec['description'] ?? '',
                        'bank_loan_schema_particular_id' => $spec['bank_loan_schema_particular_id'],
                        'bank_loan_schema_particular_value' => $spec['bank_loan_schema_particular_value'],
                        'is_percent_on_current_balance' => $spec['is_percent_on_current_balance'] ?? true,
                        'is_regular' => $spec['is_regular'] ?? false,
                        'effected_on' => $spec['effected_on'] ?? date('Y-m-d'),
                        'status' => $spec['status'] ?? 'running',
                    ];
                }, $this->schemeDetails);

                $this->extractInterestRate();
                $this->calculateEmi();
            }
        } else {
            $this->schemeDetails = [];
            $this->selectedSpecs = [];
            $this->interestRate = 0;
            $this->calculatedEmis = [];
        }
    }

    private function extractInterestRate()
    {
        $this->interestRate = 0;
        foreach ($this->selectedSpecs as $spec) {
            $name = strtolower($spec['name'] ?? '');
            if (strpos($name, 'interest') !== false || strpos($name, 'roi') !== false || strpos($name, 'rate') !== false) {
                $this->interestRate = floatval($spec['bank_loan_schema_particular_value'] ?? 0);
                break;
            }
        }
    }

    public function calculateEmi()
    {
        $this->calculatedEmis = [];
        
        $principal = floatval($this->loan_borrowed_amount);
        $years = intval($this->no_of_installments);
        $rate = floatval($this->interestRate);

        if ($principal <= 0 || $years <= 0 || $rate <= 0) {
            return;
        }

        $monthlyRate = $rate / 12 / 100;
        $totalMonths = $years;

        $monthlyEmi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $totalMonths)) / (pow(1 + $monthlyRate, $totalMonths) - 1);

        $this->installment_amount = round($monthlyEmi, 2);

        $balance = $principal;

        for ($i = 1; $i <= $totalMonths; $i++) {
            $interestAmount = $balance * $monthlyRate;
            $principalAmount = $monthlyEmi - $interestAmount;
            $balance -= $principalAmount;

            $this->calculatedEmis[] = [
                'emi_no' => $i,
                'principal_amount' => round($principalAmount, 2),
                'interest_amount' => round($interestAmount, 2),
                'total' => round($monthlyEmi, 2),
                'balance_after' => round(max(0, $balance), 2),
            ];
        }
    }

    public function updatedLoanBorrowedAmount()
    {
        if ($this->interestRate > 0 && $this->no_of_installments > 0) {
            $this->calculateEmi();
        }
    }

    public function updatedNoOfInstallments()
    {
        if ($this->interestRate > 0 && $this->loan_borrowed_amount > 0) {
            $this->calculateEmi();
        }
    }

    public function updatedInstallmentAmount()
    {
        if ($this->interestRate > 0 && $this->loan_borrowed_amount > 0 && $this->installment_amount > 0) {
            $this->recalculateNoOfInstallments();
        }
    }

    private function recalculateNoOfInstallments()
    {
        $principal = floatval($this->loan_borrowed_amount);
        $rate = floatval($this->interestRate);
        $emi = floatval($this->installment_amount);

        if ($principal <= 0 || $rate <= 0 || $emi <= 0) {
            return;
        }

        $monthlyRate = $rate / 12 / 100;
        $months = log($emi / ($emi - $principal * $monthlyRate)) / log(1 + $monthlyRate);
        $this->no_of_installments = round($months);

        $this->calculateEmi();
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        $borrowed = Ec21BankLoanBorrowed::updateOrCreate(['id' => $this->borrowed_id], array_merge($validated, [
            'bank_loan_scheme_particular_id' => $this->bank_loan_scheme_id,
            'bank_loan_borrowed_previous_balance' => $this->loan_borrowed_amount,
            'installment_amount' => $this->installment_amount,
            'no_of_installments' => $this->no_of_installments,
            'is_finalized' => $this->is_finalized,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        Ec21BankLoanBorrowedSpec::where('bank_loan_borrowed_id', $borrowed->id)->delete();

        if (!empty($this->selectedSpecs)) {
            foreach ($this->selectedSpecs as $spec) {
                $specName = $spec['name'] ?? null;
                if (empty($specName)) {
                    $specName = 'Specification ' . ($spec['bank_loan_schema_particular_id'] ?? '');
                }
                
                Ec21BankLoanBorrowedSpec::create([
                    'bank_loan_borrowed_id' => $borrowed->id,
                    'name' => $specName,
                    'description' => $spec['description'] ?? '',
                    'bank_loan_schema_particular_id' => $spec['bank_loan_schema_particular_id'] ?? null,
                    'bank_loan_schema_particular_value' => floatval($spec['bank_loan_schema_particular_value'] ?? 0),
                    'is_percent_on_current_balance' => isset($spec['is_percent_on_current_balance']) ? (bool)$spec['is_percent_on_current_balance'] : true,
                    'is_regular' => isset($spec['is_regular']) ? (bool)$spec['is_regular'] : false,
                    'effected_on' => !empty($spec['effected_on']) ? $spec['effected_on'] : date('Y-m-d'),
                    'status' => $spec['status'] ?? 'running',
                    'user_id' => $userId,
                ]);
            }
        }

        session()->flash('message', $this->borrowed_id ? 'Borrowed Updated Successfully.' : 'Borrowed Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $borrowed = Ec21BankLoanBorrowed::with('specifications')->findOrFail($id);
        
        $this->borrowed_id = $id;
        $this->bank_loan_scheme_id = $borrowed->bank_loan_scheme_particular_id;
        $this->name = $borrowed->name;
        $this->description = $borrowed->description;
        $this->loan_borrowed_amount = $borrowed->loan_borrowed_amount;
        $this->loan_borrowed_date = $borrowed->loan_borrowed_date;
        $this->installment_amount = $borrowed->installment_amount;
        $this->no_of_installments = $borrowed->no_of_installments;
        $this->status = $borrowed->status;
        $this->is_finalized = $borrowed->is_finalized;
        $this->is_active = $borrowed->is_active;
        $this->remarks = $borrowed->remarks;

        $this->selectedSpecs = array_map(function ($spec) {
            if (empty($spec['name'])) {
                $spec['name'] = 'Specification ' . ($spec['id'] ?? '');
            }
            return $spec;
        }, $borrowed->specifications->toArray());
        
        if (empty($this->selectedSpecs)) {
            $this->updatedBankLoanSchemeId($this->bank_loan_scheme_id);
        }

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
        Ec21BankLoanBorrowed::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Borrowed Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
