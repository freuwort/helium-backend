<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\DestroyManyCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\EditorCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }



    public function index(Request $request)
    {
        // Base query
        $query = Category::with(['owner', 'accesses']);

        // Search
        if ($request->filter_search)
        {
            $query->where(function ($query) use ($request) {
                $query
                    ->orWhereRaw("name % ?", [$request->filter_search])->orWhere('name', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("slug % ?", [$request->filter_search])->orWhere('slug', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("content % ?", [$request->filter_search])->orWhere('content', 'ILIKE', "%$request->filter_search%");
            });
        }

        // Filter
        if ($request->filter_parent)
        {
            $includesNull = in_array(null, $request->filter_parent);
            
            $query->where(function ($query) use ($request, $includesNull) {
                $query->whereIn('parent_id', $request->filter_parent);
                if ($includesNull) $query->orWhereNull('parent_id');
            });
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return CategoryResource::collection($query->paginate($request->size ?? 20))
        ->additional(['keys' => $query->pluck('id')->toArray()])
        ->additional(['filter_values' => [
            'parent' => Category::distinct('name')->pluck('name')->toArray(),
        ]]);
    }

    
    
    public function show(Category $category)
    {
        return EditorCategoryResource::make($category);
    }

    
    
    public function store(CreateCategoryRequest $request)
    {
        $category = Category::create($request->validated('model'));

        return EditorCategoryResource::make($category);
    }

    
    
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated('model'));

        return EditorCategoryResource::make($category);
    }

    
    
    public function destroy(Category $category)
    {
        $category->delete();
    }

    
    
    public function destroyMany(DestroyManyCategoryRequest $request)
    {
        $this->authorize('deleteMany', [Category::class, $request->ids]);

        Category::whereIn('id', $request->ids)->delete();
    }
}
