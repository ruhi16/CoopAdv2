<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec03LoanRequest;
use App\Models\MemberDb;
use App\Models\Ec01LoanScheme;
use App\Models\Ec02LoanSchemeDetail;
use App\Models\Ec04LoanRequestDetail;
use Livewire\WithPagination;

class Ec03LoanRequestComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $loan_request_id;
    public $member_id = '';
    public $loan_scheme_id = '';
    public $loan_amount = '';
    public $no_of_years = '';
    public $emi_active = false;
    public $name = '';
    public $description = '';
    public $order_index = '';
    public $is_default = false;
    public $is_active = true;
    public $remarks = '';
    public $status = '';

    public $isOpen = 0;
    public $search = '';
    public $confirmingDelete = null;
    public $schemeDetails = [];
    public $calculatedEmis = [];

    protected function rules()
    {
        return [
            'member_id' => 'required|integer',
            'loan_scheme_id' => 'nullable|integer',
            'loan_amount' => 'nullable|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $members = MemberDb::withoutGlobalScopes()->orderBy('name')->get();
        $loanSchemes = Ec01LoanScheme::orderBy('name')->get();

        $loanRequests = Ec03LoanRequest::with(['member', 'loanScheme'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('loan_amount', 'like', '%' . $this->search . '%');
            })
            ->when($this->member_id, function ($query) {
                $query->where('member_id', $this->member_id);
            })
            ->when($this->loan_scheme_id, function ($query) {
                $query->where('loan_scheme_id', $this->loan_scheme_id);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec03-loan-request-comp', compact('loanRequests', 'members', 'loanSchemes'));
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
        $this->loan_request_id = '';
        $this->member_id = '';
        $this->loan_scheme_id = '';
        $this->loan_amount = '';
        $this->no_of_years = '';
        $this->emi_active = false;
        $this->name = '';
        $this->description = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->schemeDetails = [];
        $this->calculatedEmis = [];
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        $loanRequest = Ec03LoanRequest::updateOrCreate(['id' => $this->loan_request_id], array_merge($validated, [
            'no_of_years' => $this->no_of_years,
            'emi_active' => $this->emi_active,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

        if (!$this->loan_request_id && $this->loan_scheme_id) {
            $schemeDetails = Ec02LoanSchemeDetail::where('loan_scheme_id', $this->loan_scheme_id)
                ->orderBy('order_index', 'asc')
                ->get();

            foreach ($schemeDetails as $detail) {
                Ec04LoanRequestDetail::create([
                    'loan_request_id' => $loanRequest->id,
                    'loan_scheme_detail_id' => $detail->id,
                    'loan_scheme_feature_id' => $detail->loan_scheme_feature_id,
                    'loan_scheme_feature_name' => $detail->loan_scheme_feature_name,
                    'loan_scheme_feature_value' => $detail->loan_scheme_feature_value,
                    'loan_scheme_feature_condition' => $detail->loan_scheme_feature_condition,
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
        }

        session()->flash('message', $this->loan_request_id ? 'Loan Request Updated Successfully.' : 'Loan Request Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $loanRequest = Ec03LoanRequest::findOrFail($id);
        $this->loan_request_id = $id;
        $this->member_id = $loanRequest->member_id;
        $this->loan_scheme_id = $loanRequest->loan_scheme_id;
        $this->loan_amount = $loanRequest->loan_amount;
        $this->no_of_years = $loanRequest->no_of_years;
        $this->emi_active = $loanRequest->emi_active;
        $this->name = $loanRequest->name;
        $this->description = $loanRequest->description;
        $this->is_default = $loanRequest->is_default;
        $this->is_active = $loanRequest->is_active;
        $this->remarks = $loanRequest->remarks;

        $this->loadSchemeDetails($this->loan_scheme_id);
        if ($this->emi_active && $this->loan_amount && $this->no_of_years) {
            $this->calculateEmis();
        }
        $this->resetValidation();
        $this->openModal();
    }

    public function updatedMemberId($value)
    {
        if ($value) {
            $member = MemberDb::withoutGlobalScopes()->find($value);
            if ($member) {
                $this->name = $member->name;
            }
        }
    }

    public function updatedLoanSchemeId($value)
    {
        $this->loadSchemeDetails($value);
    }

    public function updatedLoanAmount($value)
    {
        if ($this->emi_active && $value && $this->no_of_years) {
            $this->calculateEmis();
        }
    }

    public function updatedNoOfYears($value)
    {
        if ($this->emi_active && $value && $this->loan_amount) {
            $this->calculateEmis();
        }
    }

    public function updatedEmiActive($value)
    {
        if ($value && $this->loan_amount && $this->no_of_years) {
            $this->calculateEmis();
        } else {
            $this->calculatedEmis = [];
        }
    }

    private function calculateEmis()
    {
        $principal = floatval($this->loan_amount);
        $years = intval($this->no_of_years);
        
        if ($principal <= 0 || $years <= 0) {
            $this->calculatedEmis = [];
            return;
        }

        $interestRate = 12;
        $monthlyRate = $interestRate / 12 / 100;
        $totalMonths = $years * 12;

        $monthlyEmi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $totalMonths)) / (pow(1 + $monthlyRate, $totalMonths) - 1);

        $this->calculatedEmis = [];
        $totalPrincipal = 0;
        $totalInterest = 0;

        for ($i = 1; $i <= $totalMonths; $i++) {
            $interestAmount = $principal * $monthlyRate;
            $principalAmount = $monthlyEmi - $interestAmount;
            $principal -= $principalAmount;
            
            $totalPrincipal += $principalAmount;
            $totalInterest += $interestAmount;

            $this->calculatedEmis[] = [
                'emi_no' => $i,
                'principal_amount' => round($principalAmount, 2),
                'interest_amount' => round($interestAmount, 2),
                'total' => round($monthlyEmi, 2),
            ];
        }
    }

    private function loadSchemeDetails($schemeId)
    {
        if ($schemeId) {
            $this->schemeDetails = Ec02LoanSchemeDetail::where('loan_scheme_id', $schemeId)
                ->orderBy('order_index', 'asc')
                ->get()
                ->toArray();
        } else {
            $this->schemeDetails = [];
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
        Ec03LoanRequest::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Loan Request Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
