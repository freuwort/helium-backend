<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountingContact\CreateAccountingContactRequest;
use App\Http\Requests\AccountingContact\UpdateAccountingContactRequest;
use App\Http\Resources\AccountingContact\AccountingContactResource;
use App\Models\AccountingContact;
use Illuminate\Http\Request;

class AccountingContactController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountingContact::class);

        // Base query
        $query = AccountingContact::with(['sync', 'owner', 'main_address', 'billing_address', 'shipping_address']);

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return AccountingContactResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(AccountingContact $contact)
    {
        $this->authorize('view', $contact);

        return AccountingContactResource::make($contact);
    }

    
    
    public function store(CreateAccountingContactRequest $request)
    {
        $this->authorize('create', AccountingContact::class);

        $contact = AccountingContact::create($request->validated([
            'type',
            'name',
            'vat_id',
            'tax_id',
            'customer_id',
            'supplier_id',
            'employee_id',
            'contact_person',
            'sync_id',
        ]));

        if ($request->main_address) $contact->main_address()->create($request->validated('main_address'));
        if ($request->billing_address) $contact->billing_address()->create($request->validated('billing_address'));
        if ($request->shipping_address) $contact->shipping_address()->create($request->validated('shipping_address'));

        return AccountingContactResource::make($contact);
    }

    
    
    public function update(UpdateAccountingContactRequest $request, AccountingContact $contact)
    {
        $this->authorize('update', $contact);

        $contact->update($request->validated([
            'name',
            'vat_id',
            'tax_id',
            'customer_id',
            'supplier_id',
            'employee_id',
            'contact_person',
        ]));

        return AccountingContactResource::make($contact);
    }

    
    
    public function destroy(AccountingContact $contact)
    {
        $this->authorize('delete', $contact);

        $contact->delete();
    }

    
    
    public function destroyMany(Request $request)
    {
        $request->validate(['ids.*' => ['required', 'integer', 'exists:accounting_contacts,id']]);

        $contacts = AccountingContact::whereIn('id', $request->ids);

        $this->authorize('deleteMany', [AccountingContact::class, $contacts->get()]);

        $contacts->delete();
    }
}
