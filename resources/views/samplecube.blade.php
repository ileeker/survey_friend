@extends('layouts.dashboard')

@section('country')
<ul>
    <li><a href="#news" onclick="openCity('us')" id="us1" class="country active">US</a></li>
    <li><a href="#news" onclick="openCity('ca')" id="ca1" class="country">CA</a></li>
    <li><a href="#news" onclick="openCity('uk')" id="uk1" class="country">UK</a></li>
    <li><a href="#news" onclick="openCity('au')" id="au1" class="country">AU</a></li>
    <li><a href="#news" onclick="openCity('hk')" id="hk1" class="country">HK</a></li>
    <li><a href="#" class="country">{{$last_time}}</a></li>
</ul>
<hr>
@endsection
@section('table')
@foreach ($total as $keyName => $value)
<!-- 美国开始 -->
<div id="{{$keyName}}" class="w3-container city" @if ($keyName != 'us') style="display: none" @endif>
    <h2>{{$keyName}}({{count($value)}})</h2>
    <table id="table_id_example" class="myTable">
        <thead>
            <tr>
                <th type="number"><small>M</small></th>
                <th type="number"><small>ID</small></th>
                <th type="number"><small>Group</small></th>
                <th type="number"><small>CPI</small></th>
                <th type="number"><small>Tot</small></th>
                <th type="number"><small>Rem</small></th>
                <th type="number"><small>Min</small></th>
                <th type="number"><small>IR</small></th>
                <th type="number"><small>Country</small></th>
                <th type="number"><small>Utime</small></th>
                <th ><small>Q</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($value as $key => $item)
            <tr>
                <td class="sequence"><small>
                    @foreach ($remark as $remark1)
                        @if ($item[0]['groupid'] == $remark1->surveyId)
                            <a href=# title="{{$remark1->remark}}">{{$remark1->sign}}</a>
                        @endif
                    @endforeach        
                </small>
                </td>
                <td><small><a target="_blank" href="{{route('samplecube')}}/{{$item[0]['surveyid ']}}" >{{$item[0]['surveyid ']}}</a></small></td>
                <td><small>({{count($item['info'])}})<a href="#" onclick="openDialog('{{$item[0]['group_id']}}')">{{$item[0]['group_id']}}</a></small></td>
                <td><small>${{$item[0]['cpi']}}</small></td>
                <td><small>{{$item[0]['totalquota']}}</small></td>
                <td><small>{{$item[0]['remainquota']}}</small></td>
                <td><small>{{$item[0]['loi']}}.0</small></td>
                <td><small>{{$item[0]['ir']}}%</small></td>
                <td><small>{{$item[0]['country']}}</small></td>
                <td><small><small>{{$item[0]['UpdateTimeStamp']}}</small></small></td>
                <td><small><a target="_blank" href="{{route('samplecube')}}/quota/{{$item[0]['prj_id']}}" >quota</a></small></td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
</div>
{{-- 美国结束 --}}
@endforeach
@endsection
@section('hideContent')
@foreach ($new_all as $key => $item)
<div id="{{$key}}" class="white_content">
    <table id="tableSort" class="myTable">
        <thead>
            <tr>
                <th type="number"><small>ID</small></th>
                <th type="number"><small>Group</small></th>
                <th type="number"><small>CPI</small></th>
                <th type="number"><small>Tot</small></th>
                <th type="number"><small>Rem</small></th>
                <th type="number"><small>Min</small></th>
                <th type="number"><small>IR</small></th>
                {{-- <th type="number"><small>Country</small></th> --}}
                <th type="number"><small>Name</small></th>
                <th type="number"><small>Ctime</small></th>
                <th type="number"><small>Etime</small></th>
                <th ><small>Q</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($item['info'] as $sig)
            <tr>
                <td><small><a target="_blank" href="{{route('samplecube')}}/{{$sig['prj_id']}}" >{{$sig['prj_id']}}</a></small></td>
                <td><small><a href="#" onclick="openDialog('{{$sig['group_id']}}')">{{$sig['group_id']}}</a></small></td>
                <td><small>${{$sig['P_payout']}}</small></td>
                <td><small>{{$sig['total_completes']}}</small></td>
                <td><small>{{$sig['remain']}}</small></td>
                <td><small>{{$sig['loi']}}Min</small></td>
                <td><small>{{$sig['ir']}}%</small></td>
                {{-- <td><small>{{$sig['country']}}</small></td> --}}
                <td><small><small>{{$sig['prj_name']}}</small></small></td>
                <td><small><small>{{$sig['ctime']}}</small></small></td>
                <td><small><small>{{$sig['updated_at']}}</small></small></td>
                <td><small><a target="_blank" href="{{route('samplecube')}}/quota/{{$sig['prj_id']}}" >quota</a></small></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <button onclick = "closeDialog('{{$key}}')">点这里关闭本窗口</button>
</div>
@endforeach
@endsection