<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}
