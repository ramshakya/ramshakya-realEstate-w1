<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEnquiry extends Model
{
    use HasFactory;
    protected $table = "ContactEnquiry";
    protected $fillable = [
        'id',
        'AgentId',
        'Name',
        'Email',
        'Phone',
        'Message',
        'IpAddress',
        'Url',
        'Page',
        'created_at',
        'updated_at'
    ];

}
