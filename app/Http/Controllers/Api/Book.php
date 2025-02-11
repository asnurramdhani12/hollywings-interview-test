<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RedisHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Books;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Book extends Controller
{
    private $redisKeyBooks = 'books';

    public function index()
    {
        $redisKey = $this->redisKeyBooks;

        try {
            // get books from redis
            if (RedisHelper::exists($redisKey)) {
                $books = RedisHelper::get($redisKey);
                return ResponseHelper::success(json_decode($books));
            }

            $books = Books::with('category')->get();

            // if no books found
            if ($books->isEmpty()) {
                Log::error('No books found');
                return ResponseHelper::error('No books found', 404);
            }

            // set books to redis
            RedisHelper::set($redisKey, json_encode($books));

            return ResponseHelper::success($books);
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'request' => request()->all()
            ]);
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

            // get books from redis
            $redisKey = $this->redisKeyBooks . ':' . $id;
            if (RedisHelper::exists($redisKey)) {
                $books = RedisHelper::get($redisKey);
                return ResponseHelper::success(json_decode($books));
            }

            $book = Books::with('category')->find($id);

            if (!$book) {
                Log::error('Book not found', [
                    'id' => $id
                ]);
                return ResponseHelper::error('Book not found', 404);
            }

            // set book to redis
            RedisHelper::set($redisKey, json_encode($book));

            return ResponseHelper::success($book);
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'id' => $id
            ]);

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

            $book = Books::find($id);
            if (!$book) {
                DB::rollBack();
                Log::error('Book not found', [
                    'id' => $id
                ]);
                return ResponseHelper::error('Book not found', 404);
            }


            $book->delete();

            // invalidate redis
            RedisHelper::del($this->redisKeyBooks);

            DB::commit();

            return ResponseHelper::success([], 'Book deleted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [
                'id' => $id
            ]);

            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function update(BookRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate Request
            if (!is_numeric($id)) {
                DB::rollBack();
                return ResponseHelper::error('Invalid id', 400);
            }

            $validate = $request->validated();

            $book = Books::find($id);

            if (!$book) {
                DB::rollBack();
                Log::error('Book not found', [
                    'id' => $id
                ]);

                return ResponseHelper::error('Book not found', 404);
            }


            $book->update($validate);

            // invalidate redis
            RedisHelper::del($this->redisKeyBooks);

            DB::commit();

            return ResponseHelper::success($book, 'Book updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [
                'request' => $request->all()
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function store(BookRequest $request)
    {
        DB::beginTransaction();
        try {
            $validate = $request->validated();

            $book = Books::create($validate);

            // invalidate redis
            RedisHelper::del($this->redisKeyBooks);

            DB::commit();

            return ResponseHelper::success($book, 'Book created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [
                'request' => $request->all()
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }
}
