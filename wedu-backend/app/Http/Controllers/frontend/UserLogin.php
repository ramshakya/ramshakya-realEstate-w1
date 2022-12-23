<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Enquiries;
use App\Models\User;
use App\Models\SqlModel\lead\LeadsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Userlogs;
use App\Models\ForgotPassword;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\LoginDetails;
use App\Models\SqlModel\Websetting;
use App\Models\SqlModel\MostSearchedCities;
use App\Models\UserTracker;
use Carbon\Carbon;
use App\Models\SqlModel\SavedSearchFilter;
use App\Models\SqlModel\AlertsLog;
use App\Models\SqlModel\Campaign\TemplatesModel;
use Socialite;


class UserLogin extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        $responseArray = [];
        $validator = Validator::make($request->all(), [
            "Email" => "required|unique:Leads,Email",
            "Firstname" => "required",
            "Password" => "required",
            "AgentId" => "required",
            "Phone" => "required|min:10",
        ]);
        if ($validator->fails()) {
            $responseArray["errors"] = $validator->errors();
            return response($responseArray, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $Name = $request->Firstname . " " . $request->Lastname;
            $user = LeadsModel::create([
                'ContactName' => $Name,
                'Email' => $request->Email, //check git push
                'Password' => md5($request->Password),
                'Phone' => $request->Phone,
                'AssignedAgent' => $request->AgentId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),

            ]);
            // send data to zapier
            $arr = array(
                'Subject' => 'New signup',
                'Name' => $Name,
                'Email' => $request->Email,
                'Phone' => $request->Phone,
                'Date and time' => date("d/m/Y h:i:sa")
            );
            $agentId = $request->AgentId;
            $zap = ZapierSender($arr, $agentId);
            // end code
            // $user->createToken('UserRegister')->accessToken;
            $responseArray['LoginDetail'] = $this->loginAgent($request, true);
            $responseArray['success'] = "Signup Successfully";

            //Code start for dynamic email content send
            $sent_content = TemplatesModel::select('subject','content')->where('name', 'like', '%' . 'New signup' . '%' )->first();

            $AgentEmail=$request->Email;
            $AgentPhone=$request->Phone;
            // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {

            // }
            $content = ['{LeadName}','{LeadEmail}','{LeadPhone}','{Agent}','{AgentEmail}','{AgentPhone}','{OfficeName}','+ val +','{OfficeCity}','{OfficeState}','{OfficeZip}','{SiteName}','{SiteUrl}'];
            $content1 = [$Name,$AgentEmail,$AgentPhone,'','','','','','','','',env('APP_NAME'),env('WEDUURL')];
            $EmailSubject = $sent_content->subject;
            $EmailContent = str_replace($content,$content1,$sent_content->content);
            $subject = $EmailSubject;
            // $msg = "<h1>Hi " . $Name . "</h1>
            //     <p>Thanks for signing up. As we all figure out how to navigate new challenges amidst the COVID-19 pandemic, please be assured, we’re here to help, whatever your needs. As a leading source of MLS listings, market insights and deep local knowledge, there’s no better place than " . env('APP_NAME') . " to do your research and get answers to any questions you may have.</p>";
            $superAdminEmail = getSuperAdmin();
            sendEmail("SMTP", env('MAIL_FROM'), $request->Email, $request->Email, "", $subject, $EmailContent, "UserLogin - Signup", $request->AgentId,env('SIGNUP'));

            $msg = "<h1>Hi Admin</h1>
                <p>1 New user is registred</p>
                <table style='width:100%'>
            <tr>
                <th style='border:1px solid black'>Name</th>
                <th style='border:1px solid black'>Email</th>
                <th style='border:1px solid black'>Message</th>
                <th style='border:1px solid black'>Contact Number</th>
            </tr>
            <tr>
                <td style='border:1px solid black'>$Name</td>
                <td style='border:1px solid black'>$request->Email</td>
                <td style='border:1px solid black'>New user registered</td>
                <td style='border:1px solid black'>$request->Phone</td>
            </tr>

            </table>";
            $subject = "New user is registred";
            sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail,env('ALERT_CC_EMAIL_ID') , env('ALERT_BCC_EMAIL_ID'), $subject, $msg, "UserLogin - Signup", "$request->AgentId", env('SIGNUP'));

            // sendEmail("SMTP", env('MAIL_FROM'), $request->Email, $request->Email, $superAdminEmail, $subject, $msg, "UserLogin - Signup", $request->AgentId, env('SIGNUP'));
            $notification_data = [
                "ContactName" => $Name,
                "Email" => $request->Email,
                "Message" => $Name . ", " . env('SIGNUP_NOTIFICATION_MSG'),
                "StatusId" => 0,
                "AgentId" => $request->AgentId,
                "subject" => $Name . ", " . env('SIGNUP_NOTIFICATION_MSG')
            ];
            saveNotificationData($notification_data);
        }
        return response()->json($responseArray, 200);
    }


    /**
     *
     * Google Login
     *
     *
     */

    public function googleLogin(Request $request)
    {
        $user = Socialite::driver('google')->user();
        // $user = User::where('google_id', $user->id)->first();

        $Name = $user->name;
        $user = LeadsModel::updateOrCreate(
            [
                'Email' => $user->email,
            ],
            [
                'ContactName' =>  $Name,
                'Email' => $user->email,
                'AssignedAgent' => env('AGENT_ID'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
        if ($user) {
            Auth::login($user);
            $user_auth = Auth::user();
            $json['login_user_id'] = Auth::user()->id;
            $responseArray['token'] = auth()->user()->createToken('UserLogin')->accessToken;
            $json['login_email'] = Auth::user()->Email;
            $json['login_name'] = Auth::user()->ContactName;
            $json['login_mobile'] = Auth::user()->Phone;
            $responseArray['estimated_token_time'] = date('Y-m-d H:i:s', strtotime('now +1440 minutes'));
            $strtotime = strtotime($responseArray["estimated_token_time"]);
            $responseArray["estimated_token_time"] = $strtotime;
            $getFav = DB::table('FavouriteProperties')
                ->select('ListingId')
                ->where('LeadId', Auth::user()->id)
                ->where('AgentId', $request->AgentId)
                ->get();
            $json['favourite_properties'] = collect($getFav)->pluck('ListingId')->all();
            $responseArray['user_detail'] = $json;
            Userlogs::create([
                'UserId' => $user_auth->id,
                'LoginTime' => date("Y-m-d H:i:s"),
                'IpAddress' => $_SERVER['REMOTE_ADDR']
            ]);
            LoginDetails::create([
                'AgentId' => $user_auth->id,
                'IpAddress' => $_SERVER['REMOTE_ADDR'],
            ]);
            $validator = Validator::make($request->all(), [
                "Email" => "required|email|unique:Leads"
            ]);
            if (!$validator->fails()) {
                $sent_content = TemplatesModel::select('subject', 'content')->where('name', 'like', '%' . 'New signup' . '%')->get();

                $AgentEmail = $request->Email;
                $AgentPhone = '';
                // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {

                // }
                $content = ['{LeadName}', '{LeadEmail}', '{LeadPhone}', '{Agent}', '{AgentEmail}', '{AgentPhone}', '{OfficeName}', '+ val +', '{OfficeCity}', '{OfficeState}', '{OfficeZip}', '{SiteName}', '{SiteUrl}'];
                $content1 = [$Name, $AgentEmail, $AgentPhone, '', '', '', '', '', '', '', '', env('APP_NAME'), env('WEDUURL')];
                $EmailSubject = $sent_content->subject;
                $EmailContent = str_replace($content, $content1, $sent_content->content);
                $subject = $EmailSubject;
                $msg = $EmailContent;
                // $msg = "<h1>Hi " . $Name . "</h1>
                // <p>Thanks for signing up. As we all figure out how to navigate new challenges amidst the COVID-19 pandemic, please be assured, we’re here to help, whatever your needs. As a leading source of MLS listings, market insights and deep local knowledge, there’s no better place than " . env('APP_NAME') . " to do your research and get answers to any questions you may have.</p>";
                $superAdminEmail = getSuperAdmin();
                // $subject = "Welcome to your top source for condo info";
                sendEmail("SMTP", env('MAIL_FROM'), $request->Email, $request->Email, $superAdminEmail, $subject, $msg, "UserLogin - Signup", "", env('SIGNUP'));
            }
             echo "<script>window.open('http://localhost?logged_user=$user_auth->id','_self')</script>";
            //return response()->json($responseArray, 200);
        } else {
            $responseArray['failed'] = "Something went wrong";
            return response()->json($responseArray, 500);
        }
        // $user->createToken('UserRegister')->accessToken;
    }

    public function loginSocial(Request $request)
    {
        $Name = $request->Firstname . " " . $request->Lastname;
        $user = LeadsModel::updateOrCreate(
            [
                'Email' => $request->Email,
            ],
            [
                'ContactName' => $Name,
                'Email' => $request->Email,
                'AssignedAgent' => $request->AgentId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        if ($user) {
            Auth::login($user);
            $user_auth = Auth::user();
            $json['login_user_id'] = Auth::user()->id;
            $responseArray['token'] = auth()->user()->createToken('UserLogin')->accessToken;
            $json['login_email'] = Auth::user()->Email;
            $json['login_name'] = Auth::user()->ContactName;
            $json['login_mobile'] = Auth::user()->Phone;
            $responseArray['estimated_token_time'] = date('Y-m-d H:i:s', strtotime('now +1440 minutes'));
            $strtotime = strtotime($responseArray["estimated_token_time"]);
            $responseArray["estimated_token_time"] = $strtotime;
            $getFav = DB::table('FavouriteProperties')
                ->select('ListingId')
                ->where('LeadId', Auth::user()->id)
                ->where('AgentId', $request->AgentId)
                ->get();
            $json['favourite_properties'] = collect($getFav)->pluck('ListingId')->all();
            $responseArray['user_detail'] = $json;
            Userlogs::create([
                'UserId' => $user_auth->id,
                'LoginTime' => date("Y-m-d H:i:s"),
                'IpAddress' => $_SERVER['REMOTE_ADDR']
            ]);
            LoginDetails::create([
                'AgentId' => $user_auth->id,
                'IpAddress' => $_SERVER['REMOTE_ADDR'],
            ]);
            $validator = Validator::make($request->all(), [
                "Email" => "required|email|unique:Leads"
            ]);
            if (!$validator->fails()) {
                $sent_content = TemplatesModel::select('subject','content')->where('name', 'like', '%' . 'New signup' . '%' )->get();

                $AgentEmail=$request->Email;
                $AgentPhone='';
                // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {

                // }
                $content = ['{LeadName}','{LeadEmail}','{LeadPhone}','{Agent}','{AgentEmail}','{AgentPhone}','{OfficeName}','+ val +','{OfficeCity}','{OfficeState}','{OfficeZip}','{SiteName}','{SiteUrl}'];
                $content1 = [$Name,$AgentEmail,$AgentPhone,'','','','','','','','',env('APP_NAME'),env('WEDUURL')];
                $EmailSubject = $sent_content->subject;
                $EmailContent = str_replace($content,$content1,$sent_content->content);
                $subject = $EmailSubject;
                $msg = $EmailContent;
                // $msg = "<h1>Hi " . $Name . "</h1>
                // <p>Thanks for signing up. As we all figure out how to navigate new challenges amidst the COVID-19 pandemic, please be assured, we’re here to help, whatever your needs. As a leading source of MLS listings, market insights and deep local knowledge, there’s no better place than " . env('APP_NAME') . " to do your research and get answers to any questions you may have.</p>";
                $superAdminEmail = getSuperAdmin();
                // $subject = "Welcome to your top source for condo info";
                sendEmail("SMTP", env('MAIL_FROM'), $request->Email, $request->Email, $superAdminEmail, $subject, $msg, "UserLogin - Signup", "", env('SIGNUP'));
            }

            return response()->json($responseArray, 200);
        } else {
            $responseArray['failed'] = "Something went wrong";
            return response()->json($responseArray, 500);
        }
        // $user->createToken('UserRegister')->accessToken;
    }

    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $credentials = [
            'Email' => $request->Email,
            'Password' => $request->Password
        ];
        // return $credentials;
        dd(Auth::guard('leads')->attempt($request->only('Email', 'Password')));
        dd(auth()->guard('leads')->attempt($credentials));
        if (auth()->attempt($credentials)) {
            $responseArray['token'] = auth()->user()->createToken('UserLogin')->accessToken;

            return response()->json($responseArray, 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    public function loginAgent(Request $request, $isRegister = false)
    {
        $url = request()->headers->all();
        if ((!isset($url['origin']))|| (!isset($url['origin']['0']) && $url['origin']['0'] != env('FRONTENDSECURITYURL'))) {
            $responseArray['error'] = "You are not authorized to access this application";
            $responseArray['status'] = 401;
            return response()->json($responseArray, 401);
        }
        $response_data = [];
        // this is the validation method
        $validator = Validator::make($request->all(), [
            "Email" => "required",
            "Password" => "required",
            "AgentId" => "required"
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        // return  $request->all();
        if ($validator->passes()) {
            $new = new LeadsModel;
            $user = $new->get_userinfo($request);
            // dd(collect($user)->all());

            //     ->where('password', md5($request->password))
            //     ->first();
            // return $user;
            if ($user) {
                Auth::login($user);
                $user_auth = Auth::user();
                $json['login_user_id'] = Auth::user()->id;
                $responseArray['token'] = auth()->user()->createToken('UserLogin')->accessToken;
                $json['login_email'] = Auth::user()->Email;
                $json['login_name'] = Auth::user()->ContactName;
                $json['login_mobile'] = Auth::user()->Phone;
                $responseArray['estimated_token_time'] = date('Y-m-d H:i:s', strtotime('now +1440 minutes'));
                $strtotime = strtotime($responseArray["estimated_token_time"]);
                $responseArray["estimated_token_time"] = $strtotime;

                $getFav = DB::table('FavouriteProperties')
                    ->select('ListingId')
                    ->where('LeadId', Auth::user()->id)
                    ->where('AgentId', $request->AgentId)
                    ->get();
                $json['favourite_properties'] = collect($getFav)->pluck('ListingId')->all();
                $responseArray['user_detail'] = $json;
                Userlogs::create([
                    'UserId' => $user_auth->id,
                    'LoginTime' => date("Y-m-d H:i:s"),
                    'IpAddress' => $_SERVER['REMOTE_ADDR']
                ]);
                LoginDetails::create([
                    'AgentId' => $user_auth->id,
                    'IpAddress' => $_SERVER['REMOTE_ADDR'],
                ]);
                if ($isRegister) {
                    return $responseArray;
                }
                return response()->json($responseArray, 200);
            } else {

                $userAgent = new User();
                $users = $userAgent->get_userinfo($request);
                if ($users) {
                    if (!Hash::check($request->Password, $users->password)) {
                        return json_encode(["error" => "Email id or password is incorrect"]);
                    } else {
                        Auth::login($users);
                        $user_auth = Auth::user();
                        $json['login_user_id'] = Auth::user()->id;
                        $responseArray['token'] = auth()->user()->createToken('UserLogin')->accessToken;
                        $json['login_email'] = Auth::user()->email;
                        $json['login_name'] = Auth::user()->first_name." ".Auth::user()->last_name;
                        $json['login_mobile'] = Auth::user()->phone_number;
                        $json['EmailIsVerified'] = $users->is_email_verified;
                        $responseArray['estimated_token_time'] = date('Y-m-d H:i:s', strtotime('now +1440 minutes'));
                        $strtotime = strtotime($responseArray["estimated_token_time"]);
                        $responseArray["estimated_token_time"] = $strtotime;
                        $getFav = DB::table('FavouriteProperties')
                            ->select('ListingId')
                            ->where('LeadId', Auth::user()->id)
                            ->where('AgentId', $request->AgentId)
                            ->get();
                        $json['favourite_properties'] = collect($getFav)->pluck('ListingId')->all();
                        $responseArray['user_detail'] = $json;
                        Userlogs::create([
                            'UserId' => $user_auth->id,
                            'LoginTime' => date("Y-m-d H:i:s"),
                            'IpAddress' => $_SERVER['REMOTE_ADDR']
                        ]);
                        LoginDetails::create([
                            'AgentId' => $user_auth->id,
                            'IpAddress' => $_SERVER['REMOTE_ADDR'],
                        ]);
                        if ($isRegister) {
                            return $responseArray;
                        }
                        return response()->json($responseArray, 200);
                    }

                } else {
                    return json_encode(["error" => "Email id or password is incorrect"]);
                }
            }
        }
    }

    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        // return response()->json(['user' => auth()->user()->id], 200);
        $json['login_email'] = auth()->user()->Email;
        $json['login_name'] = auth()->user()->ContactName;
        $json['login_mobile'] = auth()->user()->Phone;
        $responseArray['user_detail'] = $json;
        return response()->json($responseArray, 200);
    }

    public function logout(Request $request)
    {
        // return "yes";
	$response = ['message', 'Logout successfull'];
        return response($response, 200);
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message', 'Logout successfull'];
        return response($response, 200);
    }

    public function duplicateEmail(Request $request)
    {
        $url = request()->headers->all();
        if ((!isset($url['origin']))|| (!isset($url['origin']['0']) && $url['origin']['0'] != env('FRONTENDSECURITYURL'))) {
            $responseArray['error'] = "You are not authorized to access this application";
            $responseArray['status'] = 401;
            return response()->json($responseArray, 401);
        }
        $validator = Validator::make($request->all(), [
            "email" => "required|email|unique:Leads"
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            return $error;
        } else {
            return 1;
        }
    }

    public function forgotPassword(Request $request)
    {
        $url = request()->headers->all();
        if ((!isset($url['origin']))|| (!isset($url['origin']['0']) && $url['origin']['0'] != env('FRONTENDSECURITYURL'))) {
            $responseArray['error'] = "You are not authorized to access this application";
            $responseArray['status'] = 401;
            return response()->json($responseArray, 401);
        }
        $validator = Validator::make($request->all(), [
            "Email" => "required|email"
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            return $error;
        }
        $form_data = $request->all();
        $email = $form_data['Email'];
        $email_exist = LeadsModel::where('Email', $email)->first();
        if ($email_exist) {
            $OTP = rand(100000, 999999);
            $Token = uniqid();
            // date_default_timezone_set("Asia/Kolkata");
            $Date = date("d-m-Y H:i:s");
            $expiry_date = date('d-m-Y H:i:s', strtotime($Date . ' + 1 days'));
            ForgotPassword::create([
                "UserId" => $email_exist->id,
                "Token" => $Token,
                "TimeLimit" => $expiry_date,
                "OTP" => $OTP,
                "Email" => $email_exist->Email
            ]);
            $url = $form_data['Url'];
            $redirect_url = $url . $Token;
            // $responseArray[''] = $Token;

            // Code for sending dynamic email content line no. 317 to 332
            $sent_content = TemplatesModel::select('subject','content')->where('name', 'like', '%' . 'Password reset' . '%' )->get();
            foreach ($sent_content as $sent_msg => $sent_message) {

            }
            $Name = $email_exist->ContactName;
            $AgentEmail=$email_exist->Email;
            $AgentPhone=$email_exist->Phone;
            // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {
            // }
            $hiddenWay =json_encode(request()->headers->all());
            $forgetLink = "<a style='background-color: #fe9ea1;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;' href='" . $redirect_url . "'>Click here </a> <div style='display:none;'>".$hiddenWay."</div>";
            $content = ['{LeadName}','{LeadEmail}','{LeadPhone}','{ForgetLink}','{AgentName}','{AgentEmail}','{AgentPhone}','{OfficeName}','+ val +','{OfficeCity}','{OfficeState}','{OfficeZip}','{SiteName}','{Resetlink}','{SiteUrl}'];
            $content1 = [$Name,$AgentEmail,$AgentPhone,$forgetLink,'','','','','','','','',env('APP_NAME'),'',env('WEDUURL')];
            $EmailContent = str_replace($content,$content1,$sent_message->content);
            $EmailSubject = $sent_message->subject;
            $subject = $EmailSubject;
            $message = $EmailContent;
            // $subject = 'Password reset link';
            // $message = "Here is your password link to reset your password <a style='background-color: #fe9ea1;
            // border: none;
            // color: white;
            // padding: 15px 32px;
            // text-align: center;
            // text-decoration: none;
            // display: inline-block;
            // font-size: 16px;
            // margin: 4px 2px;
            // cursor: pointer;' href='" . $redirect_url . "'>Click here </a>";
            $sent_token_url = sendEmail("SMTP", env('MAIL_FROM'), $email, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $message, "UserLogin->forgotPassword", "", env('FORGETPASSWORD'));
            if ($sent_token_url) {
                $responseArray['message'] = "Password reset link sent to your email address";
            } else {
                $responseArray['error'] = "Something went wrong";
            }

            return response()->json($responseArray, 200);
        } else {
            $responseArray['error'] = "Account not found";
            return response()->json($responseArray, 200);
        }
    }

    public function verifyTokenForgotPassword(Request $request)
    {

        $form_data = $request->all();
        if (isset($form_data['checkToken'])) {
            $checkToken = $form_data['checkToken'];
            // $Date = "25-02-2022";
            // date_default_timezone_set("Asia/Kolkata");
            $Date = date("d-m-Y H:i:s");
            $exist = ForgotPassword::where("Token", $checkToken)->first();
            if ($exist) {

                $exp = $exist->TimeLimit;
                if ($Date > $exp) {
                    return json_encode('Session Expired');
                } else {
                    return json_encode('success');
                }
            } else {
                return json_encode('Link expired');
            }
        }

        $Token = $form_data['Token'];
        $password = $form_data['Password'];
        // $cnfPassword = $request->all('ConfirmPassword');
        $verify = ForgotPassword::where("Token", $Token)->first();
        if ($verify) {
            $id = $verify->UserId;
            $data['Password'] = md5($password);
            $pwd_update = LeadsModel::updateOrCreate(['id' => $id], $data);
            if ($pwd_update) {
                ForgotPassword::updateOrCreate(['Token' => $Token], ['Token' => '']);
                return json_encode(["success" => "New password set successfully"]);
            } else {
                return json_encode(["error" => "Something went wrong"]);
            }
        } else {
            return json_encode(["error" => "Something went wrong"]);
        }
    }

    public function loginInfo(Request $request)
    {
        $form_data = $request->all();
        $id = $form_data['id'];
        $currentPage = $form_data['currentPage'];
        $limit = $form_data['limit'];
        $offset = $currentPage - 1;
        $start = ($offset * $limit);
        $query = Userlogs::where('userId', $id);
        $total = $query->count();
        $lastPage = $total % $limit;
        if ($lastPage == 0) {
            $totalPages = $total / $limit;
        } else {
            $totalPages = floor($total / $limit) + 1;
        }
        $response_data = $query->offset($start);
        $response_data = $response_data->limit($limit);
        $response_data = $response_data->orderBy('id', 'desc');
        $response_data = $response_data->get();
        $date = [];
        $time = [];
        foreach ($response_data as $key => $value) {
            $d = date_create($value->LoginTime);
            $date[] = date_format($d, "d M, Y");
            $time[] = date_format($d, "h:i:sa");
        }
        $res['login_date'] = $date;
        $res['login_time'] = $time;
        $res['total'] = $total;
        $res['currentPage'] = $currentPage;
        $res['totalPages'] = $totalPages;
        return $res;
    }

    public function updateUserDetail(Request $request)
    {
        $form_data = $request->all();
        $data = array();
        if (isset($form_data['fullname'])) {
            $data['ContactName'] = $form_data['fullname'];
        }
        if (isset($form_data['mobile'])) {
            $data['Phone'] = $form_data['mobile'];
        }
        if (isset($form_data['password'])) {
            $data['Password'] = md5($form_data['password']);
        }
        $id = $request->all('id');
        $updateProfile = false;
        if (count($data)) {
            $updateProfile = LeadsModel::updateOrCreate(['id' => $id], $data);
        }
        if ($updateProfile) {
            $json['login_user_id'] = Auth::user()->id;
            $json['login_email'] = Auth::user()->Email;
            $json['login_name'] = Auth::user()->ContactName;
            $json['login_mobile'] = Auth::user()->Phone;
            $responseArray['user_detail'] = $json;
            $responseArray['success'] = "Updated successfully";
            return json_encode($responseArray);
        } else {
            return json_encode(["error" => "Something went wrong try later"]);
        }
    }

    public function userActivity_old(Request $request)
    {
	return json_encode(["success" => "successfully inserted","data"=>[]]);
        $form_data = $request->all();
        if (isset($form_data['StayTime'])) {
            $IpAddress = $form_data['IpAddress'];
            $PageUrl = $form_data['PageUrl'];
            // $InTime    = $form_data['InTime'];
            $data['StayTime'] = $form_data['StayTime'];
            if (isset($form_data['UserId'])) { /// For Logged Users
                $UserId = $form_data['UserId'];
                // $cond = ['UserId'=>$UserId,'IpAddress'=>$IpAddress,'PageUrl'=>$PageUrl];
                $query = UserTracker::where('UserId', $UserId)->where('IpAddress', $IpAddress)->where('PageUrl', $PageUrl);
            } else { // For Not Logged Users
                // $cond = ['IpAddress'=>$IpAddress,'PageUrl'=>$PageUrl];
                $query = UserTracker::where('IpAddress', $IpAddress)->where('PageUrl', $PageUrl);
            }
            $query = $query->orderByDesc('id');
            // $query = $query->limit(1);
            $query = $query->update($data);
            // $query = UserTracker::updateOrCreate($cond,$data)->orderByDesc(id)->limit(1);
            return json_encode(["success" => "successfully Updated"]);
        } else {
            $query = UserTracker::create($form_data);
            return json_encode(["success" => "successfully inserted"]);
        }
    }


    public function userActivity(Request $request)
    {
	//return json_encode(["success" => "successfully inserted","data"=>[]]);
        $form_data = $request->all();
        $IpAddress = $form_data['IpAddress'];
        $PageUrl = $form_data['PageUrl'];
        $curr_time = date("Y-m-d H:i:s");
        $curr_date = date("Y-m-d");
        $slug = "";
        $form_data['StayTime'] = $curr_time;
        $form_data['InTime'] = $curr_time;
        $data['StayTime'] = $form_data['StayTime'];
        $query = UserTracker::query();
        $query->where("AgentId", $form_data['AgentId']);

        if (isset($form_data['FilteredData'])) {
            $slug = isset($form_data['FilteredData']['slug']) ? $form_data['FilteredData']['slug'] : "";
            if ($slug) {
                $prop = getProperties($slug);
                $mls = $prop ? $prop->Ml_num : "";
                $data['PropertyUrl'] = $PageUrl;
                $data['ListingId'] = $mls;
            }
            $txtSearch = isset($form_data['FilteredData']['text_search']) ? $form_data['FilteredData']['text_search'] : "";
            $city = isset($form_data['FilteredData']['City']) ? $form_data['FilteredData']['City'] : "";
            if ($txtSearch) {
                $cityData = RetsPropertyData::select("City")->where("City", $txtSearch)->first();
                if ($cityData) {
                    $city = $cityData->City;
                }
            }
            // text_search
            if ($city) {
                $cityQuery = MostSearchedCities::query();
                $cityQuery->where("CityName", $city);
                $cityData = $cityQuery->where("AgentId", $form_data['AgentId'])->first();
                // $cityData=$cityQuery->where("AgentId",2)->first();
                if ($cityData) {
                    $up = array(
                        "Count" => $cityData->Count + 1
                    );
                    $cityQuery->update($up);
                } else {
                    $createData = array(
                        "CityName" => $city,
                        "Count" => 1,
                        "AgentId" => $form_data['AgentId'],
                    );
                    $ci = MostSearchedCities::create($createData);
                }
            }
        }
        $prev = "";
        if (isset($form_data['UserId'])) {
            $query->where("UserId", $form_data['UserId']);
        }
        // $prev = $query->where('IpAddress', $form_data['IpAddress'])->where('PageUrl', $form_data['PageUrl'])->whereDate('created_at', Carbon::today())->first();
        $prev = $query->where('id',$request->prevId)->where('IpAddress', $form_data['IpAddress'])->where('PageUrl', $form_data['PageUrl'])->first();

        $data['PageUrl'] = $PageUrl;
        $env = env("WEDUURL");
        if ($PageUrl == $env) {
            $data['Pages'] = 1;
        }elseif(str_contains($PageUrl, $env."map")) {
            $data['Pages'] = 2;
        }elseif (str_contains($PageUrl, $env."propertydetails")) {
            $data['Pages'] = 3;
        }elseif (str_contains($PageUrl, $env."profile")) {
            $data['Pages'] = 4;
        }elseif (str_contains($PageUrl, $env."ContactUs")) {
            $data['Pages'] = 5;
        }elseif (str_contains($PageUrl, $env."city")) {
            $data['Pages'] = 6;
        }else {
            $data['Pages'] = 7;
        }
        // $data['Pages']
        if ($prev) {
            $data['StayTime'] = $form_data['StayTime'];
            $query = UserTracker::where('id', $prev->id)->update($data);
            return json_encode(["success" => "successfully updated","data"=>$prev]);
        }
        if ($PageUrl == $env) {
            $form_data['Pages'] = 1;
        }elseif(str_contains($PageUrl, $env."map")) {
            $form_data['Pages'] = 2;
        }elseif (str_contains($PageUrl, $env."propertydetails")) {
            $form_data['Pages'] = 3;
        }elseif (str_contains($PageUrl, $env."profile")) {
            $form_data['Pages'] = 4;
        }elseif (str_contains($PageUrl, $env."ContactUs")) {
            $form_data['Pages'] = 5;
        }elseif (str_contains($PageUrl, $env."city")) {
            $form_data['Pages'] = 6;
        }else {
            $form_data['Pages'] = 7;
        }
        $form_data['FilteredData'] = isset($form_data['FilteredData']) ? json_encode($form_data['FilteredData']) : "";
        $query = UserTracker::create($form_data);
        return json_encode(["success" => "successfully inserted","data"=>$query]);
    }
    //ram rename
    public function userActivity12(Request $request)
    {
        $form_data = $request->all();
        $IpAddress = $form_data['IpAddress'];
        $PageUrl = $form_data['PageUrl'];
        $curr_time = date("Y-m-d H:i:s");
        $slug = "";
        $form_data['StayTime'] = $curr_time;
        $form_data['InTime'] = $curr_time;
        $data['StayTime'] = $form_data['StayTime'];
        $query = UserTracker::query();
        $query->where("AgentId", $form_data['AgentId']);

        if (isset($form_data['FilteredData'])) {
            $slug = isset($form_data['FilteredData']['slug']) ? $form_data['FilteredData']['slug'] : "";
            if ($slug) {
                $prop = getProperties($slug);
                $mls = $prop ? $prop->Ml_num : "";
                $data['PropertyUrl'] = $PageUrl;
                $data['ListingId'] = $mls;
            }
            $txtSearch = isset($form_data['FilteredData']['text_search']) ? $form_data['FilteredData']['text_search'] : "";
            $city = isset($form_data['FilteredData']['City']) ? $form_data['FilteredData']['City'] : "";
            if ($txtSearch) {
                $cityData = RetsPropertyData::select("City")->where("City", $txtSearch)->first();
                if ($cityData) {
                    $city = $cityData->City;
                }
            }
            // text_search
            if ($city) {
                $cityQuery = MostSearchedCities::query();
                $cityQuery->where("CityName", $city);
                $cityData = $cityQuery->where("AgentId", $form_data['AgentId'])->first();
                // $cityData=$cityQuery->where("AgentId",2)->first();
                if ($cityData) {
                    $up = array(
                        "Count" => $cityData->Count + 1
                    );
                    $cityQuery->update($up);
                } else {
                    $createData = array(
                        "CityName" => $city,
                        "Count" => 1,
                        "AgentId" => $form_data['AgentId'],
                    );
                    $ci = MostSearchedCities::create($createData);
                }
            }
        }
        $prev = "";
        if (isset($form_data['UserId'])) {
            $query->where("UserId", $form_data['UserId']);
        }
        $prev = $query->where('IpAddress', $form_data['IpAddress'])->where('PageUrl', $form_data['PageUrl'])->first();
        $data['PageUrl'] = $PageUrl;
        if ($prev) {
            $data['StayTime'] = $form_data['StayTime'];
            $query = UserTracker::where('id', $prev->id)->update($data);
            return json_encode(["success" => "successfully updated"]);
        }
        $form_data['FilteredData'] = isset($form_data['FilteredData']) ? json_encode($form_data['FilteredData']) : "";
        $query = UserTracker::create($form_data);
        return json_encode(["success" => "successfully inserted"]);
    }
    public function userActivity_new(Request $request)
    {
        $form_data = $request->all();
        $IpAddress = \Request::getClientIp(true);
        $IpAddress = $form_data['IpAddress'] ? $form_data['IpAddress'] : $IpAddress;
        $PageUrl = $form_data['PageUrl'];
        $form_data['FilteredData'] = isset($form_data['FilteredData']) ? json_encode($form_data['FilteredData']) : "";
        if (isset($form_data['UserId'])) { /// For Logged Users
            $UserId = $form_data['UserId'];
            $query = UserTracker::where('UserId', $UserId)->where('IpAddress', $IpAddress)->where('PageUrl', $PageUrl);
        } else { // For Not Logged Users
            $query = UserTracker::where('IpAddress', $IpAddress)->where('PageUrl', $PageUrl);
        }
        $query = UserTracker::create($form_data);
        return json_encode(["success" => "successfully inserted"]);
    }
    public function leadForm(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "email" => "required",
            "name" => "required",
            "phone" => "required"
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        try {
            $data = $req->all();
            $data["user_ip"] = $_SERVER["REMOTE_ADDR"];
            $res = Enquiries::insert($req->all());
            $responseArray["message"] = "Thanks..";
            $msg = "<h1>Hi Admin</h1>
                <p>1 new enquiry raised for property " . $req->propertyaddress . ", please contact with this user</p>
                <table style='width:100%'>
                <tr>
                    <th style='border:1px solid black'>Name</th>
                    <th style='border:1px solid black'>Email</th>
                    <th style='border:1px solid black'>Phone</th>
                    <th style='border:1px solid black'>Message</th>
                    <th style='border:1px solid black'>URL</th>
                    <th style='border:1px solid black'>Page</th>
                    <th style='border:1px solid black'>Property Address</th>

                </tr>
                <tr>
                    <td style='border:1px solid black'>$req->name</td>
                    <td style='border:1px solid black'>$req->email</td>
                    <td style='border:1px solid black'>$req->phone</td>
                    <td style='border:1px solid black'>$req->message</td>
                    <td style='border:1px solid black'>$req->property_url</td>
                    <td style='border:1px solid black'>$req->page_from</td>
                    <td style='border:1px solid black'>$req->propertyaddress</td>
                </tr>

                </table>";
            // send data to zapier
            $arr = array(
                'Subject' => 'New enquiry from property detail page',
                'Name' => $req->name,
                'Email' => $req->email,
                'Phone' => $req->phone,
                'Message' => $req->message,
                'Property url' => $req->property_url,
                'Property address' => $req->propertyaddress,
                'Page from' => $req->page_from,
                'Date and time' => date("d/m/Y h:i:sa")
            );
            $zap = ZapierSender($arr);
            // end code
            //Uncomment from 628 to 643 for making email content dynamic
            // $sent_content = TemplatesModel::select('subject','content')->where('name', 'like', '%' . 'New schedules' . '%' )->get();
            // foreach ($sent_content as $sent_msg => $sent_message) {

            // }
            // $Name = $req->name;
            // $LeadEmail=$req->Email;
            // $LeadPhone=$req->Phone;
            // // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {

            // // }
            // $content = ['{LeadName}','{LeadEmail}','{LeadPhone}','{OfficeName}','+ val +','{OfficeCity}','{OfficeState}','{OfficeZip}','{SiteName}','{SiteUrl}'];
            // $content1 = [$Name,$LeadEmail,$LeadPhone,'','','','','',env('APP_NAME'),env('WEDUURL')];
            // $EmailSubject = $sent_message->subject;
            // $EmailContent = str_replace($content,$content1,$sent_message->content);
            // $msg = $EmailContent;
            // $subject = $EmailSubject;

            $superAdminEmail = getSuperAdmin();
            $subject = "A new enquiry raised";
            sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $msg, "UserLogin - lead form", "", env('SCHEDULESHOWING'));
            $notification_data = [
                "ContactName" => $req->name,
                "Email" => $req->email,
                "Phone" => $req->phone,
                "Message" => $req->message,
                "StatusId" => 0,
                "AgentId" => $req->agent_id,
                "subject" => $req->Name . ", " . env('SCHEDULE_A_SHOWING_FORM_NOTIFICATION'),
                "PageFrom" => $req->page_from,
                "Url" => $req->property_url
            ];
            saveNotificationData($notification_data);
            return response()->json($responseArray, 200);
        } catch (\Throwable $th) {
            return response($th, self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }
    public function getSavedSearch(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "agentId"       =>  "required",
            "userId"        =>  "required",
            "currentPage"   =>  "required",
            "limit"         =>  "required"
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        try {
            $form_data = $request->all();
            $userId = $form_data['userId'];
            $agentId = $form_data['agentId'];
            $currentPage = $form_data['currentPage'];
            $limit = $form_data['limit'];
            $offset = $currentPage - 1;
            $start = ($offset * $limit);
            $query = SavedSearchFilter::where('userId', $userId)->where('agentId', $agentId)->where("subscribe",1);
            $total = $query->count();
            $lastPage = $total % $limit;
            if ($lastPage == 0) {
                $totalPages = $total / $limit;
            } else {
                $totalPages = floor($total / $limit) + 1;
            }
            $response_data = $query->offset($start);
            $response_data = $response_data->limit($limit);
            $response_data = $response_data->orderBy('id', 'desc');
            $response_data = $response_data->get();
            $res['total'] = $total;
            $res['currentPage'] = $currentPage;
            $res['totalPages'] = $totalPages;
            $res['savedSearch'] = $response_data;
            return response()->json($res, 200);
        } catch (\Throwable $th) {
            return response($th, self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }
    public function deleteSavedSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "delId"       =>  "required",
            "userId"        =>  "required",
            "agentId"   =>  "required"
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        try {
            $form_data = $request->all();
            $id = $form_data['delId'];
            $res= SavedSearchFilter::where("id",$id)->delete();
            // $res = true;
            // return redirect('project');
            if ($res) {
                return json_encode(["success" => "Deleted successfully"]);
            } else {
                return json_encode(["error" => "Something went wrong"]);
            }
        } catch (\Throwable $th) {
            return response($th, self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }
    public function sentEmailHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "agentId"       =>  "required",
            "userId"        =>  "required",
            "currentPage"   =>  "required",
            "limit"         =>  "required"
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        try {
            $form_data = $request->all();
            $userId = $form_data['userId'];
            $agentId = $form_data['agentId'];
            $currentPage = $form_data['currentPage'];
            $limit = $form_data['limit'];
            $offset = $currentPage - 1;
            $start = ($offset * $limit);
            $query = AlertsLog::where('userId', $userId);
            $total = $query->count();
            $lastPage = $total % $limit;
            if ($lastPage == 0) {
                $totalPages = $total / $limit;
            } else {
                $totalPages = floor($total / $limit) + 1;
            }
            $response_data = $query->offset($start);
            $response_data = $response_data->limit($limit);
            $response_data = $response_data->orderBy('id', 'desc');
            $response_data = $response_data->get();
            $alertsName = [];
            if ($response_data) {
                foreach ($response_data as $key => $value) {
                    $alertId = $value->alertId;
                    $alertData = SavedSearchFilter::select('filterName')->where('id', $alertId)->first();
                    if ($alertData) {
                        $alertsName[$alertId] = $alertData->filterName;
                    } else {
                        $alertsName[$alertId] = "";
                    }
                }
            }
            $res['total'] = $total;
            $res['currentPage'] = $currentPage;
            $res['totalPages'] = $totalPages;
            $res['emailHistory'] = $response_data;
            $res['alertsName'] = $alertsName;
            return response()->json($res, 200);
        } catch (\Throwable $th) {
            return response($th, self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }
    public function getSavedSearchDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "alertId"       =>  "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        try {
            $form_data = $request->all();
            $alertId = $form_data['alertId'];
            $query = SavedSearchFilter::where('id', $alertId)->first();
            $res['alertDetail'] = $query;
            return response()->json($res, 200);
        } catch (\Throwable $th) {
            return response($th, self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }
}
