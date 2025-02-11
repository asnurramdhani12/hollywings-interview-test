<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowingBooks extends Model
{
    protected $table = 'borrowing_books';

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'return_date',
        'quantities',
        'accepted',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function book()
    {
        return $this->hasOne(Books::class, 'id', 'book_id');
    }
}
