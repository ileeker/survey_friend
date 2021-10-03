<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dynata;
use App\History;
use App\User;
use App\Remark;

class DynataController extends Controller
{
    // ID跳转
    public function id($itemId,$id)
    {
        $url = 'https://www.peanutlabs.com/marketplace/surveyEntry.php?hash=#hash#&tid=#tid#&uid=#uid#&appid=#appid#&ltid=#ltid#&qcids=#qcids#';
        // 读取Cookie
        $array_json = json_decode(request()->cookie('profile_json'));
        $uid = $array_json->dynata_user;
        // $uid = "ustest5";
        $hash = $this->sha256($uid,$itemId);
        $tid = mt_rand();
        $appid = "10710";
        $ltid = $itemId;
        $qcids = $id;

        $url = str_replace(array('#hash#', '#tid#', '#uid#', '#appid#', '#ltid#', '#qcids#'), array($hash, $tid, $uid, $appid, $ltid, $qcids), $url);
        $url = htmlspecialchars($url);

        // 正文
        $user_id = auth()->user()->id;
        $cpi = Dynata::where('lineItemId',$itemId)->select('incentive')->get()[0]['incentive'];
        $jobId = Dynata::where('lineItemId',$itemId)->select('projectId')->get()[0]['projectId'];
        $guid = $array_json->dynata_user;

        // // 保存到历史记录
        $history = new History;
        $history->user_id = $user_id;
        $history->site = "Dynata";
        $history->surveyId = $itemId;
        $history->groupId = $jobId;
        $history->cpi = $cpi;
        $history->status = "unknown";
        $history->uuid = $uid;
        $history->save();

        // return $url;
        return '<a href="'.$url.'">Click Me</a>';
        // return redirect($url);
    }

    // 提取
    public function index()
    {
        // 取出所有的调查
        $list = Dynata::orderBy('ctime', 'DESC')->get();

        $all = $list;

        $new_all = array();
        $groupId = array();
        $allGroupId = array();

        foreach ($all as $value) {
            if (isset($new_all[$value['projectId']])) {
                if ($value['incentive'] > $new_all[$value['projectId']][0]['incentive']) {
                    $new_all[$value['projectId']][0] = "";
                    $new_all[$value['projectId']][0] = $value;
                    $new_all[$value['projectId']]['info'][] = $value;
                }else {
                    $new_all[$value['projectId']]['info'][] = $value;
                }
            }else {
                $new_all[$value['projectId']][] = $value;
                $new_all[$value['projectId']]['info'][] = $value;
            }
        }

        // 获得更新时间
        $last_time = $list[0]['created_at']->format('Y-m-d H:i:s');

        // 取出相应的Best值
        $remark = auth()->user()->remarks->all();
        // $best = auth()->user()->bests->pluck('surveyId')->toArray();
        // $black = auth()->user()->blacks->pluck('surveyId')->toArray();

        // 按照国家分组
        $us = array();
        $uk = array();
        $au = array();
        $ca = array();
        $cn = array();

        // return $new_all;

        foreach ($new_all as $key => $value) {
            if ($value[0]['countryISOCode'] == 'US') {
                $us[] = $value;
            }elseif ($value[0]['countryISOCode'] == 'GB') {
                $gb[] = $value;
            }elseif ($value[0]['countryISOCode'] == 'CA') {
                $ca[] = $value;
            }elseif ($value[0]['countryISOCode'] == 'AU') {
                $au[] = $value;
            }elseif ($value[0]['countryISOCode'] == 'CN') {
                $cn[] = $value;
            }elseif ($value[0]['countryISOCode'] == 'HK') {
                $hk[] = $value;
            }else {
                $multi[] = $value;
            }
        }

        $total['us'] = $us;
        $total['ca'] = $ca;
        $total['gb'] = $gb;
        $total['au'] = $au;
        $total['cn'] = $cn;

        return view('dynata',compact('list', 'total', 'remark', 'last_time'));
    }

    // Project的列表
    public function project($id)
    {
        // 取出所有的调查
        $list = Dynata::where('projectId',$id)->get();

        // 获得更新时间
        $last_time = $list[0]['created_at']->format('Y-m-d H:i:s');

        // 取出相应的Best值
        $best = auth()->user()->bests->pluck('surveyId')->toArray();
        $black = auth()->user()->blacks->pluck('surveyId')->toArray();

        // 按照国家分组
        $us = array();
        // $uk = array();
        // $au = array();
        // $ca = array();
        // $cn = array();

        foreach ($list as $key => $value) {
            $us[] = $value;
        }

        $total['us'] = $us;
        // $total['ca'] = $ca;
        // $total['gb'] = $gb;
        // $total['au'] = $au;
        // $total['cn'] = $cn;

        return view('dynataProject',compact('list', 'total', 'best', 'black', 'last_time'));
    }

    // 存储
    public function store()
    {
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
        // $register_url = 'https://api.peanutlabs.com/marketplace/supply/api/v1/respondents';
        // $user_json = array('userId' => 'us004','sex' => '1', 'dob' => '1990-01-17', 'countryISOCode' => 'US', 'postalCode' => '90006');
        // $options = array(
        //     'http' => array(
        //         'method'  => 'POST',
        //         'content' =>  json_encode($user_json),
        //         'header'=>  "Content-Type: application/json\r\n" .
        //                     "Accept: application/json\r\n" .
        //                     "Authorization: ".$api_token."\r\n"
        //         )
        //     );
            
        // $context  = stream_context_create( $options );
        // $result = file_get_contents( $register_url, false, $context );
        // $user_id = json_decode( $result )->userId;

        //获得所有的调查
        // 读取网页内容
        $project_url = "https://api.peanutlabs.com/marketplace/supply/api/v1/projects";
        $aHTTP['http']['method']  = 'GET';
        $aHTTP['http']['header']  = 'Authorization: '.$api_token."';";
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $project_all = json_decode($response, true)['data'];

        // 测试文件
        // $project_url = "http://cobopinion.com/dynata.json";
        // $aHTTP['http']['method']  = 'GET';
        // $context = stream_context_create($aHTTP);
        // $response = file_get_contents($project_url, false, $context);
        // $project_all = json_decode($response, true)['data'];
        
        // return $project_all;

        Dynata::truncate();

        $all_data = array();
        //开始罗列排序调查了
        foreach ($project_all as $key => $value) {
            $project_id = $value['projectId'];
            $line_items = $value['lineItems'];
            $items_count = count($line_items);
            foreach ($line_items as $item) {
                // 判断状态是不是都是Launched
                $state = $item['state'];
                $stateReason = $item['stateReason'];
                if ($state == 'LAUNCHED' && $stateReason == 'Launched by Client') {
                    // 总数小于50的直接PASS
                    if ($item['requiredCompletes'] < 100) {
                        continue;
                    }
                    // 开始了
                    $sig_lineitem = array();
                    $sig_lineitem['projectId'] = $project_id;
                    $sig_lineitem['lineItemId'] = $item['lineItemId'];
                    $sig_lineitem['count'] = $items_count;
                    $sig_lineitem['total'] = $item['requiredCompletes'];
                    $sig_lineitem['incentive'] = $item['incentive'];
                    $sig_lineitem['indicativeIncidence'] = $item['indicativeIncidence'];
                    $sig_lineitem['lengthOfInterview'] = $item['lengthOfInterview'];
                    $sig_lineitem['title'] = $item['title'];
                    $sig_lineitem['countryISOCode'] = $item['countryISOCode'];
                    $sig_lineitem['ctime'] = date('Y-m-d H:i:s',(strtotime($item['createdAt'])));
                    $sig_lineitem['mtime'] = date('Y-m-d H:i:s',(strtotime($item['updatedAt'])));
                    $sig_lineitem['filters'] = json_encode($item['quotaPlan']['filters']);
                    $quota_cells = array();
                    if (isset($item['quotaPlan']['quotaGroups'])) {
                        foreach ($item['quotaPlan']['quotaGroups'] as $quotaGroup) {
                            foreach ($quotaGroup['quotaCells'] as $quotaCell) {
                                if ($quotaCell['state'] == 'LAUNCHED') {
                                    $quota_cells[] = $quotaCell;
                                }
                            }
                        }
                    }
                    // 有些quotas都是paused，所以总数为0
                    if (count($quota_cells) > 0 && $sig_lineitem['incentive'] > 1.9) {
                        // $sig_lineitem['quotaGroups'] = ($quota_cells);
                        $sig_lineitem['quotaGroups'] = json_encode($quota_cells);
                        $all_data[] = $sig_lineitem;
                        
                        $task = new Dynata;
                        foreach ($sig_lineitem as $key1 => $value1) {
                            $task[$key1] = $value1;
                        }
                        $task->save();
    
                    }
                }
            }
        }

        return $all_data;

    }

    // 过滤条件
    public function filter($id)
    {
        // $country = Dynata::where('lineItemId',$id)->select('countryISOCode')->get()[0];
        $quota = Dynata::where('lineItemId',$id)->get();
        $quota = json_decode($quota);
        $country = $quota[0]->countryISOCode;
        $quota = json_decode($quota[0]->filters);

        $filePath = "/dynata%s.json";
        $filePath = sprintf($filePath,$country);

        // 获得文件地址
        $project_url = url('/').$filePath;
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $attrUS = json_decode($response, true);

        // $quota = Dynata::where('lineItemId',$id)->select('filters')->get()[0]['filters'];
        // $quota = json_decode($quota);

        foreach ($quota as $key1 => $value) {
            foreach ($attrUS as $key2 => $item) {
                // return var_dump($item);
                if ($value->attributeId == $item['id']) {
                    $format = '(%s)-%s';
                    $value->attributeId = sprintf($format, $item['id'], $item['name']);
                    foreach ($value->options as $optKey => $optValue) {
                        foreach ($item['options'] as $itemValue) {
                            // return $itemValue;
                            if ($optValue == $itemValue['id']) {
                                $formatOpt = '(%s)-%s';
                                $value->options[$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                            }
                        }
                    }
                }
            }
        }

        return $quota;
    }

    // Quota条件
    public function quota($id)
    {
        $quota = Dynata::where('lineItemId',$id)->get();
        $quota = json_decode($quota);
        $country = $quota[0]->countryISOCode;
        $quota = json_decode($quota[0]->quotaGroups);

        $filePath = "/dynata%s.json";
        $filePath = sprintf($filePath,$country);

        // 获得文件地址
        $project_url = url('/').$filePath;
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $attrUS = json_decode($response, true);

        foreach ($quota as $key1 => $value) {
            foreach ($attrUS as $key2 => $item) {
                // 有的quotaNodes是空
                if (!empty($value->quotaNodes)) {
                    # code...
                    if ($value->quotaNodes[0]->attributeId == $item['id']) {
                        $format = '(%s)-%s';
                        $value->quotaNodes[0]->attributeId = sprintf($format, $item['id'], $item['name']);
                        foreach ($value->quotaNodes[0]->options as $optKey => $optValue) {
                            foreach ($item['options'] as $itemValue) {
                                // return $itemValue;
                                if ($optValue == $itemValue['id']) {
                                    $formatOpt = '(%s)-%s';
                                    $value->quotaNodes[0]->options[$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $quota;
    }

    // 剩余个数
    public function remain($projectId,$itemId)
    {
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

        // 读取网页内容
        $project_url = "https://api.peanutlabs.com/marketplace/supply/api/v1/projects/".$projectId.'/lineItems//'.$itemId."/report";
        $aHTTP['http']['method']  = 'GET';
        $aHTTP['http']['header']  = 'Authorization: '.$api_token."';";
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $project_all = json_decode($response, true)['data'];

        return $project_all;
    }

    public function remain_store()
    {
        $list = Dynata::all()->pluck('projectId','lineItemId')->toArray();

        // 获得API的token
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

        // 循环读取
        foreach ($list as $key => $value) {
            // 读取网页内容
            $project_url = "https://api.peanutlabs.com/marketplace/supply/api/v1/projects/".$value.'/lineItems//'.$key."/report";
            $aHTTP['http']['method']  = 'GET';
            $aHTTP['http']['header']  = 'Authorization: '.$api_token."';";
            $context = stream_context_create($aHTTP);
            $response = file_get_contents($project_url, false, $context);
            $project_all = json_decode($response, true)['data'];
            $remain_count = $project_all['stats']['remainingCompletes'];
            Dynata::where('lineItemId', $key)->update(array('remain' => $remain_count));
        }

        return $list;
    }

    public function attribute($country,$language,$id)
    {
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

        // 读取网页内容
        $project_url = "https://api.peanutlabs.com/marketplace/supply/api/v1/attributes/".$country."/".$language."?id=".$id;
        // $project_url = "https://api.peanutlabs.com/marketplace/supply/api/v1/matchRespondent?userId=".$uid."&ltid=".$itemId;
        // $project_url = htmlspecialchars($project_url);
        $aHTTP['http']['method']  = 'GET';
        $aHTTP['http']['header']  = 'Authorization: '.$api_token."';";
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $project_all = json_decode($response, true);

        return $project_all;
    }

    // 检测是否相匹配
    public function check($itemId)
    {
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

        // 读取UserId
        // 读取Cookie
        $array_json = json_decode(request()->cookie('profile_json'));
        $uid = $array_json->dynata_user;

        // 读取网页内容
        $project_url = "https://api.peanutlabs.com/marketplace/supply/api/v1/matchRespondent?userId=".$uid."&ltid=".$itemId;
        // $project_url = htmlspecialchars($project_url);
        $aHTTP['http']['method']  = 'GET';
        $aHTTP['http']['header']  = 'Authorization: '.$api_token."';";
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $project_all = json_decode($response, true);

        // 开始替换内容
        $quota = Dynata::where('lineItemId',$itemId)->get();
        $country = $quota[0]->countryISOCode;

        $filePath = "/dynata%s.json";
        $filePath = sprintf($filePath,$country);

        // 获得文件地址
        $project_url = url('/').$filePath;
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($project_url, false, $context);
        $attrUS = json_decode($response, true);

        $quota = $project_all['matchedQuotaCells'];
        // foreach改不了值，只能通过i来改了
        $i = 0;

        if (!empty($quota)) {
            foreach ($quota as $key1 => $value) {
                foreach ($attrUS as $key2 => $item) {
                    if (!empty($value['quotaNodes'])) {
                        if ($value['quotaNodes'][0]['attributeId'] == $item['id']) {
                            $format = '(%s)-%s';
                            $quota[$i]['quotaNodes'][0]['attributeId'] = sprintf($format, $item['id'], $item['name']);
                            foreach ($value['quotaNodes'][0]['options'] as $optKey => $optValue) {
                                foreach ($item['options'] as $itemValue) {
                                    // return $itemValue;
                                    if ($optValue == $itemValue['id']) {
                                        $formatOpt = '(%s)-%s';
                                        $quota[$i]['quotaNodes'][0]['options'][$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                                    }
                                }
                            }
                        }
                    }
                }
                $i = $i + 1;
            }
        }

        $project_all['matchedQuotaCells'] = $quota;

        if (isset($project_all['nonMatchedQuotaGroups'][0]['quotaCells'])) {
            // 改一改NonMatched
            $quota = $project_all['nonMatchedQuotaGroups'][0]['quotaCells'];
            // return var_dump($quota);
            // foreach改不了值，只能通过i来改了
            $i = 0;

            if (!empty($quota)) {
                foreach ($quota as $key1 => $value) {
                    foreach ($attrUS as $key2 => $item) {
                        if (!empty($value['quotaNodes'])) {
                            if ($value['state'] == "PAUSED") {
                                unset($quota[$key1]);
                                continue;
                            }
                            if ($value['quotaNodes'][0]['attributeId'] == $item['id']) {
                                $format = '(%s)-%s';
                                $quota[$i]['quotaNodes'][0]['attributeId'] = sprintf($format, $item['id'], $item['name']);
                                foreach ($value['quotaNodes'][0]['options'] as $optKey => $optValue) {
                                    foreach ($item['options'] as $itemValue) {
                                        // return $itemValue;
                                        if ($optValue == $itemValue['id']) {
                                            $formatOpt = '(%s)-%s';
                                            $quota[$i]['quotaNodes'][0]['options'][$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $i = $i + 1;
                }
            }

            $project_all['nonMatchedQuotaGroups'][0]['quotaCells'] = $quota;
        }

        if (isset($project_all['nonMatchedQuotaGroups'][1]['quotaCells'])) {
            // 改一改NonMatched
            $quota = $project_all['nonMatchedQuotaGroups'][1]['quotaCells'];
            // return var_dump($quota);
            // foreach改不了值，只能通过i来改了
            $i = 0;

            if (!empty($quota)) {
                foreach ($quota as $key1 => $value) {
                    foreach ($attrUS as $key2 => $item) {
                        if (!empty($value['quotaNodes'])) {
                            if ($value['state'] == "PAUSED") {
                                unset($quota[$key1]);
                                continue;
                            }
                            if ($value['quotaNodes'][0]['attributeId'] == $item['id']) {
                                $format = '(%s)-%s';
                                $quota[$i]['quotaNodes'][0]['attributeId'] = sprintf($format, $item['id'], $item['name']);
                                foreach ($value['quotaNodes'][0]['options'] as $optKey => $optValue) {
                                    foreach ($item['options'] as $itemValue) {
                                        // return $itemValue;
                                        if ($optValue == $itemValue['id']) {
                                            $formatOpt = '(%s)-%s';
                                            $quota[$i]['quotaNodes'][0]['options'][$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $i = $i + 1;
                }
            }

            $project_all['nonMatchedQuotaGroups'][1]['quotaCells'] = $quota;
        }

        if (isset($project_all['nonMatchedQuotaGroups'][2]['quotaCells'])) {
            // 改一改NonMatched
            $quota = $project_all['nonMatchedQuotaGroups'][2]['quotaCells'];
            // return var_dump($quota);
            // foreach改不了值，只能通过i来改了
            $i = 0;

            if (!empty($quota)) {
                foreach ($quota as $key1 => $value) {
                    foreach ($attrUS as $key2 => $item) {
                        if (!empty($value['quotaNodes'])) {
                            if ($value['state'] == "PAUSED") {
                                unset($quota[$key1]);
                                continue;
                            }
                            if ($value['quotaNodes'][0]['attributeId'] == $item['id']) {
                                $format = '(%s)-%s';
                                $quota[$i]['quotaNodes'][0]['attributeId'] = sprintf($format, $item['id'], $item['name']);
                                foreach ($value['quotaNodes'][0]['options'] as $optKey => $optValue) {
                                    foreach ($item['options'] as $itemValue) {
                                        // return $itemValue;
                                        if ($optValue == $itemValue['id']) {
                                            $formatOpt = '(%s)-%s';
                                            $quota[$i]['quotaNodes'][0]['options'][$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $i = $i + 1;
                }
            }

            $project_all['nonMatchedQuotaGroups'][2]['quotaCells'] = $quota;
        }

        if (isset($project_all['nonMatchedQuotaGroups'][3]['quotaCells'])) {
            // 改一改NonMatched
            $quota = $project_all['nonMatchedQuotaGroups'][3]['quotaCells'];
            // return var_dump($quota);
            // foreach改不了值，只能通过i来改了
            $i = 0;

            if (!empty($quota)) {
                foreach ($quota as $key1 => $value) {
                    foreach ($attrUS as $key2 => $item) {
                        if (!empty($value['quotaNodes'])) {
                            if ($value['state'] == "PAUSED") {
                                unset($quota[$key1]);
                                continue;
                            }
                            if ($value['quotaNodes'][0]['attributeId'] == $item['id']) {
                                $format = '(%s)-%s';
                                $quota[$i]['quotaNodes'][0]['attributeId'] = sprintf($format, $item['id'], $item['name']);
                                foreach ($value['quotaNodes'][0]['options'] as $optKey => $optValue) {
                                    foreach ($item['options'] as $itemValue) {
                                        // return $itemValue;
                                        if ($optValue == $itemValue['id']) {
                                            $formatOpt = '(%s)-%s';
                                            $quota[$i]['quotaNodes'][0]['options'][$optKey] = sprintf($formatOpt,$optValue,$itemValue['text']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $i = $i + 1;
                }
            }

            $project_all['nonMatchedQuotaGroups'][3]['quotaCells'] = $quota;
        }

        return $project_all;

    }

    public function sha256($uid = 'test', $lineitemid = '1234567')
    {
        // $str = '{uid} + {lineItemIDappid}';
        // $str = str_replace(array('uid','lineItemID','appid'),array($uid, $lineitemid,'10710'),$str);
        $str = $uid.$lineitemid.'10710';
        $sha256 = hash_hmac('sha256', $str, 'ec3c231daaf6f92ab38180e5c4784538');
        return $sha256;
    }
}