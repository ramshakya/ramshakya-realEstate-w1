<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAmenities extends Model
{
    use HasFactory;
    protected $table='master_amenities';
    protected $fillable = [
        'id',
        'Name',
        'Status',
        'created_at',
        'updated_at'
    ];
}
