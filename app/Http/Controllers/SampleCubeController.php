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
    public function samplecube(){

        // 判断Cookie是否存在
        if (request()->cookie('profile_json') == NULL) {
            return '<h1 style="color:red">No Cookie!</h1>';
        }

        $list = Samplecube::all();

        $group_all = Scgroup::all();

        $group_id_array = array();
        $group_id_only = array();

        // GroupID集合
        $group_id = array();

        foreach ($group_all as $value) {
            $group_id_array[$value['SurveyId']] = $value['SurveyGroupId'];
            $group_id_only[] = $value['SurveyGroupId'];
        }

        // 过滤group_id_only
        $group_id_only = array_values(array_unique($group_id_only));

        $user = auth()->user();

        $whitelist = $user->whitelists->where('status','whitelist')->all();
        $blacklist = $user->whitelists->where('status','blacklist')->all();

        foreach ($list as $key => $value1) {

            if (array_key_exists($value1['surveyid'],$group_id_array)) {
                $value1['groupID'] = $group_id_array[$value1['surveyid']];

                if ($value1['groupID'] != '') {
                    $group_id[] = $value1['groupID'];
                }

            }else {
                $value1['groupID'] = '';
            }

            foreach ($whitelist as $value2) {
                if ($value1['surveyid'] == $value2['surveyid']) {
                    $value1['whitelist'] = "w";
                }
            }
        }

        // 转换Group的数组
        $counts = array_count_values($group_id);

        foreach ($list as $key => $value1) {

            if ($value1['groupID'] != '') {
                $value1['count'] = ''.$counts[$value1['groupID']];
            }

            foreach ($blacklist as $value2) {
                if ($value1['surveyid'] == $value2['surveyid']) {
                    $value1['blacklist'] = "b";
                }
            }
        }

        // 被选中的survey id
        $select_array = array();

        foreach ($group_id_only as $id) {
            $cpi = 0;
            $select_id = '';
            foreach ($list as $value) {
                if ($value['groupID'] == $id) {
                    if ($value['cpi'] > $cpi) {
                        $cpi = $value['cpi'];
                        $select_id = $value['surveyid'];
                    }
                }
            }
            $select_array[] = $select_id;
        }

        $new_list = array();
        
        foreach ($list as $key => $value) {
            if ($value['groupID'] == '') {
                $new_list[] = $value;
            }else {
                if (in_array($value['surveyid'],$select_array)) {
                    $new_list[] = $value;
                }
            }
        }
        
        $list = $new_list;

        return $list;

        $count = count($list);

        return view('survey.samplecube',compact('list','count'));
    }
}
