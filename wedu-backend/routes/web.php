<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\agent\MainAgentController;
use App\Http\Controllers\agent\property\PropertyController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ListingsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\superAdmin\MainSuperAdminController;
use App\Http\Controllers\TestMlsConfig;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\agent\events\EventController;
use App\Http\Controllers\frontend\UserLogin;
use App\Http\Controllers\GenerateXMLFile;
use App\Http\Controllers\SocialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('redirect', [SocialController::class, 'redirect'])->name('redirect');
Route::get('googleCallBack', [UserLogin::class, 'googleLogin'])->name('googleLogin');
Route::get("/Purged_count_Mls", [\App\Http\Controllers\TestController::class, 'Purged_count_Mls']);

Route::get("/twilio/test/{id?}", [\App\Http\Controllers\TestController::class, 'TwilioTest']);
Route::get("/ImageCheck", [\App\Http\Controllers\importListings\ImageCheckSold::class, 'imageCheck']);

Route::get("/testSold", [\App\Http\Controllers\importListings\ListingsControllerVOW::class, 'importPropertyListing']);
Route::get("/testSoldNew", [\App\Http\Controllers\importListings\SoldListingControllerVOW::class, 'importPropertyListing']);
Route::get("/retsApiTest", [TestMlsConfig::class, 'retsApiTest']);
Route::get("/testMail", [\App\Http\Controllers\TestController::class, 'sendMail']);
Route::get("/testFeatures", [\App\Http\Controllers\TestController::class, 'filterPropertyFeatured']);
Route::get("/createtable", [ListingsController::class, 'createtable']);
Route::get("/importPropertyListing", [ListingsController::class, 'importPropertyListing']);
Route::get("/imageImport", [ImagesController::class, 'imageImport']);
Route::get("/refreshListing", [\App\Http\Controllers\RefreshListingController::class, 'importPropertyListing']);
Route::get("/importPropertyClass", [\App\Http\Controllers\TestMlsConfig::class, 'index']);
Route::get("/importPropertyForPrice", [\App\Http\Controllers\ListingsController::class, 'importPropertyListingV2']);
// retsApiTest
//
// ADMIN Route
Route::any('/admin', [AdminController::class, 'index']);
Route::prefix('agent')->group(function () {
    // sitemap urls
    Route::prefix('sitemap')->group(function () {
        Route::any('/', [GenerateXMLFile::class, 'index'])->name('route');
        Route::any('/generatePropertyXml', [GenerateXMLFile::class, 'generatePropertyXml']);
        Route::any('/generateSoldListingXml', [GenerateXMLFile::class, 'generateSoldListingXml']);
        Route::any('/generateBlogXml', [GenerateXMLFile::class, 'generateBlogXml']);
        Route::any('/generateCityXml', [GenerateXMLFile::class, 'generateCityXml']);
        Route::any('/generateSitemapXml', [GenerateXMLFile::class, 'generateSitemapXml']);
        Route::any('/generatePageXml', [GenerateXMLFile::class, 'generatePageXml']);
    });

    Route::prefix('events')->group(function () {
        Route::any('/', [EventController::class, 'index']);
        Route::any('/calendar', [EventController::class, 'calendar']);
        Route::any('/addSchedule', [EventController::class, 'addSchedule']);
        Route::any('/editEvent/{id?}', [EventController::class, 'edit_event']);
    });

    Route::prefix('stats')->group(function () {
        Route::any('/city', [StatsController::class, 'search_city'])->middleware('IsLogin');
        Route::any('/user_stats', [StatsController::class, 'user_stats'])->middleware('IsLogin');

        Route::any('/property_viewed', [StatsController::class, 'property_viewed'])->middleware('IsLogin');
        Route::any('/getstatsdata', [StatsController::class, 'graphData'])->middleware('IsLogin');

    });

    Route::any('/track_users', [MainAgentController::class, 'trackUsers']); //->middleware('is_superAdmin')
    Route::any('/track_user/view/{id}', [MainAgentController::class, 'trackUserView']); //->middleware('is_superAdmin')
    //Notifications
    Route::any('/Notifications', '\App\Http\Controllers\agent\MainAgentController@AllNotifications')->name('agent.notifications')->middleware('is_agent');

    //Enquiries
    Route::any('/Enquiries', '\App\Http\Controllers\agent\MainAgentController@AllEnquiries')->name('agent.enquiries')->middleware('is_agent');
    //Schedules
    Route::any('/Schedules', '\App\Http\Controllers\agent\MainAgentController@AllSchedules')->name('agent.schedules')->middleware('is_agent');

    // Email logs
    Route::any('/email_logs', '\App\Http\Controllers\agent\MainAgentController@email_logs')->name('agent.email_logs')->middleware('IsLogin');
    Route::any('/email-data', '\App\Http\Controllers\agent\MainAgentController@email_data')->middleware('IsLogin');

    Route::any('/list', '\App\Http\Controllers\agent\LeadAgentController@index')->name('agent.dashboard')->middleware('is_agent');
    Route::any('/dashboard', [\App\Http\Controllers\agent\MainAgentController::class, 'dashboard'])->middleware("auth");
    Route::any('/leadview/{id?}', '\App\Http\Controllers\agent\leads\LeadsController@LeadView')->name('agent.leadview')->middleware('is_agent');
    Route::any('/myaccount', '\App\Http\Controllers\agent\MainAgentController@UpdateAccount')->name('agent.myaccount')->middleware('is_agent');
    Route::any('/AddBrocker', '\App\Http\Controllers\TestController@AddBrocker')->name('agent.AddBrocker')->middleware('is_agent');
    //    Route for setting
    Route::prefix('setting')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\MainAgentController@Setting')->name('Setting')->middleware('is_agent');
        Route::any('/change-password', '\App\Http\Controllers\agent\MainAgentController@ChangePass')->name('agent.ChangePass')->middleware('is_agent');
    });
    //Routes for property
    Route::prefix('property')->group(function () {
        Route::any('/', [PropertyController::class, 'index'])->name('property')->middleware('auth');
        Route::any('/AddProperty/{id?}', '\App\Http\Controllers\agent\property\PropertyController@AddProperty')->name('AddProperty')->middleware('is_agent');
    });

    Route::any('/', '\App\Http\Controllers\agent\MainAgentController@index')->name('agent.login');
    Route::any('/dashboard', '\App\Http\Controllers\agent\MainAgentController@dashboard')->name('agent.dashboard')->middleware('is_agent');
    //Routes for property
    Route::prefix('staff')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\StaffController@staff')->name('superAdmin.staff.view')->middleware('IsLogin');
        Route::any('/add/{id?}', '\App\Http\Controllers\agent\StaffController@createStaff')->name('agent.staff.create')->middleware('IsLogin');
        Route::any('/inactivestaff', '\App\Http\Controllers\agent\StaffController@staffall')->name('superAdmin.staff.view')->middleware('IsLogin');
    });
    //Routes for Assignment
    Route::prefix('assignment')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\assignment\AssignmentController@index')->name('assignment')->middleware('is_agent');
    });
    // Template
    Route::prefix('template')->group(function () {
        Route::any('/create-template/{id?}', '\App\Http\Controllers\campaign\CampaignController@createTemplate')->name('create template')->middleware('is_agent');
        Route::any('/', '\App\Http\Controllers\campaign\CampaignController@template')->name(' template')->middleware('is_agent');
    });
    // Campaign
    Route::prefix('campaign')->group(function () {
        Route::any('/', '\App\Http\Controllers\campaign\CampaignController@campaign')->name(' campaigns')->middleware('is_agent');
        Route::any('/create-campaign/{id?}', '\App\Http\Controllers\campaign\CampaignController@createCampaign')->name('create campaigns')->middleware('is_agent');
        Route::any('/Leadcampaign', '\App\Http\Controllers\campaign\CampaignController@Leadcampaign')->name('Leadcampaigns')->middleware('is_agent');
        Route::any('/create-lead-campaign/{id?}', '\App\Http\Controllers\campaign\CampaignController@createLeadcampaigns')->name('createLeadcampaigns')->middleware('is_agent');
        Route::any('/run-campaign/{id?}', '\App\Http\Controllers\campaign\CampaignController@run_campaign')->name('run campaigns')->middleware('is_agent');
    });
    // Leads
    Route::prefix('lead')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\leads\LeadsController@index')->name('Leads')->middleware('is_agent');
        Route::any('/getdata', '\App\Http\Controllers\agent\leads\LeadsController@LeadData')->name('leads.LeadData');
        Route::any('/PropertiesViewed', '\App\Http\Controllers\agent\leads\LeadsController@PropertiesViewed')->name('leads.PropertiesViewed');
        Route::any('/propertyDetails', '\App\Http\Controllers\agent\leads\LeadsController@propertyDetails')->name('leads.propertyDetails');

        Route::any('/PageViewed', '\App\Http\Controllers\agent\leads\LeadsController@PageViewed')->name('leads.PageViewed');
        Route::any('/LoginDetail', '\App\Http\Controllers\agent\leads\LeadsController@LoginDetail')->name('leads.LoginDetail');
        Route::any('/FavPropperty', '\App\Http\Controllers\agent\leads\LeadsController@FavPropperty')->name('leads.FavPropperty');
    });
    // Blog
    Route::prefix('blog')->group(function () {
        Route::any('/categories', '\App\Http\Controllers\agent\BlogController@category')->name('Category')->middleware('is_agent');
        Route::any('/create-category/{id?}', '\App\Http\Controllers\agent\BlogController@CreateCategory')->name('Create Category')->middleware('is_agent');
        Route::any('/', '\App\Http\Controllers\agent\BlogController@index')->name('Blogs')->middleware('is_agent');
        Route::any('/create-blog/{id?}', '\App\Http\Controllers\agent\BlogController@CreateBlog')->name('Create Blog')->middleware('is_agent');
    });
    // testimonial
    Route::prefix('testimonial')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\TestimonialController@index')->name('testimonial')->middleware('is_agent');
        Route::any('/add-testimonial/{id?}', '\App\Http\Controllers\agent\TestimonialController@addTestimonial')->name('addTestimonial')->middleware('is_agent');
        Route::any('/edit-testimonial/{id?}', '\App\Http\Controllers\agent\TestimonialController@addTestimonial')->name('addTestimonial')->middleware('is_agent');
    });

    Route::prefix('property')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\property\PropertyController@index')->name('property')->middleware('is_agent');
        Route::any('/bulk-import', '\App\Http\Controllers\agent\property\PropertyController@BulkImport')->name('property')->middleware('is_agent');
        Route::any('/import', '\App\Http\Controllers\agent\property\PropertyController@ImportProperty')->name('propertyImport')->middleware('is_agent');
        Route::any('/ZipReader', '\App\Http\Controllers\agent\property\PropertyController@ZipReader')->name('ZipReader')->middleware('is_agent');
        Route::get('/downloadfile', '\App\Http\Controllers\agent\property\PropertyController@downloadfile');
    });
    // menu builder
    Route::prefix('menu')->group(function () {
        Route::any('/menuBuilder', '\App\Http\Controllers\agent\MenuBuilderController@index')->name('MenuBuilder')->middleware('is_agent');
    });
    // page builder
    Route::prefix('pages')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\PagesController@index')->name('Pages')->middleware('is_agent');
        Route::any('/create-page/{id?}', '\App\Http\Controllers\agent\PagesController@create_page')->name('Create page')->middleware('is_agent');
        Route::any('/predefine-pages', '\App\Http\Controllers\agent\PagesController@predefine_pages')->name('Predefine page')->middleware('is_agent');
        Route::any('/create-predefine-page/{id?}', '\App\Http\Controllers\agent\PagesController@create_predefine_pages')->name('Create Predefine page')->middleware('is_agent');
        Route::any('/edit-code/{id?}', '\App\Http\Controllers\agent\PagesController@create_code')->name('Create code')->middleware('is_agent');
    });
    // city edit pages
    Route::prefix('city')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\CityController@get_all_city')->name('city')->middleware('is_agent');
        Route::any('/edit-city/{id?}', '\App\Http\Controllers\agent\CityController@edit_city')->name('edit city')->middleware('is_agent');
        Route::any('/area/{id?}', '\App\Http\Controllers\agent\CityController@area')->name('area')->middleware('is_agent');
        Route::any('/edit-area/{id?}/{city?}', '\App\Http\Controllers\agent\CityController@edit_area')->name('edit area')->middleware('is_agent');
    });
    // pre constuction buildings
    Route::prefix('building')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\ConstructionBuilding@building_list')->name('builindg_list')->middleware('is_agent');
        Route::any('/create-update-building', '\App\Http\Controllers\agent\ConstructionBuilding@create_update_building')->name('builindg_list')->middleware('is_agent');
        Route::any('/create-update-building/{id?}', '\App\Http\Controllers\agent\ConstructionBuilding@create_update_building')->name('builindg_list')->middleware('is_agent');
        Route::any('/builders', '\App\Http\Controllers\agent\ConstructionBuilding@builders')->name('building.builders')->middleware('is_agent');
        Route::any('/add-edit-builder', '\App\Http\Controllers\agent\ConstructionBuilding@add_edit_builder')->name('building')->middleware('is_agent');
        Route::any('/add-edit-builder/{id?}', '\App\Http\Controllers\agent\ConstructionBuilding@add_edit_builder')->name('building.builders')->middleware('is_agent');
        Route::any('/amenity-list', '\App\Http\Controllers\agent\ConstructionBuilding@amenity_list')->name('building.builders')->middleware('is_agent');
        Route::any('/add-edit-amenity', '\App\Http\Controllers\agent\ConstructionBuilding@add_edit_amenities')->name('building.builders')->middleware('is_agent');
        Route::any('/add-edit-amenity/{id?}', '\App\Http\Controllers\agent\ConstructionBuilding@add_edit_amenities')->name('building.builders')->middleware('is_agent');
    });
});

Route::prefix('super-admin')->group(function () {
    Route::any('/', '\App\Http\Controllers\superAdmin\MainSuperAdminController@index')->name('superAdmin.login');
    Route::any('/dashboard', '\App\Http\Controllers\superAdmin\MainSuperAdminController@dashboard')->name('superAdmin.dashboard')->middleware('is_superAdmin');
    Route::prefix('property')->group(function () {
        Route::any('/', [\App\Http\Controllers\superAdmin\AgentController::class, 'getProperty'])->name('property')->middleware('auth');
    });
    // these are for agents routes
    Route::prefix('agent')->group(function () {
        Route::any('/', '\App\Http\Controllers\superAdmin\AgentController@index')->name('superAdmin.agent.view')->middleware('is_superAdmin');
        Route::any('/add', '\App\Http\Controllers\superAdmin\AgentController@create')->name('superAdmin.agent.create')->middleware('is_superAdmin');
    });
    // Route::any('/track_users', [MainSuperAdminController::class, 'trackUsers']);//->middleware('is_superAdmin')
});
// Website Routes
Route::prefix('website')->group(function () {
    // this is for pages section

    Route::prefix('pages')->group(function () {
        Route::any('/', '\App\Http\Controllers\agent\website\PagesController@index')->name('pages')->middleware('is_agent');
        // });
    });
});




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
