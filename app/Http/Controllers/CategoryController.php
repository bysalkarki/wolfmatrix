<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(public CategoryRepository $repository) {}

    public function index(Request $request)
    {
        $perPage = $request->get("per_page", 15);
        $categories = Category::query()->paginate($perPage);
        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $all = array_merge(['path'=>'/'], $data);
        $this->repository->store($all);

        return response()->json([
            "message" => "Category created successfully",
        ], 201);
    }

    public function show(string $id)
    {
        $category = $this->repository->find($id);
        $categories = new CategoryResource($category);
        return response()->json([
            "category" => $categories,
        ], 200);
    }

    public function update(CategoryRequest $request, string $id)
    {
        $this->repository->update($id, $request->validated());

        return response()->json([
            "message" => "Category updated successfully",
        ], 200);
    }

    public function destroy(string $id)
    {
        $this->repository->delete($id);
        return response()->json([
            "message" => "Category deleted successfully",
        ], 200);
    }
}
