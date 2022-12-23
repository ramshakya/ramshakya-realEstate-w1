<?php

namespace App\Http\Controllers;

use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataCommPurged;
use App\Models\RetsPropertyDataCondoPurged;
use App\Models\RetsPropertyDataImagesSold;
use App\Models\RetsPropertyDataResiPurged;
use App\Models\SqlModel\RetsPropertyDataPurged;
use App\Mail\SendMail;
use App\Models\ProvinceTbl;
use App\Models\RetsPropertyDataComm;
use App\Models\RetsPropertyDataCondo;
use FilesystemIterator;
use Illuminate\Support\Facades\Http;
use App\Models\RetsPropertyDataImage;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\agent\AssignmentModel;
use App\Models\SqlModel\agent\LeadAgentModel;
use App\Models\SqlModel\EmailTemplate;
use App\Models\SqlModel\FeaturesMaster;
use App\Models\SqlModel\PropertyFeatures;
use App\Models\SqlModel\RetsPropertyDataSql;
use Illuminate\Http\Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twilio\Rest\Client;
use App\Models\SqlModel\Websetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class TestController extends Controller
{
    //
    public $leadAgentModel;
    public $emailReplyModel;
    public $leadsModel;
    public $retsPropertyData;
    public $retsPropertyDataPurged;
    public $sqlemailReplyModel;
    private $fpdf;
    public function __construct()
    {
        $db = env('RUNNING_DB_INFO');
        if ($db == "sql") {
            $this->leadAgentModel = new \App\Models\SqlModel\agent\LeadAgentModel();
            $this->emailReplyModel = "";
            $this->leadsModel = new \App\Models\SqlModel\lead\LeadsModel();
            $this->retsPropertyData = "";
        } else {
        }
    }

    // public function store() {
    //     $querys = DB::select(DB::raw("SELECT * from LeadAgentProfile LIMIT 10 "));
    //     dd($querys);
    // }

    /*public $retsPropertyData;
        public function __construct() {
            $db = env('RUNNING_DB_INFO');
            if ($db == "sql"){
                $this->retsPropertyData = new PropertyData();
            }else{
                $this->retsPropertyData = new RetsPropertyData();
            }
        }*/


    public function storeold()
    {
        /*$querys = DB::select(DB::raw("SELECT * from LeadAgentProfile LIMIT 10 "));
        dd($querys);*/
        $data = $this->retsPropertyData->all();
        return response($data, 200);
    }

    public function complexQuery(Request $request)
    {
        $request_data = $request->all();
        $data = $this->retsPropertyData->get_data($request_data);
        return response($data, 200);
    }

    public function TwilioTest($id = NULL)
    {
        if (isset($id)) {
            $sendnotifications = Websetting::select('TwilioSID', 'TwilioNumber', 'TwilioToken')->where('AdminId', Auth()->user()->id)->get();
            if (sizeof($sendnotifications) != 0) {
                foreach ($sendnotifications as $key => $value) {
                }
                $accountSid = $value->TwilioSID;
                $authToken = $value->TwilioToken;
                $twilioNumber = $value->TwilioNumber;
                if (!empty($accountSid) && !empty($authToken) && !empty($twilioNumber)) {
                    $client = new Client($accountSid, $authToken);
                    $message = 'Hello Mukesh,
                This is test message from Twilio';
                    $sendfrom = $value->TwilioNumber;
                    try {
                        $client->messages->create(
                            $to,
                            [
                                "body" => $message,
                                "from" => $sendfrom,
                            ]
                        );
                        Log::info('Message sent to ' . $id);
                    } catch (TwilioException $e) {
                        Log::error(
                            'Could not send notification.' .
                                ' Twilio replied with: ' . $e
                        );
                    }
                }
            }
        }
    }

    public function storeNew()
    {
        //$this->sqlemailReplyModel;
        /*$querys = DB::select(DB::raw("SELECT * from LeadAgentProfile LIMIT 10 "));
        dd($querys);*/
        $type = "created_at";
        $date = \Carbon\Carbon::today()->subDays(15);
        $c = $date->format('Y-m-d h:i:s');
        $query = \App\Models\SqlModel\agent\LeadAgentModel::selectRaw('count(id) as total, Date(' . $type . ') as udate,mls_no')
            ->where($type, '>=', $date)
            ->groupBy('mls_no')
            ->groupBy('udate')
            ->orderBy('udate', 'ASC')
            ->get();



        $previous_agent_cond = "";
        //return response($mongoquery,200);
        //$query = "SELECT * from LeadAgentProfile where  (AgentType = 'In-House' OR AgentType = 'In-House/Zip'  ) and AgentActive='Yes' $previous_agent_cond and ( $inhouse_cond )";

        //$query =  DB::select(DB::raw("SELECT * from LeadAgentProfile where  (AgentType = 'In-House' OR AgentType = 'In-House/Zip'  ) and AgentActive='Yes' $previous_agent_cond and ( $inhouse_cond )"));
        $mls_id = 'A10896367';
        $address = '650 NE 32nd St 807';
        $inhouse_cond = "";
        $previous_agent = 0;
        /*if (isset($mls_id) && !empty($mls_id)) {
            //$inhouse_cond .= "MLSNumbers Like '%$mls_id%' ";
            $inhouse_cond .= "->Where('MLSNumbers', 'like', '%' . $mls_id . '%')";
        }
        if (isset($address) && !empty($address)) {
            if ($inhouse_cond == "") {
                //$inhouse_cond .= " Addresses like '%$address%' ";
                $inhouse_cond .= "->where('Addresses','like','%'.$address.'%')";
            } else {
                $inhouse_cond .= "->orWhere('Addresses','like','%'.$address.'%')";
            }
        }

        $previous_agent_cond = '';
        if (isset($previous_agent) && $previous_agent > 0) {
            $previous_agent_cond = "->where('_id','<>',$previous_agent)";
        }

        $result =   LeadAgentModel::select("*")
            ->where("AgentType",'=','In-House')
            ->orWhere("AgentType",'=','In-House/Zip')
            ->where("AgentActive",'=','Yes')
            ->Where('MLSNumbers', 'like', '%' . $mls_id . '%')
            ->where('Addresses','like','%'.$address.'%')
            ->orWhere('Addresses','like','%'.$address.'%')
            ->where('_id','<>',$previous_agent)->get();*/


        /*DB::select(DB::raw("SELECT * from LeadAgentProfile where QueueFlag = 0 and (AgentType = 'Zip' OR AgentType = 'In-House/Zip' ) and AgentActive='Yes' $previous_agent_cond and $property_typefield = '$property_type' $price_cond $zip_cond "))

            $result = LeadAgentModel::select("*")
                ->where("QueueFlag",'=',0)
                ->where("AgentType","=","Zip")
                ->orWhere("AgentType","=","In-House/Zip")
                ->where("AgentActive",'=',"Yes")
                ->where('_id','<>',$previous_agent)
                ->where($property_typefield,'=',$property_type)
                ->where($price, '>=' , $pt_min)
                ->where($price,'<',$pt_max)
                ->orWhere('ZipCodes','like','%'.$zipcode.'%')
                ->orWhere('Citys','like','%'.$city.'%')
                ->get();
*/
        //$query =  DB::select(DB::raw("SELECT GROUP_CONCAT(_id) as all_agents_ids from LeadAgentProfile where (AgentType = 'Zip' OR AgentType = 'In-House/Zip' ) and AgentActive='Yes' $previous_agent_cond and $property_typefield = '$property_type' $price_cond $zip_cond "));

        $result = array(array("all_agent_ids" => collect(LeadAgentModel::select("_id")
            ->where("AgentType", "=", "Zip")
            ->orWhere("AgentType", "=", "In-House/Zip")
            ->where("AgentActive", '=', "Yes")
            ->get())->map(function ($item) {
            return $item->_id;
        })->all()));
        //$result = $result->get();
        $curr_agent_row = collect($result)->first();
        //dd($curr_agent_row);
        //$curr_agent_row = collect($curr_agent_row)->all();
        //$curr_agentsid = implode(',',$curr_agent_row['all_agent_ids']);
        $curr_agentsid = $curr_agent_row['all_agent_ids'];

        //$results = LeadsModel::select("SELECT AssignedAgent,_id
        //as oredrs from Leads  where AssignedAgent in  ($curr_agentsid)  group by AssignedAgent order by oredrs DESC")->get();

        $curr_agentsid = ['12541', "1"];
        $results = LeadsModel::select("AssignedAgent")
            ->whereIn("AssignedAgent", $curr_agentsid)
            //->groupBy("AssignedAgent")
            ->orderBy("_id", 'DESC')
            ->get();

        $results = collect($results)->groupBy("AssignedAgent")->map(function ($item) {
            return collect($item)->first();
        })->all();

        /*$results =  collect($results)->first();
        $results =  collect($results)->all();
        $results = $results["AssignedAgent"];*/
        return response($results, 200);


        dd($query);
    }

    public function sendEmails()
    {
        $message = "Hello Every one this is the method where we check the test mails";
        //Mail::to('sagr7188@gmail.com')->send(new SendMail($message));
        $issent = sendEmail("SMTP", "", "", "", "", 'test email', $message, "TestController -> SendEmails");
        return response($issent, 200);
    }

    public function syncOldRetsPropertyData()
    {
        $old_data = collect(DB::select("SELECT * FROM rets_property_data_mayank_sir"))->map(function ($item) {
            return collect($item)->all();
        })->all();
        $tempValue = [];
        foreach ($old_data as $data) {
            foreach ($data as $key => $value) {
                if ($key == "id") {
                    continue;
                } else {
                    if ($key == "mls_no") {
                        $tempValue[$key] = $value;
                    } else {
                        if ($key == "Vowautomatedvaluationdisplay") {
                            $tempValue["VowAutomatedValuationDisplay"] = $value;
                        } else {
                            $tempValue[Str::ucfirst(Str::camel($key))] = $value;
                        }
                    }
                }
            }
            // this is for mongo
            RetsPropertyData::create($tempValue);
            foreach ($tempValue as $key => $val) {
                if ($val == null || $key == "id") {
                    unset($tempValue[$key]);
                }
            }
            // this is for sql
            RetsPropertyDataSql::create($tempValue);
        }
        echo "<h1>Imported</h1>";
    }

    public function changeColumnNamesIntoCamel()
    {
        $old_data = collect(DB::select("SELECT * FROM rets_property_data_cs"))->map(function ($item) {
            return collect($item)->all();
        })->first();
        $old_data = collect($old_data)->map(function ($value, $key) {
            return Str::ucfirst(Str::camel($key));
        })->values();
        return response($old_data);
    }

    public function imageStore(Request $request)
    {
        if ($request->hasFile('image')) {

            $extension = $request->ReportDocument->extension();
            $name = $request->file('ReportDocument')->getClientOriginalName();
            $request->ReportDocument->storeAs('/public/img/', $name . "." . $extension);
            $url = Storage::url($name . "." . $extension);

            //Session::flash('success', "Success!");
            //return \Redirect::back();
        }

        dd("No");
    }

    public function store(Request $request)
    {

        $data = DB::update("UPDATE LeadAgentProfile set State='ZL' where ListAgentFullName = 'sagar' and State='FL' ");
        dd($data);
        $agent_name = "sagar";
        $data = DB::select("SELECT * FROM LeadAgentProfile where ListAgentFullName ='" . $agent_name . " ' ");
        $data = collect($data)->all();
        dd($data);

        updatePropAddressFilter("A11080096");
        dd(1);
        $properties = RetsPropertyDataSql::all();
        foreach ($properties as $property) {
            dd($property["id"]);
        }



        $agent_mlsid = 'nicolevantreese@gmail.com';
        $rs_mlsid = AssignmentModel::where("ListAgentEmail",  $agent_mlsid)->get();

        $q  = collect(
            RetsPropertyDataOffMarketLiteV4::where("src_id", 2)->groupBy("StandardAddressFull")
                ->where("StandardAddressFull", "<>", NULL)
                ->get()
        )->countBy("StandardAddressFull")->sortDesc()->keys()->first();
        $data = RetsPropertyDataOffMarketLiteV4::where("StandardAddressFull", "=", $q)
            ->get();


        dd($data);
        $ds = RetsPropertyDataOffMarketLiteV4::where("src_id", 2)
            ->where("_id", "6137b239de1470078c5a5977")
            ->delete();

        /*$q = DB::connection("mongodb")->table("RetsPropertyDataOffMarketLiteV4")
            ->select(DB::raw("SELECT count(*) as cnt , CustomAddress3, ParcelNumberUnformatted,
       ParcelNumber,FileDateFormatted,CaseNumber"))
            ->where("src_id","=",2)
            ->groupBy(["ParcelNumber","CaseNumber"])->get();
        return response($q,200);
        dd($q);*/
        /*$q = DB::connection("mongodb")
            ->select("SELECT count(*) as cnt , CustomAddress3, ParcelNumberUnformatted,
       ParcelNumber,FileDateFormatted,CaseNumber,$field FROM ".$this->bulk_table."
       where src_id=$src_id $cnd  group by $grp_txt having cnt>1 ORDER BY cnt DESC ");*/
        $src_id = 2;
        $case = 1;
        $q = RetsPropertyDataOffMarketLiteV4::where("src_id", $src_id);
        if ($src_id == 2 || $src_id == 42 || $src_id == 44 || $src_id == 45) {
            $field = 'AuctionListDateFormatted';
        } else if ($src_id == 43) {
            $field = 'JudgementSaleDateFormatted';
        } else {
            $field = 'FiledateFormatted';
        }
        if ($case == 1) {
            $grp_txt = " ParcelNumberUnformatted, CaseNumber,$field ";
            $cnd = " AND (ParcelNumberUnformatted is not NULL and ParcelNumberUnformatted<>'' AND CaseNumber IS NOT NULL AND CaseNumber <>'' AND $field IS NOT NULL AND $field<>'' ) ";
            $q = $q->where(function ($item) use ($field) {
                $item->where("ParcelNumberUnformatted", "<>", NULL)
                    ->where("ParcelNumberUnformatted", "<>", "")
                    ->where("CaseNumber", "<>", NULL)
                    ->where("CaseNumber", "<>", "")
                    ->where($field, "<>", NULL)
                    ->where($field, "<>", "");
            });
            if ($src_id == 43) {
                $grp_txt = " ParcelNumberUnformatted, CaseNumber";
                $cnd = " AND (ParcelNumberUnformatted is not NULL and ParcelNumberUnformatted<>'' AND CaseNumber IS NOT NULL AND CaseNumber <>'' ) ";
                $q = $q->where(function ($item) use ($field) {
                    $item->where("ParcelNumberUnformatted", "<>", NULL)
                        ->where("ParcelNumberUnformatted", "<>", "")
                        ->where("CaseNumber", "<>", NULL)
                        ->where("CaseNumber", "<>", "");
                });
            }
        } else if ($case == 2) {
            $grp_txt = " ParcelNumberUnformatted, $field ";
            $cnd = " AND (ParcelNumberUnformatted is not NULL and ParcelNumberUnformatted<>'' AND $field IS NOT NULL AND $field<>'' ) ";
            $q = $q->where(function ($item) use ($field) {
                $item->where("ParcelNumberUnformatted", "<>", NULL)
                    ->where("ParcelNumberUnformatted", "<>", "")
                    ->where($field, "<>", NULL)
                    ->where($field, "<>", "");
            });
            if ($src_id == 43) {
                $grp_txt = " ParcelNumberUnformatted";
                $cnd = " AND (ParcelNumberUnformatted is not NULL and ParcelNumberUnformatted<>'' ) ";
                $q = $q->where(function ($item) {
                    $item->where("ParcelNumberUnformatted", "<>", NULL)
                        ->where("ParcelNumberUnformatted", "<>", "");
                });
            }
        } else if ($case == 3) {
            $grp_txt = " ParcelNumberUnformatted ";
            $cnd = " AND (ParcelNumberUnformatted is not NULL and ParcelNumberUnformatted<>'' )";
            $q = $q->where(function ($item) {
                $item->where("ParcelNumberUnformatted", "<>", NULL)
                    ->where("ParcelNumberUnformatted", "<>", "");
            });
        }
        //echo "<br>Merging By Parcel No";
        $dbf = array("Motivation", "Folder", "Prospect", "HotProspectPoints", "LeadSource", "AdjustedSF", "AgValue", "ArchitecturalStyle", "AssociationFee", "AssociationYN", "BathroomsFull", "BathroomsHalf", "BedroomsTotal", "BuildingDescription", "BuildingUseCode", "BuildingValue", "BuildingName", "CapRate", "CarportSpaces", "City", "Construction", "ConstructionMaterials", "Country", "County", "CustomAddress", "CustomAddress2", "CustomAddress3", "UnitNumber", "Customaddressfull", "CustomPropertyType", "EffectiveYearBuilt", "FullMailingAddress", "Garage", "GarageSpaces", "GatedCommunityYN", "GrossOperatingIncome", "HasGarage", "LandUse", "LandUseCode", "LandUseDescription", "Latitude", "LegalUnit", "legalDescription", "LivingArea", "Lot", "LotDepth", "LotFrontage", "LotNumber", "lotsize", "lotsizeacres", "ParcelNumber", "ParkingTotal", "ParkingFeatures", "PostalCode", "PropertyAddressLine2", "PropertyClassification", "PropertySqFt", "PropertyType", "PropertyUseCode", "PropertyUseDescription", "PropertyZipplus4", "PropertySubType", "REO", "SaleorRent", "ShortSale", "StandardAddress", "StandardAddressFull", "StandardPropType", "StateOrProvince", "Stories", "StreetDirPrefix", "StreetDirSuffix", "StreetName", "StreetNumber", "StreetSuffix", "StructuralDescription", "Style", "SubdivisionNumber", "SubdivisionName", "TypeofProperty", "UnitType", "UnparsedAddress", "Waterfront", "WaterfrontType", "YearBuilt", "Zoning", "DateofDeath", "DaysOnMarket", "DeceasedFirstName", "DeceasedLastAddress", "DeceasedLastCity", "DeceasedLastName", "DeceasedLastState", "DeceasedLastZip", "FirstName", "LastName", "FirstName2", "LastName2", "OwnerCity", "OwnerCountry", "OwnerCareOf", "OwnerMailingAddress", "OwnerMailingCity", "OwnerMailingState", "OwnerMailingZip", "OwnerMailingZip_4", "OwnerName", "OwnerName2", "OwnerPhone", "Phone", "PhoneDNCStatus", "PhoneLabel", "PhoneType", "Phone2", "Phone2DNCStatus", "Phone2Label", "Phone2Type", "Phone3", "Phone3DNCStatus", "Phone3Label", "Phone3Type", "Phone4", "Phone4DNCStatus", "Phone4Label", "Phone4Type", "Phone5", "Phone5DNCStatus", "Phone5Label", "Phone5Type", "PhoneContact", "PhoneNumber", "Email", "EmailStatus", "TaxRollYear", "TaxState", "ExpiredDate", "ListDate", "ListAgentFormattedName", "ListAgentMlsId", "ListAOR", "ListingId", "ListOfficeMlsId", "ListOfficeName", "ListPrice", "mls", "MlsStatus", "ModificationTimestamp", "NetOperatingIncome", "OfficeLeadType", "OwnerPays", "PendingDate", "PublicRemarks", "PublicRemarksFiltered", "ReasonForCancellation", "RentDate", "SaleTerms", "SoldDate", "SoldPrice", "StatusChangeDate", "TaxYear", "VirtualTourURLBranded", "WithdrawnDate", "YYYYYY", "AddDate", "AttorneyAddress", "AttorneyCity", "AttorneyFirstName", "AttorneyLastName", "AttorneyName", "AttorneyOfficePhone", "AttorneyPhoneNumber", "AttorneyState", "AttorneyZip", "Auction", "Balance", "Bid", "Bidder", "CaseID", "CaseNumber", "CertificateHolder", "CertificateNumber", "DateAdded", "Defendant1", "Defendant2", "Deposit", "Doc", "DocketNumber", "FileDate", "FinalBid", "FinalJudgment", "JudgementAmount", "JudgementDate", "JudgementEquity", "JudgementSaleDate", "JudgementSaleResult", "Lien3Amount", "Lien4Amount", "Lien4Holder", "Name", "OccupantPhoneNumber", "OpeningBid", "PlaintiffMaxBid", "PrimaryPlaintiff", "ProbateDate", "SaleDate", "TotalDue", "WinningBid", "Plaintiff2", "Plaintiff3", "Plaintiff4", "Plaintiff5", "Plaintiff6", "Plaintiff7", "SummonsAddress", "SummonsName", "AssessedValue", "Date", "DeedType", "Grantor", "HomesteadedProperty", "InOpportunityZone", "JustLandValue", "JustMarketValue", "JustValue", "LandValue", "LastSale", "LastSaleAmount", "LastSaleDate", "LastSalePrice", "Mortgage1Amount", "Mortgage1Balance", "Mortgage1Book", "Mortgage1Date", "Mortgage1InterestRate", "Mortgage1Page", "Mortgage1RateType", "Mortgage1Type", "Mortgage2Amount", "Mortgage2Balance", "Mortgage2Book", "Mortgage2Date", "Mortgage2InterestRate", "Mortgage2RateType", "Mortgage2Type", "MostRecentSaleDate", "MostRecentSalePrice", "NeighborhoodCode", "OpportunityZoneDescription", "OpportunityZoneDescriptionPropertyAddress", "PPSF", "PreviousSaleDate", "PreviousSalePrice", "TotalArea", "TotalAssessedValue", "TotalExemptAmount", "TotalImprovedValue", "TotalTaxableValue", "TotalTaxes", "TotalValue", "admin", "AgentLeadType", "ApnNumber", "AtomApiData", "Dup", "GeocodeTried", "Headline", "Image", "Images", "InternalID", "LandingPageUrl", "last", "ListingKeyNumeric", "property", "PropertyDescription", "SyndaPropType", "Tobe", "updated", "Longitude", "created_at", "updated_at");
        $dbf2 = array("Motivation", "Folder", "Prospect", "HotProspectPoints", "LeadSource", "AdjustedSF", "AgValue", "ArchitecturalStyle", "AssociationFee", "AssociationYN", "BathroomsFull", "BathroomsHalf", "BedroomsTotal", "BuildingDescription", "BuildingUseCode", "BuildingValue", "BuildingName", "CapRate", "CarportSpaces", "City", "Construction", "ConstructionMaterials", "Country", "County", "CustomAddress", "CustomAddress2", "CustomAddress3", "UnitNumber", "Customaddressfull", "CustomPropertyType", "EffectiveYearBuilt", "FullMailingAddress", "Garage", "GarageSpaces", "GatedCommunityYN", "GrossOperatingIncome", "HasGarage", "LandUse", "LandUseCode", "LandUseDescription", "Latitude", "LegalUnit", "legalDescription", "LivingArea", "Lot", "LotDepth", "LotFrontage", "LotNumber", "lotsize", "lotsizeacres", "ParcelNumber", "ParkingTotal", "ParkingFeatures", "PostalCode", "PropertyAddressLine2", "PropertyClassification", "PropertySqFt", "PropertyType", "PropertyUseCode", "PropertyUseDescription", "PropertyZipplus4", "PropertySubType", "REO", "SaleorRent", "ShortSale", "StandardAddress", "StandardAddressFull", "StandardPropType", "StateOrProvince", "Stories", "StreetDirPrefix", "StreetDirSuffix", "StreetName", "StreetNumber", "StreetSuffix", "StructuralDescription", "Style", "SubdivisionNumber", "SubdivisionName", "TypeofProperty", "UnitType", "UnparsedAddress", "Waterfront", "WaterfrontType", "YearBuilt", "Zoning", "DateofDeath", "DaysOnMarket", "DeceasedFirstName", "DeceasedLastAddress", "DeceasedLastCity", "DeceasedLastName", "DeceasedLastState", "DeceasedLastZip", "FirstName", "LastName", "FirstName2", "LastName2", "OwnerCity", "OwnerCountry", "OwnerCareOf", "OwnerMailingAddress", "OwnerMailingCity", "OwnerMailingState", "OwnerMailingZip", "OwnerMailingZip_4", "OwnerName", "OwnerName2", "OwnerPhone", "Phone", "PhoneDNCStatus", "PhoneLabel", "PhoneType", "Phone2", "Phone2DNCStatus", "Phone2Label", "Phone2Type", "Phone3", "Phone3DNCStatus", "Phone3Label", "Phone3Type", "Phone4", "Phone4DNCStatus", "Phone4Label", "Phone4Type", "Phone5", "Phone5DNCStatus", "Phone5Label", "Phone5Type", "PhoneContact", "PhoneNumber", "Email", "EmailStatus", "TaxRollYear", "TaxState", "ExpiredDate", "ListDate", "ListAgentFormattedName", "ListAgentMlsId", "ListAOR", "ListingId", "ListOfficeMlsId", "ListOfficeName", "ListPrice", "mls", "MlsStatus", "ModificationTimestamp", "NetOperatingIncome", "OfficeLeadType", "OwnerPays", "PendingDate", "PublicRemarks", "PublicRemarksFiltered", "ReasonForCancellation", "RentDate", "SaleTerms", "SoldDate", "SoldPrice", "StatusChangeDate", "TaxYear", "VirtualTourURLBranded", "WithdrawnDate", "YYYYYY", "AddDate", "AttorneyAddress", "AttorneyCity", "AttorneyFirstName", "AttorneyLastName", "AttorneyName", "AttorneyOfficePhone", "AttorneyPhoneNumber", "AttorneyState", "AttorneyZip", "Auction", "Balance", "Bid", "Bidder", "CaseID", "CaseNumber", "CertificateHolder", "CertificateNumber", "DateAdded", "Defendant1", "Defendant2", "Deposit", "Doc", "DocketNumber", "FileDate", "FinalBid", "FinalJudgment", "JudgementAmount", "JudgementDate", "JudgementEquity", "JudgementSaleDate", "JudgementSaleResult", "Lien3Amount", "Lien4Amount", "Lien4Holder", "Name", "OccupantPhoneNumber", "OpeningBid", "PlaintiffMaxBid", "PrimaryPlaintiff", "ProbateDate", "SaleDate", "TotalDue", "WinningBid", "Plaintiff2", "Plaintiff3", "Plaintiff4", "Plaintiff5", "Plaintiff6", "Plaintiff7", "SummonsAddress", "SummonsName", "AssessedValue", "Date", "DeedType", "Grantor", "HomesteadedProperty", "InOpportunityZone", "JustLandValue", "JustMarketValue", "JustValue", "LandValue", "LastSale", "LastSaleAmount", "LastSaleDate", "LastSalePrice", "Mortgage1Amount", "Mortgage1Balance", "Mortgage1Book", "Mortgage1Date", "Mortgage1InterestRate", "Mortgage1Page", "Mortgage1RateType", "Mortgage1Type", "Mortgage2Amount", "Mortgage2Balance", "Mortgage2Book", "Mortgage2Date", "Mortgage2InterestRate", "Mortgage2RateType", "Mortgage2Type", "MostRecentSaleDate", "MostRecentSalePrice", "NeighborhoodCode", "OpportunityZoneDescription", "OpportunityZoneDescriptionPropertyAddress", "PPSF", "PreviousSaleDate", "PreviousSalePrice", "TotalArea", "TotalAssessedValue", "TotalExemptAmount", "TotalImprovedValue", "TotalTaxableValue", "TotalTaxes", "TotalValue", "admin", "AgentLeadType", "ApnNumber", "AtomApiData", "Dup", "GeocodeTried", "Headline", "Image", "Images", "InternalID", "LandingPageUrl", "last", "ListingKeyNumeric", "property", "PropertyDescription", "SyndaPropType", "Tobe", "updated", "Longitude", "created_at", "updated_at");
            /*$q = DB::connection("mongodb")
            ->select("SELECT count(*) as cnt , CustomAddress3,
       ParcelNumberUnformatted,ParcelNumber,FileDateFormatted,CaseNumber,$field FROM ".$this->bulk_table."
        where src_id=$src_id $cnd  group by $grp_txt having cnt>1 ORDER BY cnt DESC ")*/;

        //$q = $q->groupBy($grp_txt)->get();
        //dd($grp_txt);
        $q = collect(
            $q->groupBy($grp_txt)
                ->get()
        )->countBy($grp_txt)->sortDesc()->keys()->first();
        $data = RetsPropertyDataOffMarketLiteV4::where("ParcelNumber", "=", $q)
            ->get();
        return response($data, 200);






        $q = collect(
            RetsPropertyDataOffMarketLiteV4::where("src_id", 2)
                ->groupBy(["ParcelNumber", "CaseNumber"])
                ->get()
        )->countBy("ParcelNumber")->sortDesc()->keys()->first();

        $data = RetsPropertyDataOffMarketLiteV4::where("ParcelNumber", "=", $q)
            ->where("CaseNumber", "=", $q)
            ->get();
        // ;



        return response($data, 200);
        $data = collect(RetsPropertyDataOffMarketLiteV4::where("id", 13740)->first())->keys()->all();
        return response($data, 200);
        $test = new TestClass($request);
        dd($test->mapping);




        $old_data = collect(DB::select("SELECT * FROM parser"))->map(function ($item) {
            return collect($item)->all();
        })->all();
        $tempValue = [];
        foreach ($old_data as $data) {
            foreach ($data as $key => $value) {
                if ($key == "id") {
                    continue;
                } else {
                    $tempValue[Str::ucfirst(Str::camel($key))] = $value;
                }
            }

            // this is for mongo
            Parser::create($tempValue);
        }


        /*$old_data = collect($old_data)->flatMap(function ($item) use() {
           return collect($item)->map(function ($value,$key){
                if ($key != "mls_no" && $key != "id"){
                    $val =  [\Illuminate\Support\Str::ucfirst(\Illuminate\Support\Str::camel($key)) => $value];
                }
                return $val;
            })->all();
        })->all();*/
        $tempValue = [];
        foreach ($old_data as $data) {
            foreach ($data as $key => $value) {
                if ($key == "id") {
                    continue;
                } else {
                    if ($key == "mls_no") {
                        $tempValue[$key] = $value;
                    } else {
                        if ($key == "Vowautomatedvaluationdisplay") {
                            $tempValue["VowAutomatedValuationDisplay"] = $value;
                        } else {
                            $tempValue[Str::ucfirst(Str::camel($key))] = $value;
                        }
                    }
                }
            }
            // this is for mongo
            RetsPropertyData::create($tempValue);
            foreach ($tempValue as $key => $val) {
                if ($val == null || $key == "id") {
                    unset($tempValue[$key]);
                }
            }
            // this is for sql
            RetsPropertyDataSql::create($tempValue);
        }
        dd(1);
        $mls_nums   = '';

        $mls_nums[] .= implode(",", [1, 2, 3, 4]);
        dd($mls_nums);





        $data = collect(RetsPropertyDataRentedSql::where("id", 1)->first())->keys()->all();
        return response($data, 200);



        $array = [
            "NumberOfPets",
            "BathroomsFull",
            "BathroomsHalf",
            "BuildingName",
            "BathroomsTotalInteger",
            "BedroomsTotal",
            "City",
            "DaysOnMarket",
            "Furnished",
            "ListAgentMlsId",
            "ListingId",
            "ListPrice",
            "LivingArea",
            "MlsStatus",
            "ModificationTimestamp",
            "ParcelNumber",
            "ParkingFeatures",
            "PetsAllowed",
            "RentIncludes",
            "RentalDepositIncludes",
            "IDXOptInYN",
            "InternetYN",
            "PoolPrivateYN",
            "PostalCode",
            "PrivateRemarks",
            "PropertyDescription",
            "PropertySubType",
            "PropertyType",
            "TypeofProperty",
            "PublicRemarks",
            "ShowingRequirements",
            "StateOrProvince",
            "StreetDirPrefix",
            "StreetDirSuffix",
            "StreetName",
            "StreetNumber",
            "StreetSuffix",
            "County",
            "SubdivisionName",
            "TaxYear",
            "UnitNumber",
            "Utilities",
            "VirtualTourURLBranded",
            "WaterExtrasYN",
            "YearBuilt",
            "ListOfficeMlsId",
            "ListOfficeName",
            "ListOfficePhone",
            "ListAgentFullName",
            "ListAgentDirectPhone",
            "ListAgentEmail",
            "CoListAgentMlsId",
            "CoListAgentFullName",
            "CoListAgentDirectPhone",
            "CoListAgentEmail",
            "ListAOR",
            "ListingKeyNumeric",
            "Appliances",
            "ArchitecturalStyle",
            "AssociationAmenities",
            "AssociationFee",
            "AssociationFeeFrequency",
            "AssociationFeeIncludes",
            "GarageSpaces",
            "AvailabilityDate",
            "BuyerAgencyCompensation",
            "NetOperatingIncome",
            "CarportSpaces",
            "ConstructionMaterials",
            "Cooling",
            "BuyerAgencyCompensationType",
            "TransactionBrokerCompensation",
            "TransactionBrokerCompensationType",
            "LeaseRenewalCompensation",
            "SubAgencyCompensation",
            "SubAgencyCompensationType",
            "PostalCodePlus4",
            "ClosePrice",
            "BuildingAreaTotal",
            "LotSizeAcres",
            "MIAMIRE_RATIO_CurrentPrice_By_SQFT"
        ];

        $array2 = [
            "id",
            "StreetDirPrefix",
            "CountryRegion",
            "OtherParking",
            "BuildingName",
            "CoBuyerAgentStateLicense",
            "PropertyType",
            "MlsStatus",
            "PropertySubTypeAdditional",
            "StreetNumber",
            "ParkingTotal",
            "BuyerAgentStateLicense",
            "PropertySubType",
            "BathroomsFull",
            "LotSizeAcres",
            "SubdivisionName",
            "InternetAddressDisplayYN",
            "StateRegion",
            "BathroomsPartial",
            "PreviousListPrice",
            "StreetNumberNumeric",
            "PostalCodePlus4",
            "BuildingAreaSource",
            "BathroomsOneQuarter",
            "BuilderModel",
            "CoListAgentMlsId",
            "BedroomsPossible",
            "ListingId",
            "BathroomsTotalInteger",
            "BuildingAreaUnits",
            "City",
            "ListAgentNameSuffix",
            "BuildingAreaTotal",
            "BedroomsTotal",
            "CoListAgentDirectPhone",
            "Longitude",
            "PublicRemarks",
            "PostalCity",
            "CoListOfficeName",
            "ListOfficeName",
            "Latitude",
            "ListPrice",
            "CoListOfficeMlsId",
            "StateOrProvince",
            "BathroomsThreeQuarter",
            "MainLevelBathrooms",
            "CoBuyerAgentMlsId",
            "StreetSuffix",
            "ListAgentMlsId",
            "CoListAgentNameSuffix",
            "CoBuyerOfficeMlsId",
            "ListAgentNamePrefix",
            "Country",
            "UnitNumber",
            "ListOfficeMlsId",
            "ListAgentDirectPhone",
            "BathroomsHalf",
            "ListAgentStateLicense",
            "StreetName",
            "MainLevelBedrooms",
            "CityRegion",
            "BuyerAgentMlsId",
            "PostalCode",
            "BuyerOfficeMlsId",
            "LotSizeSquareFeet",
            "UnparsedAddress",
            "inserted_time",
            "updated_time",
            "image_downloaded",
            "image_downloaded_time",
            "image_download_tried",
            "image_aws_sync",
            "Heating",
            "Cooling",
            "PoolFeatures",
            "ClosePrice",
            "LotSizeArea",
            "WaterfrontYN",
            "PoolPrivateYN",
            "mls_no",
            "ListingKeyNumeric",
            "Furnished",
            "Address",
            "FullAddress",
            "SimpleAddress",
            "LivingArea",
            "PrivateRemarks",
            "ImagesUrls",
            "County"
        ];
        $array4 = [];
        foreach ($array as $arr) {
            if (in_array($arr, $array2)) {
            } else {
                $array4[] = "unset(property_data['" . $arr . "'])";
            }
        }

        /*$array3 = array_merge($array,$array2);
        $array3 = array_unique($array3);*/

        return response($array4, 200);
    }
    public function syncBrokerAgent()
    {
        $sql_data = DB::table("ContractLog")->select("*")->get();
        $sql_data = collect($sql_data)->all();
        foreach ($sql_data as $sql_datum) {
            $response_data = ContractLogModel::create(collect($sql_datum)->all());
        }
    }

    public function testOutedFile()
    {
    }
    public function createPDF()
    {
        $pdf = new Fpdi();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage("L", ['100', '100']);
        $pdf->Text(10, 10, "Hello FPDF");
        $pdf->Output();
        exit;
        //        return '<embed src="'.public_path("assets/uploads/contracts/includes/ctl.pdf").'#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" frameBorder="0" scrolling="auto" height="100%" width="100%"></embed>';
        //        return public_path('assets/uploads/contracts/includes/ctl.pdf');
        //        $a='<a href="'.public_path("assets/uploads/contracts/includes/ctl.pdf").'"></a>';
        //        return $a;
    }

    public function syncOldDataBaseToNewDatabase()
    {
        $old_data = collect(DB::select("SELECT * FROM soldproperties_cron_log"))->map(function ($item) {
            return collect($item)->all();
        })->all();
        $tempValue = [];
        foreach ($old_data as $data) {
            foreach ($data as $key => $value) {
                if ($key == "id") {
                    continue;
                } else {
                    if ($key == "mls_no") {
                        $tempValue[$key] = $value;
                    } else {
                        if ($key == "Vowautomatedvaluationdisplay") {
                            $tempValue["VowAutomatedValuationDisplay"] = $value;
                        } else {
                            $tempValue[Str::ucfirst(Str::camel($key))] = $value;
                        }
                    }
                }
            }
            // this is for sql
            SoldPropertiesCronLogSql::create($tempValue);
        }
        echo "<h1>Imported</h1>";
    }

    public function storeTest()
    {
        echo "Hello World";
    }
    public function AddBrocker()
    {
        AssignmentModel::truncate();
        $pro = RetsPropertyDataSql::limit(30)->get();
        if (isset($pro) && !empty($pro)) {
            foreach ($pro as $row) {
                $data['mls_no'] = 1;
                $data['ListAOR'] = "Orlando Regional";
                $data['AdvType'] = "ASA";
                $data['PropertyTypeMix'] = $row->PropertyType;
                $data['ListAgentMlsId'] = $row->ListAgentMlsId;
                $data['ListAgentFullName'] = $row->ListAgentFullName;
                $data['ListAgentDirectPhone'] = $row->ListAgentDirectPhone;
                $data['ListAgentEmail'] = $row->ListAgentEmail;
                $data['PostalCode'] = $row->PostalCode;
                $data['TotalListingBrokerage'] = 1;
                $data['TotalListingAgent'] = 1;
                $data['OfficeLeadType'] = 'Co-Broke';
                $data['AgentLeadType'] = 'Co-Broke';
                $data['City'] = $row->City;
                //                $data['ListOfficeKeyNumeric'] = $row->ListAgentMlsId;
                //                $data['ListOfficeMlsId'] = $row->ListOfficeMlsId;
                //                $data['ListOfficeName'] = $row->ListOfficeName;
                //                $data['ListOfficePhone'] = $row->ListAgentDirectPhone;
                $dataadd = AssignmentModel::Create($data);
            }
            if ($dataadd) {
                echo "Brocker data Added!";
            }
        }
    }

    public function createData()
    {
        $sql_data_rpd = RetsPropertyDataResi::all();
        $property_inserted_rets_property_data = 0;
        foreach ($sql_data_rpd as $dataum) {
            $dataum = collect($dataum)->all();
            $dataum["City"] = $dataum["County"];
            $dataum["StandardAddress"] = $dataum["Addr"];
            $dataum["Corp_num"] = "";
            $dataum["Ens_lndry"] = "";
            $dataum["MlsStatus"] = $dataum["Status"];
            $dataum["StreetName"] = "";
            $dataum["StreetDirPrefix"] = "";
            $dataum["Stories"] = "";
            $dataum["VirtualTourURLBranded"] = "";
            $dataum["PropertySubType"] = "";
            $dataum["PostalCode"] = $dataum["Zip"];
            $dataum["BedroomsTotal"] = $dataum["Br"];
            $dataum["ListPrice"] = $dataum["Lp_dol"];
            $dataum["ListingId"] = $dataum["Ml_num"];
            $dataum["BathroomsFull"] = $dataum["Bath_tot"];
            $dataum["PropertyType"] = "Residential";
            $dataum["StreetNumber"] = "";
            $dataum["PropertySubType"] = $dataum["Type_own1_out"];
            $dataum["ShortPrice"] = number_format_short($dataum['Lp_dol']);
            RetsPropertyDataSql::where("ListingId", $dataum["ListingId"])->update(["ListPrice" => $dataum["ListPrice"], "PropertySubType" => $dataum["PropertySubType"], "ShortPrice" => $dataum["ShortPrice"]]);
            //RetsPropertyDataSql::create($dataum);
            $property_inserted_rets_property_data++;
            echo "\n property inserted for rets property data resi = " . $property_inserted_rets_property_data;
        }

        // commercial
        $sql_data_rpd = RetsPropertyDataComm::all();
        $property_inserted_rets_property_data = 0;
        foreach ($sql_data_rpd as $dataum) {
            $dataum = collect($dataum)->all();
            $dataum["City"] = $dataum["County"];
            $dataum["StandardAddress"] = $dataum["Addr"];
            $dataum["Corp_num"] = "";
            $dataum["Ens_lndry"] = "";
            $dataum["MlsStatus"] = $dataum["Status"];
            $dataum["StreetName"] = "";
            $dataum["StreetDirPrefix"] = "";
            $dataum["Stories"] = "";
            $dataum["VirtualTourURLBranded"] = "";
            $dataum["PropertySubType"] = "";
            $dataum["PostalCode"] = $dataum["Zip"];
            $dataum["BedroomsTotal"] = 0;
            $dataum["ListPrice"] = $dataum["Lp_dol"];
            $dataum["ListingId"] = $dataum["Ml_num"];
            $dataum["BathroomsFull"] = $dataum["Bath_tot"];
            $dataum["PropertyType"] = "Commercial";
            $dataum["StreetNumber"] = "";
            $dataum["PropertySubType"] = $dataum["Type_own1_out"];
            $dataum["ShortPrice"] = number_format_short($dataum['Orig_dol']);
            RetsPropertyDataSql::where("ListingId", $dataum["ListingId"])->update(["ListPrice" => $dataum["ListPrice"], "PropertySubType" => $dataum["PropertySubType"], "ShortPrice" => $dataum["ShortPrice"]]);
            $property_inserted_rets_property_data++;
            echo "\n property inserted for rets property data comm = " . $property_inserted_rets_property_data;
        }

        // condo
        $sql_data_rpd = RetsPropertyDataCondo::all();
        $property_inserted_rets_property_data = 0;
        foreach ($sql_data_rpd as $dataum) {
            $dataum = collect($dataum)->all();
            $dataum["City"] = $dataum["County"];
            $dataum["StandardAddress"] = $dataum["Addr"];
            $dataum["Corp_num"] = "";
            $dataum["Ens_lndry"] = "";
            $dataum["MlsStatus"] = $dataum["Status"];
            $dataum["StreetName"] = "";
            $dataum["StreetDirPrefix"] = "";
            $dataum["Stories"] = "";
            $dataum["VirtualTourURLBranded"] = "";
            $dataum["PropertySubType"] = "";
            $dataum["PostalCode"] = $dataum["Zip"];
            $dataum["BedroomsTotal"] = 0;
            $dataum["ListPrice"] = $dataum["Lp_dol"];
            $dataum["ListingId"] = $dataum["Ml_num"];
            $dataum["BathroomsFull"] = $dataum["Bath_tot"];
            $dataum["PropertyType"] = "Condo";
            $dataum["StreetNumber"] = "";
            $dataum["PropertySubType"] = $dataum["Type_own1_out"];
            $dataum["ShortPrice"] = number_format_short($dataum['Orig_dol']);
            RetsPropertyDataSql::where("ListingId", $dataum["ListingId"])->update(["ListPrice" => $dataum["ListPrice"], "PropertySubType" => $dataum["PropertySubType"], "ShortPrice" => $dataum["ShortPrice"]]);
            $property_inserted_rets_property_data++;
            echo "\n property inserted for rets property data condo = " . $property_inserted_rets_property_data;
        }
    }

    public function createDataForMongo()
    {
        $sql_data_rpd = RetsPropertyDataImage::all();
        $property_inserted_rets_property_data = 0;
        foreach ($sql_data_rpd as $data) {
            $data = collect($data)->all();
            RetsPropertyDataImageMongo::create($data);
            $property_inserted_rets_property_data++;
            echo "\n property inserted for rets property image data = " . $property_inserted_rets_property_data;
        }
        $sql_data_rpd = RetsPropertyDataSql::all();
        $property_inserted_rets_property_data = 0;
        $property_inserted_rets_property_data_resi = 0;
        $property_inserted_rets_property_data_comm = 0;
        $property_inserted_rets_property_data_condo = 0;
        foreach ($sql_data_rpd as $data) {
            $data = collect($data)->all();
            RetsPropertyDataMongo::create($data);
            $property_inserted_rets_property_data++;
            echo "\n property inserted for rets property data = " . $property_inserted_rets_property_data;
        }
        $sql_data_rpd_resi = RetsPropertyDataResi::all();
        foreach ($sql_data_rpd_resi as $data) {
            $data = collect($data)->all();
            RetsPropertyDataResiMongo::create($data);
            $property_inserted_rets_property_data_resi++;
            echo "\n property inserted for resi = " . $property_inserted_rets_property_data_resi;
        }
        $sql_data_rpd_condo = RetsPropertyDataCondo::all();
        foreach ($sql_data_rpd_condo as $data) {
            $data = collect($data)->all();
            RetsPropertyDataCondoMongo::create($data);
            $property_inserted_rets_property_data_condo++;
            echo "\n property inserted for condo = " . $property_inserted_rets_property_data_condo;
        }
        $sql_data_rpd_comm = RetsPropertyDataComm::all();
        foreach ($sql_data_rpd_comm as $data) {
            $data = collect($data)->all();
            RetsPropertyDataCommMongo::create($data);
            $property_inserted_rets_property_data_comm++;
            echo "\n property inserted for comm = " . $property_inserted_rets_property_data_comm;
        }
    }

    public function updateImagesSql()
    {


        $datas = \App\Models\RetsPropertyData::all();
        $property_inserted = 0;
        $propCount = 0;
        foreach ($datas as $data) {
            $property_inserted++;
            $propCount++;
            if ($propCount == 200) {
                echo "\n in sleep";
                sleep(3);
                $propCount = 0;
            }
            $image = RetsPropertyDataImage::where("listingID", $data["ListingId"])->first();
            $image = collect($image)->all();
            if (count($image) > 0) {
                //\App\Models\RetsPropertyData::where("ListingId",$data["ListingId"])->update(["ImageUrl" => $image["s3_image_url"]]);
                DB::update("UPDATE rets_property_data SET `ImageUrl` = '" . $image["s3_image_url"] . "' WHERE `ListingId` = '" . $data["ListingId"] . "'");
                echo "\n updated for mls no = " . $data["ListingId"] . " and total count = " . $property_inserted;
            } else {
                echo "\n image not found";
            }
        }

        $datas = RetsPropertyDataMongo::all();
        $property_inserted = 0;
        $propCount = 0;
        foreach ($datas as $data) {
            $property_inserted++;
            $propCount++;
            if ($propCount == 200) {
                echo "\n in sleep";
                sleep(3);
                $propCount = 0;
            }
            $image = RetsPropertyDataImageMongo::where("listingID", $data["ListingId"])->first();
            $image = collect($image)->all();
            if (count($image) > 0) {
                RetsPropertyDataMongo::where("ListingId", $data["ListingId"])->update(["ImageUrl" => $image["s3_image_url"]]);
                echo "\n Mongo updated for mls no = " . $data["ListingId"] . " and total count = " . $property_inserted;
            } else {
                echo "\n image not found";
            }
        }
    }

    public function shortPriceAndPropertiesStatus()
    {
        $datas = \App\Models\RetsPropertyData::all();
        $property_inserted = 0;
        foreach ($datas as $data) {
            $data = collect($data)->all();
            $data["ShortPrice"] = number_format_short($data['ListPrice']);
            if ($data["PropertyType"] == "Residential") {
                $data_resi = RetsPropertyDataResi::where("Ml_num", $data["ListingId"])->first();
                $data_resi = collect($data_resi)->all();
                $data["PropertyStatus"] = $data_resi["S_r"];
                $data["City"] = str_replace("'", '', $data_resi["Municipality"]);
            }
            if ($data["PropertyType"] == "Commercial") {
                $data_resi = RetsPropertyDataComm::where("Ml_num", $data["ListingId"])->first();
                $data_resi = collect($data_resi)->all();
                $data["PropertyStatus"] = $data_resi["S_r"];
                $data["City"] = str_replace("'", '', $data_resi["Municipality"]);;
            }
            if ($data["PropertyType"] == "Condo") {
                $data_resi = RetsPropertyDataCondo::where("Ml_num", $data["ListingId"])->first();
                $data_resi = collect($data_resi)->all();
                $data["PropertyStatus"] = $data_resi["S_r"];
                $data["City"] = str_replace("'", '', $data_resi["Municipality"]);;
            }
            $property_inserted++;
            $query = "UPDATE rets_property_data SET `ShortPrice` = '" . $data["ShortPrice"] . "',`PropertyStatus`='" . $data["PropertyStatus"] . "',`City`='" . $data["City"] . "' WHERE `ListingId` = '" . $data["ListingId"] . "'";
            DB::update($query);
            echo "\n updated for mls no = " . $data["ListingId"] . " and total count = " . $property_inserted;
        }
    }

    public function updateRoomsData()
    {
        $datas = RetsPropertyDataResi::all();
        $property_updated = 0;
        foreach ($datas as $data) {
            $data = collect($data)->all();
            $data["RoomsDescription"] = [
                "Rm1_out" => $data["Rm1_out"],
                "Rm1_wth" => $data["Rm1_wth"],
                "Rm1_len" => $data["Rm1_len"],
                "Rm1_dc1_out" => $data["Rm1_dc1_out"],
                "Rm1_dc2_out" => $data["Rm1_dc2_out"],
                "Rm1_dc3_out" => $data["Rm1_dc3_out"],
                "Rm2_out" => $data["Rm2_out"],
                "Rm2_wth" => $data["Rm2_wth"],
                "Rm2_len" => $data["Rm2_len"],
                "Rm2_dc1_out" => $data["Rm2_dc1_out"],
                "Rm2_dc2_out" => $data["Rm2_dc2_out"],
                "Rm2_dc3_out" => $data["Rm2_dc3_out"],
                "Rm3_out" => $data["Rm3_out"],
                "Rm3_wth" => $data["Rm3_wth"],
                "Rm3_len" => $data["Rm3_len"],
                "Rm3_dc1_out" => $data["Rm3_dc1_out"],
                "Rm3_dc2_out" => $data["Rm3_dc2_out"],
                "Rm3_dc3_out" => $data["Rm3_dc3_out"],
                "Rm4_out" => $data["Rm4_out"],
                "Rm4_wth" => $data["Rm4_wth"],
                "Rm4_len" => $data["Rm4_len"],
                "Rm4_dc1_out" => $data["Rm4_dc1_out"],
                "Rm4_dc2_out" => $data["Rm4_dc2_out"],
                "Rm4_dc3_out" => $data["Rm4_dc3_out"],
                "Rm5_out" => $data["Rm5_out"],
                "Rm5_wth" => $data["Rm5_wth"],
                "Rm5_len" => $data["Rm5_len"],
                "Rm5_dc1_out" => $data["Rm5_dc1_out"],
                "Rm5_dc2_out" => $data["Rm5_dc2_out"],
                "Rm5_dc3_out" => $data["Rm5_dc3_out"],
                "Rm6_out" => $data["Rm6_out"],
                "Rm6_wth" => $data["Rm6_wth"],
                "Rm6_len" => $data["Rm6_len"],
                "Rm6_dc1_out" => $data["Rm6_dc1_out"],
                "Rm6_dc2_out" => $data["Rm6_dc2_out"],
                "Rm6_dc3_out" => $data["Rm6_dc3_out"],
                "Rm7_out" => $data["Rm7_out"],
                "Rm7_wth" => $data["Rm7_wth"],
                "Rm7_len" => $data["Rm7_len"],
                "Rm7_dc1_out" => $data["Rm7_dc1_out"],
                "Rm7_dc2_out" => $data["Rm7_dc2_out"],
                "Rm7_dc3_out" => $data["Rm7_dc3_out"],
                "Rm8_out" => $data["Rm8_out"],
                "Rm8_wth" => $data["Rm8_wth"],
                "Rm8_len" => $data["Rm8_len"],
                "Rm8_dc1_out" => $data["Rm8_dc1_out"],
                "Rm8_dc2_out" => $data["Rm8_dc2_out"],
                "Rm8_dc3_out" => $data["Rm8_dc3_out"],
                "Rm9_out" => $data["Rm9_out"],
                "Rm9_wth" => $data["Rm9_wth"],
                "Rm9_len" => $data["Rm9_len"],
                "Rm9_dc1_out" => $data["Rm9_dc1_out"],
                "Rm9_dc2_out" => $data["Rm9_dc2_out"],
                "Rm9_dc3_out" => $data["Rm9_dc3_out"],
                "Rm10_out" => $data["Rm10_out"],
                "Rm10_wth" => $data["Rm10_wth"],
                "Rm10_len" => $data["Rm10_len"],
                "Rm10_dc1_out" => $data["Rm10_dc1_out"],
                "Rm10_dc2_out" => $data["Rm10_dc2_out"],
                "Rm10_dc3_out" => $data["Rm10_dc3_out"],
                "Rm11_out" => $data["Rm11_out"],
                "Rm11_wth" => $data["Rm11_wth"],
                "Rm11_len" => $data["Rm11_len"],
                "Rm11_dc1_out" => $data["Rm11_dc1_out"],
                "Rm11_dc2_out" => $data["Rm11_dc2_out"],
                "Rm11_dc3_out" => $data["Rm11_dc3_out"],
                "Rm12_out" => $data["Rm12_out"],
                "Rm12_wth" => $data["Rm12_wth"],
                "Rm12_len" => $data["Rm12_len"],
                "Rm12_dc1_out" => $data["Rm12_dc1_out"],
                "Rm12_dc2_out" => $data["Rm12_dc2_out"],
                "Rm12_dc3_out" => $data["Rm12_dc3_out"],
                'Level1' => $data['Level1'],
                'Level2' => $data['Level2'],
                'Level3' => $data['Level3'],
                'Level4' => $data['Level4'],
                'Level5' => $data['Level5'],
                'Level6' => $data['Level6'],
                'Level7' => $data['Level7'],
                'Level8' => $data['Level8'],
                'Level9' => $data['Level9'],
                'Level10' => $data['Level10'],
                'Level11' => $data['Level11'],
                'Level12' => $data['Level12']
            ];
            $property_updated++;
            RetsPropertyDataResi::where("Ml_num", $data["Ml_num"])->update(["RoomsDescription" => $data["RoomsDescription"]]);
            echo "\n Property Updated for residential count = " . $property_updated;
        }

        $datas = RetsPropertyDataCondo::all();
        $property_updated = 0;
        foreach ($datas as $data) {
            $data = collect($data)->all();
            $data["RoomsDescription"] = [
                "Rm1_out" => $data["Rm1_out"],
                "Rm1_wth" => $data["Rm1_wth"],
                "Rm1_len" => $data["Rm1_len"],
                "Rm1_dc1_out" => $data["Rm1_dc1_out"],
                "Rm1_dc2_out" => $data["Rm1_dc2_out"],
                "Rm1_dc3_out" => $data["Rm1_dc3_out"],
                "Rm2_out" => $data["Rm2_out"],
                "Rm2_wth" => $data["Rm2_wth"],
                "Rm2_len" => $data["Rm2_len"],
                "Rm2_dc1_out" => $data["Rm2_dc1_out"],
                "Rm2_dc2_out" => $data["Rm2_dc2_out"],
                "Rm2_dc3_out" => $data["Rm2_dc3_out"],
                "Rm3_out" => $data["Rm3_out"],
                "Rm3_wth" => $data["Rm3_wth"],
                "Rm3_len" => $data["Rm3_len"],
                "Rm3_dc1_out" => $data["Rm3_dc1_out"],
                "Rm3_dc2_out" => $data["Rm3_dc2_out"],
                "Rm3_dc3_out" => $data["Rm3_dc3_out"],
                "Rm4_out" => $data["Rm4_out"],
                "Rm4_wth" => $data["Rm4_wth"],
                "Rm4_len" => $data["Rm4_len"],
                "Rm4_dc1_out" => $data["Rm4_dc1_out"],
                "Rm4_dc2_out" => $data["Rm4_dc2_out"],
                "Rm4_dc3_out" => $data["Rm4_dc3_out"],
                "Rm5_out" => $data["Rm5_out"],
                "Rm5_wth" => $data["Rm5_wth"],
                "Rm5_len" => $data["Rm5_len"],
                "Rm5_dc1_out" => $data["Rm5_dc1_out"],
                "Rm5_dc2_out" => $data["Rm5_dc2_out"],
                "Rm5_dc3_out" => $data["Rm5_dc3_out"],
                "Rm6_out" => $data["Rm6_out"],
                "Rm6_wth" => $data["Rm6_wth"],
                "Rm6_len" => $data["Rm6_len"],
                "Rm6_dc1_out" => $data["Rm6_dc1_out"],
                "Rm6_dc2_out" => $data["Rm6_dc2_out"],
                "Rm6_dc3_out" => $data["Rm6_dc3_out"],
                "Rm7_out" => $data["Rm7_out"],
                "Rm7_wth" => $data["Rm7_wth"],
                "Rm7_len" => $data["Rm7_len"],
                "Rm7_dc1_out" => $data["Rm7_dc1_out"],
                "Rm7_dc2_out" => $data["Rm7_dc2_out"],
                "Rm7_dc3_out" => $data["Rm7_dc3_out"],
                "Rm8_out" => $data["Rm8_out"],
                "Rm8_wth" => $data["Rm8_wth"],
                "Rm8_len" => $data["Rm8_len"],
                "Rm8_dc1_out" => $data["Rm8_dc1_out"],
                "Rm8_dc2_out" => $data["Rm8_dc2_out"],
                "Rm8_dc3_out" => $data["Rm8_dc3_out"],
                "Rm9_out" => $data["Rm9_out"],
                "Rm9_wth" => $data["Rm9_wth"],
                "Rm9_len" => $data["Rm9_len"],
                "Rm9_dc1_out" => $data["Rm9_dc1_out"],
                "Rm9_dc2_out" => $data["Rm9_dc2_out"],
                "Rm9_dc3_out" => $data["Rm9_dc3_out"],
                "Rm10_out" => $data["Rm10_out"],
                "Rm10_wth" => $data["Rm10_wth"],
                "Rm10_len" => $data["Rm10_len"],
                "Rm10_dc1_out" => $data["Rm10_dc1_out"],
                "Rm10_dc2_out" => $data["Rm10_dc2_out"],
                "Rm10_dc3_out" => $data["Rm10_dc3_out"],
                "Rm11_out" => $data["Rm11_out"],
                "Rm11_wth" => $data["Rm11_wth"],
                "Rm11_len" => $data["Rm11_len"],
                "Rm11_dc1_out" => $data["Rm11_dc1_out"],
                "Rm11_dc2_out" => $data["Rm11_dc2_out"],
                "Rm11_dc3_out" => $data["Rm11_dc3_out"],
                "Rm12_out" => $data["Rm12_out"],
                "Rm12_wth" => $data["Rm12_wth"],
                "Rm12_len" => $data["Rm12_len"],
                "Rm12_dc1_out" => $data["Rm12_dc1_out"],
                "Rm12_dc2_out" => $data["Rm12_dc2_out"],
                "Rm12_dc3_out" => $data["Rm12_dc3_out"],
                'Level1' => $data['Level1'],
                'Level2' => $data['Level2'],
                'Level3' => $data['Level3'],
                'Level4' => $data['Level4'],
                'Level5' => $data['Level5'],
                'Level6' => $data['Level6'],
                'Level7' => $data['Level7'],
                'Level8' => $data['Level8'],
                'Level9' => $data['Level9'],
                'Level10' => $data['Level10'],
                'Level11' => $data['Level11'],
                'Level12' => $data['Level12']
            ];
            $property_updated++;
            RetsPropertyDataCondo::where("Ml_num", $data["Ml_num"])->update(["RoomsDescription" => $data["RoomsDescription"]]);
            echo "\n Property Updated for commercial count = " . $property_updated;
        }
    }




    public function updateSlug()
    {

        echo getActualDom("2022-09-27 00:00:00.0");
        dd(1);

        $sql_query  = "SELECT * FROM RetsPropertyData where Reimport = 0 and PropertyType = 'Condos'";
        $sql_data = DB::select($sql_query);
        $count = count($sql_data);
        echo "total properties count = " . $count;
        foreach ($sql_data as $item) {
            $item = collect($item)->all();
            $item["Status"] = "U";
            $item["Reimport"] = 2;
            $item["updated_time"] = date("Y-m-d H:i:s");
            unset($item["id"]);
            $check_sql = RetsPropertyDataPurged::where("ListingId", $item["ListingId"])->first();
            if ($check_sql == null) {
                // create
                echo "\n In create";
                echo "\n Mls no = " . $item["ListingId"];
                RetsPropertyDataPurged::updateOrCreate(["ListingId" => $item["ListingId"]], $item);
                // get data from active records for original table
                $data = RetsPropertyDataCondo::where("Ml_num", $item["ListingId"])->first();
                if ($data != null) {
                    $data = collect($data)->all();
                    $data["Status"] = "U";
                    $data["Reimport"] = 2;
                    $data["property_last_updated"] = date("Y-m-d H:i:s");
                    unset($data["id"]);
                    echo "\n in active table";
                    RetsPropertyDataCondoPurged::updateOrCreate(["Ml_num" => $data["Ml_num"]], $data);
                }
            } else {
                echo "\n In update";
            }
        }
        dd(1);
        updateHomePageJson();
        updateAutoSuggestionJson();

        $rpd_data = RetsPropertyData::all();
        $total_count  = collect($rpd_data)->count();
        $property_checked = 0;
        echo "\n total count = " . $total_count;
        foreach ($rpd_data as $data) {
            $data = collect($data)->all();
            RetsPropertyDataPurged::where("ListingId", $data["ListingId"])->update(["IsMatched" => 1]);
            echo "property checked = " . $property_checked++;
        }
        dd(1);

        $datas = RetsPropertyDataResi::select("St_num", "St_dir", "St", "St_sfx", "Apt_num", "Municipality", "County", "Zip", "Ml_num")->get();
        $property_count = 0;
        foreach ($datas as $property_data) {
            $property_data = collect($property_data)->all();
            $custom_address = '';
            $custom_address .= isset($property_data['St_num']) ? $property_data['St_num'] . ' ' : '';
            if (isset($property_data['St_dir'])) {
                $enum_stprefix = $property_data['St_dir'];
                $custom_address .= $enum_stprefix . ' ';
            }
            $custom_address .= isset($property_data['St']) ? $property_data['St'] . ' ' : '';
            if (isset($property_data['St_sfx'])) {
                $enum_stprsufix = $property_data['St_sfx'];
                $custom_address .= $enum_stprsufix . ' ';
            }
            $custom_address .= isset($property_data['Apt_num']) ? $property_data['Apt_num'] . ' ' : '';
            $property_address = $custom_address;
            $property_address = preg_replace('/\s+/', '-', $property_address);
            $property_address = trim($property_address);
            $property_address = str_ireplace("-", " ", $property_address);
            $property_address = preg_replace('/\s+/', ' ', $property_address);
            $property_address = preg_replace('/\-+/', ' ', $property_address);
            $full_address = trim($property_address) . ', ' . $property_data['Municipality'] . " " . $property_data['County'] . " " . $property_data['Zip'];
            $full_address = str_ireplace(',', ' ', $full_address);
            $full_address = preg_replace('/\s+/', ' ', $full_address);

            $property_data['slug_url'] = str_ireplace(' ', '-', $full_address);
            $property_data['slug_url'] = preg_replace('/[^A-Za-z0-9\-\s]/', '-', $property_data['slug_url']);
            $property_data['slug_url'] = preg_replace('/\-+/', '-', $property_data['slug_url']);
            $property_data['slug_url'] = str_ireplace("/", '-', $property_data['slug_url']);
            $property_data['slug_url'] = str_ireplace("&", '-', $property_data['slug_url']);
            $property_data['slug_url'] = str_ireplace("'", '', $property_data['slug_url']);
            $property_data['slug_url'] = preg_replace('/\-+/', '-', $property_data['slug_url']);
            $query = "UPDATE rets_property_data_resi SET `SlugUrl` = '" . $property_data['slug_url'] . "' WHERE `Ml_num` = '" . $property_data["Ml_num"] . "'";
            $query_data = "UPDATE rets_property_data SET `SlugUrl` = '" . $property_data['slug_url'] . "' WHERE `ListingId` = '" . $property_data["Ml_num"] . "'";
            DB::update($query_data);
            $property_count++;
            echo "\n Property Updated for Mls = " . $property_data["Ml_num"] . " and count = " . $property_count;
        }
    }

    public function updateInsertTime()
    {
        $data =  \App\Models\RetsPropertyData::select("ListingId", "inserted_time", "updated_time")->get();
        $property_updated = 0;
        echo "cron started";
        foreach ($data as $datum) {
            $datum = collect($datum)->all();
            $datum["inserted_time"] = date('Y-m-d H:i:s');
            $datum["updated_time"] = date('Y-m-d H:i:s');
            \App\Models\RetsPropertyData::where("ListingId", $datum["ListingId"])->update($datum);
            $property_updated++;
            echo "\n Property Updated for commercial count = " . $property_updated;
        }
    }


    /*public function sendMail() {
        $email="sagr7188@gmail.com";
        $cc="sagar@peregrine-it.com";
        $subject="test email from server";
        $message="Hello vinay this mail is coming from live server";
        $mail =  sendEmail("SMTP", "vinod560m@gmail.com", $email, $cc, $email, $subject, $message, "UserLogin->forgotPassword");
        return response($mail,200);
    }*/

    public function updateRets()
    {
        $all_data = DB::select("select * from RetsPropertyDataCondo_old");
        $property_inserted_in_db = 0;
        foreach ($all_data as $data) {
            $data = collect($data)->all();
            //dd(json_encode($data));
            unset($data["id"]);
            RetsPropertyDataCondo::create($data);
            $property_inserted_in_db++;
            echo "\n property inserted in db = " . $property_inserted_in_db;
        }
    }

    public function testEmail()
    {
        return view('emails.housenComman');
    }

    public function testFeatures()
    {
        $query = "SELECT Extras,Prop_feat1_out,Prop_feat2_out,Prop_feat4_out,Prop_feat5_out,Prop_feat6_out,A_c,Fuel,Heating,Laundry,Pool from RetsPropertyData";
        $prev_featured_query = "SELECT Features from FeaturesMaster";
        $prev_featured  = collect(DB::select($prev_featured_query))->pluck('Features')->all();
        $data = DB::select($query);
        $temp_data = [];
        $properties_inserted_in_db = 0;
        foreach ($data as $value) {
            $properties_inserted_in_db++;
            $value = collect($value)->all();
            $extras = explode(',', $value["Extras"]);
            if ($extras !== []) {
                foreach ($extras as $extra) {
                    if (in_array($extra, $prev_featured) == false) {
                        $temp_data[] = $extra;
                    }
                }
            }
            if ($value["Prop_feat1_out"] !== "" && in_array($value["Prop_feat1_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat1_out"];
            }
            if ($value["Prop_feat2_out"] !== "" && in_array($value["Prop_feat2_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat2_out"];
            }
            if ($value["Prop_feat4_out"] !== "" && in_array($value["Prop_feat4_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat4_out"];
            }
            if ($value["Prop_feat6_out"] !== "" && in_array($value["Prop_feat6_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat6_out"];
            }
            if ($value["A_c"] !== "" && in_array($value["A_c"], $prev_featured) == false) {
                $temp_data[] = $value["A_c"];
            }
            if ($value["Fuel"] !== "" && in_array($value["Fuel"], $prev_featured) == false) {
                $temp_data[] = $value["Fuel"];
            }
            if ($value["Heating"] !== "" && in_array($value["Heating"], $prev_featured) == false) {
                $temp_data[] = $value["Heating"];
            }
            if ($value["Laundry"] !== "" && in_array($value["Laundry"], $prev_featured) == false) {
                $temp_data[] = $value["Laundry"];
            }
            if ($value["Pool"] !== "" && in_array($value["Pool"], $prev_featured) == false) {
                $temp_data[] = $value["Pool"];
            }
        }
        foreach ($temp_data as $val) {
            $ins_array["Features"] = $val;
            $temp_property = ["Features" => $val];
            FeaturesMaster::updateOrCreate(
                $temp_property,
                $temp_property
            );
            //FeaturesMaster::create($ins_array);
        }
        echo "\n working on data = " . $properties_inserted_in_db;
    }

    public function filterPropertyFeatured()
    {
        $query = "SELECT ListingId,Extras,Prop_feat1_out,Prop_feat2_out,Prop_feat4_out,Prop_feat5_out,Prop_feat6_out,A_c,Fuel,Heating,Laundry,Pool from RetsPropertyData";
        $prev_featured_query = "SELECT id,Features from FeaturesMaster";
        $prev_featured  = DB::select($prev_featured_query);
        $data = DB::select($query);
        $temp_data = [];
        $temp_property = [];
        foreach ($data as $value) {
            $value = collect($value)->all();
            $extras = explode(',', $value["Extras"]);
            if ($extras !== []) {
                foreach ($extras as $extra) {
                    $temp_data[] = $extra;
                }
            }
            if ($value["Prop_feat1_out"] !== "") {
                $temp_data[] = $value["Prop_feat1_out"];
            }
            if ($value["Prop_feat2_out"] !== "") {
                $temp_data[] = $value["Prop_feat2_out"];
            }
            if ($value["Prop_feat4_out"] !== "") {
                $temp_data[] = $value["Prop_feat4_out"];
            }
            if ($value["Prop_feat6_out"] !== "") {
                $temp_data[] = $value["Prop_feat6_out"];
            }
            if ($value["A_c"] !== "") {
                $temp_data[] = $value["A_c"];
            }
            if ($value["Fuel"] !== "") {
                $temp_data[] = $value["Fuel"];
            }
            if ($value["Heating"] !== "") {
                $temp_data[] = $value["Heating"];
            }
            if ($value["Laundry"] !== "") {
                $temp_data[] = $value["Laundry"];
            }
            if ($value["Pool"] !== "") {
                $temp_data[] = $value["Pool"];
            }
            foreach ($prev_featured as $prev) {
                if (in_array($prev->Features, $temp_data)) {
                    $temp_property = ["PropertyId" => $value["ListingId"], "FeaturesId" => $prev->id];
                    PropertyFeatures::updateOrCreate(
                        $temp_property,
                        $temp_property
                    );
                }
            }
            echo "\n value inserted = " . $value['ListingId'];
        }
    }

    public function sendMail()
    {
        $email = "sagr7188@gmail.com";
        $cc = "sagar@peregrine-it.com";
        $subject = "test email from server";
        $message = "Hello vinay this mail is coming from live server";
        $mail =  sendEmail("SMTP", env('SUPERBBROKERFROM'), $email, $cc, $email, $subject, $message, "UserLogin->forgotPassword", 3);
        return response($mail, 200);
    }

    public function getCommunities()
    {
        $province = array(['province' => 'ON', 'lang' => 'en_US'], ['province' => 'BC', 'lang' => 'en_US']);
        $province = array(['province' => 'ON', 'lang' => 'en_US']);
        $province = ProvinceTbl::get();
        foreach ($province as $key => $value) {
            $payload = array(
                "community" => "all",
                "house_type" => "all",
                "ign" => "",
                "lang" => "en_US",
                "municipality" => $value->Ids,
                "province" => $value->Province
            );

            $response = Http::withHeaders([
                'x-load-balance' => 'housesigma.com',
                'Authorization' => 'Bearer 20220315qplnldb1sk5hd8i944q93nacs9',
            ])->post('https://housesigma.com/bkv2/api/stats/trend/summary',  $payload);
            if ($response->ok()) {
                $res = $response->body();
                $res = json_decode($res);
                $community_filter = $res->data->community_filter;
                $datas = array();
                $temp = array();
                foreach ($community_filter as $key => $community) {
                    // $datas[]= $community->name;
                    array_push($datas, $community->name);
                }

                $saveData = array(
                    'Community' =>  $datas,
                );
                ProvinceTbl::where('Ids', $value->Ids)->update($saveData);
            }
        }
    }

    public function getPropertyCount()
    {
        $this->getCommunities();
        return;
        $province = array(['province' => 'ON', 'lang' => 'en_US'], ['province' => 'BC', 'lang' => 'en_US']);
        foreach ($province as $key => $value) {

            $response = Http::withHeaders([
                'x-load-balance' => 'housesigma.com',
                'Authorization' => 'Bearer 20220315qplnldb1sk5hd8i944q93nacs9',
                'X-Second' => 'bar'
            ])->post('https://housesigma.com/bkv2/api/stats/trend/summary',  $value);
            if ($response->ok()) {
                $res = $response->body();
                $res = json_decode($res);
                $municipality_filter = $res->data->municipality_filter;
                foreach ($municipality_filter as $key => $municipality) {
                    $listings = $municipality->list;
                    $filteredList = array();
                    foreach ($listings as $key => $list) {
                        $saveData = array(
                            'Province' => $value['province'],
                            'MunicipalityHeading' => $municipality->title,
                            'Municipality' => $list->name,
                            'Ids' => $list->id,
                        );
                        ProvinceTbl::updateOrCreate($saveData);
                    }
                }
            }
        }
    }

    public function updateJson()
    {
        updateHomePageJson();
        updateAutoSuggestionJson();
        echo "\n json's are updated now. happy coding :-)";
    }
    public function updateLpDol()
    {
        // Price


        $data = RetsPropertyDataPurged::select('id', 'ListPrice')->where("Price", null)->limit(500000)->get();
        foreach ($data as $key => $value) {
            $value->Price = (int)$value->ListPrice;
            $res = $value->save();
            echo "\n\n";
            echo "Updated===>>" . $value->id;
            echo "";
        }
        dd(count($data));
    }

    public function checkQuery(Request $request)
    {
        $query = $request->sqlquery ? $request->sqlquery : "";
        $queryTemp = strtolower($query);

        if (str_contains($queryTemp, 'delete')) {
            return;
        }
        if (str_contains($queryTemp, 'update')) {
            return;
        }
        $res = DB::select($query);
        return response($res, 200);
    }

    public function getImageSizeAndExistence()
    {
        $sql_query = "SELECT Ml_num,image_downloaded from RetsPropertyDataResiPurged";
        $sql_data = DB::select($sql_query);
        foreach ($sql_data as $data) {
            $data = collect($data)->all();
            $image_check = RetsPropertyDataImage::where("listingID", $data["Ml_num"])->get();
            if ($image_check != []) {
                if (collect($image_check)->count() > 2) {
                    echo "\n image found for the properties which is sold";
                    // insert all the data into the purged images table
                    foreach ($image_check as $image) {
                        $image =  collect($image)->all();
                        unset($image["id"]);
                        RetsPropertyDataImagesSold::insert($image);
                        echo "\n inserted into the sold images table";
                    }
                    // get the size of the folder
                    $size = $this->GetDirectorySize("/storage/app/public/img/mls_images/" . $data["Ml_num"]);
                    echo "\n got the size of an images = " . $size;
                    // update the table for the images existed in the active table
                    $update_query = "update RetsPropertyDataResiPurged set image_downloaded = 1, imagesSize = '" . $size . "' where Ml_num = '" . $data['Ml_num'] . "'";
                    DB::update($update_query);
                }
            } else {
                // update the table for the images not existed in the active table
                $size = '0';
                $update_query = "update RetsPropertyDataResiPurged set image_downloaded = 0, imagesSize = '" . $size . "' where Ml_num = '" . $data['Ml_num'] . "'";
                DB::update($update_query);
            }
        }
    }

    public function testSize()
    {
        $size =  $this->GetDirectorySize("/home/peregrine/Videos");
        echo "\n size = " . $size;
        echo "\n " . $this->formatSizeUnits($size);
    }

    public function GetDirectorySize($path)
    {
        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false && $path != '' && file_exists($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

        public function fillPropertyAddressData()
    {
        $all_tables = array("RetsPropertyDataResi","RetsPropertyDataResiPurged","RetsPropertyDataCondo","RetsPropertyDataCondoPurged");
        foreach ($all_tables as $table) {
            echo $table;
            $PurgedCommCount = DB::table($table)->select("Ml_num")->count();
            echo "\n Insertation started for $table";
            $start_index = 0;
            $limit       = 1000;
            $lc          = (($PurgedCommCount - $start_index) / $limit);
            $lcp = 0;
            $i=0;
            $left = $PurgedCommCount;
            for ($lcp = 0; $lcp <= $lc; $lcp++) {
                $offset = $start_index + $lcp * $limit;
                echo "Limit $limit and skip $offset \n";
                $addressData = [];
                $all_data = DB::table($table)->select("Ml_num","Municipality","Community","Area","Zip","County","StandardAddress","Status","property_insert_time","property_last_updated")->limit($limit)->skip($offset)->get();
                foreach ($all_data as $key => $value) {$i++;
                    $addressData[] = array(
                        "ListingId" => $value->Ml_num,
                        "StandardAddress" => $value->StandardAddress,
                        "City" => $value->Municipality,
                        "Area" => $value->Area,
                        "ZipCode" => $value->Zip,
                        "County" => $value->County,
                        "Status" => $value->Status,
                        "created_at" => $value->property_insert_time,
                        "updated_at" => $value->property_last_updated,
                        "Community" => $value->Community,
                    );
                    $left = ($left - $i);
                    echo "\n $i Inserted MLS :: $value->Ml_num    Left===> $left";
                }
                    DB::table("PropertyDataAddressNew")->insert($addressData);
                    sleep(2);
            }
        }
    }

    public function deleteCommImages()
    {
        $dir = "/home/mukesh/public_html/panel/storage/app/public/img";
        // For Active Images
        // $ActiveComm = DB::table("RetsPropertyDataComm")->select("Ml_num")->get();
        //   $ActiveComm = DB::table("RetsPropertyDataComm")->select("Ml_num")->get();
        //   $deleted_count =1;
        //     foreach ($ActiveComm as $key => $value)
        //     {
        //         if (isset($value->Ml_num) && $value->Ml_num !="" && $value->Ml_num !== NULL) {
        //             if ($deleted_count%500 ==0) {
        //                 sleep(5);
        //                 echo "\n <br> Sleep :: $deleted_count Deleted <br> \n";
        //             }
        //             echo "<br> MLS:: ".$value->Ml_num."<br> \n";
        //             $folder_path = $dir ."/mls_images/".$value->Ml_num;
        //             $files = glob($folder_path."/*");
        //             foreach($files as $file){
        //                 if(is_file($file)) {
        //                     @unlink($file);
        //                     echo "Deleted Image: ".$file."\n </br>";
        //                 }
        //             }
        //             $folder_path_th = $dir ."/mls_images/".$value->Ml_num."/thumbnailImages";
        //             $files = glob($folder_path_th."/*");
        //             foreach($files as $file){
        //                 if(is_file($file)) {
        //                     @unlink($file);
        //                     echo "Deleted Image: ".$file."\n </br>";
        //                 }
        //             }
        //             $folder_path_tumbnail = $dir ."/mls_images/".$value->Ml_num."/thumbnailImages/";
        //             if(is_dir($folder_path_tumbnail)){
        //                 rmdir($folder_path_tumbnail);
        //                 echo "Deleted Folder: ".$folder_path_tumbnail."\n </br>";
        //             }
        //             $folder_path = $dir ."/mls_images/".$value->Ml_num."/";
        //             if(is_dir($folder_path)){
        //                 rmdir($folder_path);
        //                 echo "Deleted Folder: ".$folder_path."\n </br>";
        //             }
        //             $deleteImages = DB::table("RetsPropertyDataImages")->where("listingID",$value->Ml_num)->delete();
        //             $deleted_count++;
        //         }
        //     }
        // For sold Images
        // $PurgedComm = DB::table("RetsPropertyDataCommPurged")->select("Ml_num")->get();

        $PurgedCommCount = DB::table("RetsPropertyDataCommPurged")->select("Ml_num")->count();
        $start_index = 0;
        $limit       = 5000;
        $lc          = (($PurgedCommCount - $start_index) / $limit);
        $lcp = 0;
        $deleted_count_purged = 1;
        for ($lcp = 0; $lcp <= $lc; $lcp++) {
            $offset = $start_index + $lcp * $limit;
            echo "Limit $limit and skip $offset \n";
            $PurgedComm = DB::table("RetsPropertyDataCommPurged")->select("Ml_num")->limit($limit)->skip($offset)->get();
            foreach ($PurgedComm as $key => $value_ml) {
                if (isset($value_ml->Ml_num) && $value_ml->Ml_num != "" && $value_ml->Ml_num !== NULL) {
                    if ($deleted_count_purged % 500 == 0) {
                        sleep(5);
                        echo "\n <br> Sleep :: $deleted_count_purged Purged Deleted <br> \n";
                    }
                    echo "<br> MLS:: " . $value_ml->Ml_num . "<br> \n";
                    $folder_path = $dir . "/mls_images/soldProperty/" . $value_ml->Ml_num;
                    $files = glob($folder_path . "/*");
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            @unlink($file);
                            echo "Deleted Image: " . $file . "\n </br>";
                        }
                    }
                    $folder_path_th = $dir . "/mls_images/soldProperty/" . $value_ml->Ml_num . "/thumbnailImages";
                    $files = glob($folder_path_th . "/*");
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            @unlink($file);
                            echo "Deleted Image: " . $file . "\n </br>";
                        }
                    }
                    $folder_path_tumbnail = $dir . "/mls_images/soldProperty/" . $value_ml->Ml_num . "/thumbnailImages/";
                    if (is_dir($folder_path_tumbnail)) {
                        rmdir($folder_path_tumbnail);
                        echo "Deleted Folder: " . $folder_path_tumbnail . "\n </br>";
                    }
                    $folder_path = $dir . "/mls_images/soldProperty/" . $value_ml->Ml_num . "/";
                    if (is_dir($folder_path)) {
                        rmdir($folder_path);
                        echo "Deleted Folder: " . $folder_path . "\n </br>";
                    }
                    $deleteImagesPurged = DB::table("RetsPropertyDataImagesSold")->where("listingID", $value_ml->Ml_num)->delete();
                    $deleted_count_purged++;
                }
            }
        }
    }

    public function sentWatchAlertsTest()
    {
        sentWatchAlerts();
    }
}
