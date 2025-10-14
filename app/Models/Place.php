<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //
    use HasFactory;

    protected $table = "place";
    protected $fillable = [
        'name',
        'descriptions',
        'address',
        'images',
        'idCategory',
    ];

    public function category()
{
    return $this->belongsTo(Category::class, 'idCategory');
}
}
