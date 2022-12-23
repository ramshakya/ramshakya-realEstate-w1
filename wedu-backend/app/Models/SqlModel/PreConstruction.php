<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreConstruction extends Model
{
    use HasFactory;
    protected $table='PreConstructionBuilding';
    protected $fillable = [
        'id',
        'AgentId',
        'BuildingName',
        'BuilderId',
        'Address',
        'Country',
        'City',
        'State',
        'MainInterection',
        'PostelCode',
        'DemoF',
        'Community',
        'Demo',
        'AddrInfo',
        'BuildingType',
        'BuildingStatus',
        'SaleStatus',
        'SizeRange',
        'PriceRange',
        'Storeys',
        'Suites',
        'Bedroom',
        'Bathroom',
        'Possession',
        'Content',
        'Adrrr55',
        'Asasasas',
        'MediaImage',
        'VideoLink',
        'Attechments',
        'Amenities',
        'AmenitiesMaintenance',
        'Status',
        'Map',
        'created_at',
        'updated_at'
    ];
}
 