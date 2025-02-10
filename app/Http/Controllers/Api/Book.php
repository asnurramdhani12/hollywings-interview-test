<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Books;
use Illuminate\Support\Facades\Log;

class Book extends Controller
{
    public function index()
    {
        try {
            $books = Books::with('category')->get();

            // if no books found
            if ($books->isEmpty()) {
                Log::error('No books found');
                return ResponseHelper::error('No books found', 404);
            }

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

            $book = Books::find($id);

            if (!$book) {
                Log::error('Book not found', [
                    'id' => $id
                ]);
                return ResponseHelper::error('Book not found', 404);
            }

            return ResponseHelper::success($book);
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

            $book = Books::find($id);
            if (!$book) {
                Log::error('Book not found', [
                    'id' => $id
                ]);
                return ResponseHelper::error('Book not found', 404);
            }

            $book->delete();

            return ResponseHelper::success([], 'Book deleted successfully');
        } catch (\Throwable $th) {
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function update(BookRequest $request, $id)
    {
        try {
            // Validate Request
            if (!is_numeric($id)) {
                return ResponseHelper::error('Invalid id', 400);
            }

            $validate = $request->validated();

            $book = Books::find($id);

            if (!$book) {
                Log::error('Book not found', [
                    'id' => $id
                ]);
                return ResponseHelper::error('Book not found', 404);
            }

            $book->update($validate);

            return ResponseHelper::success($book, 'Book updated successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'request' => $request->all()
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function store(BookRequest $request)
    {
        try {
            $validate = $request->validated();

            $book = Books::create($validate);

            return ResponseHelper::success($book, 'Book created successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'request' => $request->all()
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }
}
