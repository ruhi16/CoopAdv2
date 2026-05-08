<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec05LoanAssign;
use App\Models\Ec06LoanAssignDetail;
use App\Models\Ec07LoanEmiSchedule;
use App\Models\Ec11LoanPayment;
use App\Models\Ec12LoanPaymentDetail;
use App\Models\Ec01LoanScheme;
use Carbon\Carbon;

class Ec11LoanPaymentComp5 extends Component
{
    public $search = '';
    public $selectedLoans = [];
    public $groupedLoans = [];
    public $schemeNames = [];
    public $isOpen = 0;
    public $payment_date = '';
    public $payment_amount = '';
    public $payment_method = 'cash';
    public $remarks = '';
    public $payment_details = [];
    public $calculatedPayments = [];
    public $confirmingPayment = false;
    public $lumpsum_mode = false;
    public $emi_adjustment_mode = 'reduce_no';
    public $custom_emi_amount = '';

    protected function rules()
    {
        return [
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,upi,other',
        ];
    }

    public function render()
    {
        $this->groupedLoans = [];
        $this->schemeNames = [];

        $loans = Ec05LoanAssign::with(['member', 'loanScheme', 'loanAssignDetails', 'emiSchedules'])
            ->where('status', '!=', 'closed')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('loan_current_balance')
                  ->orWhere('loan_current_balance', '>', 0);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('member', function ($mq) {
                            $mq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('loan_scheme_id')
            ->orderBy('is_emi_enabled', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($loans as $loan) {
            $schemeName = $loan->loanScheme->name ?? 'Uncategorized';
            $isEmi = $loan->is_emi_enabled;
            $groupKey = $schemeName . '_' . ($isEmi ? 'emi' : 'nonemi');

            $totalPaid = Ec11LoanPayment::where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->sum('payment_total_amount');

            $balance = max(0, floatval($loan->loan_amount) - $totalPaid);

            $roi = 0;
            $otherDues = 0;
            $schemeDetails = [];
            $dueDetails = Ec06LoanAssignDetail::where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->get();

            foreach ($dueDetails as $detail) {
                $name = strtolower($detail->loan_scheme_detail_feature_name ?? '');
                if (strpos($name, 'interest') !== false || strpos($name, 'roi') !== false) {
                    $roi = floatval($detail->loan_scheme_detail_feature_value ?? 0);
                } elseif (strpos($name, 'principal') !== false) {
                } else {
                    $val = floatval($detail->loan_scheme_detail_feature_value ?? 0);
                    $type = strtolower($detail->loan_scheme_detail_feature_type ?? 'fixed');
                    if ($type === 'percent') {
                        $otherDues += ($balance * $val) / 100;
                    } else {
                        $otherDues += $val;
                    }
                }
                $schemeDetails[] = [
                    'name' => $detail->loan_scheme_detail_feature_name,
                    'type' => $detail->loan_scheme_detail_feature_type,
                    'value' => $detail->loan_scheme_detail_feature_value,
                ];
            }

            $monthlyInterest = 0;
            if ($roi > 0 && $balance > 0) {
                $monthlyRate = $roi / 12 / 100;
                $monthlyInterest = $balance * $monthlyRate;
            }

            $lastPayments = Ec11LoanPayment::with('paymentDetails')
                ->where('loan_assign_id', $loan->id)
                ->where('is_active', true)
                ->orderBy('payment_date', 'desc')
                ->take(3)
                ->get()
                ->map(function ($p) {
                    return [
                        'date' => $p->payment_date,
                        'amount' => $p->payment_total_amount,
                        'method' => $p->payment_method,
                        'details' => $p->paymentDetails->map(function ($d) {
                            return [
                                'amount' => $d->loan_assign_detail_amount,
                                'remarks' => $d->remarks,
                            ];
                        }),
                    ];
                })
                ->toArray();

            $lastPaymentDate = null;
            if (!empty($lastPayments)) {
                $lastPaymentDate = $lastPayments[0]['date'];
            }
            $daysSinceLastPayment = $lastPaymentDate
                ? Carbon::now()->startOfDay()->diffInDays(Carbon::parse($lastPaymentDate)->startOfDay())
                : ($loan->loan_released_date ? Carbon::now()->startOfDay()->diffInDays(Carbon::parse($loan->loan_released_date)->startOfDay()) : 0);

            $dailyRate = $roi > 0 ? ($roi / 100) / 365 : 0;
            $dueInterest = $balance * $dailyRate * $daysSinceLastPayment;

            $pendingEmis = Ec07LoanEmiSchedule::where('loan_assign_id', $loan->id)
                ->where('status', 'Pending')
                ->orderBy('emi_schedule_index')
                ->get();

            $pendingEmiCount = $pendingEmis->count();
            $nextEmiDueDate = $pendingEmis->first()->emi_due_date ?? null;

            $this->schemeNames[$schemeName] = true;

            $loanEntry = [
                'id' => $loan->id,
                'member_id' => $loan->member->id ?? '-',
                'member_name' => $loan->member->name ?? '-',
                'scheme_id' => $loan->loan_scheme_id,
                'scheme_name' => $schemeName,
                'loan_amount' => floatval($loan->loan_amount),
                'balance' => $balance,
                'roi' => $roi,
                'is_emi_enabled' => $isEmi,
                'emi_amount' => floatval($loan->emi_amount ?? 0),
                'no_of_emi' => intval($loan->no_of_emi ?? 0),
                'loan_duration_in_months' => intval($loan->loan_duration_in_months ?? 0),
                'next_emi_due_date' => $nextEmiDueDate,
                'pending_emi_count' => $pendingEmiCount,
                'monthly_interest' => round($monthlyInterest, 2),
                'due_interest' => round($dueInterest, 2),
                'other_dues' => round($otherDues, 2),
                'days_since_last_payment' => $daysSinceLastPayment,
                'last_payment_date' => $lastPaymentDate,
                'last_payments' => $lastPayments,
                'scheme_details' => $schemeDetails,
                'pending_emis' => $pendingEmis->toArray(),
                'status' => $loan->status,
            ];

            $this->groupedLoans[$groupKey][] = $loanEntry;
        }

        $schemeOrder = Ec01LoanScheme::orderBy('order_index')->pluck('name')->toArray();

        uksort($this->groupedLoans, function ($a, $b) use ($schemeOrder) {
            $partsA = explode('_', $a);
            $partsB = explode('_', $b);
            $schemeA = $partsA[0] ?? '';
            $schemeB = $partsB[0] ?? '';
            $indexA = array_search($schemeA, $schemeOrder);
            $indexB = array_search($schemeB, $schemeOrder);
            if ($indexA === false) $indexA = 999;
            if ($indexB === false) $indexB = 999;
            if ($indexA !== $indexB) return $indexA - $indexB;
            $emiA = ($partsA[1] ?? '') === 'emi' ? 0 : 1;
            $emiB = ($partsB[1] ?? '') === 'emi' ? 0 : 1;
            return $emiA - $emiB;
        });

        return view('livewire.ec11-loan-payment-comp5');
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

    public function selectAllInGroup($groupKey)
    {
        $ids = collect($this->groupedLoans[$groupKey] ?? [])->pluck('id')->toArray();
        foreach ($ids as $id) {
            if (!in_array($id, $this->selectedLoans)) {
                $this->selectedLoans[] = $id;
            }
        }
    }

    public function deselectAllInGroup($groupKey)
    {
        $ids = collect($this->groupedLoans[$groupKey] ?? [])->pluck('id')->toArray();
        $this->selectedLoans = array_values(array_diff($this->selectedLoans, $ids));
    }

    public function openPaymentModal()
    {
        if (empty($this->selectedLoans)) {
            session()->flash('error', 'Please select at least one loan to pay.');
            return;
        }

        $this->payment_date = date('Y-m-d');
        $this->payment_details = [];
        $this->lumpsum_mode = false;
        $this->emi_adjustment_mode = 'reduce_no';
        $this->custom_emi_amount = '';

        foreach ($this->selectedLoans as $loanId) {
            $loanItem = $this->findLoanById($loanId);
            if ($loanItem) {
                $defaultPrincipal = $loanItem['is_emi_enabled']
                    ? $loanItem['emi_amount']
                    : max($loanItem['monthly_interest'] + $loanItem['other_dues'], $loanItem['balance'] * 0.01);
                $this->payment_details[$loanId] = [
                    'principal' => $defaultPrincipal,
                    'interest' => $loanItem['due_interest'],
                    'others' => $loanItem['other_dues'],
                ];
            }
        }

        $this->calculatePayment();
        $this->isOpen = 1;
    }

    private function findLoanById($loanId)
    {
        foreach ($this->groupedLoans as $group) {
            foreach ($group as $loan) {
                if ($loan['id'] == $loanId) return $loan;
            }
        }
        return null;
    }

    public function calculatePayment()
    {
        $this->calculatedPayments = [];
        if (empty($this->selectedLoans)) return;

        $totalPayment = 0;

        foreach ($this->selectedLoans as $loanId) {
            $loanItem = $this->findLoanById($loanId);
            if (!$loanItem) continue;

            $details = $this->payment_details[$loanId] ?? ['principal' => 0, 'interest' => 0, 'others' => 0];
            $principal = floatval($details['principal'] ?? 0);
            $interest = floatval($details['interest'] ?? 0);
            $others = floatval($details['others'] ?? 0);
            $total = $principal + $interest + $others;

            $this->calculatedPayments[$loanId] = [
                'loan_id' => $loanId,
                'member_name' => $loanItem['member_name'],
                'scheme_name' => $loanItem['scheme_name'],
                'roi' => $loanItem['roi'],
                'balance' => $loanItem['balance'],
                'is_emi_enabled' => $loanItem['is_emi_enabled'],
                'emi_amount' => $loanItem['emi_amount'],
                'pending_emi_count' => $loanItem['pending_emi_count'],
                'principal' => round($principal, 2),
                'interest' => round($interest, 2),
                'others' => round($others, 2),
                'total' => round($total, 2),
                'balance_after' => round(max(0, $loanItem['balance'] - $principal), 2),
            ];

            $totalPayment += $total;
        }

        $this->payment_amount = round($totalPayment, 2);
    }

    public function updatedPaymentDetails()
    {
        $this->calculatePayment();
    }

    public function updatedPaymentDate()
    {
        $this->recalculateInterest();
        $this->calculatePayment();
    }

    public function updatedLumpsumMode()
    {
        if (!$this->lumpsum_mode) {
            $this->custom_emi_amount = '';
        }
    }

    public function toggleLumpsum()
    {
        $this->lumpsum_mode = !$this->lumpsum_mode;
        if (!$this->lumpsum_mode) {
            $this->custom_emi_amount = '';
        }
    }

    public function updatedCustomEmiAmount()
    {
        if ($this->lumpsum_mode && $this->custom_emi_amount) {
            $totalExtra = floatval($this->custom_emi_amount);
            if ($totalExtra > 0) {
                foreach ($this->selectedLoans as $loanId) {
                    $loanItem = $this->findLoanById($loanId);
                    if ($loanItem && $loanItem['is_emi_enabled'] && isset($this->payment_details[$loanId])) {
                        $this->payment_details[$loanId]['principal'] += $totalExtra / count($this->selectedLoans);
                    }
                }
            }
            $this->calculatePayment();
        }
    }

    private function recalculateInterest()
    {
        foreach ($this->payment_details as $loanId => &$details) {
            $loanItem = $this->findLoanById($loanId);
            if (!$loanItem) continue;

            $lastDate = $loanItem['last_payment_date'] ?? $this->getLoanReleaseDate($loanId);
            $days = 0;
            if ($lastDate) {
                $days = Carbon::parse($lastDate)->startOfDay()->diffInDays(Carbon::parse($this->payment_date ?: date('Y-m-d'))->startOfDay());
                if ($days < 0) $days = 0;
            }

            $dailyRate = $loanItem['roi'] > 0 ? ($loanItem['roi'] / 100) / 365 : 0;
            $calculatedInterest = $loanItem['balance'] * $dailyRate * max(1, $days);

            if (isset($details)) {
                $details['interest'] = round($calculatedInterest, 2);
            }
        }
        unset($details);
    }

    private function getLoanReleaseDate($loanId)
    {
        foreach ($this->groupedLoans as $group) {
            foreach ($group as $loan) {
                if ($loan['id'] == $loanId) return $loan['last_payment_date'] ?? null;
            }
        }
        return null;
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
            if (!$paymentData || $paymentData['total'] <= 0) continue;

            $loan = Ec05LoanAssign::find($loanId);
            if (!$loan) continue;

            $details = $this->payment_details[$loanId] ?? ['principal' => 0, 'interest' => 0, 'others' => 0];
            $principalAmount = floatval($details['principal'] ?? 0);
            $interestAmount = floatval($details['interest'] ?? 0);
            $otherAmount = floatval($details['others'] ?? 0);
            $payAmount = $principalAmount + $interestAmount + $otherAmount;
            $currentBalance = floatval($paymentData['balance']);
            $balanceAfterPayment = max(0, $currentBalance - $principalAmount);

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

            Ec12LoanPaymentDetail::create([
                'loan_payment_id' => $payment->id,
                'loan_assign_detail_amount' => $principalAmount,
                'is_active' => true,
                'remarks' => 'Principal Payment',
            ]);

            if ($interestAmount > 0) {
                Ec12LoanPaymentDetail::create([
                    'loan_payment_id' => $payment->id,
                    'loan_assign_detail_amount' => $interestAmount,
                    'is_active' => true,
                    'remarks' => 'Interest Payment',
                ]);
            }

            if ($otherAmount > 0) {
                Ec12LoanPaymentDetail::create([
                    'loan_payment_id' => $payment->id,
                    'loan_assign_detail_amount' => $otherAmount,
                    'is_active' => true,
                    'remarks' => 'Other Charges Payment',
                ]);
            }

            $loan->loan_current_balance = $balanceAfterPayment;

            // Handle EMI schedule adjustments for EMI-enabled loans
            if ($loanItem = $this->findLoanById($loanId)) {
                if ($loanItem['is_emi_enabled'] && $principalAmount > 0) {
                    $pendingEmis = Ec07LoanEmiSchedule::where('loan_assign_id', $loanId)
                        ->where('status', 'Pending')
                        ->orderBy('emi_schedule_index')
                        ->get();

                    if ($pendingEmis->isNotEmpty()) {
                        if ($this->lumpsum_mode) {
                            if ($this->emi_adjustment_mode === 'reduce_no') {
                                // Reduce the number of EMIs
                                $emiAmount = floatval($loan->emi_amount);
                                if ($emiAmount > 0) {
                                    $emisToReduce = intdiv($principalAmount, $emiAmount);
                                    $adjustmentRemainder = fmod($principalAmount, $emiAmount);

                                    $countToRemove = min($emisToReduce, $pendingEmis->count());
                                    for ($i = 0; $i < $countToRemove; $i++) {
                                        $pendingEmis[$i]->update([
                                            'status' => 'Adjusted',
                                            'remarks' => 'Lumpsum payment - EMI #' . $pendingEmis[$i]->emi_schedule_index . ' adjusted',
                                        ]);
                                    }

                                    if ($adjustmentRemainder > 0 && $countToRemove < $pendingEmis->count()) {
                                        $nextEmi = $pendingEmis[$countToRemove];
                                        $newRemaining = max(0, $nextEmi->total_emi_amount - $adjustmentRemainder);
                                        $nextEmi->update([
                                            'total_emi_amount' => $newRemaining,
                                            'principal_emi_amount' => max(0, floatval($nextEmi->principal_emi_amount) - $adjustmentRemainder),
                                            'remarks' => 'Lumpsum adjustment applied',
                                        ]);
                                    }
                                }
                            } else {
                                // Keep amount fixed but adjust number of EMIs
                                // Recalculate remaining EMIs with the same EMI amount
                                $emiAmount = floatval($loan->emi_amount);
                                $remainingBalance = $balanceAfterPayment;
                                $newEmiCount = 0;
                                $monthlyRate = $loan->roi > 0 ? ($loan->roi / 100 / 12) : 0;

                                $tempBalance = $remainingBalance;
                                if ($monthlyRate > 0 && $emiAmount > 0) {
                                    $newEmiCount = 0;
                                    while ($tempBalance > 0 && $newEmiCount < 240) {
                                        $interestPart = $tempBalance * $monthlyRate;
                                        $principalPart = $emiAmount - $interestPart;
                                        $tempBalance -= $principalPart;
                                        $newEmiCount++;
                                    }
                                } else {
                                    $newEmiCount = ceil($remainingBalance / $emiAmount);
                                }

                                $newEmiCount = max(1, $newEmiCount);

                                // Mark existing pending EMIs as adjusted
                                foreach ($pendingEmis as $pe) {
                                    $pe->update(['status' => 'Adjusted', 'remarks' => 'Recalculated due to lumpsum payment']);
                                }

                                // Create new EMIs
                                $firstDueDate = $pendingEmis->first()->emi_due_date ?? Carbon::now()->addMonth();
                                for ($i = 0; $i < $newEmiCount; $i++) {
                                    $dueDate = Carbon::parse($firstDueDate)->addMonths($i);
                                    $remainingPrincipal = $remainingBalance - ($i * ($emiAmount - ($remainingBalance * $monthlyRate)));
                                    $interestPart = $remainingPrincipal * $monthlyRate;
                                    $principalPart = $emiAmount - $interestPart;
                                    if ($i === $newEmiCount - 1) {
                                        $principalPart = $remainingBalance;
                                        $interestPart = 0;
                                    }
                                    $remainingBalance -= $principalPart;

                                    Ec07LoanEmiSchedule::create([
                                        'loan_assign_id' => $loanId,
                                        'name' => 'EMI ' . ($i + 1),
                                        'emi_schedule_index' => $i + 1,
                                        'emi_due_date' => $dueDate->toDateString(),
                                        'total_emi_amount' => round($principalPart + $interestPart, 2),
                                        'principal_emi_amount' => round($principalPart, 2),
                                        'interest_emi_amount' => round($interestPart, 2),
                                        'principal_balance_amount_before_emi' => round($remainingBalance + $principalPart, 2),
                                        'principal_balance_amount_after_emi' => round(max(0, $remainingBalance), 2),
                                        'is_active' => true,
                                        'status' => 'Pending',
                                    ]);
                                }

                                $loan->no_of_emi = $newEmiCount;
                            }
                        } else {
                            // Normal payment - mark the closest pending EMIs as paid
                            $remainingPrincipal = $principalAmount;
                            foreach ($pendingEmis as $pe) {
                                if ($remainingPrincipal <= 0) break;
                                $emiTotal = floatval($pe->total_emi_amount);
                                $emiPrincipal = floatval($pe->principal_emi_amount);
                                $emiInterest = floatval($pe->interest_emi_amount);

                                if ($remainingPrincipal >= $emiPrincipal) {
                                    $pe->update([
                                        'status' => 'Paid',
                                        'emi_paid_date' => $this->payment_date,
                                        'remarks' => 'Paid via payment #' . $payment->id,
                                    ]);
                                    $remainingPrincipal -= $emiPrincipal;
                                } else {
                                    $newPrincipal = $emiPrincipal - $remainingPrincipal;
                                    $pe->update([
                                        'principal_emi_amount' => max(0, $newPrincipal),
                                        'total_emi_amount' => max(0, $newPrincipal + $emiInterest),
                                        'remarks' => 'Partial payment #' . $payment->id . ' - remaining principal adjusted',
                                    ]);
                                    $remainingPrincipal = 0;
                                }
                            }
                        }
                    }
                }
            }

            $loan->save();
        }

        session()->flash('message', 'Payment processed successfully.');
        $this->closeModal();
    }

    public function cancelConfirmation()
    {
        $this->confirmingPayment = false;
    }

    public function closeModal()
    {
        $this->isOpen = 0;
        $this->selectedLoans = [];
        $this->payment_details = [];
        $this->calculatedPayments = [];
        $this->confirmingPayment = false;
        $this->payment_amount = '';
        $this->payment_date = '';
        $this->payment_method = 'cash';
        $this->remarks = '';
        $this->lumpsum_mode = false;
        $this->emi_adjustment_mode = 'reduce_no';
        $this->custom_emi_amount = '';
    }
}
