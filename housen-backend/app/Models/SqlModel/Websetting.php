<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websetting extends Model
{
    use HasFactory;
    protected $table = "Websetting";
    protected $fillable = [
        'id',
        'AdminId',
        'WebsiteName',
        'WebsiteTitle',
        'PhoneNo',
        'WebsiteEmail',
        'ZapierSID',
        'ZapierToken',
        'WebhookUrl',
        'FromEmail',
        'EmailPassword',
        'GoogleAnalyticsCode',
        'FacebookPixelCode',
        'MapApiKey',
        'FrontSiteTheme',
        'WebsiteAddress',
        'UploadLogo',
        'LogoAltTag',
        'Favicon',
        'Facebook',
        'Twitter',
        'Linkedin',
        'Instagram',
        'Youtube',
        'WebsiteColor',
        'WebsiteMapColor',
        'GoogleMapApiKey',
        'HoodQApiKey',
        'WalkScoreApiKey',
        'FavIconAltTag',
        'FacebookUrl',
        'TwitterUrl',
        'LinkedinUrl',
        'InstagramUrl',
        'YoutubeUrl',
        'ScriptTag',
        'YelpKey',
        'YelpClientId',
        'TopBanner',
        'TwilioToken',
        'TwilioNumber',
        'TwilioSID',
        'GoogleClientId', 
        'FbAppId',
        'DarkLogo',
        'bodyscriptTag'

    ];
}
