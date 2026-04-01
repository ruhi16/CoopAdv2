<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ec16ShfundMemberTransaction;
use App\Models\Ec16ShfundMemberMasterDb;
use App\Models\Member;
use App\Models\Ec05LoanAssign;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Ec16ShfundMemberTransactionComp extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $isOpen = 0;
    public $transaction_id = null;
    public $shfund_member_master_db_id = '';
    public $member_id = '';
    public $loan_assign_id = '';
    public $name = '';
    public $description = '';
    public $transaction_id_input = '';
    public $transaction_type = '';
    public $transaction_amount = '';
    public $transaction_date = '';
    public $transaction_reasons = '';
    public $status = 'draft';
    public $is_active = true;
    public $remarks = '';
    public $confirmingDelete = null;

    public $memberMasterDbs = [];
    public $members = [];
    public $loanAssigns = [];

    protected function rules()
    {
        return [
            'shfund_member_master_db_id' => 'nullable|integer|min:0',
            'member_id' => 'nullable|integer|min:0',
            'loan_assign_id' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'transaction_id_input' => 'required|string|max:50',
            'transaction_type' => 'required|in:deposit,withdrawal',
            'transaction_amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'transaction_reasons' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function render()
    {
        $transactions = Ec16ShfundMemberTransaction::with(['memberMasterDb', 'member'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('transaction_id', 'like', '%' . $this->search . '%')
                    ->orWhere('transaction_reasons', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ec16-shfund-member-transaction-comp', compact('transactions'));
    }

    public function mount()
    {
        $this->memberMasterDbs = Ec16ShfundMemberMasterDb::where('is_active', true)->orderBy('name')->get();
        $this->members = Member::where('is_active', true)->orderBy('name')->get();
        $this->loanAssigns = Ec05LoanAssign::where('is_active', true)->orderBy('name')->get();
        $this->transaction_date = date('Y-m-d');
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
        $this->transaction_id = null;
        $this->shfund_member_master_db_id = '';
        $this->member_id = '';
        $this->loan_assign_id = '';
        $this->name = '';
        $this->description = '';
        $this->transaction_id_input = '';
        $this->transaction_type = '';
        $this->transaction_amount = '';
        $this->transaction_date = date('Y-m-d');
        $this->transaction_reasons = '';
        $this->status = 'draft';
        $this->is_active = true;
        $this->remarks = '';
        $this->confirmingDelete = null;
    }

    public function store()
    {
        $validated = $this->validate();

        $userId = Auth::id() ?? 1;

        Ec16ShfundMemberTransaction::updateOrCreate(['id' => $this->transaction_id], array_merge($validated, [
            'transaction_id' => $this->transaction_id_input,
            'member_id' => $this->member_id ?: null,
            'loan_assign_id' => $this->loan_assign_id ?: null,
            'transaction_amount' => floatval($this->transaction_amount),
            'transaction_reasons' => $this->transaction_reasons,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
            'user_id' => $userId,
        ]));

        session()->flash('message', $this->transaction_id ? 'Transaction Updated Successfully.' : 'Transaction Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $transaction = Ec16ShfundMemberTransaction::findOrFail($id);
        
        $this->transaction_id = $id;
        $this->shfund_member_master_db_id = $transaction->shfund_member_master_db_id ?? '';
        $this->member_id = $transaction->member_id ?? '';
        $this->loan_assign_id = $transaction->loan_assign_id ?? '';
        $this->name = $transaction->name;
        $this->description = $transaction->description ?? '';
        $this->transaction_id_input = $transaction->transaction_id;
        $this->transaction_type = $transaction->transaction_type;
        $this->transaction_amount = $transaction->transaction_amount;
        $this->transaction_date = $transaction->transaction_date ? date('Y-m-d', strtotime($transaction->transaction_date)) : date('Y-m-d');
        $this->transaction_reasons = $transaction->transaction_reasons ?? '';
        $this->status = $transaction->status;
        $this->is_active = $transaction->is_active;
        $this->remarks = $transaction->remarks ?? '';

        $this->resetValidation();
        $this->openModal();
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
        Ec16ShfundMemberTransaction::find($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Transaction Deleted Successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
