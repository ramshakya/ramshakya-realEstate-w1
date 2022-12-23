<?php

namespace App\Constants;

class PropertyConstants
{


    const LIMIT = 12;
    const HOME_PAGE_LIMIT = 4;
    const PAGE_DATA_LIMIT = 12;
    const MAP_PAGE_DATA_LIMIT = 500;
    const MAP_PAGE_DATA_LIMIT_SQL = 500;
    const AUTO_SUGGESTION_LIMIT = 5;
    const AUTO_SUGGESTION_DEFAULT_LIMIT = 5;
    const SIMILAR_LIST = 4;
    const PRICELIST = 150;
    const GTACITY = "Toronto";
    const YELP_API_KEY = "X3ASKQfAYvSj1IkY_wx307yfoxqfaTpDHxH0xqUlRcD_fkXwj73-K9wWzCHUDjVxMLAiC_ho0qoBy0AJVg7q0vJ3-6KMExTzPPFCMf3JJE0l0lmArUEXisGP7eDhX3Yx";
    const YELP_BUSINESS_SEARCH = "https://api.yelp.com/v3/businesses/search?term=";
    const RADIUS_FOR_NEARBY = '3000';
    const YELP_CLIENT_ID = "BXd8Ii2p6Y6bZdzhq4DGKQ";
    const FEATURED_CITY = "Markham";
    const FEATURED_PRICE = '1500000';
    const MAP_MARKERS_SELECT_DATA = [
        "ListingId",
        "id",
        "Latitude",
        "Longitude",
        "ShortPrice"
    ];
    const SELECT_SOLD_DATA = [
        'id',
        "Orig_dol",
        'Sp_dol',
        'BedroomsTotal',
        'BathroomsFull',
        'Sqft',
        "ListPrice",
        "StandardAddress",
        "City",
        "ListingId",
        "Gar",
        "Dom",
        "ImageUrl",
        "PropertyStatus",
        "Status",
        "Dom",
        "PropertySubType",
        'County',
        "PropertyType",
        "SlugUrl",
        "Park_spcs",
        "Community",
        "Extras",
        "Latitude",
        "Longitude",
        "Vow_exclusive",
        "Ad_text",
	"Timestamp_sql"
    ];
    const SELECT_DATA = [
        'id',
        "Orig_dol",
        'BedroomsTotal',
        'BathroomsFull',
        'Sqft',
        "ListPrice",
        "StandardAddress",
        "City",
        "ListingId",
        "Gar",
        "Dom",
        "ImageUrl",
        "PropertyStatus",
        "Status",
        "Dom",
        "PropertySubType",
        'County',
        "PropertyType",
        "SlugUrl",
        "Park_spcs",
        "Community",
        "Extras",
        "Latitude",
        "Longitude",
        "Sp_dol",
        "Vow_exclusive",
        "Ad_text",
        "Timestamp_sql"
    ];
    const HOME_SELECT_DATA = [
        'id',
        "Orig_dol",
        'BedroomsTotal',
        'BathroomsFull',
        'Sqft',
        "ListPrice",
        "StandardAddress",
        "ListingId",
        "Gar",
        "Dom",
        "ImageUrl",
        "PropertyStatus",
        "Dom",
        "SlugUrl",
        "Park_spcs",
        "Vow_exclusive",
        "Ad_text",
        "City",
	"Timestamp_sql"
    ];
    const STATS_PROPERTY_DETAILS_DATA = [
        "id",
        "Extras",
        "ListPrice",
        "ListingId",
        "ImageUrl",
        "SlugUrl",

    ];
    const PROPERTY_DETAILS_SELECT_DATA = [
        'id',
        'Br',
        'Bath_tot',
        "Orig_dol",
        'Sqft',
        "Lp_dol",
        "Addr",
        "Area",
        "Ml_num",
        "Gar_spaces",
        "Dom",
        "Kit_plus",
        "S_r",
        "Type_own1_out",
        'County',
        "Water",
        "Taxes",
        "Extras",
        "Ad_text",
        "Rltr",
        "Status",
        "Community",
        "A_c",
        "Bsmt1_out",
        "Fpl_num",
        "Cross_st",
        "Gar_type",
        "Fuel",
        "Pool",
        "Latitude",
        "Longitude",
        "RoomsDescription",
        "SlugUrl",
        "Park_spcs",
        "Prop_feat1_out",
        "Prop_feat2_out",
        "Timestamp_sql"
    ];
    const PROPERTY_DETAILS_SELECT_DATA_COMM = [
        'id',
        'Bath_tot',
        "Orig_dol",
        "Lp_dol",
        "Addr",
        "Area",
        "Ml_num",
        "Dom",
        "S_r",
        "Type_own1_out",
        'County',
        "Water",
        "Taxes",
        "Extras",
        "Ad_text",
        "Rltr",
        "Status",
        "Community",
        "A_c",
        "Bsmt1_out",
        "Cross_st",
        "Gar_type",
        "Latitude",
        "Longitude",
        "SlugUrl",
        "Park_spcs",
        "Timestamp_sql"
    ];
    const PROPERTY_DETAILS_SELECT_DATA_CONDO = [
        'id',
        'Br',
        'Bath_tot',
        "Orig_dol",
        'Sqft',
        "Lp_dol",
        "Addr",
        "Area",
        "Ml_num",
        "Dom",
        "Kit_plus",
        "S_r",
        "Type_own1_out",
        'County',
        "Taxes",
        "Extras",
        "Ad_text",
        "Rltr",
        "Status",
        "Community",
        "A_c",
        "Bsmt1_out",
        "Fpl_num",
        "Cross_st",
        "Gar_type",
        "Fuel",
        "Latitude",
        "Longitude",
        "RoomsDescription",
        "SlugUrl",
        "Park_spcs",
        "Prop_feat1_out",
        "Prop_feat2_out",
        "Timestamp_sql"
    ];
    const MAP_SELECT_DATA = [
        'id',
        "ImageUrl",
        "Orig_dol",
        "SlugUrl",
        "PropertyStatus",
        "ListPrice",
        "StandardAddress",
        'BedroomsTotal',
        'BathroomsFull',
        'Sqft',
        "Dom",
        "ListingId",
        "Latitude",
        "Longitude",
        "ShortPrice"
    ];
    const PROPERTY_STATS_DATA = [
        'id',
        "ListPrice",
        "StandardAddress",
        "City",
        "ListingId",
        "ImageUrl",
        "MlsStatus",
        "SlugUrl",
    ];
    //
}
