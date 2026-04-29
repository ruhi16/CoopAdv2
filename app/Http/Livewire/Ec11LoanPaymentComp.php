<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec05LoanAssign;
use App\Models\Ec11LoanPayment;
use App\Models\Ec07LoanEmiSchedule;
use App\Models\Ec06LoanAssignDetail;
use App\Models\Ec12LoanPaymentDetail;
use App\Models\MemberDb;
use Carbon\Carbon;
use Livewire\WithPagination;

class Ec11LoanPaymentComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $member_filter = '';
    public $perPage = 10;
    public $selectedLoans = [];
    public $isOpen = 0;
    public $isMultiPaymentOpen = 0;
    public $payment_id = null;
    public $loan_assign_id = '';
    public $selectedLoan = null;
    public $payment_details = [];
    public $nextEmi = null;
    public $lastPayment = null;
    public $pendingInterest = 0;
    public $pendingFine = 0;
    public $totalDue = 0;

    public $selectedLoansData = [];
    public $paymentItems = [];
    public $grandTotal = 0;

    public $payment_total_amount = '';
    public $payment_date = '';
    public $payment_method = 'cash';
    public $remarks = '';
    public $confirmingPayment = null;

    protected function rules()
    {
        return [
            'loan_assign_id' => 'required|integer',
            'payment_total_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ];
    }

    public function render()
    {
        $members = MemberDb::orderBy('name')->get();

        $perPageValue = $this->perPage === 'all' ? 1000 : (int) $this->perPage;

        $assignedLoans = Ec05LoanAssign::with(['member', 'loanScheme', 'emiSchedules'])
            ->where('is_active', true)
            ->where('status', 'Assigned')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->member_filter, function ($query) {
                $query->where('member_id', $this->member_filter);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPageValue);

        $payments = Ec11LoanPayment::with(['loanAssign.member'])
            ->orderBy('id', 'desc')
            ->paginate($perPageValue);

        return view('livewire.ec11-loan-payment-comp', compact('assignedLoans', 'payments', 'members'));
    }

    public function openPaymentModal($loanAssignId)
    {
        $this->loan_assign_id = $loanAssignId;
        $this->selectedLoan = Ec05LoanAssign::with(['member', 'loanScheme'])->find($loanAssignId);

        $this->lastPayment = Ec11LoanPayment::where('loan_assign_id', $loanAssignId)
            ->where('is_paid', true)
            ->orderBy('payment_date', 'desc')
            ->first();

        if ($this->selectedLoan->is_emi_enabled) {
            $this->nextEmi = Ec07LoanEmiSchedule::where('loan_assign_id', $loanAssignId)
                ->where('status', 'Pending')
                ->orderBy('emi_schedule_index', 'asc')
                ->first();

            if ($this->nextEmi) {
                $this->payment_total_amount = $this->nextEmi->total_emi_amount;
            } else {
                $this->payment_total_amount = $this->selectedLoan->emi_amount ?? 0;
            }
        } else {
            $lastDate = $this->lastPayment ? $this->lastPayment->payment_date : $this->selectedLoan->loan_assigned_date;
            $daysSinceLastPayment = \Carbon\Carbon::parse($lastDate)->diffInDays(\Carbon\Carbon::now());
            
            $this->pendingInterest = 0;
            if ($daysSinceLastPayment > 0 && $this->selectedLoan->roi > 0) {
                $this->pendingInterest = ($this->selectedLoan->loan_current_balance * $this->selectedLoan->roi * $daysSinceLastPayment) / 36500;
            }

            if ($daysSinceLastPayment > 30) {
                $this->pendingFine = ($daysSinceLastPayment - 30) * 100;
            }

            $this->totalDue = $this->selectedLoan->loan_current_balance + $this->pendingInterest + $this->pendingFine;

            if ($this->pendingInterest > 0 || $this->pendingFine > 0) {
                $this->payment_total_amount = $this->selectedLoan->emi_amount ?? ($this->selectedLoan->loan_amount * 0.02);
            } else {
                $this->payment_total_amount = $this->selectedLoan->emi_amount ?? ($this->selectedLoan->loan_amount * 0.01);
            }
        }

        $this->payment_date = now()->toDateString();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetPaymentFields();
    }

    private function resetPaymentFields()
    {
        $this->payment_id = null;
        $this->loan_assign_id = '';
        $this->selectedLoan = null;
        $this->nextEmi = null;
        $this->lastPayment = null;
        $this->pendingInterest = 0;
        $this->pendingFine = 0;
        $this->totalDue = 0;
        $this->payment_total_amount = '';
        $this->payment_date = '';
        $this->payment_method = 'cash';
        $this->remarks = '';
        $this->confirmingPayment = null;
    }

    public function store()
    {
        $validated = $this->validate();

        $loanAssign = Ec05LoanAssign::find($this->loan_assign_id);
        $balanceBefore = $loanAssign->loan_current_balance;
        
        $amount = floatval($this->payment_total_amount);
        $newBalance = max(0, $balanceBefore - $amount);

        $payment = Ec11LoanPayment::create([
            'loan_assign_id' => $this->loan_assign_id,
            'member_id' => $loanAssign->member_id,
            'payment_total_amount' => $amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'is_paid' => true,
            'principal_balance_amount_before_payment' => $balanceBefore,
            'principal_balance_amount_after_payment' => $newBalance,
            'is_active' => true,
            'remarks' => $this->remarks,
        ]);

        $loanAssign->update([
            'loan_current_balance' => $newBalance,
            'next_emi_due_date' => now()->addMonth()->toDateString(),
        ]);

        if ($loanAssign->is_emi_enabled && $this->nextEmi) {
            $this->nextEmi->update([
                'emi_paid_date' => $this->payment_date,
                'principal_balance_amount_after_emi' => $newBalance,
                'status' => 'Paid',
            ]);
        }

        session()->flash('message', 'Payment Recorded Successfully.');
        $this->closeModal();
    }

    public function confirmPayment($id)
    {
        $this->confirmingPayment = $id;
    }

    public function cancelConfirmPayment()
    {
        $this->confirmingPayment = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleAll()
    {
        if (count($this->selectedLoans) === count($this->assignedLoans->pluck('id')->toArray())) {
            $this->selectedLoans = [];
        } else {
            $this->selectedLoans = $this->assignedLoans->pluck('id')->toArray();
        }
    }

    public function openMultiPaymentModal()
    {
        if (empty($this->selectedLoans)) {
            session()->flash('error', 'Please select at least one loan.');
            return;
        }

        $this->selectedLoansData = Ec05LoanAssign::with(['member', 'loanScheme', 'loanAssignDetails', 'emiSchedules'])
            ->whereIn('id', $this->selectedLoans)
            ->get()
            ->toArray();

        $this->paymentItems = [];
        $this->grandTotal = 0;

        foreach ($this->selectedLoansData as $loan) {
            $principal = $loan['emi_amount'] ?? ($loan['loan_amount'] * 0.02);
            $interest = 0;
            
            $assignDetails = array_filter($loan['loan_assign_details'] ?? [], function($d) {
                return ($d['is_active'] ?? false) === true;
            });
            
            $otherAmount = 0;
            foreach ($assignDetails as $detail) {
                $featureName = strtolower($detail['loan_scheme_detail_feature_name'] ?? '');
                if (strpos($featureName, 'interest') !== false || strpos($featureName, 'roi') !== false) {
                    $otherAmount += floatval($detail['loan_scheme_detail_feature_value'] ?? 0);
                }
            }

            $this->paymentItems[] = [
                'loan_assign_id' => $loan['id'],
                'member_name' => $loan['member']['name'] ?? '-',
                'loan_name' => $loan['name'],
                'loan_amount' => $loan['loan_amount'],
                'current_balance' => $loan['loan_current_balance'],
                'principal' => $principal,
                'interest' => $interest,
                'other' => $otherAmount,
                'total' => $principal + $interest + $otherAmount,
            ];
            
            $this->grandTotal += ($principal + $interest + $otherAmount);
        }

        $this->payment_date = now()->toDateString();
        $this->isMultiPaymentOpen = true;
    }

    public function updatePaymentItemTotal($index)
    {
        $principal = floatval($this->paymentItems[$index]['principal'] ?? 0);
        $interest = floatval($this->paymentItems[$index]['interest'] ?? 0);
        $other = floatval($this->paymentItems[$index]['other'] ?? 0);
        
        $this->paymentItems[$index]['total'] = $principal + $interest + $other;
        
        $this->grandTotal = array_sum(array_column($this->paymentItems, 'total'));
    }

    public function closeMultiPaymentModal()
    {
        $this->isMultiPaymentOpen = false;
        $this->selectedLoans = [];
        $this->selectedLoansData = [];
        $this->paymentItems = [];
        $this->grandTotal = 0;
    }

    public function storeMultiPayment()
    {
        foreach ($this->paymentItems as $item) {
            if ($item['total'] <= 0) {
                continue;
            }

            $loanAssign = Ec05LoanAssign::find($item['loan_assign_id']);
            if (!$loanAssign) {
                continue;
            }

            $balanceBefore = $loanAssign->loan_current_balance;
            $newBalance = max(0, $balanceBefore - floatval($item['total']));

            $payment = Ec11LoanPayment::create([
                'loan_assign_id' => $item['loan_assign_id'],
                'member_id' => $loanAssign->member_id,
                'payment_total_amount' => $item['total'],
                'payment_date' => $this->payment_date,
                'payment_method' => $this->payment_method,
                'is_paid' => true,
                'principal_balance_amount_before_payment' => $balanceBefore,
                'principal_balance_amount_after_payment' => $newBalance,
                'is_active' => true,
                'remarks' => $this->remarks,
            ]);

            $loanAssign->update([
                'loan_current_balance' => $newBalance,
                'next_emi_due_date' => now()->addMonth()->toDateString(),
            ]);

            $assignDetails = Ec06LoanAssignDetail::where('loan_assign_id', $item['loan_assign_id'])
                ->where('is_active', true)
                ->get();

            foreach ($assignDetails as $detail) {
                Ec12LoanPaymentDetail::create([
                    'loan_payment_id' => $payment->id,
                    'loan_assign_detail_amount' => floatval($item['other'] ?? 0),
                    'is_active' => true,
                ]);
            }

            $pendingEmi = Ec07LoanEmiSchedule::where('loan_assign_id', $item['loan_assign_id'])
                ->where('status', 'Pending')
                ->orderBy('emi_schedule_index', 'asc')
                ->first();

            if ($pendingEmi) {
                $pendingEmi->update([
                    'emi_paid_date' => $this->payment_date,
                    'principal_balance_amount_after_emi' => $newBalance,
                    'status' => 'Paid',
                ]);
            }
        }

        session()->flash('message', 'Payments Recorded Successfully.');
        $this->closeMultiPaymentModal();
    }
}
