<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Dynata;
use App\History;
use App\User;

class updateD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:dynata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
    }
}
