<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryBookRequest;
use App\Models\CategoryBook as ModelsCategoryBook;
use Illuminate\Support\Facades\Log;

class CategoryBook extends Controller
{
    public function store(CategoryBookRequest $request)
    {
        try {
            // Validate Request
            $validate = $request->validated();

            $category = ModelsCategoryBook::create([
                'category_name' => $validate['category_name']
            ]);

            return ResponseHelper::success($category, 'Category created successfully', 201);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function index()
    {
        try {
            $category = ModelsCategoryBook::all();

            if ($category->isEmpty()) {
                Log::error('No categories found');
                return ResponseHelper::error('Category not found', 404);
            }

            return ResponseHelper::success($category);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function show($id)
    {
        try {
            // Validate Request
            if (!is_numeric($id)) {
                return ResponseHelper::error('Invalid id', 400);
            }

            $category = ModelsCategoryBook::find($id);

            if (!$category) {
                Log::error('Category not found');
                return ResponseHelper::error('Category not found', 404);
            }

            return ResponseHelper::success($category);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function destroy($id)
    {
        try {
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
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function update(CategoryBookRequest $request, $id)
    {
        try {
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
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }
}
