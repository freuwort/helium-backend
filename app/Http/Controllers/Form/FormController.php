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
    public function index(Request $request)
    {
        $this->authorize('viewAny', Form::class);

        // Base query
        $query = Form::with(['fields', 'submissions']);

        // Search
        if ($request->filter_search)
        {
            $query->where(function ($query) use ($request) {
                $query
                    ->orWhereRaw("name % ?", [$request->filter_search])->orWhere('name', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("slug % ?", [$request->filter_search])->orWhere('slug', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("description % ?", [$request->filter_search])->orWhere('description', 'ILIKE', "%$request->filter_search%");
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
        $this->authorize('view', $form);

        return EditorFormResource::make($form);
    }

    
    
    public function store(CreateFormRequest $request)
    {
        $this->authorize('create', Form::class);

        $form = Form::create($request->model);

        $form->fields()->createMany($request->form_fields);

        return EditorFormResource::make($form);
    }

    
    
    public function update(UpdateFormRequest $request, Form $form)
    {
        $this->authorize('update', $form);

        $form->update($request->model);

        $form->fields()->delete();
        $form->fields()->createMany($request->form_fields);

        return EditorFormResource::make($form);
    }

    
    
    public function destroy(Form $form)
    {
        $this->authorize('delete', $form);

        $form->delete();
    }

    
    
    public function destroyMany(DestroyManyFormRequest $request)
    {
        $forms = Form::whereIn('id', $request->validated('ids'));

        $this->authorize('deleteMany', [Form::class, $forms->get()]);

        $forms->delete();
    }
}
