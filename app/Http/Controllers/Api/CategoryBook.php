<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RedisHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryBookRequest;
use App\Models\CategoryBook as ModelsCategoryBook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryBook extends Controller
{
    private $redisKey = 'categories';

    public function store(CategoryBookRequest $request)
    {
        DB::beginTransaction();
        try {
            // Validate Request
            $validate = $request->validated();

            $redisKey = $this->redisKey;

            $category = ModelsCategoryBook::create([
                'category_name' => $validate['category_name']
            ]);

            // invalidate redis
            RedisHelper::del($redisKey);

            DB::commit();

            return ResponseHelper::success($category, 'Category created successfully', 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function index()
    {
        try {
            $redisKey = $this->redisKey;

            // get categories from redis
            if (RedisHelper::exists($redisKey)) {
                $category = RedisHelper::get($redisKey);
                return ResponseHelper::success(json_decode($category));
            }

            $category = ModelsCategoryBook::all();

            if ($category->isEmpty()) {
                Log::error('No categories found');
                return ResponseHelper::error('Category not found', 404);
            }

            // set categories to redis
            RedisHelper::set($redisKey, json_encode($category));

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

            $redisKey = $this->redisKey . ':' . $id;

            // get category from redis
            if (RedisHelper::exists($redisKey)) {
                $category = RedisHelper::get($redisKey);
                return ResponseHelper::success(json_decode($category));
            }

            $category = ModelsCategoryBook::find($id);

            if (!$category) {
                Log::error('Category not found');
                return ResponseHelper::error('Category not found', 404);
            }

            // set category to redis
            RedisHelper::set($redisKey, json_encode($category));

            return ResponseHelper::success($category);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Validate Request
            if (!is_numeric($id)) {
                DB::rollBack();
                return ResponseHelper::error('Invalid id', 400);
            }

            $category = ModelsCategoryBook::find($id);

            if (!$category) {
                DB::rollBack();
                return ResponseHelper::error('Category not found', 404);
            }

            $category->delete();

            // invalidate redis
            $redisKey = $this->redisKey;
            RedisHelper::del($redisKey);

            DB::commit();

            return ResponseHelper::success([], 'Category deleted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function update(CategoryBookRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate Request
            if (!is_numeric($id)) {
                DB::rollBack();
                return ResponseHelper::error('Invalid id', 400);
            }

            $validate = $request->validated();

            $category = ModelsCategoryBook::find($id);

            if (!$category) {
                DB::rollBack();
                return ResponseHelper::error('Category not found', 404);
            }

            $category->category_name = $validate['category_name'];
            $category->save();

            // invalidate redis
            $redisKey = $this->redisKey;
            RedisHelper::del($redisKey);

            return ResponseHelper::success($category, 'Category updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return ResponseHelper::error('Something went wrong', 500);
        }
    }
}
