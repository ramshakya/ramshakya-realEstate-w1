<?php
$fronendUrl=env('HOUSENFRONTURL');
$fronendUrl=$fronendUrl?$fronendUrl:"https://housen.ca";
?>

<table align="center"
       style="border:hidden; font-family:Arial, Helvetica, sans-serif; background:#fff;border: #ccc 1px solid;padding:10px;margin-top:10px;"
       width="98%">

    <tr>
        <td style="text-align:center;  border:hidden;   font-family:Arial, Helvetica, sans-serif;padding: 1px 15px;">
            <div class="safs" style="width:100%;margin:20px 0px">
                <div class="name_convention" align="left">
                    <span class="" style="margin-right:20px;">Hi, {{@$username}} </span>
                    <!--<span>{Not Email} </span>-->
                    <p style="margin-top:30px;">
                        Check out these new Listings that meet your saved search criteria !
                    </p>
                </div>
                <table align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="100%"
                       class="business"
                       style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                       data-bgcolor="Guest Inner">
                    <tbody>
                    @if(count($listings) > 0 && !empty($listings))
                        @foreach($listings as $listing)

                            <table align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                   width="100%"
                                   class="property_listing_cls"
                                   style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                   data-bgcolor="Guest Inner">
                                <tbody>
                                <tr style="width: 100%;
            display: inline-block;
            clear: both;
            padding: 30px 0px;
            border-bottom: #ccc 1px solid;">
                                    <td contenteditable="false" class="editable">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                               class="display-width-child" width="100%"
                                               style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;margin-bottom:30px;">
                                            <tbody>
                                            <tr>
                                                <td style="text-align:left;color:#333333; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-weight:600; font-size:20px; line-height:30px;">
                                                    <a href="{{$fronendUrl."/propertydetails/".$listing["SlugUrl"]}}" style="color: black">{{$listing["StandardAddress"]}}  City({{ $listing['City'] }})</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <!-- TABLE LEFT -->
                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="responsive"
                                               class="display-width-child" width="50%"
                                               style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                            <tbody>
                                            <tr>
                                                <td align="left">
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                           style="width:auto !important;">
                                                        <tbody>
                                                        <tr>
                                                            <td align="center" style="line-height:0; font-size:0;"
                                                                width="100%">
                                                                <img src="<?php $imageURL = ($listing["ImageUrl"] !=="")? env('APP_URL_IMAGE').$listing["ImageUrl"] : env('DEFAULTIMAGESRC'); echo $imageURL ; ?>"
                                                                     alt="282x196x1"
                                                                     width="282" height="196"
                                                                     style="color:#333333; margin:0; border:0; padding:0; width:100%; height:auto;">
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <!-- TABLE Center -->

                                        <table align="left" border="0" cellpadding="0" class="responsive3" cellspacing="0"
                                               class="display-width-child" width="50%"
                                               style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                            <tbody>

                                            <tr>
                                                <td align="center">
                                                    <table align="center" border="0" cellpadding="0" cellspacing="0"
                                                           width="95%"
                                                           style="width:95% !important; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                        <tbody>
                                                        <tr>
                                                            <!-- ID:TXT GUEST-HEADING -->
                                                            <td align="left" class="Heading"
                                                                style="color:#333333; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-weight:500; font-size:18px; line-height:25px;"
                                                                data-color="Guest Heading" data-size="Guest Heading"
                                                                data-min="15" data-max="42">
                                                                {{$listing["PropertyType"]}}<br/>
                                                                {{(int)$listing["BedroomsTotal"]}} Beds
                                                                / {{(int)$listing["BathroomsFull"]}} Baths
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <!-- ID:TXT GUEST-CONTENT -->
                                                            <td align="left" class="Heading"
                                                                style="color:#848484; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size:14px; font-weight:400; line-height:24px;"
                                                                data-color="Guest Content" data-size="Guest Content"
                                                                data-min="10" data-max="34">
                                                                MLS Number#: {{$listing["ListingId"]}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="1">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td height="1">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <!-- ID:TXT GUEST-CONTENT -->
                                                            <td align="left" class="Heading"
                                                                style="color:#848484; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size:14px; font-weight:400; line-height:24px;"
                                                                data-color="Guest Content" data-size="Guest Content"
                                                                data-min="10" data-max="34">
                                                                DOM : {{$listing["Dom"]}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="14">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" class="button-width editable"
                                                                contenteditable="true">
                                                                <!-- ID:BTN GUEST-BTN -->
                                                                <table align="left" bgcolor="#f1c40f" border="0" class="responsive2"
                                                                       cellspacing="0"
                                                                       cellpadding="0" class="display-button"
                                                                       style="border-radius:3px;"
                                                                       data-bgcolor="Guest Btn">
                                                                    <tbody>
                                                                    <tr>
                                                                        @if($listing["Status"] == "A")
                                                                            <td align="center" valign="middle"
                                                                                class="Heading"
                                                                                style="background-color:#0081a7;color:#ffffff; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size:13px; font-weight:bold; letter-spacing:1px; padding:7px 15px;">
                                                                                $ &nbsp;
                                                                                {{@number_format($listing["ListPrice"])}}
                                                                            </td>
                                                                        @else
                                                                            <td align="center" valign="middle"
                                                                                class="Heading"
                                                                                style="background-color:#0081a7;color:#ffffff; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size:13px; font-weight:bold; letter-spacing:1px; padding:7px 15px;">
                                                                                $ &nbsp;
                                                                                {{@number_format($listing["Sp_dol"])}}
                                                                            </td>
                                                                        @endif

                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="10">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <!-- TABLE RIGHT -->


                                    </td>
                                </tr>


                                </tbody>
                            </table>
                        @endforeach
                    @endif
                    <table align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="100%"
                           class="business"
                           style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; mso-table-lspace:0pt; mso-table-rspace:0pt;margin-top:30px;"
                           data-bgcolor="Guest Inner">
                        <tbody>
                        <tr>
                            <td align="center">
                                <a href="{{$fronendUrl."/map"}}"
                                   style="background: #0081a7;color: #fff;padding: 15px 30px;display:inline-block;text-decoration: none;font-weight: 700;">
                                    See More New Listings
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="color:black;padding:15px 30px;margin:0px;font-size:10px;font-weight:500;" class="responsive4">
                            If you do not wish to receive these types of emails/listing alerts, click &nbsp;<a href="{{env('WEDUURLUNSUBSCRIBE')}}{{@$emailHash}}" style="color:#0081a7;display:inline-block;text-decoration:none;font-weight:700;font-size:12px;margin-top:10px;" target="_blank"> <u> Unsubscribe </u> </a>  {{@$websiteTitle}}
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    </tbody>
                </table>


            </div>


        </td>

    </tr>
</table>


