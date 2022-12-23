<?php

use App\Http\Controllers\agent\property\PropertyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\agent\assignment\AssignmentController;
use App\Http\Controllers\campaign\CampaignController;
use App\Http\Controllers\agent\LeadAgentController;
use App\Http\Controllers\agent\leads\LeadsController;
use \App\Http\Controllers\agent\MainAgentController;
//use \App\Http\Controllers\agent\ContractLogController;
use \App\Http\Controllers\TestController;
use \App\Http\Controllers\agent\BlogController;
use App\Http\Controllers\agent\StaffController;
use \App\Http\Controllers\agent\MenuBuilderController;
use \App\Http\Controllers\agent\PagesController;
use App\Http\Controllers\agent\CityController;
use App\Http\Controllers\agent\ConstructionBuilding;
use App\Http\Controllers\agent\DeleteController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\propertiesListings\PropertiesController;
use App\Http\Controllers\frontend\FrontendGetProperty;
use App\Http\Controllers\frontend\UserLogin;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\agent\events\EventController;
use App\Http\Controllers\agent\TestimonialController;
use App\Http\Controllers\frontend\propertiesListings\SearchController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//

Route::any('/sentWatchAlerts', [TestController::class, 'sentWatchAlertsTest']);


Route::any('/testProperty', [HomeController::class, 'testProperty']);
Route::any('/testStats', [HomeController::class, 'testStats']);

Route::any('/statsDataStore', [HomeController::class, 'statsDataStore']);
Route::any('/statsDataStore', [TestController::class, 'statsDataStore']);
Route::any('/getPropertyCount', [TestController::class, 'getPropertyCount']);
Route::any('/checkQuery', [TestController::class, 'checkQuery']);


Route::any('/changeSqft', [PropertiesController::class, 'changeSqft']);
Route::any('/getPolygonsData', [PropertiesController::class, 'getPolygonsData']);

Route::any('/allleads', [LeadsController::class, 'getAllLeads']);
Route::any('/allapidata', [LeadsController::class, 'AllApiData']);
Route::any('/propertyimages', [LeadsController::class, 'PropertyImages']);
Route::any('/get-lead-results-get', [LeadsController::class, 'GetLeadResultsGet']);
Route::any('/leads-info-update', [LeadsController::class, 'LeadsInfoUpdate']);

Route::any('/agentsignup', [LeadAgentController::class, 'AgentSignup']);
Route::any('/profilesetting', [LeadAgentController::class, 'ProfileSettings']);
Route::any('/profileupdate', [LeadAgentController::class, 'ProfileUpdate']);
Route::any('/additionalisting', [PropertyController::class, 'AdditionaListing']);
Route::any('/statusleadcontract', [LeadsController::class, 'StatusLeadContract']);
Route::any('/get-info-bymlsid', [PropertyController::class, 'GetInfoBymlsid']);
Route::any('/allzipcodes', [PropertyController::class, 'AllZipCodes']);
Route::any('/allcities', [PropertyController::class, 'AllCities']);
Route::any('/status-update', [LeadsController::class, 'StatusUpdate']);
//Route::any('/contractlogdetailbyleadid', [ContractLogController::class, 'ContractLogDetailbyLeadId']);
Route::any('/genratepdf', [LeadsController::class, 'GenratePdf']);
Route::any('/getmatchingmlsandaddress', [PropertyController::class, 'getMatchingMlsAndAddress']);
Route::any('/testEmail', [TestController::class, 'sendMail']);
//Route::any('/getcontractdetailbyleadId', [ContractLogController::class, 'getContractDetailByLeadId']);
Route::any('/addleadapi', [LeadsController::class, 'AddLeadApi']);
Route::any('/bugsreportpost', [LeadsController::class, 'BugsReportPost']);
Route::any('/savesuggestionreport', [LeadsController::class, 'saveSuggestionReportpost']);
//Route::any('/deletecontractlogbyid', [ContractLogController::class, 'DeleteContractLogById']);
//Route::any('/saveContractLogData', [ContractLogController::class, 'saveContractLogData']);
Route::any('/deleteleads', [LeadsController::class, 'DeleteleadsPost']);
Route::any('/updateagent', [LeadAgentController::class, 'UpdateAgent']);


Route::group(['prefix' => 'v1'], function () {
    //import properties
    Route::any('/testApi', [StatsController::class, 'testApi']);

    Route::group(['prefix' => 'importProperty'], function () {
        Route::any('/vowListing', [\App\Http\Controllers\importListings\ListingsControllerVOW::class, 'importPropertyListing']);
    });

    // This is agent
    //

    Route::group(['prefix' => 'agent'], function () {
        //Route::post('/getdata', [PropertyController::class, 'getData']);
        // This API is for Agent
        // propFeature
        Route::any('/stats/getPropertyGraphData', [StatsController::class, 'getPropertyGraphData']);

        Route::any('/stats/getstatsdata', [StatsController::class, 'graphData']);
        Route::any('/stats/getCityGraphData', [StatsController::class, 'getCityGraphData']);
        Route::any('/stats/getPropertyGraphData', [StatsController::class, 'getPropertyGraphData']);

        Route::any('/stats/getuserstats', [StatsController::class, 'getUserStats']);
        Route::any('/stats/getuserstats_filter', [StatsController::class, 'getuserstats_filter']);
        Route::any('/stats/getCityData', [StatsController::class, 'getCityData']);
        Route::group(['prefix' => 'events'], function () {
            Route::post('/timeShow', [EventController::class, 'timeShow']);
            Route::post('/addEvent', [EventController::class, 'add_event']);
            Route::post('/editEvent', [EventController::class, 'edit_event_form']);
            Route::post('/deleteEvent', [EventController::class, 'delete_event']);
            Route::post('/getSlots', [EventController::class, 'getSlots']);
            Route::post('/addSlots', [EventController::class, 'addSlots']);
            Route::post('/meetAgent', [EventController::class, 'meetAgent']);

        });
        Route::post('/changePassword', [MainAgentController::class, 'changePassword']);
        Route::post('/addAgent', [\App\Http\Controllers\superAdmin\AgentController::class, 'store']);

        Route::group(['prefix' => 'profile'], function () {
        });
        Route::post('/getagentinfo', [LeadAgentController::class, 'getagentinfo']);
        Route::any('/getdata', [PropertyController::class, 'getData']);
        Route::any('/getPropData', [PropertyController::class, 'getPropData']);
        Route::post('/get-lead-agent', [LeadAgentController::class, 'getData']);
        Route::any('/agent-graph-data', [LeadAgentController::class, 'getlapGraphData']);
        //GetNotifications
        Route::get('/all-notifications', [MainAgentController::class, 'showNotifications']);
        Route::any('/enquiries', [MainAgentController::class, 'enquiries_data']);
        Route::any('/schedules', [MainAgentController::class, 'schedules_data']);

        Route::any('/listing-graph-data', [MainAgentController::class, 'getListingGraphData']);

        Route::post('/refreshlead', [MainAgentController::class, 'refreshOnlineLead']);

        Route::any('/pergedata-graph-data', [MainAgentController::class, 'getPergeGraphData']);
        Route::group(['prefix' => 'staff'], function () {
            Route::post('/add', [StaffController::class, 'store']);
        });
        // This API is for Setting
        Route::group(['prefix' => 'setting'], function () {
            Route::post('/UpdSetting', [MainAgentController::class, 'UpdSetting']);
            Route::post('/changePassword', [MainAgentController::class, 'changePassword']);
        });
        // This API is for Agent Assignment
        Route::group(['prefix' => 'profile'], function () {
            Route::post('/get-county', [LeadAgentController::class, 'getCounty']);
            Route::post('/get-city', [LeadAgentController::class, 'getCity']);
            Route::post('/get-zip', [LeadAgentController::class, 'getZip']);
        });
        // This API is for Agent Property
        Route::group(['prefix' => 'property'], function () {
            Route::post('/bulk-import-file', [PropertyController::class, 'BulkImportFile']);
            Route::post('/importCSV', [PropertyController::class, 'import_data']);
            Route::post('/importZip', [PropertyController::class, 'importZip']);
            Route::post('/images', [PropertyController::class, 'SliderImg']);
            Route::post('/AddPropertyInfo', [PropertyController::class, 'AddPropertyInfo']);
            Route::post('/DescriptionAdd', [PropertyController::class, 'DescriptionAdd']);
            Route::post('/FeaturesAdd', [PropertyController::class, 'FeaturesAdd']);
            Route::post('/DocumentAdd', [PropertyController::class, 'DocumentAdd']);
            Route::post('/ImagesAdd', [PropertyController::class, 'ImagesAdd']);
            Route::post('/DelImg', [PropertyController::class, 'DelImg']);
            Route::post('/propFeature', [PropertyController::class, 'propFeature']);
        });
        // This API is for Agent Assignment
        /*Route::group(['prefix' => 'assignment'], function () {
            // This is agent assignment for boards
            Route::post('/getdata', [AssignmentController::class, 'getData']);
            // This is agent assignment for offices
            Route::post('/getboardData', [AssignmentController::class, 'getboardData']);
            // This is agent assignment for agents
            Route::post('/getagentData', [AssignmentController::class, 'getagentData']);
        });*/
        // This is campaign API
        Route::group(['prefix' => 'campaign'], function () {
            Route::post('/add-template', [CampaignController::class, 'addTemplate']);
            Route::post('/add-campaign', [CampaignController::class, 'addCampaign']);
            Route::post('/get-template', [CampaignController::class, 'getTemplate']);
            Route::post('/get-board', [CampaignController::class, 'getBoard']);
            Route::post('/get-agent-type', [CampaignController::class, 'getAgentType']);
            Route::post('/get-agent-office', [CampaignController::class, 'getAgentOffice']);
            Route::post('/get-agent', [CampaignController::class, 'getAgent']);
            Route::post('/get-leads', [CampaignController::class, 'getLeads']);
            Route::post('/get-agent-leadData', [CampaignController::class, 'getleadDataCamp']);
            Route::post('/delete-template', [CampaignController::class, 'DeleteTemplate']);
            Route::post('/delete-leadCamp', [CampaignController::class, 'DeleteLead']);
        });
        // This is Blog API
        Route::group(['prefix' => 'blog'], function () {
            Route::post('/add-category', [BlogController::class, 'addCategory']);
            Route::post('/add-blog', [BlogController::class, 'addBlog']);
            Route::post('/delete-blog', [BlogController::class, 'DeleteBlog']);
            Route::post('/delete-blogcategory', [BlogController::class, 'DeleteBlogCategory']);
        });
        // This is campaign API
        Route::group(['prefix' => 'leads'], function () {
            Route::post('/get-leads', [LeadsController::class, 'getLeads']);
            Route::any('/AddNotes', [LeadsController::class, 'AddNotes']);
            Route::post('/EmailTemp', [LeadsController::class, 'GetEmailTemp']);
            Route::post('/UpdateLeadAgent', [LeadsController::class, 'UpdateLeadAgent']);
            Route::post('/AddMail', [LeadsController::class, 'AddMail']);
            Route::any('/PropertiesViewed', [LeadsController::class, 'PropertiesViewed']);
            Route::any('/ChangeAgent', [LeadsController::class, 'ChangeAgent']);
            Route::any('/all-leads', [LeadsController::class, 'LeadsAll']);
            Route::any('/allListing', [LeadsController::class, 'allListing']);


        });
        // This is for creating  Dynamic menu API
        Route::group(['prefix' => 'menu'], function () {
            Route::post('/add-menu', [MenuBuilderController::class, 'add_menu']);
            Route::post('/add-menu-data', [MenuBuilderController::class, 'add_menu_data']);
            Route::post('/get-menu', [MenuBuilderController::class, 'get_menu']);
        });
        // This is for creating  Dynamic page API
        Route::group(['prefix' => 'pages'], function () {
            Route::post('/add-page', [PagesController::class, 'AddPage']);
            Route::post('/add-predefine-page', [PagesController::class, 'AddPredefinePage']);
            Route::post('/update-code', [PagesController::class, 'update_code']);
            Route::post('/update-code-property', [PagesController::class, 'update_code_property']);
        });
        Route::group(['prefix' => 'city'], function () {
            Route::post('/update-city', [CityController::class, 'update_create_city']);
            Route::post('/get-cities', [CityController::class, 'get_city_list']);
            Route::post('/get-area', [CityController::class, 'get_area_list']);
            Route::post('/update-area', [CityController::class, 'update_create_area']);
            Route::post('/city-featured', [CityController::class, 'city_featured']);
            Route::post('/area-featured', [CityController::class, 'area_featured']);
        });
        Route::group(['prefix' => 'building'], function () {
            Route::post('/add_builder_data', [ConstructionBuilding::class, 'add_builder_data']);
            Route::post('/get_builder', [ConstructionBuilding::class, 'get_builder']);
            Route::post('/add_construction_data', [ConstructionBuilding::class, 'add_construction_data']);
            Route::post('/get_pre_construction', [ConstructionBuilding::class, 'get_preconstruction_building']);
            Route::post('/get_builder_data', [ConstructionBuilding::class, 'get_builder_data']);
            Route::post('/get_amenity_data', [ConstructionBuilding::class, 'get_amenity_data']);
            Route::post('/add_amenity_data', [ConstructionBuilding::class, 'add_amenity_data']);
        });

        // delete
        Route::post('/delete-data', [DeleteController::class, 'delete_data']);
        // For leads rating
        Route::any('/rating-data', [LeadsController::class, 'leads_rating']);

        Route::group(['prefix' => 'testimonial'], function () {
            Route::any('/getTestimonial', [TestimonialController::class, 'getTestimonial']);
            Route::any('/add-edit-testimonial', [TestimonialController::class, 'addEditTestimonial']);
            Route::any('/delete-testimonial', [TestimonialController::class, 'DeleteTestimonial']);
        });
        Route::post('/ClearNotification', [DeleteController::class, 'ClearNotification']);
        Route::post('/leaddata-graph-data', [MainAgentController::class, 'leaddataGraphData']);
        Route::post('/getNotification', [MainAgentController::class, 'getNotifications']);
        Route::post('/Notifications', [MainAgentController::class, 'Notifications']);
    });

    // Frontend API'S
    //
    Route::group(['prefix' => 'services'], function () {
        //get url slugs
        Route::any('/getFiltersData', [FrontendGetProperty::class, 'getFiltersData']);
        Route::any('getSlugs', [FrontendGetProperty::class, 'getSlugs']);
        Route::post('login', [UserLogin::class, 'loginAgent']);
        Route::any('shareEmail', [PropertiesController::class, 'shareEmail']);
        Route::post('saveSearch', [PropertiesController::class, 'saveSearch']);
        Route::post('markerInfo', [PropertiesController::class, 'markerInfo']);
        Route::post('checkuser', [UserLogin::class, 'checkuser']);
        Route::get('unsubscribe/{id}', [FrontendGetProperty::class, 'UnsubscribeEmail']);


        Route::get('updateReadEmail/{id}', [FrontendGetProperty::class, 'updateEmail']);
        // user register
        Route::post('register', [UserLogin::class, 'register']);
        Route::post('loginSocial', [UserLogin::class, 'loginSocial']);
        // Route::post('sendCode', [UserLogin::class, 'sendCode']);
        Route::post('confirmCode', [UserLogin::class, 'confirmCode']);
        Route::post('resendOtp', [UserLogin::class, 'resendOtp']);


        Route::middleware([ 'checkUser'])->any('/logout', [UserLogin::class, 'logout']);
        Route::post('duplicateEmail', [UserLogin::class, 'duplicateEmail']);
        Route::post('forgotPassword', [UserLogin::class, 'forgotPassword']);
        Route::post('verifyToken', [UserLogin::class, 'verifyTokenForgotPassword']);
        Route::middleware([ 'checkUser'])->any('/loginInfo', [UserLogin::class, 'loginInfo']);
        Route::middleware([ 'checkUser'])->any('/updateUserDetail', [UserLogin::class, 'updateUserDetail']);
        Route::middleware('checkUser')->any('/details', [UserLogin::class, 'details']);
        Route::middleware([ 'checkUser'])->post('/getSavedSearch', [UserLogin::class, 'getSavedSearch']);
        Route::middleware([ 'checkUser'])->post('/deleteSavedSearch', [UserLogin::class, 'deleteSavedSearch']);
        Route::middleware([ 'checkUser'])->post('/sentEmailHistory', [UserLogin::class, 'sentEmailHistory']);
        Route::post('/getSavedSearchDetail', [UserLogin::class, 'getSavedSearchDetail']);

        Route::post('/AddUserPreference', [UserLogin::class, 'AddUserPreference']);


        Route::post('userActivity', [UserLogin::class, 'userActivity']);
        Route::post('leadForm', [UserLogin::class, 'leadForm']);
        Route::post('MakeFavouriteProperty', [FrontendGetProperty::class, 'MakeFavouriteProperty']);
        Route::post('DeleteFavouriteProperty', [FrontendGetProperty::class, 'DeleteFavouriteProperty']);
        Route::post('SubmitHomeValue', [HomeController::class, 'SubmitHomeValue']);
        Route::post('GetStaffs', [HomeController::class, 'GetStaffs']);
        Route::post('GetBlogs', [HomeController::class, 'GetBlogs']);
        Route::post('GetBlogTitle', [HomeController::class, 'GetBlogTitle']);
        Route::post('GetBlogCategory', [HomeController::class, 'GetBlogCategory']);
        Route::post('ContactUsForm', [HomeController::class, 'ContactUsForm']);
        Route::post('FeedbackForm', [HomeController::class, 'FeedbackForm']);
        Route::any('GetBlogsSlugs', [HomeController::class, 'GetBlogsSlugs']);
        Route::post('GetSoldProperty', [HomeController::class, 'GetSoldProperty']);
        Route::post('SendListingRequest', [HomeController::class, 'SendListingRequest']);
        Route::post('GetPreferenceData', [HomeController::class, 'GetPreferenceData']);
        Route::post('PopularSearch', [HomeController::class, 'PopularSearch']);
        Route::post('getWatchProperty', [UserLogin::class, 'getWatchProperty']);


        // Predefined Data
        Route::group(['prefix' => 'bootstrap'], function () {
            Route::any('/getMoreFilters', [FrontendGetProperty::class, 'getMoreFilters']);
            Route::any('/getCities', [FrontendGetProperty::class, 'getCities']);
            Route::any('/filterData', [PropertiesController::class, 'filterData']);
        });
        // New Routes By Ram
        Route::group(['prefix' => 'search'], function () {
            Route::any('/propertiesSearch', [PropertiesController::class, 'propertiesSearch']);
            Route::any('/propertiesSearchProperty', [PropertiesController::class, 'propertiesSearchProperty']);
            Route::any('/propertiesSearchMap', [PropertiesController::class, 'propertiesSearchMap']);
            Route::post('/suggestionSearch', [PropertiesController::class, 'getAutoSearchResults']);
            Route::any('/getDataFromYelp', [PropertiesController::class, 'getDataFromYelp']);
            // Route::post('/mapSearchTotal', [SearchController::class, 'mapSearchTotal']);

            Route::post('/mapSearchList', [SearchController::class, 'mapSearchList']);
            Route::post('/mapBoundary', [SearchController::class, 'mapBoundary']);
            Route::post('/mapSearchMarkers', [SearchController::class, 'mapSearchMarkers']);
            Route::any('/updategeojson', [SearchController::class, 'updategeojson']);
        });

        // property-details
        Route::group(['prefix' => 'fetchPageViews'], function () {
            Route::any('/propertyDetails', [PropertiesController::class, 'propertiesDetails']);
            Route::any('/similarProperty', [PropertiesController::class, 'similarProperty']);
            Route::post('/listingHistory', [PropertiesController::class, 'listingHistory']);
        });

        // home page routes
        Route::group(['prefix' => 'home'], function () {
            Route::any('/getBlogs', [HomeController::class, 'getBlog']);
            Route::any('/saveContactForm', [HomeController::class, 'saveContactForm']);
            Route::any('/propertyList', [PropertiesController::class, 'propertiesList']);
            Route::any('/getCities', [HomeController::class, 'getCities']);
            Route::any('/getWebsettings', [HomeController::class, 'getWebSettings']);
            Route::any('/getSearchData', [HomeController::class, 'getSearchData']);
        });

        // global routes
        //
        Route::group(['prefix' => 'global'], function () {
            Route::middleware([ 'checkUser'])->any('/GetFavouriteProperty', [FrontendGetProperty::class, 'GetFavouriteProperty']);
            Route::get('/getProperties', [HomeController::class, 'getProperties']);
            Route::any('/featuredMlsListing', [HomeController::class, 'getFeaturedListings']);
            Route::any('/recentMlsListing', [HomeController::class, 'getRecentListings']);
            Route::any('/soldListings', [HomeController::class, 'getSoldListings']);
            Route::any('/webSettings', [HomeController::class, 'webSettings']);
            Route::post('/ContactEnquiry', [HomeController::class, 'ContactEnquiry']);
            Route::post('fav-property', [FrontendGetProperty::class, 'MakeFavouriteProperty']);
            Route::post('DeleteFavouriteProperty', [FrontendGetProperty::class, 'DeleteFavouriteProperty']);
            Route::post('getFavouriteProperties', [FrontendGetProperty::class, 'getFavouriteProperties']);
            Route::any('/featuredList', [HomeController::class, 'featuredList']);
            Route::any('/getTestimonials', [HomeController::class, 'getTestimonials']);

            Route::any('/homeStats', [HomeController::class, 'homeStats']);
            Route::any('/marketStatsFilterData', [HomeController::class, 'marketStatsFilterData']);
            Route::post('/marketStatsCitiesData', [HomeController::class, 'marketStatsCitiesData']);
            Route::any('/domAvgMedian', [HomeController::class, 'domAvgMedian']);
            Route::any('/soldActive', [HomeController::class, 'soldActive']);
            Route::any('/medianRental', [HomeController::class, 'medianRental']);
            Route::any('/propertyTyprDist', [HomeController::class, 'propertyTypeDistribution']);
            Route::any('/absorptionData', [HomeController::class, 'absorptionData']);
            Route::any('/getSoldByAgent', [HomeController::class, 'getSoldByAgent']);
            Route::any('/preConstruction', [HomeController::class, 'preConstruction']);
            Route::any('/preConstructionDetail', [HomeController::class, 'preConstructionDetail']);

        });
    });
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
