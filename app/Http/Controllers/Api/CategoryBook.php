<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryBookRequest;
use App\Models\CategoryBook as ModelsCategoryBook;

class CategoryBook extends Controller
{
    public function store(CategoryBookRequest $request)
    {
        $validate = $request->validated();

        $category = ModelsCategoryBook::create([
            'category_name' => $validate['category_name']
        ]);

        if (!$category) {
            return ResponseHelper::error('Something went wrong', 500);
        }

        return ResponseHelper::success($category, 'Category created successfully', 201);
    }

    public function index()
    {
        $category = ModelsCategoryBook::all();

        if (!$category) {
            return ResponseHelper::error('Something went wrong', 500);
        }

        if ($category->isEmpty()) {
            return ResponseHelper::error('Category not found', 404);
        }

        return ResponseHelper::success($category);
    }

    public function show($id)
    {
        // Validate Request
        if (!is_numeric($id)) {
            return ResponseHelper::error('Invalid id', 400);
        }

        $category = ModelsCategoryBook::find($id);

        if (!$category) {
            return ResponseHelper::error('Category not found', 404);
        }

        return ResponseHelper::success($category);
    }

    public function destroy($id)
    {
        // Validate Request
        if (!is_numeric($id)) {
            return ResponseHelper::error('Invalid id', 400);
        }

        $category = ModelsCategoryBook::find($id);

        if (!$category) {
            return ResponseHelper::error('Category not found', 404);
        }

        $category->delete();

        return ResponseHelper::success([], 'Category deleted successfully');
    }

    public function update(CategoryBookRequest $request, $id)
    {
        // Validate Request
        if (!is_numeric($id)) {
            return ResponseHelper::error('Invalid id', 400);
        }

        $validate = $request->validated();

        $category = ModelsCategoryBook::find($id);

        if (!$category) {
            return ResponseHelper::error('Category not found', 404);
        }

        $category->category_name = $validate['category_name'];
        $category->save();

        return ResponseHelper::success($category, 'Category updated successfully');
    }
}
