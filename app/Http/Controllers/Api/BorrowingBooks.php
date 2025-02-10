<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowingBooksRequest;
use App\Models\Books;
use App\Models\BorrowingBooks as ModelsBorrowingBooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BorrowingBooks extends Controller
{
    public function borrow(BorrowingBooksRequest $request)
    {

        try {
            // Validate Request
            $validated = $request->validated();

            // Get Book
            $book = Books::find($validated['book_id']);
            if (!$book) {
                Log::error('Book not found', [
                    'id' => $validated['book_id']
                ]);

                return ResponseHelper::error('Book not found', 404);
            }

            // Validate Stock
            if ($book->stock < $validated['quantities']) {
                Log::error('Stock not enough', [
                    'id' => $validated['book_id']
                ]);

                return ResponseHelper::error('Stock not enough', 400);
            }

            // add borrowing date
            $validated['borrow_date'] = now();

            // add user id
            $validated['user_id'] = auth()->user()->id;

            // Create BorrowingBooks
            $borrowingBooks = ModelsBorrowingBooks::create($validated);

            // Return Response
            return ResponseHelper::success($borrowingBooks, 'Book borrowed successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'request' => $request->all(),
            ]);

            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function accepted($id)
    {
        DB::beginTransaction();
        try {
            // Validate Request
            if (!is_numeric($id)) {
                DB::rollBack();
                return ResponseHelper::error('Invalid id', 400);
            }

            // Get BorrowingBooks
            $borrowingBooks = ModelsBorrowingBooks::where('id', $id)->where('accepted', false)->first();
            if (!$borrowingBooks) {
                DB::rollBack();
                Log::error('Borrowing Books not found or already accepted', [
                    'id' => $id
                ]);

                return ResponseHelper::error('Borrowing Books not found or already accepted', 404);
            }

            // Update BorrowingBooks
            $borrowingBooks->accepted = true;
            $borrowingBooks->save();

            // Update Stock
            $book = Books::find($borrowingBooks->book_id);
            if ($book->stock < $borrowingBooks->quantities) {
                DB::rollBack();
                Log::error('Stock not enough', [
                    'id' => $borrowingBooks->book_id
                ]);

                return ResponseHelper::error('Stock not enough', 400);
            }

            $book->stock -= $borrowingBooks->quantities;
            $book->save();

            DB::commit();

            // Return Response
            return ResponseHelper::success($borrowingBooks, 'BorrowingBooks accepted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [
                'request' => request()->all(),
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }


    public function returned($id)
    {
        DB::beginTransaction();
        try {
            // Validate Request
            if (!is_numeric($id)) {
                DB::rollBack();
                return ResponseHelper::error('Invalid id', 400);
            }

            // Get BorrowingBooks
            $borrowingBooks = ModelsBorrowingBooks::where('id', $id)->where('accepted', true)->where('return_date', null)->first();
            if (!$borrowingBooks) {
                Log::error('Borrowing Books not found or already returned', [
                    'id' => $id
                ]);

                return ResponseHelper::error('Borrowing Books not found or already returned', 404);
            }

            // Update BorrowingBooks
            $borrowingBooks->return_date = now();
            $borrowingBooks->save();

            // Update Stock
            $book = Books::find($borrowingBooks->book_id);
            $book->stock += $borrowingBooks->quantities;
            $book->save();

            DB::commit();

            // Return Response
            return ResponseHelper::success($borrowingBooks, 'BorrowingBooks returned successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [
                'request' => request()->all(),
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }

    public function list()
    {
        try {
            // Default Relation
            $relations = ['user', 'book'];

            $userRequest = auth()->user()->role == 'user';

            // Get BorrowingBooks
            $data = ModelsBorrowingBooks::with($relations)->where('user_id', $userRequest ? '=' : '!=', $userRequest ? auth()->user()->id : null)->get();

            return ResponseHelper::success($data, 'List BorrowingBooks successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'request' => request()->all(),
            ]);
            return ResponseHelper::error('Something went wrong', 500);
        }
    }
}
