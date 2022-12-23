<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = "staff";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'FullName',
        'FirstName',
        'LastName',
        'Email',
        'AgentId',
        'Gender',
        'Phone',
        'UserId',
        'DeletedAt',
        'ImageUrl',
        'ProjectId',
        'RoleId',
        'created_at',
        'updated_at'
    ];
}
