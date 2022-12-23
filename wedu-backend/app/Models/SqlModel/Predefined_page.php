<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Predefined_page extends Model
{
    use HasFactory;
    protected $table = "PredefinedPage";
    protected $fillable = [
        'id',
        'AgentId',
        'PageName',
        'PageUrl',
        'MetaTitle',
        'MetaTags',
        'MetaDescription',
        'MlsStatus',
        'Area',
        'Bathrooms',
        'Bedrooms',
        'City',
        'MinPrice',
        'MaxPrice',
        'PropertyType',
        'SqftRange',
        'ZipCode',
        'Status',
        'created_at',
        'updated_at'
    ];
}
