<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Http\Requests\Form\CreateFormRequest;
use App\Http\Requests\Form\DestroyManyFormRequest;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Http\Resources\Form\EditorFormResource;
use App\Http\Resources\Form\FormResource;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Form::class, 'form');
    }



    public function index(Request $request)
    {
        // Base query
        $query = Form::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('slug', $request->filter_search)
                    ->orWhereFuzzy('description', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return FormResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Form $form)
    {
        return EditorFormResource::make($form);
    }

    
    
    public function store(CreateFormRequest $request)
    {
        $form = Form::create($request->model);

        $form->fields()->createMany($request->form_fields);

        return EditorFormResource::make($form);
    }

    
    
    public function update(UpdateFormRequest $request, Form $form)
    {
        $form->update($request->model);

        $form->fields()->delete();
        $form->fields()->createMany($request->form_fields);

        return EditorFormResource::make($form);
    }

    
    
    public function destroy(Form $form)
    {
        $form->delete();
    }

    
    
    public function destroyMany(DestroyManyFormRequest $request)
    {
        $this->authorize('deleteMany', [Form::class, $request->ids]);

        Form::whereIn('id', $request->ids)->delete();
    }
}
