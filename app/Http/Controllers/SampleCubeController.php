<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Samplecube;
use App\Scgroup;
use App\History;

class SampleCubeController extends Controller
{
    // Sample-Cube的调查记录保存
    public function sample_cube_store(){
        
        // 读取网页内容
        $URL = "https://api.sample-cube.com/api/Survey/GetSupplierAllocatedSurveys/1505/0d57ee95-70a1-49e6-89ec-8e8a2558a6a1";
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($URL, false, $context);
        $array = json_decode($response, true);

        // return $array;

        Samplecube::truncate();

        // // 插入所有调查链接到数据库
        foreach ($array['Surveys'] as $value) 
        {
            if ($value['CPI'] > 1.5) {
                $task = new Samplecube();
                $task->surveyID = $value['SurveyId'];
                $task->surveyName = $value['ProjectId'];
                $task->totalQuota = $value['LanguageId'];
                $task->remainQuota = $value['TotalRemaining'];
                if ($value['LanguageId'] == 1) {
                    $country_code = 'GB';
                }elseif ($value['LanguageId'] == 3) {
                    $country_code = 'US';
                }elseif ($value['LanguageId'] == 4) {
                    $country_code = 'AU';
                }elseif ($value['LanguageId'] == 5) {
                    $country_code = 'CA';
                }elseif ($value['LanguageId'] == 20 or $value['LanguageId'] == 43 or $value['LanguageId'] == 46) {
                    $country_code = 'HK';
                }elseif ($value['LanguageId'] == 50) {
                    $country_code = 'TW';
                }else {
                    $country_code = 'OTHERS';
                }
                if ($value['IsSurveyGroupExist'] == true) {
                    $task->groupid = 'yes';
                }
                $task->country = $country_code;
                $task->cpi = $value['CPI'];
                $task->loi = $value['LOI'];
                $task->ir = $value['IR'];
                $task->UpdateTimeStamp = $value['UpdateTimeStamp'];
                $task->url = $value['LiveLink'];
                $task->save();
            }
            
        }

        return $array['Surveys'];
    }

    // Sample-Cube读取
    public function index(){
        // 获得用户的信息
        $user = auth()->user();

        $all = Samplecube::orderBy('UpdateTimeStamp', 'DESC')->get();

        $new_all = array();
        $groupId = array();
        $allGroupId = array();

        $last_time = $all[0]['created_at']->format('Y-m-d H:i:s');

        foreach ($all as $value) {
            if (isset($new_all[$value['groupid']])) {
                if ($value['cpi'] > $new_all[$value['groupid']][0]['cpi']) {
                    $new_all[$value['groupid']][0] = "";
                    $new_all[$value['groupid']][0] = $value;
                    $new_all[$value['groupid']]['info'][] = $value;
                }else {
                    $new_all[$value['groupid']]['info'][] = $value;
                }
            }else {
                $new_all[$value['groupid']][] = $value;
                $new_all[$value['groupid']]['info'][] = $value;
            }
        }

        // 获取user的best
        $remark = auth()->user()->remarks->all();
        // $black = auth()->user()->blacks->pluck('surveyId')->toArray();

        // 重新赋值

        // 按照国家分组
        $us = array();
        $uk = array();
        $au = array();
        $ca = array();
        $cn = array();
        $fr = array();
        $de = array();
        $jp = array();
        $best = array();

        foreach ($new_all as $value) {

            if (strpos($value[0]['country'], 'US') !== false) {
                $us[] = $value;
            }
            
            if (strpos($value[0]['country'], 'GB') !== false) {
                $uk[] = $value;
            }
            
            if (strpos($value[0]['country'], 'CA') !== false) {
                $ca[] = $value;
            }
            
            if (strpos($value[0]['country'], 'AU') !== false) {
                $au[] = $value;
            }
            
            if (strpos($value[0]['country'], 'HK') !== false) {
                $hk[] = $value;
            }
            
        }

        $total['us'] = $us;
        $total['uk'] = $uk;
        $total['ca'] = $ca;
        $total['au'] = $au;
        $total['hk'] = $hk;

        // return $total;

        // return $remark;
        return view('samplecube',compact('total','remark','new_all', 'last_time'));
    }

    // Sample-Cube的id读取
    public function sample_cube_id($id){
        
        // 获取Cookie
        $array_json = json_decode(request()->cookie('profile_json'));
        
        // 正文
        $user_id = auth()->user()->id;
        $user_guid = $array_json->innovate_uuid;
        $data = Samplecube::where('surveyId',$id)->first();

        $url = $data->url;
        $cpi = $data->cpi;
        $groupId = $data->groupid;
        $pid = strtoupper($this->gen_uuid());
        
        // 历史记录保存
        $history = new History();
        $history->user_id = $user_id;
        $history->site = "Sample-cube";
        $history->surveyId = $id;
        $history->groupId = $groupId;
        $history->cpi = $cpi;
        $history->status = "unknown";
        // 这个地方例外，因为sc不返回surveyid，所以用的是pid，其实都应该用pid
        $history->uuid = $pid;
        $history->save();

        // 链接替换
        $url = str_replace("[#scid#]", $pid, $url);
        $url = str_replace("[#scid2#]", $user_guid, $url);
        $url = str_replace("[#scid3#]", $user_guid, $url);
        return $url;
        return redirect($url);

    }

    // Sample-Cube的quota读取
    public function sample_cube_quota($id,$country){
            
        // 确定读取哪个文件
        if ($country == 'us') {
            $fileName = 'sc-us.txt';
        }
        if ($country == 'uk') {
            $fileName = 'sc-gb.txt';
        }
        if ($country == 'ca') {
            $fileName = 'sc-ca.txt';
        }
        if ($country == 'au') {
            $fileName = 'sc-au.txt';
        }
        if ($country == 'hk') {
            $fileName = 'sc-hk.txt';
        }
        if ($country == 'tw') {
            $fileName = 'sc-tw.txt';
        }

        // 读取网页内容
        $URL = "https://api.sample-cube.com/api/Survey/GetSupplierSurveyQuotas/1505/0d57ee95-70a1-49e6-89ec-8e8a2558a6a1/".$id;
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($URL, false, $context);
        $array = json_decode($response, true);

        // 读取答案
        $string = file_get_contents(route('index_home').'/'.$fileName);
        $json_qa = json_decode($string, true);

        // 取出quota
        $array_quota = $array['SurveyQuotas'];

        if (count($array_quota)>1) {
            
            // 删除第一个无用元素
            array_shift($array_quota);

            // 循环所有的配额
            for ($i=0; $i < count($array_quota); $i++) { 
                
                for ($x=0; $x < count($array_quota[$i]['Conditions']); $x++) { 
                    unset($array_quota[$i]['Conditions'][$x]['AnswerCodes']);
                    $question = $array_quota[$i]['Conditions'][$x]['QualificationId'];

                    // 创建答案数组
                    $answer_a = array();

                    // 循环表格
                    foreach ($json_qa['Sheet1'] as $key2 => $value2) {
                        // 对比看看有没有一样的
                        if ($question == $value2['QualificationId']) {
                            $array_quota[$i]['Conditions'][$x]['QualificationId'] = $value2['Text'];
                        }

                        //查看值是否在数组
                        if (in_array($value2['AnswerId'],$array_quota[$i]['Conditions'][$x]['AnswerIds'])) {
                            $answer_a[] = $value2['AnswerText'];
                        }

                        // 年龄特殊判断
                        if (strpos($array_quota[$i]['Conditions'][$x]['AnswerIds'][0],'-')) {
                            $answer_a = $array_quota[$i]['Conditions'][$x]['AnswerIds'];
                        }
                    }

                    if ($answer_a != NULL) {
                        $array_quota[$i]['Conditions'][$x]['AnswerIds'] = $answer_a;
                    }

                
                }

            }

            // return $array;
            return $array_quota;

        }else {
            
            // return $array;
            return '<title>None</title>';
        }
    }

    // 产生uuid
    public function gen_uuid(){
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
}
