<?php

namespace App\Http\Controllers\agent\events;

use App\Http\Controllers\Controller;
use App\Models\SqlModel\Events;
use App\Models\SqlModel\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    //

    public function index(Request $request){
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Events";
        $events= Events::where("AdminId",auth()->user()->id)->get();
        $data["events"] = $events;
        $data["AdminId"] = auth()->user()->id;
        return view('agent.events.index', $data);
    }

    public function calendar() {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Calendar";
        $events= Events::where("AdminId",auth()->user()->id)->get();
        $data["events"] = $events;
        $schedules= Schedules::with('events')->where("AdminId",auth()->user()->id)->get();
        $data["schedules"] = $schedules;
        $data["AdminId"] = auth()->user()->id;
        return view('agent.events.calendar', $data);
    }

    public function timeShow(Request $request) {
        $date = $request->value;
        $date = date("d-m-Y", strtotime($date));
        $qry = "select * from TableSchedules where StartDate LIKE '$date%'";
        $res =  DB::select($qry);
        // get the booked slots
        if (!empty($res)) {
            foreach ($res as $time_get) {
                $time_get = collect($time_get)->all();
                $start_time = $time_get["StartTime"];
                $end_time = $time_get["EndTime"];
                $start_time = str_replace("AM", "", $start_time);
                $start_time = str_replace("PM", "", $start_time);
                $end_time = str_replace("PM", "", $end_time);
                $end_time = str_replace("AM", "", $end_time);
                while (strtotime($start_time) < strtotime($end_time)) {
                    $temp_array_1[] = date("H:i A", strtotime($start_time));
                    $start_time = date("H:i", strtotime("+15 minutes", strtotime($start_time)));
                }
                $start_time = "";
                $end_time = "";
            }
            for ($i = 0; $i < 96; $i++) {
                if ($i == 0) {
                    $temp_time = "00:00";
                }
                $dateTime = new \DateTime($temp_time);
                $dateTime->modify('+60 minutes');
                $time = $dateTime->format("H:i A");

                if (!in_array($time, $temp_array_1)) {
                    $tempArray[] = $time;
                }
                $time = $dateTime->format("H:i");
                $temp_time = $time;
            }
        } else {
            for ($i = 0; $i < 96; $i++) {
                if ($i == 0) {
                    $temp_time = "00:00";
                }
                $dateTime = new \DateTime($temp_time);
                $dateTime->modify('+60 minutes');
                $time = $dateTime->format("H:i A");
                $tempArray[] = $time;
                $time = $dateTime->format("H:i");
                $temp_time = $time;
            }
        }
        $available_slots = $tempArray;
        return response($tempArray,200);
        //echo json_encode($tempArray);
    }

    public function addSchedule(Request $request) {
        $data = $request->all();
        // get events
        $events_data= Events::where("id",$data["Subject"])->first();
        $events_data = collect($events_data)->all();
        $booking_start_time = $data["Date"]. " ". $data["StartEndTime"][0];
        $booking_end_time = $data["Date"]. " ". $data["StartEndTime"][1];
        $booking_start_time = str_replace("AM", "",$booking_start_time);
        $booking_start_time = str_replace("PM", "",$booking_start_time);
        $booking_end_time = str_replace("PM", "",$booking_end_time);
        $booking_end_time = str_replace("AM", "",$booking_end_time);
        $data_s = array();
        $data_s["Created"] = date('Y-m-d H:i:s');
        $data_s["Subject"] = $events_data["EventTitle"];
        $data_s["ScheduleStartTime"] = $booking_start_time;;
        $data_s["ScheduleEndTime"] = $booking_end_time;
        $data_s["AdminId"] = auth()->user()->id;
        $data_s["EventColor"] = $events_data["id"];
        $data_s["StartTime"] = $data["StartEndTime"][0];
        $data_s["EndTime"] = $data["StartEndTime"][1];
        $data_s["StartDate"] = $data["Date"];
        $data_s["Description"] = $data["Description"];
        try {
            Schedules::create($data_s);
            $events_data_up= Events::where("AdminId",auth()->user()->id)->get();
            $schedules_data_up= Schedules::where("AdminId",auth()->user()->id)->get();
            $data["events"] = $events_data_up;
            $data["schedules"] = $schedules_data_up;
            return redirect('agent/events/calendar');
            //return view('agent.events.calendar', $data);
        }
        catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    public function add_event(Request $request) {
        if ($request->has('id')){
            $id = $request->id;
            $event_title = $request->EventTitle;
            $event_name = $request->EventName;
            $color = $request->Color;
            $custom["EventTitle"] = $event_title;
            $custom["EventName"] = $event_name;
            $custom["Color"] = $color;
            $custom["AdminId"] = $request->AdminId;
            try {
                Events::where("id",$id)->update($custom);
                return response("Event added successfully",200);
            }catch (\Exception $e){
                return response($e->getMessage(),422);
            }
        }else{
            $event_title = $request->EventTitle;
            $event_name = $request->EventName;
            $color = $request->Color;
            $custom["EventTitle"] = $event_title;
            $custom["EventName"] = $event_name;
            $custom["Color"] = $color;
            $custom["AdminId"] = $request->AdminId;
            try {
                Events::create($custom);
                return response("Event added successfully",200);
            }catch (\Exception $e){
                return response($e->getMessage(),422);
            }
        }


    }

    public function edit_event($id) {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Edit Events";
        $query = Events::where("id",$id)->first();
        $data['editEvent'] = $query;
        $events_data_up= Events::where("AdminId",auth()->user()->id)->get();
        $data['events'] = $events_data_up;
        $data["AdminId"] = auth()->user()->id;
        return view('agent.events.edit_event', $data);
    }

    public function delete_event(Request $request) {
        $id = $request->id;
        try {
            Events::where("id",$id)->delete();
            return response("Event deleted successfully",200);
        }catch (\Exception $e){
            return response($e->getMessage(),422);
        }
    }

    public function getSlots(Request $request) {
        $validator = Validator::make($request->all(), [
            "Date" => "required|date_format:d-m-Y",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $date = $request->Date;
            $date = date("d-m-Y", strtotime($date));
            $qry = "select * from TableSchedules where StartDate LIKE '$date%'";
            $res =  DB::select($qry);
            // get the booked slots
            if (!empty($res)) {
                foreach ($res as $time_get) {
                    $time_get = collect($time_get)->all();
                    $start_time = $time_get["StartTime"];
                    $end_time = $time_get["EndTime"];
                    $start_time = str_replace("AM", "", $start_time);
                    $start_time = str_replace("PM", "", $start_time);
                    $end_time = str_replace("PM", "", $end_time);
                    $end_time = str_replace("AM", "", $end_time);
                    while (strtotime($start_time) < strtotime($end_time)) {
                        $temp_array_1[] = date("H:i A", strtotime($start_time));
                        $start_time = date("H:i", strtotime("+15 minutes", strtotime($start_time)));
                    }
                    $start_time = "";
                    $end_time = "";
                }
                for ($i = 0; $i < 96; $i++) {
                    if ($i == 0) {
                        $temp_time = "00:00";
                    }
                    $dateTime = new \DateTime($temp_time);
                    $dateTime->modify('+60 minutes');
                    $time = $dateTime->format("H:i A");

                    if (!in_array($time, $temp_array_1)) {
                        $tempArray[] = $time;
                    }
                    $time = $dateTime->format("H:i");
                    $temp_time = $time;
                }
            } else {
                for ($i = 0; $i < 96; $i++) {
                    if ($i == 0) {
                        $temp_time = "00:00";
                    }
                    $dateTime = new \DateTime($temp_time);
                    $dateTime->modify('+60 minutes');
                    $time = $dateTime->format("H:i A");
                    $tempArray[] = $time;
                    $time = $dateTime->format("H:i");
                    $temp_time = $time;
                }
            }
            $available_slots = $tempArray;
            return response($tempArray,200);
        }
    }

    public function addSlots(Request $request) {
        $validator = Validator::make($request->all(), [
            "Date" => "required|date_format:d-m-Y",
            "StartTime" => "required",
            "EndTime" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $data = $request->all();
            // get events
            $events_data= Events::where("id",5)->first();
            $events_data = collect($events_data)->all();
            $booking_start_time = $data["Date"]. " ". $data["StartTime"];
            $booking_end_time = $data["Date"]. " ". $data["EndTime"];
            $query = 'SELECT * FROM `TableSchedules` WHERE StartDate="'.$data["Date"].'" and StartTime="'.$data["StartTime"].'" and EndTime = "'.$data["EndTime"].'" and AdminId ='.$data["AgentId"];
            $checkData = DB::select($query);
            if ($checkData != []){
               $response["errors"] = "Please add another slots, this is already present";
               $response["status"] = 422;
               return response($response,422);
            }
            $booking_start_time = str_replace("AM", "",$booking_start_time);
            $booking_start_time = str_replace("PM", "",$booking_start_time);
            $booking_end_time = str_replace("PM", "",$booking_end_time);
            $booking_end_time = str_replace("AM", "",$booking_end_time);
            $data_s = array();
            $data_s["Created"] = date('Y-m-d H:i:s');
            $data_s["Subject"] = $events_data["EventTitle"];
            $data_s["ScheduleStartTime"] = $booking_start_time;;
            $data_s["ScheduleEndTime"] = $booking_end_time;
            $data_s["AdminId"] = $data["AgentId"];
            $data_s["EventColor"] = $events_data["id"];
            $data_s["StartTime"] = $data["StartTime"];
            $data_s["EndTime"] = $data["EndTime"];
            $data_s["StartDate"] = $data["Date"];
            $data_s["Phone"] = "00000000";
            $data_s["Description"] = $data["Description"];
            if ($request->has('Name')){
                $data_s["Name"] = $data["Name"];
            }
            if ($request->has('Email')){
                $data_s["Email"] = $data["Email"];
            }
            if ($request->has('Phone')){
                $data_s["Phone"] = $data["Phone"];
            }
            try {
                Schedules::create($data_s);
                $response["success"] = "Schedule Added Successfully";
                $response["status"] = 200;
                $notification_data = [
                    "ContactName" => $data["Name"],
                    "Email" => $data["Email"],
                    "Phone" => $data["Phone"],
                    "Message" => $data["Description"],
                    "StatusId" => 0,
                    "AgentId" => $data["AgentId"],
                    "subject" => $data["Name"].", ".env('SCHEDULE_A_SHOWING_FORM_NOTIFICATION_MSG'),
                ];
                saveNotificationData($notification_data);
                $time = strtotime($data["Date"]);
                $newformat = date('m-d-Y',$time);
                $data["Date"] = $newformat;

                // time converter
                $pieces = explode(' ' ,$data["StartTime"]);
                $data["StartTime"] = date("g:i a", strtotime($pieces[0]));
                $data["StartTime"] = $data["StartTime"].' '.$pieces[1];

                $pieces2 = explode(' ' ,$data["EndTime"]);
                $data["EndTime"] = date("g:i a", strtotime($pieces2[0]));
                $data["EndTime"] = $data["EndTime"].' '.$pieces2[1];

                $msg = "<h1>Hi Admin</h1>
                <p>1 schedule a showing for " . $data["Description"] . ", please contact with this user</p>
                <table style='width:100%'>
                <tr>
                    <th style='border:1px solid black'>Name</th>
                    <th style='border:1px solid black'>Email</th>
                    <th style='border:1px solid black'>Phone</th>
                    <th style='border:1px solid black'>Message</th>
                    <th style='border:1px solid black'>Date</th>
                    <th style='border:1px solid black'>Start Time</th>
                    <th style='border:1px solid black'>End Time</th>
                    <th style='border:1px solid black'>Property url</th>

                </tr>
                <tr>
                    <td style='border:1px solid black'>".$data["Name"]."</td>
                    <td style='border:1px solid black'>".$data["Email"]."</td>
                    <td style='border:1px solid black'>".$data["Phone"]."</td>
                    <td style='border:1px solid black'>".$data["Description"]."</td>
                    <td style='border:1px solid black'>".$data["Date"]."</td>
                    <td style='border:1px solid black'>".$data["StartTime"]."</td>
                    <td style='border:1px solid black'>".$data["EndTime"]."</td>
                    <td style='border:1px solid black'>".isset($data["PropertyUrl"]) ? $data["PropertyUrl"] : ''."</td>
                </tr>

                </table>";
                $superAdminEmail = getAdmin($data["AgentId"]);
                $subject = "A New Book A Schedule is Booked for Property ".$data["Description"];
                sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $msg, "Event Controller - addSlots", 2, env('SCHEDULESHOWING'));
                return response($response,200);
            }
            catch (\Exception $e){
                $response["errors"] = $e->getMessage();
                $response["status"] = 500;
                return response($response,500);
            }
        }
    }
}
