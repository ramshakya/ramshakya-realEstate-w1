<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    use HasFactory;
    protected $table = "Pages";
    protected $fillable = [
        'id',
        'AgentId',
        'PageName',
        'PageUrl',
        'Content',
        'MetaTitle',
        'MetaTags',
        'MetaDescription',
        'Status',
        'CreatedBy',
        'Setting',
        'created_at',
        'updated_at'
    ];
}
