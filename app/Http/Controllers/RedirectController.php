<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\History;
use App\User;

class RedirectController extends Controller
{
    // InnovateMR开始
    public function im_postback(Request $request){
        $data = $request->except('_token');

        foreach($data as $key => $value){
            $parameter[$key] = $value;
        }

        $message = "postback";
        
        return view('success',compact('parameter','message'));
    }
    
    public function im_success(Request $request){
        
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['pid']);
        $surveyId = $data['surveyId'];
        $status = 'success';

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1 style="color:red">'.$surveyId.'---Success</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }
    
    public function im_fail(Request $request){
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['pid']);
        $surveyId = $data['surveyId'];
        $status = $data['status'];
        $reason = '';
        if (array_key_exists('termReason', $data)) {
            $reason = $data['termReason'];
        }

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>'.$surveyId.'---'.$reason.'---Fail</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }
    
    public function im_overquota(Request $request){
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['pid']);
        $surveyId = $data['surveyId'];
        $status = $data['status'];
        $reason = '';
        if (array_key_exists('termReason', $data)) {
            $reason = $data['termReason'];
        }

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>'.$surveyId.'---'.$reason.'---Overquota</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }
    
    public function im_terminate(Request $request){
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['pid']);
        $surveyId = $data['surveyId'];
        $status = $data['status'];
        $reason = '';
        if (array_key_exists('termReason', $data)) {
            $reason = $data['termReason'];
        }

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>'.$surveyId.'---'.$reason.'---Terminate</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }
    
    public function im_quality_terminate(Request $request){
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['pid']);
        $surveyId = $data['surveyId'];
        $status = $data['status'];
        $reason = '';
        if (array_key_exists('termReason', $data)) {
            $reason = $data['termReason'];
        }

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            return '<h1>'.$surveyId.'---'.$reason.'---Quality Terminate</h1>';
        }else{
            return '<h1>No this survey!</h1>';
        }
    }
    // InnovateMR结束

    // Opinionetwork开始
    public function ps_complete(Request $request){
        
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['ug']);
        $surveyId = $data['surveyid'];
        $status = strtolower($data['status']);
        if ($status == 's') {
            $status = 'success';
        }

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            if ($status == 'success') {
                return '<h1 style="color:red">'.$surveyId.'---Success</h1>';
            }else {
                return '<h1>'.$surveyId.'---'.$data['title'].'---'.$status.'</h1>';
            }
        }else{
            return '<h1>No this survey!</h1>';
        }
    }

    public function ps_postback(Request $request){
        // 所有的传递参数
        $data = $request->except('_token');

        // 参数
        $uuid = strtoupper($data['ug']);
        $surveyId = $data['surveyid'];
        $status = strtolower($data['status']);
        if ($status == 's') {
            $status = 'success';
        }

        // 判断是否有这个数据
        $history_data = History::where('uuid',$uuid)->where('surveyId',$surveyId)->first();

        if ($history_data !== NULL) {
            $history_data['status'] = $status;
            $history_data->save();
            if ($status == 'success') {
                return '<h1 style="color:red">'.$surveyId.'---Success</h1>';
            }else {
                return '<h1>'.$surveyId.'---'.$data['title'].'---'.$status.'</h1>';
            }
        }else{
            return '<h1>No this survey!</h1>';
        }
    }
    // Opinionetwork结束

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
