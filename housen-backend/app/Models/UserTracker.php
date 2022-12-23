<?php

namespace App\Models;

use App\Models\SqlModel\lead\LeadsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTracker extends Model
{
    use HasFactory;
    protected $table = "UserTracker";
    protected $fillable = [
        "id",
        "UserId",
        "PageUrl",
        "IpAddress",
        "InTime",
        "StayTime",
        "PropertyId",
        "AgentId",
        "FilteredData",
        'PropertyUrl',
        'ListingId',
    ];
    public function userData()
    {
        return $this->hasOne(LeadsModel::class, 'id', 'UserId')->select("id", "ContactName", "Email", "Phone");
    }
}
