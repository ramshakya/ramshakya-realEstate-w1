<?php

namespace App\Models\SqlModel;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedListing extends Model
{
    use HasFactory;
    protected $table = "FeaturedListings";
    protected $fillable = [
        'id',
        'PropertyId',
        'AgentId',
        'ListingId',
        'created_at',
        'updated_at'
    ];

    public function getProperty(){
        return $this->belongsTo('App\Models\RetsPropertyData', 'ListingId','ListingId');
    }
}
