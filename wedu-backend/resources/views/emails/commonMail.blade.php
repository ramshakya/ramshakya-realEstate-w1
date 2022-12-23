<?php
$data = \App\Models\SqlModel\Websetting::where("AdminId",'=',3)->first();
$emailAddress = $data->WebsiteEmail;
$phone = $data->PhoneNo;
$websiteTitle = $data->WebsiteTitle;
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <!--[if mso]>
    <noscript>
    <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style>
        table, td, div, h1, p {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body style="margin:0;padding:0;">
<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
    <tr>
        <td align="center" style="padding:0;">
            <table role="presentation"
                   style="width:802px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                <tr>
                    <td align="center" style="padding:40px 0 30px 0;background:#ffffff;">
                        <img src="http://3.144.136.139/assets/property/mls_images/1642016583.png" alt="" width="300"
                             style="height:auto;display:block;"/>
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 30px 42px 30px;">
                        <table role="presentation"
                               style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                            <tr>
                                <td style="padding:0 0 36px 0;color:#424242;">
<!--                                    <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">Creating Wedu Mail</h1>-->
                                    {!! $content !!}


                                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">

                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <table align="center" style="border:hidden; font-family:Arial, Helvetica, sans-serif; background:#f7f7f7;margin-top:20px;" width="80%" border="none">

                    <tr>
                        <td align="center" style="border:hidden; font-size:14px; line-height:20px; background:#ffffff; color:#696969;">
                            This email was sent by {{$emailAddress}} because you registered with us.<br /><br />
                            Add us to your address book
                        </td>
                    </tr>
                    <br/>
                    <tr>
                        <td align="center" style="border:hidden; font-size:14px; line-height:20px; background:#ffffff; color:#696969;">
                            <a href="#">Unsubscribe</a>  {{$websiteTitle}}  |    <a href="#">Email Preference</a>
                        </td>
                    </tr>
                </table>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
