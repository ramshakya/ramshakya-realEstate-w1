<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefMasterData extends Model
{
    use HasFactory;
    protected $table = "RefMasterData";
    protected $fillable = [
        'name','description','meta','tags','status_id','code','icon_as_css','type_id'
    ];
    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
    ];
}
