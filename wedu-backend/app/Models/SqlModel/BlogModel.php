<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogModel extends Model
{
    use HasFactory;
    protected $table = "Blogs";
    protected $fillable = [
        'id',
        'AdminId',
        'Title',
        'MetaTitle',
        'MetaKeyword',
        'MetaDesc',
        'Url',
        'Categories',
        'MainImg',
        'ImgTags',
        'Content',
        'BlogTags',
        'created_at',
        'updated_at'
    ];
}
