<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec05LoanAssign;
use App\Models\Ec06LoanAssignDetail;
use App\Models\Ec11LoanPayment;
use App\Models\Ec12LoanPaymentDetail;
use Illuminate\Support\Facades\Auth;

class Ec11LoanPaymentComp2 extends Component
{
    public $search = '';
    public $selectedLoans = [];
    public $isOpen = 0;
    public $payment_date = '';
    public $payment_amount = '';
    public $payment_method = 'cash';
    public $remarks = '';
    public $calculatedPayments = [];

    protected function rules()
    {
        return [
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,upi,other',
        ];
    }

    public function render()
    {
        $loanData = [];
        
        $loans = Ec05LoanAssign::with([
            'member', 
            'loanScheme',
            'loanAssignDetails' => function($query) {
                $query->where('is_default', true)->where('is_active', true);
            }
        ])
        ->whereHas('loanAssignDetails', function($query) {
            $query->where('is_default', true)->where('is_active', true);
        })
        ->where('status', '!=', 'closed')
        ->where('is_active', true)
        ->when($this->search, function ($query) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('member', function($mq) {
                      $mq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        })
        ->orderBy('id', 'desc')
        ->get();

        foreach ($loans as $loan) {
            $totalPaid = Ec11LoanPayment::where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->sum('payment_total_amount');
            
            $balance = floatval($loan->loan_amount) - $totalPaid;
            
            $dueDetails = Ec06LoanAssignDetail::where('loan_assign_id', $loan->id)
                ->where('is_default', true)
                ->where('is_active', true)
                ->get();
            
            $roi = 0;
            $principal = 0;
            $other_dues = 0;
            $schemeDetails = [];
            
            foreach ($dueDetails as $detail) {
                $name = strtolower($detail->loan_scheme_detail_feature_name ?? '');
                if (strpos($name, 'interest') !== false || strpos($name, 'roi') !== false) {
                    $roi = floatval($detail->loan_scheme_detail_feature_value ?? 0);
                } elseif (strpos($name, 'principal') !== false) {
                    $principal = floatval($detail->loan_scheme_detail_feature_value ?? 0);
                } else {
                    $other_dues += floatval($detail->loan_scheme_detail_feature_value ?? 0);
                }
                
                $schemeDetails[] = [
                    'name' => $detail->loan_scheme_detail_feature_name,
                    'value' => $detail->loan_scheme_detail_feature_value,
                ];
            }
            
            if ($principal == 0) {
                $principal = floatval($loan->loan_amount);
            }
            
            $interest = 0;
            if ($roi > 0 && $balance > 0) {
                $monthlyRate = $roi / 12 / 100;
                $interest = $balance * $monthlyRate;
            }
            
            $monthlyEmi = floatval($loan->emi_amount ?? 0);
            $totalMonthlyDue = $interest + $monthlyEmi + $other_dues;
            
            $lastPayment = Ec11LoanPayment::where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->orderBy('payment_date', 'desc')
                ->first();
            
            $paymentHistory = Ec11LoanPayment::where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->orderBy('payment_date', 'desc')
                ->take(5)
                ->get();
            
            $loanData[] = [
                'id' => $loan->id,
                'name' => $loan->name,
                'member_name' => $loan->member->name ?? '-',
                'scheme_name' => $loan->loanScheme->name ?? '-',
                'loan_amount' => $loan->loan_amount,
                'roi' => $roi,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'monthly_emi' => $monthlyEmi,
                'monthly_interest' => round($interest, 2),
                'total_monthly_due' => $totalMonthlyDue,
                'status' => $loan->status,
                'scheme_details' => $schemeDetails,
                'last_payment_amount' => $lastPayment ? $lastPayment->payment_total_amount : null,
                'last_payment_date' => $lastPayment ? $lastPayment->payment_date : null,
                'payment_history' => $paymentHistory,
            ];
        }

        return view('livewire.ec11-loan-payment-comp2', compact('loanData'));
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
    }

    public function selectAll()
    {
        $allLoanIds = Ec05LoanAssign::with([
            'member', 
            'loanScheme',
            'loanAssignDetails' => function($query) {
                $query->where('is_default', true)->where('is_active', true);
            }
        ])
        ->whereHas('loanAssignDetails', function($query) {
            $query->where('is_default', true)->where('is_active', true);
        })
        ->where('status', '!=', 'closed')
        ->where('is_active', true)
        ->when($this->search, function ($query) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('member', function($mq) {
                      $mq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        })
        ->pluck('id')
        ->toArray();
        
        if (!empty($this->selectedLoans)) {
            $this->selectedLoans = [];
        } else {
            $this->selectedLoans = $allLoanIds;
        }
    }

    public function deselectAll()
    {
        $this->selectedLoans = [];
    }

    public function openPaymentModal()
    {
        if (empty($this->selectedLoans)) {
            session()->flash('error', 'Please select at least one loan to pay.');
            return;
        }

        $this->calculatePayment();
        $this->isOpen = 1;
        $this->payment_date = date('Y-m-d');
    }

    public function calculatePayment()
    {
        $this->calculatedPayments = [];
        
        if (empty($this->selectedLoans)) {
            return;
        }

        $totalMonthlyDue = 0;
        $loansData = [];
        
        foreach ($this->selectedLoans as $loanId) {
            $loanItem = collect($this->loanData)->firstWhere('id', $loanId);
            
            if (!$loanItem) continue;
            
            $loanItem['pay_amount'] = $loanItem['total_monthly_due'];
            $totalMonthlyDue += $loanItem['total_monthly_due'];
            $loansData[$loanId] = $loanItem;
        }

        $totalAmount = floatval($this->payment_amount ?? 0);
        
        if ($totalAmount == 0) {
            $totalAmount = $totalMonthlyDue;
        }

        $remainingAmount = $totalAmount;
        
        foreach ($loansData as $loanId => &$loanItem) {
            $balance = $loanItem['balance'];
            $monthlyDue = $loanItem['total_monthly_due'];
            
            if ($totalAmount >= $totalMonthlyDue) {
                $payAmount = min($remainingAmount, $balance);
            } else {
                $ratio = $totalAmount / $totalMonthlyDue;
                $payAmount = min($monthlyDue * min($ratio + 0.5, 1), $balance);
            }
            
            $payAmount = min($payAmount, $balance);
            $remainingAmount -= $payAmount;
            
            $loanItem['pay_amount'] = round($payAmount, 2);
        }
        
        unset($loanItem);
        
        $this->calculatedPayments = $loansData;
        
        if (empty($this->payment_amount)) {
            $this->payment_amount = $totalMonthlyDue;
        }
    }

    public function updatedPaymentAmount()
    {
        $this->calculatePayment();
    }

    public function closeModal()
    {
        $this->isOpen = 0;
        $this->selectedLoans = [];
        $this->calculatedPayments = [];
        $this->payment_amount = '';
        $this->payment_date = '';
        $this->payment_method = 'cash';
        $this->remarks = '';
    }

    public function store()
    {
        $this->validate();

        $userId = Auth::id() ?? 1;

        foreach ($this->selectedLoans as $loanId) {
            $payAmount = $this->calculatedPayments[$loanId]['pay_amount'] ?? 0;
            
            if ($payAmount <= 0) {
                continue;
            }

            $loan = Ec05LoanAssign::find($loanId);
            if (!$loan) {
                continue;
            }

            $totalPaid = Ec11LoanPayment::where('loan_assign_id', $loanId)
                ->where('is_active', true)
                ->sum('payment_total_amount');

            $currentBalance = floatval($loan->loan_amount) - $totalPaid;
            $balanceAfterPayment = $currentBalance - $payAmount;

            $payment = Ec11LoanPayment::create([
                'loan_assign_id' => $loanId,
                'member_id' => $loan->member_id,
                'payment_total_amount' => $payAmount,
                'payment_date' => $this->payment_date,
                'payment_method' => $this->payment_method,
                'is_paid' => true,
                'principal_balance_amount_before_payment' => $currentBalance,
                'principal_balance_amount_after_payment' => $balanceAfterPayment,
                'is_active' => true,
                'remarks' => $this->remarks,
            ]);

            $specs = Ec06LoanAssignDetail::where('loan_assign_id', $loanId)
                ->where('is_default', true)
                ->where('is_active', true)
                ->get();

            foreach ($specs as $spec) {
                Ec12LoanPaymentDetail::create([
                    'loan_payment_id' => $payment->id,
                    'loan_assign_detail_amount' => $payAmount,
                    'is_active' => true,
                    'user_id' => $userId,
                ]);
            }
        }

        session()->flash('message', 'Payment processed successfully.');
        $this->closeModal();
    }
}