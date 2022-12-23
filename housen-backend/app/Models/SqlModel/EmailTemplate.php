<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    protected $table = "EmailTemplates";
    public $timestamps = false;
    protected $fillable = [
        "TemplateName",
        "Content",
        "Subject",
        "Status",
        "AddedTime",
        "UpdatedTime",
        "Type",
    ];
}
