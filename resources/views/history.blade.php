@extends('layouts.dashboard')

@section('country')

@endsection
@section('table')
<!-- 美国开始 -->
<div id="us" class="w3-container city">
    <h2>History({{count($history)}})</h2>
    <table id="table_id_example" class="myTable">
        <thead>
            <tr>
                <th type="number"><small>ID</small></th>
                <th type="number"><small>GroupId</small></th>
                <th type="string"><small>CPI</small></th>
                <th type="string"><small>Site</small></th>
                <th type="string"><small>Status</small></th>
                <th type="string"><small>CreateIime</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($history as $key => $item)
            <tr>
                <td><small>{{$item['surveyId']}}</small></td>
                <td><small>{{$item['groupId']}}</small></td>
                <td><small>{{$item['cpi']}}</small></td>
                <td><small>{{$item['site']}}</small></td>
                <td><small>{{$item['status']}}</small></td>
                <td><small>{{$item['created_at']}}</small></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- 美国结束 --}}
@endsection
@section('hideContent')

@endsection