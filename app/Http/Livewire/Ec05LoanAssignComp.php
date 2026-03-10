<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec03LoanRequest;
use App\Models\Ec04LoanRequestDetail;
use App\Models\Ec05LoanAssign;
use App\Models\Ec06LoanAssignDetail;
use App\Models\Ec07LoanEmiSchedule;
use App\Models\Ec11LoanPayment;
use App\Models\Ec12LoanPaymentDetail;
use Carbon\Carbon;
use Livewire\WithPagination;

class Ec05LoanAssignComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $status = '';
    public $isOpen = 0;
    public $isDetailOpen = 0;
    public $selectedLoanRequestId = null;
    public $selectedLoanRequest = null;
    public $loanRequestDetails = [];
    public $loanAssignId = null;
    public $assignedLoans = [];
    public $calculatedEmis = [];
    public $selectedLoanAssign = null;
    public $selectedLoanAssignDetails = [];
    public $selectedEmiSchedules = [];

    public $loan_assigned_date = '';
    public $loan_released_date = '';
    public $loan_closed_date = '';
    public $loan_amount = '';
    public $loan_current_balance = '';
    public $roi = '';
    public $is_emi_enabled = false;
    public $no_of_years = '';
    public $no_of_emi = '';
    public $emi_amount = '';
    public $first_emi_due_date = '';
    public $next_emi_due_date = '';
    public $remarks = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'loan_assigned_date' => 'nullable|date',
            'loan_released_date' => 'nullable|date',
            'loan_closed_date' => 'nullable|date',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_current_balance' => 'nullable|numeric|min:0',
            'roi' => 'nullable|numeric|min:0',
            'no_of_emi' => 'nullable|integer|min:0',
            'emi_amount' => 'nullable|numeric|min:0',
            'first_emi_due_date' => 'nullable|date',
            'next_emi_due_date' => 'nullable|date',
        ];
    }

    public function render()
    {
        $unassignedLoans = Ec03LoanRequest::with(['member', 'loanScheme', 'loanRequestDetails'])
            ->whereDoesntHave('loanAssigns')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('loan_amount', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        $this->assignedLoans = Ec05LoanAssign::with(['member', 'loanScheme', 'loanAssignDetails', 'emiSchedules'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($loan) {
                $loan->loan_amount = (float) $loan->loan_amount;
                $loan->loan_current_balance = (float) $loan->loan_current_balance;
                $loan->roi = (float) $loan->roi;
                $loan->emi_amount = (float) $loan->emi_amount;
                $loan->no_of_emi = (int) $loan->no_of_emi;
                return $loan;
            })
            ->toArray();

        return view('livewire.ec05-loan-assign-comp', compact('unassignedLoans'));
    }

    public function openAssignModal($loanRequestId)
    {
        $this->selectedLoanRequestId = $loanRequestId;
        $this->selectedLoanRequest = Ec03LoanRequest::with(['member', 'loanScheme'])->find($loanRequestId);
        
        $this->loanRequestDetails = Ec04LoanRequestDetail::where('loan_request_id', $loanRequestId)
            ->with(['loanSchemeDetail', 'loanSchemeFeature'])
            ->get()
            ->toArray();

        $roiValue = '';
        foreach ($this->loanRequestDetails as $detail) {
            $featureName = strtolower($detail['loan_scheme_feature_name'] ?? '');
            if (strpos($featureName, 'roi') !== false || strpos($featureName, 'interest') !== false) {
                $roiValue = $detail['loan_scheme_feature_value'];
                break;
            }
        }

        $this->loan_assigned_date = now()->toDateString();
        $this->loan_amount = $this->selectedLoanRequest->loan_amount ?? '';
        $this->loan_current_balance = $this->selectedLoanRequest->loan_amount ?? '';
        $this->roi = $roiValue;
        $this->no_of_years = $this->selectedLoanRequest->no_of_years ?? 0;
        $this->no_of_emi = $this->no_of_years * 12;
        $this->is_emi_enabled = $this->selectedLoanRequest->emi_active ?? false;
        
        $this->calculateEmis();
        
        $this->isOpen = true;
    }

    private function calculateEmis()
    {
        $this->calculatedEmis = [];
        
        if (!$this->is_emi_enabled || !$this->loan_amount || !$this->roi || !$this->no_of_emi) {
            return;
        }

        $principal = floatval($this->loan_amount);
        $roi = floatval($this->roi);
        $totalEmis = intval($this->no_of_emi);
        
        if ($principal <= 0 || $roi <= 0 || $totalEmis <= 0) {
            return;
        }

        $monthlyRate = $roi / 12 / 100;
        $monthlyEmi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $totalEmis)) / (pow(1 + $monthlyRate, $totalEmis) - 1);
        
        $this->emi_amount = round($monthlyEmi, 2);
        
        $balance = $principal;
        
        for ($i = 1; $i <= $totalEmis; $i++) {
            $interestAmount = $balance * $monthlyRate;
            $principalAmount = $monthlyEmi - $interestAmount;
            $balance -= $principalAmount;
            
            $this->calculatedEmis[] = [
                'emi_no' => $i,
                'emi_principal' => round($principalAmount, 2),
                'emi_interest' => round($interestAmount, 2),
                'emi_total' => round($monthlyEmi, 2),
                'balance_after' => round(max(0, $balance), 2),
            ];
        }
    }

    public function updatedIsEmiEnabled()
    {
        $this->calculateEmis();
    }

    public function updatedLoanAmount()
    {
        $this->calculateEmis();
    }

    public function updatedRoi()
    {
        $this->calculateEmis();
    }

    public function updatedNoOfYears()
    {
        $this->no_of_emi = intval($this->no_of_years) * 12;
        $this->calculateEmis();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function openDetailModal($loanAssignId)
    {
        $loanAssign = Ec05LoanAssign::with(['member', 'loanScheme', 'loanRequest'])
            ->find($loanAssignId);
        
        $loanAssign->loan_amount = (float) $loanAssign->loan_amount;
        $loanAssign->loan_current_balance = (float) $loanAssign->loan_current_balance;
        $loanAssign->roi = (float) $loanAssign->roi;
        $loanAssign->emi_amount = (float) $loanAssign->emi_amount;
        $loanAssign->no_of_emi = (int) $loanAssign->no_of_emi;
        
        $this->selectedLoanAssign = $loanAssign->toArray();

        $this->selectedLoanAssignDetails = Ec06LoanAssignDetail::where('loan_assign_id', $loanAssignId)
            ->get()
            ->toArray();

        $this->selectedEmiSchedules = Ec07LoanEmiSchedule::where('loan_assign_id', $loanAssignId)
            ->orderBy('emi_schedule_index', 'asc')
            ->get()
            ->map(function ($emi) {
                $emi->total_emi_amount = (float) $emi->total_emi_amount;
                $emi->principal_emi_amount = (float) $emi->principal_emi_amount;
                $emi->interest_emi_amount = (float) $emi->interest_emi_amount;
                $emi->principal_balance_amount_before_emi = (float) $emi->principal_balance_amount_before_emi;
                $emi->principal_balance_amount_after_emi = (float) $emi->principal_balance_amount_after_emi;
                return $emi;
            })
            ->toArray();

        $this->isDetailOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailOpen = false;
        $this->selectedLoanAssign = null;
        $this->selectedLoanAssignDetails = [];
        $this->selectedEmiSchedules = [];
    }

    private function resetInputFields()
    {
        $this->selectedLoanRequestId = null;
        $this->selectedLoanRequest = null;
        $this->loanRequestDetails = [];
        $this->loanAssignId = null;
        $this->assignedLoans = [];
        $this->calculatedEmis = [];
        $this->loan_assigned_date = '';
        $this->loan_released_date = '';
        $this->loan_closed_date = '';
        $this->loan_amount = '';
        $this->loan_current_balance = '';
        $this->roi = '';
        $this->is_emi_enabled = false;
        $this->no_of_years = '';
        $this->no_of_emi = '';
        $this->emi_amount = '';
        $this->first_emi_due_date = '';
        $this->next_emi_due_date = '';
        $this->remarks = '';
        $this->is_active = true;
    }

    public function store()
    {
        $this->validate();

        $loanAssign = Ec05LoanAssign::create([
            'member_id' => $this->selectedLoanRequest->member_id,
            'loan_request_id' => $this->selectedLoanRequest->id,
            'loan_scheme_id' => $this->selectedLoanRequest->loan_scheme_id,
            'name' => $this->selectedLoanRequest->name,
            'description' => $this->selectedLoanRequest->description,
            'loan_assigned_date' => $this->loan_assigned_date,
            'loan_released_date' => $this->loan_released_date,
            'loan_closed_date' => $this->loan_closed_date,
            'loan_amount' => $this->loan_amount,
            'loan_current_balance' => $this->loan_current_balance,
            'roi' => $this->roi,
            'is_emi_enabled' => $this->is_emi_enabled,
            'no_of_emi' => $this->no_of_emi,
            'emi_amount' => $this->emi_amount,
            'first_emi_due_date' => $this->first_emi_due_date,
            'next_emi_due_date' => $this->next_emi_due_date,
            'is_active' => $this->is_active,
            'status' => 'Assigned',
        ]);

        $requestDetails = Ec04LoanRequestDetail::where('loan_request_id', $this->selectedLoanRequest->id)->get();

        foreach ($requestDetails as $detail) {
            Ec06LoanAssignDetail::create([
                'loan_assign_id' => $loanAssign->id,
                'loan_scheme_detail_id' => $detail->loan_scheme_detail_id,
                'loan_scheme_detail_feature_id' => $detail->loan_scheme_feature_id,
                'loan_scheme_detail_feature_name' => $detail->loan_scheme_feature_name,
                'loan_scheme_detail_feature_value' => $detail->loan_scheme_feature_value,
                'loan_scheme_detail_feature_condition' => $detail->loan_scheme_feature_condition,
                'name' => $detail->name,
                'description' => $detail->description,
                'order_index' => $detail->order_index,
                'is_default' => $detail->is_default,
                'is_active' => $detail->is_active,
                'created_by' => $detail->created_by,
                'approved_by' => $detail->approved_by,
                'school_id' => $detail->school_id,
                'remarks' => $detail->remarks,
                'status' => $detail->status,
            ]);
        }

        if ($this->is_emi_enabled && !empty($this->calculatedEmis)) {
            $firstDueDate = $this->first_emi_due_date ? \Carbon\Carbon::parse($this->first_emi_due_date) : \Carbon\Carbon::now()->addMonth();
            
            foreach ($this->calculatedEmis as $emi) {
                $dueDate = $firstDueDate->copy()->addMonths($emi['emi_no'] - 1);
                
                Ec07LoanEmiSchedule::create([
                    'loan_assign_id' => $loanAssign->id,
                    'name' => 'EMI ' . $emi['emi_no'],
                    'emi_schedule_index' => $emi['emi_no'],
                    'emi_due_date' => $dueDate->toDateString(),
                    'total_emi_amount' => $emi['emi_total'],
                    'principal_emi_amount' => $emi['emi_principal'],
                    'interest_emi_amount' => $emi['emi_interest'],
                    'principal_balance_amount_before_emi' => $emi['emi_no'] == 1 ? $this->loan_amount : $this->calculatedEmis[$emi['emi_no'] - 2]['balance_after'] ?? $this->loan_amount,
                    'principal_balance_amount_after_emi' => $emi['balance_after'],
                    'is_active' => true,
                    'status' => 'Pending',
                ]);
            }
        }

        $this->createMockPaymentEntries($loanAssign);

        session()->flash('message', 'Loan Assigned Successfully.');
        $this->closeModal();
    }

    private function createMockPaymentEntries($loanAssign)
    {
        $loanPayment = Ec11LoanPayment::create([
            'loan_assign_id' => $loanAssign->id,
            'member_id' => $loanAssign->member_id,
            'payment_total_amount' => $loanAssign->loan_amount,
            'payment_date' => $loanAssign->loan_assigned_date,
            'payment_method' => 'bank',
            'is_paid' => true,
            'principal_balance_amount_before_payment' => $loanAssign->loan_amount,
            'principal_balance_amount_after_payment' => 0,
            'is_active' => true,
            'remarks' => 'Mock payment entry created on loan assignment',
        ]);

        Ec12LoanPaymentDetail::create([
            'loan_payment_id' => $loanPayment->id,
            'loan_emi_schedule_id' => null,
            'loan_assign_detail_amount' => 0,
            'is_active' => true,
            'remarks' => 'Mock payment detail entry',
        ]);
    }

    public function isShortTerm($noOfYears)
    {
        return $noOfYears <= 1;
    }

    public function isMidTerm($noOfYears)
    {
        return $noOfYears > 1 && $noOfYears <= 5;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
