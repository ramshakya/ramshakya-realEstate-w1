<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiries extends Model
{
    use HasFactory;
    protected $table = "Enquiries";
    protected $fillable = [
        "id",
        "name",
        "email",
        "phone",
        "best_time_to_call",
        "purchase_price",
        "down_payment",
        "total_mortgage",
        "page_from",
        "created_at",
        "admin_id",
        "status_id",
        "follow_up",
        "property_id",
        "property_url",
        "user_id",
        "message",
        "schedule_a_showing",
        "user_ip",
        "date",
        "time",
        "booking_start_time",
        "booking_end_time",
        "realtor",
        "property_size",
        "home_style",
        "pro_type",
        "garage_type",
        "bedrooms",
        "bathrooms",
        "Bsmt1_out",
        "propertyaddress",
        "TimeLine",
        "Queries",
        "UserLocation"
    ];
}
