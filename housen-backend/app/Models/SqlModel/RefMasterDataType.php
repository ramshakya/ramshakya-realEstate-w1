<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefMasterDataType extends Model
{
    use HasFactory;
    protected $table = "RefMasterDataTypes";
    protected $fillable = [
        'name','description','meta','tags','status_id','code','icon_as_css'
    ];
    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
    ];
}
