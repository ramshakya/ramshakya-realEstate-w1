<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    protected $table = "TableEventsColors";
    protected $fillable = [
        'id',
        'EventName',
        'EventTitle',
        'AdminId',
        'Status',
        'Color',
        'IsEditable',
        'created_at',
        'updated_at'
    ];
}
