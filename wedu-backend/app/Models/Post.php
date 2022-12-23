<?php

namespace App\Models;

use Corcel\Model\Post as Corcel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertiesCronLog extends Model
{
    Post::find($id);
    Post::status('publish')
           ->whereIn('post_type', ['post', 'cpt_name', 'other_cpt'])
           ->whereHas('taxonomies', function ($query) {
            $query->whereIn('taxonomy',['category', 'category2']);
           })
           ->newest()
           ->get();
}
