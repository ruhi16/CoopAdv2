<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec05LoanAssign;
use App\Models\Ec06LoanAssignDetail;
use App\Models\Ec11LoanPayment;
use App\Models\Ec12LoanPaymentDetail;
use Illuminate\Support\Facades\Auth;

class Ec11LoanPaymentComp4 extends Component
{
    public $search = '';
    public $selectedLoans = [];
    public $loanData = [];
    public $isOpen = 0;
    public $payment_date = '';
    public $payment_amount = '';
    public $payment_method = 'cash';
    public $remarks = '';
    public $calculatedPayments = [];
    public $payment_details = []; // [loanId => ['principal' => 0, 'interest' => 0, 'others' => 0]]
    public $confirmingPayment = false;

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
        $this->loanData = [];

        $loans = Ec05LoanAssign::with([
            'member',
            'loanScheme',
            'loanAssignDetails' => function ($query) {
                $query->where('is_default', true)->where('is_active', true);
            }
        ])
            ->whereHas('loanAssignDetails', function ($query) {
                $query->where('is_default', true)->where('is_active', true);
            })
            ->where('status', '!=', 'closed')
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('member', function ($mq) {
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
                    $type = strtolower($detail->loan_scheme_detail_feature_type ?? 'fixed');
                    $val = floatval($detail->loan_scheme_detail_feature_value ?? 0);
                    if ($type === 'percent') {
                        $other_dues += ($loan->loan_current_balance * $val) / 100;
                    } else {
                        $other_dues += $val;
                    }
                }

                $schemeDetails[] = [
                    'name' => $detail->loan_scheme_detail_feature_name,
                    'type' => $detail->loan_scheme_detail_feature_type,
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

            $paymentHistory = Ec11LoanPayment::with('paymentDetails')
                ->where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->orderBy('payment_date', 'desc')
                ->take(5)
                ->get()
                ->map(function ($payment) {
                    return [
                        'payment_date' => $payment->payment_date,
                        'payment_total_amount' => $payment->payment_total_amount,
                        'payment_method' => $payment->payment_method,
                        'remarks' => $payment->remarks,
                        'payment_details' => $payment->paymentDetails,
                    ];
                })
                ->toArray();

            $this->loanData[] = [
                'id' => $loan->id,
                'name' => $loan->name,
                'member_id' => $loan->member->id ?? '-',
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
                'last_payment_date' => $lastPayment ? $lastPayment->payment_date : $loan->loan_released_date,
                'payment_history' => $paymentHistory,
            ];
        }

        return view('livewire.ec11-loan-payment-comp4');
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
            'loanAssignDetails' => function ($query) {
                $query->where('is_default', true)->where('is_active', true);
            }
        ])
            ->whereHas('loanAssignDetails', function ($query) {
                $query->where('is_default', true)->where('is_active', true);
            })
            ->where('status', '!=', 'closed')
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('member', function ($mq) {
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

        $this->payment_date = date('Y-m-d');
        $this->payment_details = [];

        foreach ($this->selectedLoans as $loanId) {
            $loanItem = collect($this->loanData)->firstWhere('id', $loanId);
            if ($loanItem) {
                $this->payment_details[$loanId] = [
                    'principal' => $loanItem['monthly_emi'] > 0 ? $loanItem['monthly_emi'] : 0,
                    'interest' => 0,
                    'others' => 0,
                ];
            }
        }

        $this->calculatePayment();
        $this->isOpen = 1;
    }

    public function calculatePayment()
    {
        $this->calculatedPayments = [];
        if (empty($this->selectedLoans)) return;

        $totalPaymentAmount = 0;

        foreach ($this->selectedLoans as $loanId) {
            $loanItem = collect($this->loanData)->firstWhere('id', $loanId);
            if (!$loanItem) continue;

            $lastDate = $loanItem['last_payment_date'] ? new \DateTime($loanItem['last_payment_date']) : null;
            $currentDate = new \DateTime($this->payment_date ?: date('Y-m-d'));

            $days = 0;
            if ($lastDate) {
                $interval = $lastDate->diff($currentDate);
                $days = $interval->days;
                if ($interval->invert) $days = 0;
            }

            $dailyRate = ($loanItem['roi'] / 100) / 365;
            $calculatedInterest = $loanItem['balance'] * $dailyRate * $days;

            $otherDues = 0;
            if (!empty($loanItem['scheme_details'])) {
                foreach ($loanItem['scheme_details'] as $detail) {
                    $name = strtolower($detail['name'] ?? '');
                    if (strpos($name, 'interest') === false && strpos($name, 'roi') === false && strpos($name, 'principal') === false) {
                        $val = floatval($detail['value'] ?? 0);
                        $type = strtolower($detail['type'] ?? 'fixed');
                        
                        if ($type === 'percent') {
                            $otherDues += ($loanItem['balance'] * $val) / 100;
                        } else {
                            $otherDues += $val;
                        }
                    }
                }
            }

            // Update details array with calculated values if not manually overridden
            if (!isset($this->payment_details[$loanId]['interest'])) {
                $this->payment_details[$loanId]['interest'] = 0;
            }
            if (!isset($this->payment_details[$loanId]['others'])) {
                $this->payment_details[$loanId]['others'] = 0;
            }

            $principalInput = floatval($this->payment_details[$loanId]['principal'] ?? 0);
            $interestInput = floatval($this->payment_details[$loanId]['interest'] ?? $calculatedInterest);
            $othersInput = floatval($this->payment_details[$loanId]['others'] ?? $otherDues);

            $totalForThisLoan = $principalInput + $interestInput + $othersInput;

            $this->calculatedPayments[$loanId] = [
                'loan_id' => $loanId,
                'name' => $loanItem['name'],
                'member_name' => $loanItem['member_name'],
                'scheme_name' => $loanItem['scheme_name'],
                'roi' => $loanItem['roi'],
                'balance' => $loanItem['balance'],
                'days' => $days,
                'interest' => round($interestInput, 2),
                'others' => round($othersInput, 2),
                'principal' => round($principalInput, 2),
                'total' => round($totalForThisLoan, 2),
            ];

            $totalPaymentAmount += $totalForThisLoan;
        }

        $this->payment_amount = round($totalPaymentAmount, 2);
    }

    public function updatedPaymentDate()
    {
        $this->calculatePayment();
    }

    public function updatedPaymentDetails()
    {
        $this->calculatePayment();
    }

    public function closeModal()
    {
        $this->isOpen = 0;
        $this->selectedLoans = [];
        $this->calculatedPayments = [];
        $this->payment_details = [];
        $this->confirmingPayment = false;
        $this->payment_amount = '';
        $this->payment_date = '';
        $this->payment_method = 'cash';
        $this->remarks = '';
    }

public function store()
    {
        $this->validate();

        if (!$this->confirmingPayment) {
            $this->confirmingPayment = true;
            return;
        }

        foreach ($this->selectedLoans as $loanId) {
            $paymentData = $this->calculatedPayments[$loanId] ?? null;
            if (!$paymentData || $paymentData['total'] <= 0) {
                continue;
            }

            $loan = Ec05LoanAssign::find($loanId);
            if (!$loan) {
                continue;
            }

            $currentBalance = $paymentData['balance'];
            $principalAmount = floatval($this->payment_details[$loanId]['principal'] ?? 0);
            $interestAmount = floatval($this->payment_details[$loanId]['interest'] ?? 0);
            $otherAmount = floatval($this->payment_details[$loanId]['others'] ?? 0);
            $payAmount = $principalAmount + $interestAmount + $otherAmount;

            $balanceAfterPayment = $currentBalance - $principalAmount;

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

            // Always create a detail entry for Principal
            Ec12LoanPaymentDetail::create([
                'loan_payment_id' => $payment->id,
                'loan_assign_detail_amount' => $principalAmount,
                'is_active' => true,
                'remarks' => 'Principal Payment',
            ]);

            // Create detail entry for Interest if applicable
            if ($interestAmount > 0) {
                Ec12LoanPaymentDetail::create([
                    'loan_payment_id' => $payment->id,
                    'loan_assign_detail_amount' => $interestAmount,
                    'is_active' => true,
                    'remarks' => 'Interest Payment',
                ]);
            }

            // Create detail entry for Other charges if applicable
            if ($otherAmount > 0) {
                Ec12LoanPaymentDetail::create([
                    'loan_payment_id' => $payment->id,
                    'loan_assign_detail_amount' => $otherAmount,
                    'is_active' => true,
                    'remarks' => 'Other Charges Payment',
                ]);
            }

            if (isset($loan->loan_current_balance)) {
                $loan->update(['loan_current_balance' => $balanceAfterPayment]);
            }
        }

        session()->flash('message', 'Payment processed successfully.');
        $this->closeModal();
    }

    public function cancelConfirmation()
    {
        $this->confirmingPayment = false;
    }
}
