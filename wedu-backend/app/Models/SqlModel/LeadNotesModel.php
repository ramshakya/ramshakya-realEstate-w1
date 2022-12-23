<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadNotesModel extends Model
{
    use HasFactory;
    protected $table = "LeadNotes";
    protected $fillable = [
        'id',
        'AgentId',
        'LeadId',
        'Type',
        'Notes',
        'created_at',
        'updated_at'
    ];
}
