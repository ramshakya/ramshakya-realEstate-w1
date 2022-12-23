<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = "Menu";
    protected $fillable = [
        'id',
        'AgentId',
        'MenuName',
        'MenuPosition',
        'MenuContent',
        'Status',
        'created_at',
        'updated_at'
    ];

}
