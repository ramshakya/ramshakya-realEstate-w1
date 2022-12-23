<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = "Countries";
    protected $fillable = [
        'code','name','nationality_name','languages','icon_as_css','description','meta','tags','status_id'
    ];
    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
        "languages" => 'array'
    ];
}
