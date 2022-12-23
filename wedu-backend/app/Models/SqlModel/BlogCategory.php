<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;
    protected $table = "BlogCategory";
    protected $fillable = [
        "Name",
        "ParentId",
        "Alias"
    ];
}
