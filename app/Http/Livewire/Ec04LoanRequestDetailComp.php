<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec04LoanRequestDetail;
use App\Models\Ec03LoanRequest;
use App\Models\Ec02LoanSchemeDetail;
use App\Models\Ec02LoanSchemeFeature;
use Livewire\WithPagination;

class Ec04LoanRequestDetailComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $detail_id;
    public $loan_request_id = '';
    public $loan_scheme_detail_id = '';
    public $loan_scheme_feature_id = '';
    public $loan_scheme_feature_name = '';
    public $loan_scheme_feature_value = '';
    public $loan_scheme_feature_condition = '';
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
    public $selectedSchemeDetails = [];

    protected function rules()
    {
        return [
            'loan_request_id' => 'required|integer',
            'loan_scheme_detail_id' => 'nullable|integer',
            'loan_scheme_feature_id' => 'nullable|integer',
            'loan_scheme_feature_name' => 'nullable|string|max:255',
            'loan_scheme_feature_value' => 'nullable|numeric',
            'loan_scheme_feature_condition' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'order_index' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $loanRequests = Ec03LoanRequest::with('member')->orderBy('id', 'desc')->get();
        
        $loanSchemeDetails = [];
        $loanSchemeFeatures = [];
        
        if ($this->loan_request_id) {
            $loanRequest = Ec03LoanRequest::find($this->loan_request_id);
            if ($loanRequest && $loanRequest->loan_scheme_id) {
                $loanSchemeDetails = Ec02LoanSchemeDetail::where('loan_scheme_id', $loanRequest->loan_scheme_id)
                    ->orderBy('order_index', 'asc')
                    ->get();
                $loanSchemeFeatures = Ec02LoanSchemeFeature::orderBy('name')->get();
            }
        }

        $details = Ec04LoanRequestDetail::with(['loanRequest.member', 'loanSchemeDetail', 'loanSchemeFeature'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('loan_scheme_feature_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->loan_request_id, function ($query) {
                $query->where('loan_request_id', $this->loan_request_id);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec04-loan-request-detail-comp', compact('details', 'loanRequests', 'loanSchemeDetails', 'loanSchemeFeatures'));
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
        $this->detail_id = '';
        $this->loan_request_id = '';
        $this->loan_scheme_detail_id = '';
        $this->loan_scheme_feature_id = '';
        $this->loan_scheme_feature_name = '';
        $this->loan_scheme_feature_value = '';
        $this->loan_scheme_feature_condition = '';
        $this->name = '';
        $this->description = '';
        $this->order_index = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->remarks = '';
        $this->status = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $this->validate([
            'loan_request_id' => 'required|integer',
        ]);

        if (!empty($this->selectedSchemeDetails)) {
            foreach ($this->selectedSchemeDetails as $detail) {
                Ec04LoanRequestDetail::create([
                    'loan_request_id' => $this->loan_request_id,
                    'loan_scheme_detail_id' => $detail['loan_scheme_detail_id'] ?? null,
                    'loan_scheme_feature_id' => $detail['loan_scheme_feature_id'] ?? null,
                    'loan_scheme_feature_name' => $detail['loan_scheme_feature_name'] ?? '',
                    'loan_scheme_feature_value' => $detail['loan_scheme_feature_value'] ?? '',
                    'loan_scheme_feature_condition' => $detail['loan_scheme_feature_condition'] ?? '',
                    'name' => $detail['name'] ?? '',
                    'is_active' => $detail['is_active'] ?? true,
                ]);
            }
            session()->flash('message', 'Loan Details Created Successfully.');
        } else {
            $validated = $this->validate();
            Ec04LoanRequestDetail::updateOrCreate(['id' => $this->detail_id], array_merge($validated, [
                'is_default' => $this->is_default,
                'is_active' => $this->is_active,
            ]));
            session()->flash('message', $this->detail_id ? 'Loan Detail Updated Successfully.' : 'Loan Detail Created Successfully.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $detail = Ec04LoanRequestDetail::findOrFail($id);
        $this->detail_id = $id;
        $this->loan_request_id = $detail->loan_request_id;
        $this->loan_scheme_detail_id = $detail->loan_scheme_detail_id;
        $this->loan_scheme_feature_id = $detail->loan_scheme_feature_id;
        $this->loan_scheme_feature_name = $detail->loan_scheme_feature_name;
        $this->loan_scheme_feature_value = $detail->loan_scheme_feature_value;
        $this->loan_scheme_feature_condition = $detail->loan_scheme_feature_condition;
        $this->name = $detail->name;
        $this->description = $detail->description;
        $this->order_index = $detail->order_index;
        $this->is_default = $detail->is_default;
        $this->is_active = $detail->is_active;
        $this->remarks = $detail->remarks;
        $this->status = $detail->status;

        $this->resetValidation();
        $this->openModal();
    }

    public function updatedLoanSchemeDetailId($value)
    {
        if ($value) {
            $schemeDetail = Ec02LoanSchemeDetail::find($value);
            if ($schemeDetail) {
                $this->loan_scheme_feature_name = $schemeDetail->loan_scheme_feature_name;
                $this->loan_scheme_feature_value = $schemeDetail->loan_scheme_feature_value;
                $this->loan_scheme_feature_condition = $schemeDetail->loan_scheme_feature_condition;
            }
        }
    }

    public function updatedLoanSchemeFeatureId($value)
    {
        if ($value) {
            $feature = Ec02LoanSchemeFeature::find($value);
            if ($feature) {
                $this->loan_scheme_feature_name = $feature->name;
            }
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
        Ec04LoanRequestDetail::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Loan Detail Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedLoanRequestId($value)
    {
        $this->selectedSchemeDetails = [];
        
        if ($value) {
            $loanRequest = Ec03LoanRequest::find($value);
            if ($loanRequest && $loanRequest->loan_scheme_id) {
                $schemeDetails = Ec02LoanSchemeDetail::where('loan_scheme_id', $loanRequest->loan_scheme_id)
                    ->orderBy('order_index', 'asc')
                    ->get();
                
                foreach ($schemeDetails as $detail) {
                    $this->selectedSchemeDetails[] = [
                        'loan_scheme_detail_id' => $detail->id,
                        'loan_scheme_feature_id' => $detail->loan_scheme_feature_id,
                        'loan_scheme_feature_name' => $detail->loan_scheme_feature_name,
                        'loan_scheme_feature_value' => $detail->loan_scheme_feature_value,
                        'loan_scheme_feature_condition' => $detail->loan_scheme_feature_condition,
                        'name' => $detail->name,
                        'is_active' => true,
                    ];
                }
            }
        }
        
        $this->resetPage();
    }
}
