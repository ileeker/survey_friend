<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Scgroup;
use App\Samplecube;

class ScGroupUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScGroupUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '存储SC的并处理它的Group';

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
        // 读取网页内容
        $URL = "https://api.sample-cube.com/api/Survey/GetSupplierAllocatedSurveys/1505/0d57ee95-70a1-49e6-89ec-8e8a2558a6a1";
        $aHTTP['http']['method']  = 'GET';
        $context = stream_context_create($aHTTP);
        $response = file_get_contents($URL, false, $context);
        $array = json_decode($response, true);

        Samplecube::truncate();

        // // 插入所有调查链接到数据库
        foreach ($array['Surveys'] as $value) 
        {
            if ($value['CPI'] > 1.49) {
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
                    $task->groupid = 12345;
                }else{
                    $task->groupid = $value['SurveyId'];
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

        // 清空Scgroup的数据库
        Scgroup::truncate();
        // 获取所有有Group的数据
        $group = Samplecube::where('groupid', 12345)->get();
        // 存储Group的数据
        foreach ($group as $value) {
            // 读取网页内容
            $URL = "http://api.sample-cube.com/api/Survey/GetSurveyGroups/1505/0d57ee95-70a1-49e6-89ec-8e8a2558a6a1/".$value['surveyid'];
            $aHTTP['http']['method']  = 'GET';
            $context = stream_context_create($aHTTP);
            $response = file_get_contents($URL, false, $context);
            $array = json_decode($response, true);
            Samplecube::where('surveyid', $value['surveyid'])->update(array('groupid' => $array['SurveyGroups'][0]['SurveyGroupId']));
        }
    }
}
