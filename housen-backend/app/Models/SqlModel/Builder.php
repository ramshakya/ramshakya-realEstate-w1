<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Builder extends Model
{
    use HasFactory;
    protected $table = "builder";
    protected $fillable = [
        'id',
        'AgentId',
        'BuilderName',
        'BuilderPhone',
        'BuilderEmail',
        'BuilderCountry',
        'BuilderAddress',
        'BuilderCity',
        'BuilderState',
        'BuilderPostalCode',
        'BuilderDescription',
        'Logo',
        'Status',
        'created_at',
        'updated_at',
        'SecondLogo',
        'ThirdLogo'
    ];
}
