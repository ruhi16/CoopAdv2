<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec05LoanAssign;
use App\Models\MemberDb;
use App\Models\Ec01LoanScheme;
use App\Models\Ec03LoanRequest;
use App\Models\Ec02LoanSchemeDetail;
use App\Models\Ec04LoanRequestDetail;
use App\Models\Ec06LoanAssignDetail;
use App\Models\Ec07LoanEmiSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec05LoanAssignComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $members = [];
    public $loanSchemes = [];
    public $loanRequests = [];
    public $allLoanRequests = [];
    public $schemeDetails = [];

    public $selectedMemberId = '';
    public $selectedLoanSchemeId = '';
    public $selectedLoanRequestId = '';
    public $name = '';
    public $description = '';
    public $loanAmount = '';
    public $roi = '';
    public $noOfEmi = '';
    public $emiAmount = '';
    public $loanAssignedDate = '';
    public $loanReleasedDate = '';
    public $loanClosedDate = '';
    public $isEmiEnabled = false;
    public $isDefault = false;
    public $isActive = true;
    public $remarks = '';
    public $status = 'pending';

    public $editId = null;
    public $isOpen = false;
    public $search = '';
    public $confirmingDelete = null;

    protected function rules()
    {
        return [
            'selectedLoanRequestId' => 'required|integer|min:1',
            'selectedMemberId' => 'required|integer|min:1',
            'selectedLoanSchemeId' => 'required|integer|min:1',
            'loanAmount' => 'required|numeric|min:1',
            'roi' => 'required|numeric|min:0',
            'noOfEmi' => 'required|integer|min:1',
            'loanAssignedDate' => 'required|date',
            'status' => 'required|string',
        ];
    }

    public function mount()
    {
        $this->loadMembers();
        $this->loadLoanSchemes();
        $this->loanAssignedDate = now()->toDateString();
    }

    public function loadMembers()
    {
        $memberIdsWithRequests = Ec03LoanRequest::withoutGlobalScopes()
            ->whereIn('status', ['approved', 'pending'])
            ->distinct()
            ->pluck('member_id');

        $this->members = MemberDb::withoutGlobalScopes()
            ->select('id', 'name', 'member_type_id')
            ->with('memberType:id,name')
            ->whereIn('id', $memberIdsWithRequests)
            ->orderBy('name')
            ->get();
    }

    public function loadLoanSchemes()
    {
        $this->loanSchemes = Ec01LoanScheme::withoutGlobalScopes()
            ->select('id', 'name', 'is_active')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadLoanRequests()
    {
        $assignedRequestIds = Ec05LoanAssign::withoutGlobalScopes()
            ->whereNotNull('loan_request_id')
            ->pluck('loan_request_id')
            ->toArray();

        if (!empty($this->selectedMemberId)) {
            $this->loanRequests = Ec03LoanRequest::withoutGlobalScopes()
                ->where('member_id', $this->selectedMemberId)
                ->whereNotIn('id', $assignedRequestIds)
                ->whereIn('status', ['approved', 'pending'])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $this->loanRequests = [];
        }
    }

    public function loadAllUnassignedLoanRequests()
    {
        $assignedRequestIds = Ec05LoanAssign::withoutGlobalScopes()
            ->whereNotNull('loan_request_id')
            ->pluck('loan_request_id')
            ->toArray();

        return Ec03LoanRequest::withoutGlobalScopes()
            ->with(['member', 'loanScheme'])
            ->whereNotIn('id', $assignedRequestIds)
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function loadSchemeDetails()
    {
        if (!empty($this->selectedLoanSchemeId)) {
            $this->schemeDetails = Ec02LoanSchemeDetail::withoutGlobalScopes()
                ->where('loan_scheme_id', $this->selectedLoanSchemeId)
                ->where('is_active', true)
                ->get();
        } else {
            $this->schemeDetails = [];
        }
    }

    public function updatedSelectedMemberId($value)
    {
        $this->loadLoanRequests();
        $this->selectedLoanRequestId = '';
    }

    public function updatedSelectedLoanSchemeId($value)
    {
        $this->loadSchemeDetails();
        $this->calculateEmi();
    }

    public function updatedSelectedLoanRequestId($value)
    {
        if (!empty($value)) {
            $loanRequest = Ec03LoanRequest::withoutGlobalScopes()->find($value);
            if ($loanRequest) {
                $this->loanAmount = $loanRequest->loan_amount;
                $this->selectedMemberId = $loanRequest->member_id;
                $this->selectedLoanSchemeId = $loanRequest->loan_scheme_id;
                $this->name = $loanRequest->name;
                $this->description = $loanRequest->description;
                $this->noOfEmi = $loanRequest->no_of_years * 12;
                $this->isEmiEnabled = $loanRequest->emi_active;
                $this->loadLoanRequests();
                $this->loadSchemeDetails();
                $this->calculateEmi();
            }
        }
    }

    public function calculateEmi()
    {
        if (!empty($this->loanAmount) && !empty($this->roi) && !empty($this->noOfEmi)) {
            $principal = (float) $this->loanAmount;
            $annualRate = (float) $this->roi;
            $months = (int) $this->noOfEmi;

            if ($annualRate > 0) {
                $monthlyRate = $annualRate / 12 / 100;
                $emi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
            } else {
                $emi = $principal / $months;
            }

            $this->emiAmount = round($emi, 2);
        } else {
            $this->emiAmount = '';
        }
    }

    public function updatedLoanAmount()
    {
        $this->calculateEmi();
    }

    public function updatedRoi()
    {
        $this->calculateEmi();
    }

    public function updatedNoOfEmi()
    {
        $this->calculateEmi();
    }

    public function render()
    {
        $loanAssigns = Ec05LoanAssign::with(['member', 'loanScheme'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('member', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec05-loan-assign-comp', [
            'loanAssigns' => $loanAssigns,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->loadMembers();
        $this->loadLoanSchemes();
        $this->loadAllLoanRequests();
        $this->resetValidation();
        $this->openModal();
    }

    public function loadAllLoanRequests()
    {
        $assignedRequestIds = Ec05LoanAssign::withoutGlobalScopes()
            ->whereNotNull('loan_request_id')
            ->pluck('loan_request_id')
            ->toArray();

        $this->allLoanRequests = Ec03LoanRequest::withoutGlobalScopes()
            ->with(['member', 'loanScheme'])
            ->whereNotIn('id', $assignedRequestIds)
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('id', 'desc')
            ->get();
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
        $this->editId = null;
        $this->selectedMemberId = '';
        $this->selectedLoanSchemeId = '';
        $this->selectedLoanRequestId = '';
        $this->name = '';
        $this->description = '';
        $this->loanAmount = '';
        $this->roi = '';
        $this->noOfEmi = '';
        $this->emiAmount = '';
        $this->loanAssignedDate = now()->toDateString();
        $this->loanReleasedDate = '';
        $this->loanClosedDate = '';
        $this->isEmiEnabled = false;
        $this->isDefault = false;
        $this->isActive = true;
        $this->remarks = '';
        $this->status = 'pending';
        $this->loanRequests = [];
        $this->allLoanRequests = [];
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        $data = [
            'member_id' => $this->selectedMemberId,
            'loan_scheme_id' => $this->selectedLoanSchemeId,
            'loan_request_id' => !empty($this->selectedLoanRequestId) ? $this->selectedLoanRequestId : null,
            'name' => $this->name,
            'description' => $this->description,
            'loan_amount' => $this->loanAmount,
            'loan_current_balance' => $this->loanAmount,
            'roi' => $this->roi,
            'loan_assigned_date' => $this->loanAssignedDate,
            'loan_released_date' => $this->loanReleasedDate,
            'loan_closed_date' => $this->loanClosedDate,
            'is_emi_enabled' => $this->isEmiEnabled,
            'no_of_emi' => $this->noOfEmi,
            'emi_amount' => $this->emiAmount,
            'is_default' => $this->isDefault,
            'is_active' => $this->isActive,
            'remarks' => $this->remarks,
            'status' => $this->status,
            'created_by' => $userId,
        ];

        $loanAssign = null;

        if ($this->editId) {
            $loanAssign = Ec05LoanAssign::find($this->editId);
            $loanAssign->update($data);
            session()->flash('message', 'Loan Assign Updated Successfully.');
        } else {
            $loanAssign = Ec05LoanAssign::create($data);

            if (!empty($this->selectedLoanRequestId)) {
                $requestDetails = Ec04LoanRequestDetail::where('loan_request_id', $this->selectedLoanRequestId)->get();
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

                Ec03LoanRequest::withoutGlobalScopes()->where('id', $this->selectedLoanRequestId)->update(['status' => 'completed']);
            }

            if ($this->isEmiEnabled && !empty($this->noOfEmi) && !empty($this->loanAmount)) {
                $principal = floatval($this->loanAmount);
                $interestRate = floatval($this->roi);
                $monthlyRate = $interestRate / 12 / 100;
                $totalMonths = intval($this->noOfEmi);
                $monthlyEmi = floatval($this->emiAmount);

                $firstEmiDueDate = !empty($this->loanAssignedDate) ? \Carbon\Carbon::parse($this->loanAssignedDate)->addMonth() : now()->addMonth();

                for ($i = 1; $i <= $totalMonths; $i++) {
                    $interestAmount = $principal * $monthlyRate;
                    $principalAmount = $monthlyEmi - $interestAmount;
                    $principal -= $principalAmount;

                    Ec07LoanEmiSchedule::create([
                        'loan_assign_id' => $loanAssign->id,
                        'name' => 'EMI ' . $i,
                        'emi_schedule_index' => $i,
                        'emi_due_date' => $firstEmiDueDate->copy()->addMonths($i - 1)->toDateString(),
                        'total_emi_amount' => $monthlyEmi,
                        'principal_emi_amount' => round($principalAmount, 2),
                        'interest_emi_amount' => round($interestAmount, 2),
                        'principal_balance_amount_before_emi' => round($principal + $principalAmount, 2),
                        'principal_balance_amount_after_emi' => round($principal, 2),
                        'is_default' => false,
                        'is_active' => true,
                        'created_by' => $userId,
                        'status' => 'pending',
                    ]);
                }
            }

            session()->flash('message', 'Loan Assign Created Successfully.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $loanAssign = Ec05LoanAssign::find($id);
        if ($loanAssign) {
            $this->editId = $loanAssign->id;
            $this->selectedMemberId = $loanAssign->member_id;
            $this->selectedLoanSchemeId = $loanAssign->loan_scheme_id;
            $this->selectedLoanRequestId = $loanAssign->loan_request_id;
            $this->name = $loanAssign->name;
            $this->description = $loanAssign->description;
            $this->loanAmount = $loanAssign->loan_amount;
            $this->roi = $loanAssign->roi;
            $this->noOfEmi = $loanAssign->no_of_emi;
            $this->emiAmount = $loanAssign->emi_amount;
            $this->loanAssignedDate = $loanAssign->loan_assigned_date;
            $this->loanReleasedDate = $loanAssign->loan_released_date;
            $this->loanClosedDate = $loanAssign->loan_closed_date;
            $this->isEmiEnabled = $loanAssign->is_emi_enabled;
            $this->isDefault = $loanAssign->is_default;
            $this->isActive = $loanAssign->is_active;
            $this->remarks = $loanAssign->remarks;
            $this->status = $loanAssign->status;

            $this->loadLoanRequests();
            $this->loadSchemeDetails();
            $this->resetValidation();
            $this->openModal();
        }
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
        Ec05LoanAssign::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Loan Assign Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
