<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec21BankLoanBorrowed;
use App\Models\Ec21BankLoanBorrowedSpec;
use App\Models\Ec23BankLoanPayment;
use App\Models\Ec23BankLoanPaymentDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec23BankLoanPaymentComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $selectedLoans = [];
    public $payment_date = '';
    public $payment_amount = '';
    public $payment_method = 'bank';
    public $remarks = '';
    public $loanDetails = [];
    public $calculatedPayment = [];
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
        ];
    }

    public function render()
    {
        $borrowedLoans = Ec21BankLoanBorrowed::with(['loanScheme'])
            ->where('is_active', true)
            ->whereIn('status', ['running'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        $borrowedLoans->getCollection()->map(function ($loan) {
            $totalPaid = Ec23BankLoanPayment::where('bank_loan_borrowed_id', $loan->id)
                ->where('is_active', true)
                ->sum('installment_total_amount');
            
            $loan->total_paid = $totalPaid;
            $loan->balance = floatval($loan->loan_borrowed_amount) - $totalPaid;
            return $loan;
        });

        return view('livewire.ec23-bank-loan-payment-comp', compact('borrowedLoans'));
    }

    public function openPaymentModal()
    {
        if (empty($this->selectedLoans)) {
            session()->flash('error', 'Please select at least one loan to pay.');
            return;
        }

        $this->loanDetails = Ec21BankLoanBorrowed::whereIn('id', $this->selectedLoans)
            ->with(['loanScheme', 'specifications'])
            ->get()
            ->map(function ($loan) {
                $totalPaid = Ec23BankLoanPayment::where('bank_loan_borrowed_id', $loan->id)
                    ->where('is_active', true)
                    ->sum('installment_total_amount');
                
                $loan->total_paid = $totalPaid;
                $loan->balance = floatval($loan->loan_borrowed_amount) - $totalPaid;
                return $loan;
            })
            ->toArray();

        $this->payment_date = now()->toDateString();
        $this->calculatePayment();
        $this->isOpen = 1;
    }

    public function updatedSelectedLoans()
    {
        if (!empty($this->selectedLoans)) {
            $this->loanDetails = Ec21BankLoanBorrowed::whereIn('id', $this->selectedLoans)
                ->with(['loanScheme', 'specifications'])
                ->get()
                ->map(function ($loan) {
                    $totalPaid = Ec23BankLoanPayment::where('bank_loan_borrowed_id', $loan->id)
                        ->where('is_active', true)
                        ->sum('installment_total_amount');
                    
                    $loan->total_paid = $totalPaid;
                    $loan->balance = floatval($loan->loan_borrowed_amount) - $totalPaid;
                    return $loan;
                })
                ->toArray();
        } else {
            $this->loanDetails = [];
        }
    }

    public function calculatePayment()
    {
        $this->calculatedPayment = [];
        
        if (empty($this->loanDetails)) {
            return;
        }

        $totalEmiAmount = 0;
        $totalBalance = 0;

        foreach ($this->loanDetails as $loan) {
            $balance = floatval($loan['balance'] ?? 0);
            $installmentAmount = floatval($loan['installment_amount'] ?? 0);
            
            $totalEmiAmount += $installmentAmount;
            $totalBalance += $balance;
        }

        $paymentAmount = floatval($this->payment_amount ?? 0);
        
        $remainingAmount = $paymentAmount;
        
        foreach ($this->loanDetails as $index => &$loan) {
            $balance = floatval($loan['balance'] ?? 0);
            $installmentAmount = floatval($loan['installment_amount'] ?? 0);
            
            $payAmount = 0;
            if ($paymentAmount >= $totalEmiAmount) {
                $payAmount = min($remainingAmount, $balance);
            } else {
                $ratio = $paymentAmount / $totalEmiAmount;
                $payAmount = $installmentAmount * min($ratio + 0.5, 1);
            }
            
            $payAmount = min($payAmount, $balance);
            
            $this->calculatedPayment[$loan['id']] = [
                'loan_id' => $loan['id'],
                'loan_name' => $loan['name'],
                'balance' => $balance,
                'pay_amount' => round($payAmount, 2),
            ];
            
            $remainingAmount -= $payAmount;
        }

        if (empty($this->payment_amount)) {
            $this->payment_amount = $totalEmiAmount;
        }
    }

    public function updatedPaymentAmount()
    {
        $this->calculatePayment();
    }

    public function closeModal()
    {
        $this->isOpen = 0;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->selectedLoans = [];
        $this->payment_date = '';
        $this->payment_amount = '';
        $this->payment_method = 'bank';
        $this->remarks = '';
        $this->loanDetails = [];
        $this->calculatedPayment = [];
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $this->validate();

        $userId = Auth::id() ?? 1;

        foreach ($this->selectedLoans as $loanId) {
            $payAmount = $this->calculatedPayment[$loanId]['pay_amount'] ?? 0;
            
            if ($payAmount <= 0) {
                continue;
            }

            $loan = Ec21BankLoanBorrowed::find($loanId);
            if (!$loan) {
                continue;
            }

            $totalPaid = Ec23BankLoanPayment::where('bank_loan_borrowed_id', $loanId)
                ->where('is_active', true)
                ->sum('installment_total_amount');

            $currentBalance = floatval($loan->loan_borrowed_amount) - $totalPaid;
            
            $installmentNo = Ec23BankLoanPayment::where('bank_loan_borrowed_id', $loanId)
                ->where('is_active', true)
                ->count() + 1;

            $payment = Ec23BankLoanPayment::create([
                'name' => 'Payment for ' . $loan->name . ' - Installment #' . $installmentNo,
                'bank_loan_borrowed_id' => $loanId,
                'bank_loan_borrowed_current_balance' => $currentBalance - $payAmount,
                'installment_total_amount' => $payAmount,
                'installment_no' => $installmentNo,
                'payment_date' => $this->payment_date,
                'status' => 'running',
                'is_active' => true,
                'user_id' => $userId,
                'remarks' => $this->remarks,
            ]);

            $specs = Ec21BankLoanBorrowedSpec::where('bank_loan_borrowed_id', $loanId)->get();
            foreach ($specs as $spec) {
                Ec23BankLoanPaymentDetail::create([
                    'name' => $spec->name,
                    'bank_loan_payment_id' => $payment->id,
                    'bank_loan_borrowed_spec_id' => $spec->id,
                    'bank_loan_schema_particular_id' => $spec->bank_loan_schema_particular_id,
                    'bank_loan_schema_particular_amount' => $spec->bank_loan_schema_particular_value,
                    'installment_amount' => $payAmount,
                    'status' => 'running',
                    'is_active' => true,
                    'user_id' => $userId,
                ]);
            }
        }

        session()->flash('message', 'Payment processed successfully.');
        $this->closeModal();
    }

    public function toggleLoanSelection($loanId)
    {
        $index = array_search($loanId, $this->selectedLoans);
        if ($index !== false) {
            unset($this->selectedLoans[$index]);
            $this->selectedLoans = array_values($this->selectedLoans);
        } else {
            $this->selectedLoans[] = $loanId;
        }
        $this->updatedSelectedLoans();
    }

    public function selectAll()
    {
        $allLoanIds = Ec21BankLoanBorrowed::where('is_active', true)
            ->whereIn('status', ['running'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->pluck('id')
            ->toArray();
        
        $this->selectedLoans = $allLoanIds;
        $this->updatedSelectedLoans();
    }

    public function deselectAll()
    {
        $this->selectedLoans = [];
        $this->loanDetails = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
