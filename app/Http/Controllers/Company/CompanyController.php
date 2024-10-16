<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DestroyManyCompanyRequest;
use App\Http\Requests\UploadProfileMediaRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Company\EditorCompanyResource;
use App\Models\Address;
use App\Models\BankConnection;
use App\Models\Company;
use App\Models\Date;
use App\Models\Email;
use App\Models\Identifier;
use App\Models\LegalDetail;
use App\Models\Link;
use App\Models\Phonenumber;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Company::class);

        // Base query
        $query = Company::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('notes', $request->filter_search)
                    ->orWhereFuzzy('description', $request->filter_search);
            });
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return CompanyResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Company $company)
    {
        $this->authorize('view', $company);

        return EditorCompanyResource::make($company);
    }

    
    
    public function store(Request $request)
    {
        $this->authorize('create', Company::class);

        $company = Company::create($request->model);

        $company->syncMany(LegalDetail::class, $request->legal_details, 'legal_details');
        $company->syncMany(Identifier::class, $request->identifiers);
        $company->syncMany(Address::class, $request->addresses);
        $company->syncMany(BankConnection::class, $request->bank_connections, 'bank_connections');
        $company->syncMany(Email::class, $request->emails);
        $company->syncMany(Phonenumber::class, $request->phonenumbers);
        $company->syncMany(Date::class, $request->dates);
        $company->syncMany(Link::class, $request->links);

        return EditorCompanyResource::make($company);
    }

    
    
    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);

        $company->update($request->model);

        $company->syncMany(LegalDetail::class, $request->legal_details, 'legal_details');
        $company->syncMany(Identifier::class, $request->identifiers);
        $company->syncMany(Address::class, $request->addresses);
        $company->syncMany(BankConnection::class, $request->bank_connections, 'bank_connections');
        $company->syncMany(Email::class, $request->emails);
        $company->syncMany(Phonenumber::class, $request->phonenumbers);
        $company->syncMany(Date::class, $request->dates);
        $company->syncMany(Link::class, $request->links);

        return EditorCompanyResource::make($company);
    }



    public function uploadLogo(UploadProfileMediaRequest $request, Company $company)
    {
        // $this->authorize('uploadImage', $company);

        $company->uploadProfileMedia($request->file('file'), 'logo');
    }

    public function uploadBanner(UploadProfileMediaRequest $request, Company $company)
    {
        // $this->authorize('uploadImage', $company);

        $company->uploadProfileMedia($request->file('file'), 'banner');
    }

    
    
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        $company->delete();
    }



    public function destroyMany(DestroyManyCompanyRequest $request)
    {
        $companies = Company::whereIn('id', $request->validated('ids'));
        
        $this->authorize('deleteMany', [Company::class, $companies->get()]);
        
        $companies->delete();
    }
}
