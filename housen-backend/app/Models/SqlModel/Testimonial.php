<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    protected $table = "Testimonial";
    protected $fillable = [
        'id',
        'AgentId',
        'Name',
        'Description',
        'Image',
        'Status',
        'Rating',
        'created_at',
        'updated_at'
    ];
}
