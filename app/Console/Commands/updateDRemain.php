<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Dynata;
use App\History;
use App\User;

class updateDRemain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:dynataRemain';

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
    }
}
