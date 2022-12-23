<?php

namespace App\Models\Sqlmodel\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatesModel extends Model
{
    use HasFactory;
    protected $table = "templates";
    protected $fillable = [
        'name', 'type', 'agent_id', 'subject', 'content'
    ];
}
