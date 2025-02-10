<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBook extends Model
{
    protected $table = 'category_book';

    protected $fillable = [
        'category_name',
    ];
}
