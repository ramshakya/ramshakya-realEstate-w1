<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityNeighbours extends Model
{
    use HasFactory;
    protected $table = "CityNeighbours";
    protected $fillable = [
        'id',
        'AgentId',
        'AreaName',
        'CityName',
        'Slug',
        'MetaTitle',
        'MetaTags',
        'MetaDescription',
        'Content',
        'Status',
        'Featured',
        'created_at',
        'updated_at'
    ];
}
