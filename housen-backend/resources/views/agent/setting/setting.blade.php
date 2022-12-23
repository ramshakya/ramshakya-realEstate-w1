@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    <!-- third party css -->

    <style type="text/css">
        .PropertyModelBtn{
            cursor: pointer;
        }
        .spinner-border{
            height: 16px;
            width: 16px;
        }
        .img-size{
            /* 	padding: 0;
                margin: 0; */
            height: 400px;
            width: 100%;
            background-size: cover;
            overflow: hidden;
        }
        .border-none{
            border-style: none;
            background-color: #f7f7f7 !important;
        }
        .btn-xs{
            background-color: #fff;
        }
        .card-title {
            margin-bottom: 0px;
        }
        .card-body{
            /* padding-top:0px !important;*/
        }
        .project-tab {
            padding: 10%;
            margin-top: -8%;
        }
        .project-tab #tabs{
            background: #007b5e;
            color: #eee;
        }
        .project-tab #tabs h6.section-title{
            color: #eee;
        }
        .project-tab #tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
            color: #0062cc;
            background-color: transparent;
            border-color: transparent transparent #f3f3f3;
            border-bottom: 1px solid !important;
            /*font-size: 16px;
            font-weight: bold;*/
        }
        .project-tab .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            color: #0062cc;
            /*font-size: 16px;
            font-weight: 600;*/
        }
        .project-tab .nav-link:hover {
            border: none;
        }
        .project-tab thead{
            background: #f3f3f3;
            color: #333;
        }
        /*.project-tab a{
            text-decoration: none;
            color: #333;
            font-weight: 600;
        }*/
        .nav-link {
            display: block;
            padding: 0px;
        }
        .nav-item{
            font-weight: 400;
        }
        p{
            font-weight: 100;
        }
        .wd-60{
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto,sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow:hidden;
            text-align: center;
        }
        .wd-30{
            max-width: 30px !important;
            min-width: 20px !important;
            font-family: Roboto,sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow:hidden;
            text-align: center;
        }
        .th-wd-30{
            max-width: 30px !important;
            min-width: 10px !important;
            font-family: Roboto,sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }
        .th-wd-60{
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto,sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }
        button.btn.search_button_one {
            position: absolute;
            top: 2px;
            right: 17px;
            padding: 5px 16px;
            background-color: #5b69bc!important;
            border: none;
            min-height: 10px;
            min-width: 2px;
            /* border-radius: 20px; */
        }
        .cursor-pointer{
            cursor: pointer;
        }
        #spinLoader {
            position: fixed;
            top: 50%;
            left: 48%;
            background-color: #fff;
            z-index: 9999;
        }
    </style>
    <style>
        center {
            font-size: 30px;
            color: green;
        }

        .popup {
            display: none;
            width: 500px;
            border: solid red 3px
        }
        /* Base styling*/
        /*body {*/
        /*    background-color: lightgrey;*/
        /*    max-width: 768px;*/
        /*    margin: 0 auto;*/
        /*    padding: 1em 0;*/
        /*    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;*/
        /*}*/

        /* Popover styling */

        /*a {*/
        /*    text-decoration: none;*/
        /*}*/

        .popover__title {
            font-size: 24px;
            line-height: 36px;
            text-decoration: none;
            color: rgb(228, 68, 68);
            text-align: center;
            padding: 15px 0;
        }

        .popover__wrapper {
            position: relative;
            margin-top: 1.5rem;
            display: inline-block;
        }
        .popover__content {
            opacity: 0;
            display: none;
            position: absolute;
            left: -150px;
            transform: translate(0, 10px);
            background-color: #bfbfbf;
            padding: 1.5rem;
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
            width: auto;
        }
        .popover__content:before {
            /*position: absolute;*/
            z-index: -1;
            content: "";
            right: calc(50% - 10px);
            top: -8px;
            border-style: solid;
            border-width: 0 10px 10px 10px;
            border-color: transparent transparent #bfbfbf transparent;
            transition-duration: 0.3s;
            transition-property: transform;
        }
        .popover__wrapper:hover .popover__content {
            z-index: 10;
            opacity: 1;
            display: block;
            transform: translate(0, -20px);
            transition: all 0.5s cubic-bezier(0.75, -0.02, 0.2, 0.97);
        }
        .popover__message {
            text-align: center;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header mb-0 ">
                                <h4 class="card-title card-title-heading mb-0  pb-0"> Website Setting</h4>
                            </div>
                            <div class="card-body">
                           
                            {{--     <div class="row p-1 mb-2">--}}
{{--                                    <div class="col-md-12 mb-2">--}}
{{--                                        <p>You need to have a csv formate for properties to be added/updated. Must provided ListingId(MLS#) to add/update a property. You can download sample csv from <a href="{{url('agent/property/downloadfile')}}" target="_blank">Download </a> </p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <form class="pro-add-form" id="ImportForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                    <input type="hidden" name="userId" value="{{@$id}}">
                                    <input type="hidden" name="AdminId" value="{{@$AdminId}}" />

                                 <div class="inline-group">
                                     <div class="shadow bg-body rounded">
                                        <div class="card-header my-1 mb-0 ">
                                          <h4 class="card-title card-title-heading m-0  pb-0"> Main Settings</h4>
                                        </div>
                                     <div class="p-3 mb-3">
                                        <div class="row ">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Website Name</label>
                                                <input type="text" class="form-control" id="WebsiteName" name="WebsiteName" value="{{@$user->WebsiteName}}" required="" placeholder="Website Name"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Website Title</label>
                                                <input type="text" class="form-control" id="WebsiteTitle" name="WebsiteTitle" value="{{@$user->WebsiteTitle}}" required="" placeholder="Website Title" />
                                            </div>
                                        </div>
                                        <div class="row ">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Phone No</label>
                                                <input type="text" class="form-control" id="PhoneNo" name="PhoneNo" value="{{@$user->PhoneNo}}" required="" placeholder="Phone No"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Website Email</label>
                                                <input type="text" class="form-control" id="WebsiteEmail" name="WebsiteEmail" value="{{@$user->WebsiteEmail}}" required="" placeholder="Website Email" />
                                            </div>
                                        </div>
                                        <div class="row ">
                                            <div class="col-md-12 form-group">
                                                <label class="control-label">Website Address</label>
                                                <textarea id="WebsiteAddress" name="WebsiteAddress" class="form-control">{{@$user->WebsiteAddress}}</textarea>
                                            </div>
                                        </div>
                                        <div class="row ">
                                            <div class="col-md-4 form-group">
                                                <label class="control-label">Upload Logo</label>
                                                <input type="file" class="form-control" id="UploadLogo" name="UploadLogo" placeholder="Google Analytics Code"/>
                                                <img src="{{@$user->UploadLogo}}" height="50">
                                            </div> 
                                            <div class="col-md-4 form-group">
                                                <label class="control-label">Upload Dark Logo</label>
                                                <input type="file" class="form-control" id="DarkLogo" name="DarkLogo" placeholder=""/>
                                                <img src="{{@$user->DarkLogo}}" height="50">
                                            </div> 

                                            <div class="col-md-4 form-group">
                                                <label class="control-label">Logo Alt Tag</label>
                                                <input type="text" class="form-control" id="LogoAltTag" name="LogoAltTag" value="{{@$user->LogoAltTag}}" required="" placeholder="Logo Alt Tag" />
                                            </div>
                                        </div>
                                        <div class="row ">
                                        <div class="col-md-6 form-group">
                                                <label class="control-label">Upload Favicon Icon</label>
                                                <input type="file" class="form-control" id="Favicon" name="Favicon" placeholder="Favicon"/>
                                                <img src="{{@$user->Favicon}}" height="50">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Favicon Alt Tag</label>
                                                <input type="text" class="form-control" id="FavIconAltTag" name="FavIconAltTag" value="{{@$user->LogoAltTag}}" required="" placeholder="FavIcon Alt Tag" />
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0"> SMTP Details</h4>
                                    </div>
                                     <div class="p-3 mb-3">
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label class="control-label">SMTP Email</label>
                                                <input type="text" class="form-control" id="FromEmail" name="FromEmail" value="{{@$user->FromEmail}}" required="" placeholder="From Email"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">SMTP Password</label>
                                                <input type="password" class="form-control" id="EmailPassword" value="{{@$user->EmailPassword}}" name="EmailPassword" required="" placeholder="Email Password" />
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label class="control-label">SMTP HOST</label>
                                                <input type="text" class="form-control" id="Host" name="Host" value="" placeholder="SMTP HOST"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">SMTP Port</label>
                                                <input type="text" class="form-control" id="HostPort" value="" name="HostPort" placeholder="SMTP Port" />
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            
                                <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0">Templates</h4>
                                    </div>
                                     <div class="p-3 mb-3">
                                        <div class="row ">
                                            <div class="col-md-3 form-group">
                                                <label class="control-label">Website Color</label>
                                                <input type="color" class="form-control" id="WebsiteColor" name="WebsiteColor" placeholder="WebsiteColor" value="{{@$user->WebsiteColor}}"/>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="control-label">Website Map Color</label>
                                                <input type="color" class="form-control" id="WebsiteMapColor" name="WebsiteMapColor" placeholder="WebsiteMapColor" value="{{@$user->WebsiteMapColor}}"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Front Site Theme</label>
                                                <select class="form-control" id="FrontSiteTheme" name="FrontSiteTheme">
                                                    <option value="blue">Blue</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0"> Major Pages </h4>
                                    </div>
                                     <div class="p-3 mb-3">
                                        <div class="row ">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Google Map Api Key</label>
                                                <input type="text" class="form-control" id="GoogleMapApiKey" name="GoogleMapApiKey" value="{{@$user->GoogleMapApiKey}}"  placeholder="Google Map Api Key" />
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Mapbox Api Key</label>
                                                <input type="text" class="form-control" id="MapApiKey" name="MapApiKey" value="{{@$user->MapApiKey}}"  placeholder="Google Analytics Code"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">WalkScore Api Key</label>
                                                <input type="text" class="form-control" id="WalkScoreApiKey" name="WalkScoreApiKey" value="{{@$user->WalkScoreApiKey}}"  placeholder="WalkScore Api Key"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">HoodQ Api Key</label>
                                                <input type="text" class="form-control" id="HoodQApiKey" name="HoodQApiKey" value="{{@$user->HoodQApiKey}}"  placeholder="HoodQ Api Key"/>
                                            </div>
                                           
                                        </div>
                                        <div class="row ">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Yelp Key</label>
                                                <input type="text" class="form-control" id="YelpKey" name="YelpKey" value="{{@$user->YelpKey}}"  placeholder="Yelp Key"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Yelp Client Id </label>
                                                <input type="text" class="form-control" id="YelpClientId" name="YelpClientId" value="{{@$user->YelpClientId}}"  placeholder="Yelp Client Id"/>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0"> Social Details</h4>
                                    </div>
                                     <div class="p-3 mb-3">
                                      <div class="row ">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Facebook Url</label>
                                                <input type="text" class="form-control" id="FacebookUrl" name="FacebookUrl" value="{{@$user->FacebookUrl}}"  placeholder="Facebook Url"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Instagram Url</label>
                                                    <input type="text" class="form-control" id="InstagramUrl" name="InstagramUrl" value="{{@$user->InstagramUrl}}"  placeholder="Instagram Url"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Linkedin Url</label>
                                                <input type="text" class="form-control" id="LinkedinUrl" name="LinkedinUrl" value="{{@$user->LinkedinUrl}}"  placeholder="Linkedin Url"/>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Twitter Url</label>
                                                <input type="text" class="form-control" id="TwitterUrl" name="TwitterUrl" value="{{@$user->TwitterUrl}}"  placeholder="Twitter Url"/>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="control-label">Youtube Url</label>
                                                <input type="text" class="form-control" id="YoutubeUrl" name="YoutubeUrl" value="{{@$user->YoutubeUrl}}"  placeholder="YouTube Url" />
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Facebook App Id</label>
                                                <input type="text" class="form-control" id="FbAppId" name="FbAppId" value="{{@$user->FbAppId}}"  placeholder="FbAppId" />
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Google Client Id</label>
                                                <input type="text" class="form-control" id="GoogleClientId" name="GoogleClientId" value="{{@$user->GoogleClientId}}"  placeholder="Google Client Id" />
                                            </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0"> Twilio </h4>
                                    </div>
                                    <div class="p-3 mb-3">
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label class="control-label">Contact Number</label>
                                                <input type="text" class="form-control" id="TwilioNumber" name="TwilioNumber" value="{{@$user->TwilioNumber}}" placeholder="Number"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Account SID</label>
                                                <input type="text" class="form-control" id="TwilioSID" name="TwilioSID" value="{{@$user->TwilioSID}}" placeholder="Account SID"/>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label"> Account Token</label>
                                                <input type="text" class="form-control" id="TwilioToken" value="{{@$user->TwilioToken}}" name="TwilioToken" placeholder="Account Token" />
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0"> Zapier Credentials (Optional)</h4>
                                    </div>
                                    <div class="p-3 mb-3">
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label class="control-label">SID</label>
                                                <input type="text" class="form-control" id="ZapierSID" name="ZapierSID" value="{{@$user->ZapierSID}}" placeholder="SID"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Token</label>
                                                <input type="text" class="form-control" id="ZapierToken" name="ZapierToken" value="{{@$user->ZapierToken}}" placeholder="Token"/>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Webhook URL</label>
                                                <input type="text" class="form-control" id="WebhookUrl" value="{{@$user->WebhookUrl}}" name="WebhookUrl" placeholder="Webhook Url" />
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0">  Tracker And Analytics </h4>
                                    </div>
                                     <div class="p-3 mb-3">
                                        <div class="row ">
                                                <div class="col-md-6 form-group">
                                                    <label class="control-label">Google Analytics Code</label>
                                                    <input type="text" class="form-control" id="GoogleAnalyticsCode" value="{{@$user->GoogleAnalyticsCode}}" name="GoogleAnalyticsCode" required="" placeholder="Google Analytics Code"/>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label class="control-label">Facebook Pixel Code</label>
                                                    <input type="text" class="form-control" id="FacebookPixelCode" value="{{@$user->FacebookPixelCode}}" name="FacebookPixelCode" required="" placeholder="Facebook Pixel Code" />
                                                </div>
                                            </div>
                                            <div class="row ">
                                                <div class="col-md-12 form-group">
                                                    <label for="Subclass">Add Script Tag<span class="required"></span></label>
                                                    <textarea class="textarea_editor form-control ck1" required name="ScriptTag" id="ScriptTag" rows="8" placeholder="Enter text ..." data-validation-engine="validate[required]">{{@$user->ScriptTag}}</textarea>
                                                </div>
                                            </div>
                                            
                                             <div class="row ">
                                                <div class="col-md-12 form-group">
                                                    <label for="Subclass">Add Body Script <span class="required"></span></label>
                                                    <textarea class="textarea_editor form-control ck1" required name="bodyscriptTag" id="bodyscriptTag" rows="8" placeholder="Enter text ..." data-validation-engine="validate[required]">{{@$user->bodyscriptTag}}</textarea>
                                                </div>
                                            </div>
                                             
                                        </div>
                                        
                                        </div>
                                        <div class="shadow bg-body rounded">
                                    <div class="card-header my-1 mb-0 ">
                                        <h4 class="card-title card-title-heading m-0  pb-0">  Offices </h4>
                                    </div>
                                     <div class="p-3 mb-3">
                                        <div class="row ">
                                                <div class="col-md-12 form-group">
                                                    <label class="control-label">Office Name (use double quotes " for add another office name)</label>
                                                    <!-- <input type="text" class="form-control" id="OfficeName" value="{{@$user->OfficeAddress}}" name="OfficeAddress" required="" placeholder="Office Name"/> -->
                                                    <textarea name="OfficeName" id="OfficeName" placeholder='Office name 1"office name2"etc' class="form-control">{{@$user->OfficeName}}</textarea>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>


                                            <div class="row">
                                                <div class="col-md-9"></div>
                                                <div class="col-md-3 form-group">
                                                    <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 pl-1 pr-1" name="importbtn" id="SubmitBtn" style="width:90%;">  <div class="spinner-border d-none" role="status" id="rule-btn2">--}}
                                                            <span class="sr-only">Loading...</span>
                                                        </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i>  Save</button>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                   </div>
                                 </div>
                                </form><br><br/>
{{--                                <form method="POST" enctype="multipart/form-data" id="ImportZip">--}}
{{--                                    <div class="inline-group">--}}
{{--                                        <div class="row ">--}}
{{--                                            <div class="col-md-9">--}}
{{--                                                <input type="file" class="form-control" id="import" name="file" required="" placeholder="Import" accept=".zip,.rar,.7zip" >--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-3">--}}
{{--                                                <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 pl-5 pr-5" name="importbtn"  id="Zipimport" style="width:100%;">  &nbsp;<div class="spinner-border d-none" role="status" id="zipimportBtn">--}}
{{--                                                        <span class="sr-only">Loading...</span>--}}
{{--                                                    </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Import Image </button>--}}
{{--                                            </div><br/>--}}
{{--                                        </div>--}}
{{--                                        <div class="row mt-1">--}}
{{--                                            <div class="col-md-12">--}}
{{--                                                <p>Please provide image name as MLS-1.jpg or MLS-1.jpeg, should be same formatted.</p>--}}
{{--                                            </div>--}}

{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </form>--}}
{{--                                <div class="row p-1 mb-2">--}}
{{--                                    <div class="col-md-12 mb-2">--}}
{{--                                        <p></p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->
    </div>
@endsection
@section('pageLevelJS')
    <!-- third party js ends -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>

    <!-- Datatables init -->
    <script src="{{ asset('assets') }}/agent/js/pages/datatables.init.js"></script>
    <!-- Dashboard init js-->
    <!--  <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script> -->
    <script>
        setTimeout(function(){
            var x = document.getElementById("content1").value;
            CKEDITOR.replace('content1');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
    </script>
    <script type="text/javascript">
        $(document).on('submit','#ImportForm',function(e){
            e.preventDefault();

            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/setting/UpdSetting")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        location.reload();
                    },3000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    console.log(errors);
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
                    if(status.status == 422){
                        $.each(errors.errors, function(i,v){
                            console.log(v);
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        function phoneFormat(input){
            // Strip all characters from the input except digits
            input = input.replace(/\D/g,'');
            
            // Trim the remaining input to ten characters, to preserve phone number format
            input = input.substring(0,10);

            // Based upon the length of the string, we add formatting as necessary
            var size = input.length;
            if(size == 0){
                input = input;
            }else if(size < 4){
                input = '('+input;
            }else if(size < 7){
                input = '('+input.substring(0,3)+') '+input.substring(3,6);
            }else{
                input = '('+input.substring(0,3)+') '+input.substring(3,6)+' - '+input.substring(6,10);
            }
            return input; 
        }
        document.getElementById('PhoneNo').addEventListener('keyup',function(evt){
            var phoneNumber = document.getElementById('PhoneNo');
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            phoneNumber.value = phoneFormat(phoneNumber.value);
        });
    </script>
@endsection
