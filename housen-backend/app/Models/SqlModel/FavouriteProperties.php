<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteProperties extends Model
{
    use HasFactory;
    protected $table = "FavouriteProperties";
    protected $fillable = [
        'id',
        'ListingId',
        'LeadId',
        'AgentId',
        'created_at',
        'updated_at'
    ];
}
