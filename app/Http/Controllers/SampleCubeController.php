<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Samplecube;
use App\Scgroup;


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
            
            if (strpos($value[0]['country'], 'UK') !== false) {
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
}
