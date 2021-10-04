<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\History;
use App\User;

class RedirectController extends Controller
{

    // SC的开始
    public function sc_succsee(Request $request){
        
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $guid = strtoupper($data['pid']);
        $status = 'success';

        // 判断是否有这个数据
        $history_data = History::where('uuid',$guid)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1 style="color:red">Success</h1>';
        }else{
            return '<h1>Success? No this survey?</h1>';
        }
    }

    public function sc_overquota(Request $request){
        
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $guid = strtoupper($data['pid']);
        $status = 'overquota';

        // 判断是否有这个数据
        $history_data = History::where('uuid',$guid)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>Overquota</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }

    public function sc_terminate(Request $request){
        
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $guid = strtoupper($data['pid']);
        $status = 'Terminate';

        // 判断是否有这个数据
        $history_data = History::where('uuid',$guid)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>Terminate</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }

    public function sc_security_terminate(Request $request){
        
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $guid = strtoupper($data['pid']);
        $status = 'security terminate';

        // 判断是否有这个数据
        $history_data = History::where('uuid',$guid)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>Security Terminate</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }

    // SC的结束

    // Dynata开始
    public function dynata(Request $request){
        
        $data = $request->except('_token');
        
        // 读取数据
        $status = $data['status'];
        if ($status == "C") {
            $status = "success";
        }
        $uuid = $data['endUserId'];
        $surveyId = $data['offerInvitationId'];

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            // if ($status == 'C') {
            //     return '1';
            // }else {
            //     return '2';
            // }
        }

        return "1";
    }
    // Dynata结束
}
