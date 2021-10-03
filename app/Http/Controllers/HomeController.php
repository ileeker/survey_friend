<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use App\User;
use App\Best;
use App\Black;
use App\History;
use App\Acxiom;
use App\Innovate;
use App\InnovateSub;
use App\Opinionetwork;
use App\OpinionetworkSub;
use App\Remark;
use Cookie;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (auth()->user()->referee == 'leeker') {
            // return view('home');
        }else {
            return '<h1>It is under construction.</h1>';
        }

        // 判断有无cookie
        if (request()->cookie('profile_json') == NULL) {

            // 获取IP(ipstack)信息
            $ip = $this->getClientIp();
            $response_api = file_get_contents('http://api.ipstack.com/'.$ip.'?access_key=ab95d83f0f34a2bd838d8c9ddb713e73&format=1');
            $response_ip = json_decode($response_api);
            $country = $response_ip->country_code;
            $region = $response_ip->region_name;
            $region_code = $response_ip->region_code;
            $zip = $response_ip->zip;

            // 获取IP（ip-api）信息
            // $ip = $this->getClientIp();
            // $response_api = file_get_contents('http://ip-api.com/json/'.$ip);
            // $response_ip = json_decode($response_api);
            // $country = $response_ip->countryCode;
            // $region = $response_ip->regionName;
            // $region_code = $response_ip->region;
            // $zip = $response_ip->zip;
            
            // 获得uuid通用
            $innovate_uuid = $this->gen_uuid();
            $opinionetwork_uuid = '';
            $opinionetwork_dob = '';
            $opinionetwork_city = '';
            $opinionetwork_zipcode = '';
            $dynata_user = '';

            // 创建数组
            $user = array(
                'country'=>$country,
                'region'=>$region,
                'region_code'=>$region_code,
                'zip'=>$zip,
                'innovate_uuid'=>$innovate_uuid,
                'opinionetwork_uuid'=>$opinionetwork_uuid,
                'opinionetwork_dob'=>$opinionetwork_dob,
                'opinionetwork_city'=>$opinionetwork_city,
                'opinionetwork_zipcode'=>$opinionetwork_zipcode,
                'dynata_user'=>$dynata_user,
            );
            
            // Json打包
            $array_json = json_encode($user);

            Cookie::queue('profile_json', $array_json, 3600);

        }else {

            $innovate_uuid = json_decode(request()->cookie('profile_json'))->innovate_uuid;
            $country = json_decode(request()->cookie('profile_json'))->country;
            $region = json_decode(request()->cookie('profile_json'))->region;
            $region_code = json_decode(request()->cookie('profile_json'))->region_code;
            $zip = json_decode(request()->cookie('profile_json'))->zip;
            $opinionetwork_uuid = json_decode(request()->cookie('profile_json'))->opinionetwork_uuid;
            $opinionetwork_dob = json_decode(request()->cookie('profile_json'))->opinionetwork_dob;
            $opinionetwork_city = json_decode(request()->cookie('profile_json'))->opinionetwork_city;
            $opinionetwork_zipcode = json_decode(request()->cookie('profile_json'))->opinionetwork_zipcode;
            $dynata_user = json_decode(request()->cookie('profile_json'))->dynata_user;
            
        }

        if ($dynata_user != '') {
            $dynata_username = $dynata_user;
        }else {
            $dynata_username = strtolower($this->GetRandStr(8));
        }

        $jscode = auth()->user()->jscode;

        return view('home',compact('country','region','region_code','zip','innovate_uuid','opinionetwork_uuid','dynata_user','dynata_username','jscode','opinionetwork_dob','opinionetwork_city','opinionetwork_zipcode'));

    }

    public function url_login()
    {

        $user = auth()->user();

        $url = URL::signedRoute('autologin', ['user' => $user]);

        return $url;
    }

    public function opinionetwork_uuid(Request $request)
    {
        // 获取IP信息(IPStack)
        $ip = $this->getClientIp();
        $response_api = file_get_contents('http://api.ipstack.com/'.$ip.'?access_key=ab95d83f0f34a2bd838d8c9ddb713e73&format=1');
        $response_ip = json_decode($response_api);
        $state = $response_ip->region_name;
        $state_code = $response_ip->region_code;
        $city = $response_ip->city;
        $zip = $response_ip->zip;
        $country = $response_ip->country_code;

        // 获取IP（ip-api）信息
        // $ip = $this->getClientIp();
        // $response_api = file_get_contents('http://ip-api.com/json/'.$ip);
        // $response_ip = json_decode($response_api);
        // $state = $response_ip->regionName;
        // $state_code = $response_ip->region;
        // $city = $response_ip->city;
        // $zip = $response_ip->zip;
        // $country = $response_ip->countryCode;

        $user = auth()->user();
        $data = $request->except('_token');
        $url = 'https://api4.opinionetwork.com/api/Member/Create';
        $random_id = time().rand(1, 1000000).$this->GetRandStr(5);

        if ($data['zips'] != "") {

            $data['type'] = "zipcode";
            
            if ($data['dob'] != "") {
                $data['type'] = "zipcodeyear";
            }
            
            $zip_array = preg_split('/\r\n|[\r\n]/', $data['zips']);
            $data['zipcode'] = $zip_array[array_rand($zip_array, 1)];
        }

        // return $zip_array;
        // return $data['zipcode'];

        $data['Country'] = $country;
        // 测试
        // $data['Country'] = 'US';

        if ($data['Country'] == 'AU') {
            $url_country = 'au';
        }
        
        if ($data['Country'] == 'CA') {
            $url_country = 'ca';
        }
        
        if ($data['Country'] == 'GB') {
            $url_country = 'gb';
        }

        if ($data['Country'] == 'US') {
            $url_country = 'us';
        }

        if ($data['Country'] == 'FR') {
            $url_country = 'fr';
        }

        if ($data['Country'] == 'DE') {
            $url_country = 'de';
        }

        if ($data['Country'] != 'US') {
            // 不是美国用户，随机产生用户数据
            // 随机用户数据
            $URL_random = "https://randomuser.me/api/?nat=".$url_country;
            $response = file_get_contents($URL_random);
            $all = json_decode($response, true);
            $array = array();

            $array['Country'] = $data['Country'];
            $array['FirstName'] = $this->onlyEngString($all['results'][0]['name']['first']);
            $array['LastName'] = $this->onlyEngString($all['results'][0]['name']['last']);
            $array['State'] = $state;
            $array['City'] = $city;
            $array['Zip'] = $zip;
            $array['Address1'] = $all['results'][0]['location']['street']['number'].' '.$all['results'][0]['location']['street']['name'];        
            $array['Address2'] = '';    
            $array['Ethnicity'] = $data['Ethnicity'];
            $array['EmailAddress'] = strtolower($array['FirstName'].$array['LastName'].'@gmail.com');
            $array['Gender'] = $data['gender'];
            if ($data['dob'] == '') {
                $int= mt_rand(31507200,1009814400);
            } else {
                $min = strtotime(''.$data['dob'].'-01-01');
                $max = strtotime(''.$data['dob'].'-12-30');
                $int = mt_rand($min,$max);
            }
            $dob = ''.date("m/d/Y",$int);
            $array['Dob'] = $dob;
            $all['persondateofbirthyear'] = $dob;
            $all['cityname'] = $city;
            $all['ZipCode'] = $zip;
            // return $array;
        }else {
            // $state_code = 'CA';
            $URL_random = sprintf("http://88.99.252.213/data-api.php?type=%s&state=%s&city=%s&year=%s&zipcode=%s",$data['type'],$state_code,$data['city'],$data['dob'],$data['zipcode']);
            $response = file_get_contents($URL_random);
            $all = json_decode($response, true);
            // return $all;
            $array['Country'] = $data['Country'];
            $array['FirstName'] = $all['personfirstname'];
            $array['LastName'] = $all['personlastname'];
            $array['State'] = $state_code;
            $array['City'] = $all['cityname'];
            $array['Zip'] = $all['ZipCode'];
            $array['Address1'] = $all['primaryaddress'];        
            $array['Address2'] = '';
            $array['Ethnicity'] = $data['Ethnicity'];
            $array['EmailAddress'] = $all['personlastname'].$this->GetRandStr(5).'@GMAIL.COM';
            $array['Gender'] = $data['gender'];

            $dob_month = $all['persondateofbirthmonth'];
            $dob_date = $all['persondateofbirthday'];

            if ($dob_month == 0 || $dob_month == '0') {
                // $dob_month = rand(1,12);
                $dob_month = 6;
            }

            $dob_month = sprintf("%02d", $dob_month);

            if ($dob_date == 0 || $dob_date == '0') {
                // $dob_date = rand(1,28);
                $dob_date = 15;
            }
            
            $dob_date = sprintf("%02d", $dob_date);
            
            $int = strtotime($all['persondateofbirthyear'].'-'.$dob_month.'-'.$dob_date);
            $dob = ''.date("m/d/Y",$int);

            $array['Dob'] = $dob;
            // return $array;
        }

        $data_api = array('Rid' => 20871, 'ExtMemberId' => $random_id);
        $data = array_merge($array, $data_api);
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\nAppKey: D36D5C0E-39A7-4A02-AEDF-0A7D14955D1F",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $str = file_get_contents($url, false, $context);

        $result = array();
        preg_match_all("/<UserGuid>(.+)<\/UserGuid>/i",$str, $result);
        
        $array['Guid'] = $result[1][0];
        
        $array_json = json_decode(request()->cookie('profile_json'));
        $array_json->opinionetwork_uuid = $array['Guid'];
        $array_json->opinionetwork_dob = $all['persondateofbirthyear'];
        $array_json->opinionetwork_city = $all['cityname'];
        $array_json->opinionetwork_zipcode = $all['ZipCode'];
        $array_json = json_encode($array_json);

        if ($data['Country'] == 'US'){

            // 保存到Acxiom数据库
            try{
                $acxiom = new Acxiom;
                $acxiom->userId = $all['id'];
                $acxiom->personfirstname = $all['personfirstname'];
                $acxiom->personlastname = $all['personlastname'];
                $acxiom->primaryaddress = $all['primaryaddress'];
                $acxiom->state = $state_code;
                $acxiom->cityname = $all['cityname'];
                $acxiom->ZipCode = $all['ZipCode'];
                $acxiom->persondateofbirthyear = $all['persondateofbirthyear'];
                $acxiom->persondateofbirthmonth = $all['persondateofbirthmonth'];
                $acxiom->persondateofbirthday = $all['persondateofbirthday'];
                $acxiom->uuid = $array['Guid'];
                $acxiom->save();
            }
            catch(\Exception $e){
                // do task when error
                return 'This user information may have been used before.';
                // return $e->getMessage();   // insert query
            }

        }
        

        return redirect('home')->cookie('profile_json', $array_json, 14400);

    }

    public function dynata_user(Request $request)
    {
        $user = auth()->user();
        $data = $request->except('_token');
        $array = $data;
        // 不知道为什么新建的数组不行
        unset($array['userId']);
        unset($array['sex']);
        unset($array['dob']);
        unset($array['countryISOCode']);
        unset($array['postalCode']);
        unset($array['profileData']);

        $profileData = explode("\r\n", $data['profileData']);

        foreach ($profileData as $key => $value) {

            $Id_value = explode(":", $value);

            $array['attributeId'] = (int)$Id_value[0];
            $array['values'] = array($Id_value[1]);

            $profileData[$key] = $array;
        }

        $data['profileData'] = $profileData;

        $profileJSON = json_encode($data);

        // 获得AUTH TOKEN的POST
        $auth_url = 'https://api.peanutlabs.com/marketplace/supply/api/v1/oauth2/accessToken';
        $data = array('grant_type' => 'client_credentials', 'client_id' => 10710, 'client_secret' => 'ec3c231daaf6f92ab38180e5c4784538', 'scope' => 'basic email');
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $str = file_get_contents($auth_url, false, $context);
        $api_token = json_decode($str)->access_token;

        // 注册用户
        $register_url = 'https://api.peanutlabs.com/marketplace/supply/api/v1/respondents';
        // $user_json = array('userId' => 'us004','sex' => '1', 'dob' => '1990-01-17', 'countryISOCode' => 'US', 'postalCode' => '90006');
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $profileJSON,
                'header'=>  "Content-Type: application/json\r\n" .
                            // "Accept: application/json\r\n" .
                            "Authorization: ".$api_token."\r\n"
                )
            );
            
        $context  = stream_context_create( $options );
        $result = file_get_contents( $register_url, false, $context );
        $result = json_decode($result);
        $user_id = $result->userId;
        $resultCode = $result->resultCode;
        $status = $result->status;

        if ($resultCode == '1' && $status == '200') {

            $array_json = json_decode(request()->cookie('profile_json'));
            $array_json->dynata_user = $user_id;
            $array_json = json_encode($array_json);
            
            return redirect('home')->cookie('profile_json', $array_json, 14400);

        }else {
            return 'Failed';
        }

    }

    public function store(Request $request)
    {
        $user = auth()->user();
        // return $user->id;
        $data = $request->except('_token');

        if ($data['site'] == 'InnovateMR' && $data['func'] == 'Add') {
            // 判断是否是Innovate
            $surveyData = Innovate::where('surveyId',$data['surveyId'])->first();
            $newSurvey = new InnovateSub;
            $newSurvey->user_id = $user->id;
            $newSurvey->surveyId = $surveyData->surveyId;
            $newSurvey->surveyName = $surveyData->surveyName;
            $newSurvey->N = $surveyData->N;
            $newSurvey->remainingN = $surveyData->remainingN;
            $newSurvey->Country = 'Best';
            $newSurvey->CPI = $surveyData->CPI;
            $newSurvey->LOI = $surveyData->LOI;
            $newSurvey->IR = $surveyData->IR;
            $newSurvey->groupType = $surveyData->groupType;
            $newSurvey->jobCategory = $surveyData->jobCategory;
            $newSurvey->jobId = $surveyData->jobId.'A';
            $newSurvey->entryLink = $surveyData->entryLink;
            $newSurvey->ctime = $surveyData->ctime;
            $newSurvey->mtime = $surveyData->mtime;
            $newSurvey->created_at = $surveyData->created_at;
            $newSurvey->updated_at = $surveyData->updated_at;
            $newSurvey->save();
            return "<h1 style='color:red'>Success</h1>";
        }

        if ($data['site'] == 'InnovateMR' && $data['func'] == 'Del') {
            InnovateSub::where('surveyId',$data['surveyId'])->delete();
            return "<h1 style='color:red'>Success</h1>";
        }

        if ($data['site'] == 'Opinionetwork' && $data['func'] == 'Add') {
            // 判断是否是Opinionetwork
            $surveyData = Opinionetwork::where('prj_id',$data['surveyId'])->first();
            $newSurvey = new OpinionetworkSub;
            $newSurvey->user_id = $user->id;
            $newSurvey->prj_id = $surveyData->prj_id;
            $newSurvey->group_id = $surveyData->group_id.'A';
            $newSurvey->prj_name = $surveyData->prj_name;
            $newSurvey->total_completes = $surveyData->total_completes;
            $newSurvey->remain = $surveyData->remain;
            $newSurvey->country = 'Best';
            $newSurvey->P_payout = $surveyData->P_payout;
            $newSurvey->loi = $surveyData->loi;
            $newSurvey->ir = $surveyData->ir;
            $newSurvey->quota = $surveyData->quota;
            $newSurvey->url = $surveyData->url;
            $newSurvey->ctime = $surveyData->ctime;
            $newSurvey->save();
            return "<h1 style='color:red'>Success</h1>";
        }

        if ($data['site'] == 'Opinionetwork' && $data['func'] == 'Del') {
            OpinionetworkSub::where('prj_id',$data['surveyId'])->delete();
            return "<h1 style='color:red'>Success</h1>";
        }

    }

    public function remark(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->except('_token');
        $remark = new Remark;
        $remark->surveyId = $data['surveyId'];
        $remark->remark = $data['remark'];
        $remark->sign = $data['sign'];
        $remark->user_id = $user_id;
        $remark->save();
        return '<h1 style="color:red">Success</h1>';
    }

    public function add(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->except('_token');
        $whitelist = new Best;
        $whitelist->user_id = $user_id;
        $whitelist->surveyId = $data['surveyId'];
        $whitelist->save();
        return '<h1 style="color:red">Add to whitelist successfully</h1>';
    }

    public function del(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->except('_token');
        $whitelist = new Black;
        $whitelist->user_id = $user_id;
        $whitelist->surveyId = $data['surveyId'];
        $whitelist->save();
        return '<h1 style="color:black">Add to blacklist successfully</h1>';
    }

    public function jscode(Request $request)
    {
        $userProfile = auth()->user();
        $data = $request->except('_token');
        $jscode = $data['jscode'];
        $userProfile->jscode = $jscode;
        $userProfile->save();
        
        return redirect('home');
    }

    public function success()
    {
        $success = History::where('status','success')->orderBy('created_at', 'desc')->paginate(50);
        foreach ($success as $key => $value) {
            echo $value['user_id'].' -- '.$value['surveyId'].'---'.$value['cpi'].' -- '.$value['site'].' -- '.$value['created_at'].'<br>';
        }

    }

    public function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        if (getenv('HTTP_X_REAL_IP')) {
            $ip = getenv('HTTP_X_REAL_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
            $ips = explode(',', $ip);
            $ip = $ips[0];
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = '0.0.0.0';
        }

        return $ip;
    }

    public function gen_uuid() 
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
    
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
    
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public function GetRandStr($length)
    {
        //字符组合
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($str)-1;
        $randstr = '';
        for ($i=0;$i<$length;$i++) {
            $num=mt_rand(0,$len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }

    // 只提取英文字符
    function onlyEngString($str)
    {
        if(preg_match('/[a-zA-Z]+/',$str,$arr)){
            return $arr[0];
        }
    }

    public function test()
    {
        // 测试文件
        $project_url = "http://final.test/dynata.json";
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $project_all = json_decode($response, true)['data'];

        $all_attributes = array();

        foreach ($project_all as $key => $value) {
            $lineItems = $value['lineItems'];
            foreach ($lineItems as $key1 => $value1) {
                if (isset($value1['quotaPlan']['filters'])) {
                    $filters = $value1['quotaPlan']['filters'];
                    foreach ($filters as $key2 => $value2) {
                        $all_attributes[] = $value2['attributeId'];
                    }
                }

                if (isset($value1['quotaPlan']['quotaGroups'])) {
                    foreach ($value1['quotaPlan']['quotaGroups'] as $quotaGroup) {
                        foreach ($quotaGroup['quotaCells'] as $quotaCell) {
                            foreach ($quotaCell['quotaNodes'] as $quotaNodes) {
                                $all_attributes[] = $quotaNodes['attributeId'];
                            }
                        }
                    }
                }

            }
        }

        $array_unique = array();

        foreach (array_unique($all_attributes) as $key => $value) {
            $array_unique[] = $value;
        }


        // return $array_unique;

        return array_count_values($all_attributes);
        // return $all_attributes;
        // return $project_all;
    }

    public function innovateRevenue($year,$month)
    {
        $y = $year;
        $m = $month;

        $ym = $y."-".$m;

        //本月第一天
        $beginDate = date($ym.'-01', strtotime(date($ym."-d")));

        //本月最后一天
        $endDate = date($ym.'-d', strtotime("$beginDate +1 month -1 day"));

        $endtime = strtotime($endDate);

        if ($endtime > time()) {
            $endtime = time();
        }

        $endDate = date('Y-m-d',$endtime);

        // 读取网页内容
        $URL = "https://supplier.innovatemr.net/api/v2/supply/getSurveyStatsByDateRange?startDate=".$beginDate."&endDate=".$endDate."&status=Completes";
        $aHTTP['http']['method']  = 'GET';
        $aHTTP['http']['header']  = "x-access-token:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjYxNDIzNzQzZjhlODZmMTRkNWRjM2U0MyIsInVzcl9pZCI6NzEwLCJ1c3JfdHlwZSI6InN1cHBsaWVyIiwiaWF0IjoxNjMxNzMwMjY3fQ.6RmScxRzxzfkBBcCuIM_J5D4_iVmk2guBWzh62XVRtM";
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($URL, false, $context);
        $array = json_decode($response, true);

        return $array;
    }

    
}
