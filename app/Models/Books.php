<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'title',
        'author',
        'description',
        'image',
        'category_id',
        'stock',
    ];

    public function category()
    {
        return $this->hasOne(CategoryBook::class, 'id');
    }
}
