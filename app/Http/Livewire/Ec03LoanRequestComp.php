<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec03LoanRequest;
use App\Models\MemberDb;
use App\Models\Ec01LoanScheme;
use App\Models\Ec02LoanSchemeDetail;
use Livewire\WithPagination;

class Ec03LoanRequestComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $loan_request_id;
    public $member_id = '';
    public $loan_scheme_id = '';
    public $loan_amount = '';
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

    protected function rules()
    {
        return [
            'member_id' => 'required|integer',
            'loan_scheme_id' => 'nullable|integer',
            'loan_amount' => 'nullable|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:50',
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
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = 'Pending';
        $this->schemeDetails = [];
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        Ec03LoanRequest::updateOrCreate(['id' => $this->loan_request_id], array_merge($validated, [
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ]));

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
        $this->name = $loanRequest->name;
        $this->description = $loanRequest->description;
        $this->order_index = $loanRequest->order_index;
        $this->is_default = $loanRequest->is_default;
        $this->is_active = $loanRequest->is_active;
        $this->remarks = $loanRequest->remarks;
        $this->status = $loanRequest->status;

        $this->loadSchemeDetails($this->loan_scheme_id);
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
