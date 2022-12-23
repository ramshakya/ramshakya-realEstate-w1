<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityData extends Model
{
    use HasFactory;
    protected $table = "CityData";
    protected $fillable = [
        'id',
        'AgentId',
        'CityName',
        'Slug',
        'MetaTitle',
        'MetaTags',
        'MetaDescription',
        'Content',
        'Image',
        'Status',
        'Featured',
        'created_at',
        'updated_at'
    ];
}

