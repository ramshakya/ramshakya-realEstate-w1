<?php
$data = \App\Models\SqlModel\Websetting::where("AdminId", '=', 3)->first();
$emailAddress = $data->WebsiteEmail;
$phone = $data->PhoneNo;
$websiteTitle = $data->WebsiteTitle;
$websiteLogo = $data->UploadLogo;
$twitterUrl = $data->TwitterUrl;
$facebookUrl = $data->FacebookUrl;
$linkedInUrl = $data->LinkedinUrl;
$instagramUrl = $data->InstagramUrl;
$youtubeUrl = $data->YoutubeUrl;
$address = $data->WebsiteAddress;
?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <!--[if gte mso 9]>
    <xml>
    <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
    <title></title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


    <style type="text/css">
        table, td {
            color: #000000;
        }

        a {
            color: #f29ca0;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            #u_content_image_1 .v-src-width {
                width: auto !important;
            }

            #u_content_image_1 .v-src-max-width {
                max-width: 59% !important;
            }

            #u_content_image_2 .v-src-width {
                width: 100% !important;
            }

            #u_content_image_2 .v-src-max-width {
                max-width: 100% !important;
            }

            #u_content_heading_1 .v-container-padding-padding {
                padding: 20px !important;
            }

            #u_content_heading_1 .v-font-size {
                font-size: 23px !important;
            }

          /*  #u_content_text_2 .v-container-padding-padding {
                padding: 40px 30px 50px 15px !important;
            }*/

            #u_content_button_1 .v-size-width {
                width: 81% !important;
            }

            #u_content_image_3 .v-container-padding-padding {
                padding: 30px 0px 25px !important;
            }

            #u_content_image_3 .v-src-width {
                width: auto !important;
            }

            #u_content_image_3 .v-src-max-width {
                max-width: 38% !important;
            }

            #u_content_heading_2 .v-container-padding-padding {
                padding: 10px 10px 0px 0px !important;
            }

            #u_content_heading_2 .v-font-size {
                font-size: 25px !important;
            }

            #u_content_heading_2 .v-text-align {
                text-align: center !important;
            }

            #u_content_text_3 .v-text-align {
                text-align: center !important;
            }

            #u_content_text_5 .v-text-align {
                text-align: center !important;
            }
        }

        @media only screen and (min-width: 620px) {
            .u-row {
                width: 600px !important;
            }

            .u-row .u-col {
                vertical-align: top;
            }

            .u-row .u-col-37p16 {
                width: 222.95999999999995px !important;
            }

            .u-row .u-col-62p84 {
                width: 377.04px !important;
            }

            .u-row .u-col-100 {
                width: 600px !important;
            }

        }

        @media (max-width: 620px) {
            .u-row-container {
                max-width: 100% !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .u-row .u-col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }

            .u-row {
                width: calc(100% - 40px) !important;
            }

            .u-col {
                width: 100% !important;
            }

            .u-col > div {
                margin: 0 auto;
            }
        }

        body {
            margin: 0;
            padding: 0;
        }

        table,
        tr,
        td {
            vertical-align: top;
            border-collapse: collapse;
        }

        p {
            margin: 0;
        }

        .ie-container table,
        .mso-container table {
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors='true'] {
            color: inherit !important;
            text-decoration: none !important;
        }

        @media (max-width: 550px) {
            .responsive {
                width: 100% !important;
            }
            .responsive3 {
                width: 100% !important;
            }
            .responsive2 {
                float: right;
            }
            .responsive4 {
                width: 90%;
                font-size: 12px;
            }
            .mobile_width_white{
                background-color: #ffffff !important;
            }
            .mobile_width_pink{
                background-color: #f29ca0 !important;
            }
            .mobile_width_black{
                background-color: #1a2e35 !important;
            }
            .full_width{
                width: 100% !important;
            }
           /* .padding-low{
                padding:15px 20px 50px 15px !important;
            }*/
        }
    </style>


    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700&display=swap" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,700&display=swap" rel="stylesheet" type="text/css">
    <!--<![endif]-->

</head>

<body class="clean-body u_body"
      style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #e7e7e7;color: #000000">
<div class="ie-container">
    <div class="mso-container">
        <table
            style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #e7e7e7;width:100%"
            cellpadding="0" cellspacing="0">
            <tbody>
            <tr style="vertical-align: top">
                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                    <!--[if (mso)|(IE)]>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" style="background-color: #e7e7e7;"><![endif]-->
                    <div class="u-row-container mobile_width_white" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #1a2e35;"><![endif]-->

                                <!--[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"
                                    valign="top"><![endif]-->
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div style="width: 100% !important;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                            <!--<![endif]-->

                                            <table id="u_content_image_1" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:30px 10px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td class="v-text-align"
                                                                    style="padding-right: 0px;padding-left: 0px;"
                                                                    align="center">
                                                                    <a href="{{env('WEDUURL')}}" target="_blank">
                                                                        <img align="center" border="0"
                                                                             src="{{$websiteLogo}}"
                                                                             alt="Logo" title="Logo"
                                                                             style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 29%;max-width: 168.2px;"
                                                                             width="168.2"
                                                                             class="v-src-width v-src-max-width"/>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>


                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #ffffff;"><![endif]-->

                                <!--[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]-->

                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>


                    <div class="u-row-container mobile_width_pink" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f29ca0;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #f29ca0;"><![endif]-->

                                <!--[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]-->
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table id="u_content_heading_1" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:20px 25px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>


                    <div class="u-row-container mobile_width_white" style="padding: 0px;background-color: transparent">
                        <div class="u-row full_width"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f5f5f5;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #f5f5f5;"><![endif]-->

                                <!--[if (mso)|(IE)]>
                                <td align="center" width="598"
                                    style="width: 598px;padding: 0px;border-top: 1px solid #CCC;border-left: 1px solid #CCC;border-right: 1px solid #CCC;border-bottom: 1px solid #CCC;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]-->
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="padding: 0px;border-top: 1px solid #CCC;border-left: 1px solid #CCC;border-right: 1px solid #CCC;border-bottom: 1px solid #CCC;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table id="u_content_text_2" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding padding-low"
                                                        style="overflow-wrap:break-word;word-break:break-word;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <div class="v-text-align"
                                                        style="color: #5c5c5c; line-height: 170%; text-align: left; word-wrap: break-word;padding: 2px 6px;">

                                                            <p style="font-size: 14px; line-height: 170%;">   {!! @$content !!} </p>

                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>


                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #01499d;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #01499d;"><![endif]-->

                                <!--[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]-->
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->


                                            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>


<!--                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                &lt;!&ndash;[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #ffffff;"><![endif]&ndash;&gt;

                                &lt;!&ndash;[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]&ndash;&gt;
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            &lt;!&ndash;<![endif]&ndash;&gt;

                                            <table style="font-family:'Rubik',sans-serif;" role="presentation"
                                                   cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:0px 0px 20px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <table height="0px" align="center" border="0" cellpadding="0"
                                                               cellspacing="0" width="100%"
                                                               style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 4px solid #1a2e35;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                            <tbody>
                                                            <tr style="vertical-align: top">
                                                                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                                    <span>&#160;</span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;</div>&lt;!&ndash;<![endif]&ndash;&gt;
                                    </div>
                                </div>
                                &lt;!&ndash;[if (mso)|(IE)]></td><![endif]&ndash;&gt;
                                &lt;!&ndash;[if (mso)|(IE)]></tr></table></td></tr></table><![endif]&ndash;&gt;
                            </div>
                        </div>
                    </div>


                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                &lt;!&ndash;[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #ffffff;"><![endif]&ndash;&gt;

                                &lt;!&ndash;[if (mso)|(IE)]>
                                <td align="center" width="223"
                                    style="width: 223px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]&ndash;&gt;
                                <div class="u-col u-col-37p16"
                                     style="max-width: 320px;min-width: 223px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            &lt;!&ndash;<![endif]&ndash;&gt;

                                            <table id="u_content_image_3" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:45px 0px 25px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td class="v-text-align"
                                                                    style="padding-right: 0px;padding-left: 0px;"
                                                                    align="center">

                                                                    <img align="center" border="0"
                                                                         src="{{asset('assets/email/image-7.png')}}"
                                                                         alt="Icon" title="Icon"
                                                                         style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 48%;max-width: 107.04px;"
                                                                         width="107.04"
                                                                         class="v-src-width v-src-max-width"/>

                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;</div>&lt;!&ndash;<![endif]&ndash;&gt;
                                    </div>
                                </div>
                                &lt;!&ndash;[if (mso)|(IE)]></td><![endif]&ndash;&gt;
                                &lt;!&ndash;[if (mso)|(IE)]>
                                <td align="center" width="377"
                                    style="width: 377px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]&ndash;&gt;
                                <div class="u-col u-col-62p84"
                                     style="max-width: 320px;min-width: 377px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            &lt;!&ndash;<![endif]&ndash;&gt;

                                            <table id="u_content_heading_2" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:40px 10px 0px 0px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <h1 class="v-text-align v-font-size"
                                                            style="margin: 0px; color: #1b2e35; line-height: 140%; text-align: left; word-wrap: break-word; font-weight: normal; font-family: 'Raleway',sans-serif; font-size: 24px;">
                                                            <strong>Feel Free To Contact Us.</strong>
                                                        </h1>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <table id="u_content_text_3" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 9px 1px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <div class="v-text-align"
                                                             style="line-height: 140%; text-align: left; word-wrap: break-word;">
                                                            <p style="font-size: 14px; line-height: 140%;"><span
                                                                    style="font-size: 20px; line-height: 28px;"><a
                                                                        rel="noopener" href="#" target="_blank">{{$emailAddress}}</a></span>
                                                            </p>
                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <table id="u_content_text_5" style="font-family:'Rubik',sans-serif;"
                                                   role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 50px 0px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <div class="v-text-align"
                                                             style="color: #1b2e35; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                            <p style="font-size: 14px; line-height: 140%;"><span
                                                                    style="font-size: 20px; line-height: 28px;">{{$phone}}</span>
                                                            </p>
                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;</div>&lt;!&ndash;<![endif]&ndash;&gt;
                                    </div>
                                </div>
                                &lt;!&ndash;[if (mso)|(IE)]></td><![endif]&ndash;&gt;
                                &lt;!&ndash;[if (mso)|(IE)]></tr></table></td></tr></table><![endif]&ndash;&gt;
                            </div>
                        </div>
                    </div>-->


<!--                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                &lt;!&ndash;[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #ffffff;"><![endif]&ndash;&gt;

                                &lt;!&ndash;[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]&ndash;&gt;
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            &lt;!&ndash;<![endif]&ndash;&gt;

                                            <table style="font-family:'Rubik',sans-serif;" role="presentation"
                                                   cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td class="v-text-align"
                                                                    style="padding-right: 0px;padding-left: 0px;"
                                                                    align="center">

                                                                    <img align="center" border="0"
                                                                         src="{{asset('assets/email/image-8.png')}}"
                                                                         alt="curve border" title="curve border"
                                                                         style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100%;max-width: 600px;"
                                                                         width="600"
                                                                         class="v-src-width v-src-max-width"/>

                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            &lt;!&ndash;[if (!mso)&(!IE)]>&lt;!&ndash;dash;&gt;</div>&lt;!&ndash;<![endif]&ndash;&gt;
                                    </div>
                                </div>
                                &lt;!&ndash;[if (mso)|(IE)]></td><![endif]&ndash;&gt;
                                &lt;!&ndash;[if (mso)|(IE)]></tr></table></td></tr></table><![endif]&ndash;&gt;
                            </div>
                        </div>
                    </div>-->

                    <div class="u-row"
                         style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                        <div
                            style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                            <!--[if (mso)|(IE)]>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding: 0px;background-color: transparent;" align="center">
                                    <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                        <tr style="background-color: #ffffff;"><![endif]-->
                                        <!-- <p style="padding-top: 10px"></p> -->
                            <!--[if (mso)|(IE)]>
                            <td align="center" width="600"
                                style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                valign="top"><![endif]-->

                            <!--[if (mso)|(IE)]></td><![endif]-->
                            <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                        </div>
                    </div>

                    <div class="u-row-container mobile_width_black" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                             style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #1a2e35;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0px;background-color: transparent;" align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;">
                                            <tr style="background-color: #1a2e35;"><![endif]-->

                                <!--[if (mso)|(IE)]>
                                <td align="center" width="600"
                                    style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"
                                    valign="top"><![endif]-->
                                <div class="u-col u-col-100"
                                     style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Rubik',sans-serif;" role="presentation"
                                                   cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:24px 10px 20px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <div align="center">
                                                            <div style="display: table; max-width:179px;">
                                                                <!--[if (mso)|(IE)]>
                                                                <table width="179" cellpadding="0" cellspacing="0"
                                                                       border="0">
                                                                <tr>
                                                                    <td style="border-collapse:collapse;"
                                                                        align="center">
                                                                        <table width="100%" cellpadding="0"
                                                                               cellspacing="0" border="0"
                                                                               style="border-collapse:collapse; mso-table-lspace: 0pt;mso-table-rspace: 0pt; width:179px;">
                                                                            <tr><![endif]-->


                                                                <!--[if (mso)|(IE)]>
                                                                <td width="32" style="width:32px; padding-right: 13px;"
                                                                    valign="top"><![endif]-->
                                                                <table align="left" border="0" cellspacing="0"
                                                                       cellpadding="0" width="32" height="32"
                                                                       style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 13px">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td align="left" valign="middle"
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                            <a href="{{$twitterUrl}}"
                                                                               title="Twitter" target="_blank">
                                                                                <img
                                                                                    src="{{asset('assets/email/image-1.png')}}"
                                                                                    alt="Twitter" title="Twitter"
                                                                                    width="32"
                                                                                    style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <!--[if (mso)|(IE)]></td><![endif]-->

                                                                <!--[if (mso)|(IE)]>
                                                                <td width="32" style="width:32px; padding-right: 13px;"
                                                                    valign="top"><![endif]-->
                                                                <table align="left" border="0" cellspacing="0"
                                                                       cellpadding="0" width="32" height="32"
                                                                       style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 13px">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td align="left" valign="middle"
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                            <a href="{{$instagramUrl}}"
                                                                               title="Instagram" target="_blank">
                                                                                <img
                                                                                    src="{{asset('assets/email/image-3.png')}}"
                                                                                    alt="Instagram" title="Instagram"
                                                                                    width="32"
                                                                                    style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <!--[if (mso)|(IE)]></td><![endif]-->

                                                                <!--[if (mso)|(IE)]>
                                                                <td width="32" style="width:32px; padding-right: 13px;"
                                                                    valign="top"><![endif]-->
                                                                <table align="left" border="0" cellspacing="0"
                                                                       cellpadding="0" width="32" height="32"
                                                                       style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 13px">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td align="left" valign="middle"
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                            <a href="{{$youtubeUrl}}"
                                                                               title="YouTube" target="_blank">
                                                                                <img
                                                                                    src="{{asset('assets/email/image-4.png')}}"
                                                                                    alt="YouTube" title="YouTube"
                                                                                    width="32"
                                                                                    style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <!--[if (mso)|(IE)]></td><![endif]-->

                                                                <!--[if (mso)|(IE)]>
                                                                <td width="32" style="width:32px; padding-right: 0px;"
                                                                    valign="top"><![endif]-->
                                                                <table align="left" border="0" cellspacing="0"
                                                                       cellpadding="0" width="32" height="32"
                                                                       style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 0px">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td align="left" valign="middle"
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                            <a href="{{$linkedInUrl}}"
                                                                               title="LinkedIn" target="_blank">
                                                                                <img
                                                                                    src="{{asset('assets/email/image-2.png')}}"
                                                                                    alt="LinkedIn" title="LinkedIn"
                                                                                    width="32"
                                                                                    style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                 <img
                                                                                    src="{{@$hashId}}"
                                                                                    alt="LinkedIn" title="LinkedIn"
                                                                                    width="32"
                                                                                    style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: none !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <!--[if (mso)|(IE)]></td><![endif]-->


                                                                <!--[if (mso)|(IE)]></tr></table></td></tr></table>
                                                                <![endif]-->
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <table style="font-family:'Rubik',sans-serif;" role="presentation"
                                                   cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                <tr>
                                                    <td class="v-container-padding-padding"
                                                        style="overflow-wrap:break-word;word-break:break-word;padding:10px 44px 35px;font-family:'Rubik',sans-serif;"
                                                        align="left">

                                                        <div class="v-text-align"
                                                             style="color: #ecf0f1; line-height: 180%; text-align: center; word-wrap: break-word;">
                                                            <p style="font-size: 14px; line-height: 180%;"><span
                                                                    style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 25.2px;">This email was sent by {{$emailAddress}} because you registered with us..</span>
                                                            </p>
                                                            <p style="font-size: 14px; line-height: 180%;"><span
                                                                    style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 25.2px;">&copy;2022 wedu | {{$address}}</span>
                                                            </p>
                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>


                                            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>



                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
        </table>
        <!--[if mso]></div><![endif]-->
        <!--[if IE]></div><![endif]-->
</body>
<script>
    $(document).ready(function(){
        // $.ajax({url: "https://panel.wedu.ca/api/v1/services/updateReadEmail/{{@$hashId}}", success: function(result){
        //         $("#div1").html(result);
        //     }});
    });
</script>
</html>
