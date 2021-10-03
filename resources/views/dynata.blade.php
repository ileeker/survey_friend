@extends('layouts.dashboard')

@section('country')
<ul>
    <li><a href="#news" onclick="openCity('us')" id="us1" class="country active">US</a></li>
    <li><a href="#news" onclick="openCity('ca')" id="ca1" class="country">CA</a></li>
    <li><a href="#news" onclick="openCity('gb')" id="gb1" class="country">GB</a></li>
    <li><a href="#news" onclick="openCity('au')" id="au1" class="country">AU</a></li>
    <li><a href="#news" onclick="openCity('cn')" id="cn1" class="country">CN</a></li>
    <li><a href="{{route('dynata')}}/attribute/US/en/12372485" target="_blank" id="attribute" class="country">Attribute</a></li>
    <li><a href="#" class="country">{{$last_time}}</a></li>
</ul>
<hr>
@endsection
@section('table')
<!-- 美国开始 -->
@foreach ($total as $keyName => $value)
<div id="{{$keyName}}" class="w3-container city" @if ($keyName != 'us') style="display: none" @endif>
    <h2>{{$keyName}}({{count($value)}})</h2>
    <table id="table_id_example" class="myTable">
        <thead>
            <tr>
                <th ><small>M</small></th>
                <th ><small>ProjectId</small></th>
                <th ><small>LineItemId</small></th>
                <th ><small>CPI</small></th>
                <th ><small>Tot</small></th>
                <th ><small>Rem</small></th>
                <th ><small>Time</small></th>
                <th ><small>Diff</small></th>
                <th ><small>Check</small></th>
                {{-- <th ><small>Count</small></th> --}}
                <th ><small>Title</small></th>
                {{-- <th ><small>Country</small></th> --}}
                <th ><small>Ctime</small></th>
                <th ><small>Mtime</small></th>
                <th ><small>Filter</small></th>
                <th ><small>Quota</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($value as $key => $item)
            <tr>
                    <td class="sequence"><small>
                        @foreach ($remark as $remark1)
                            @if ($item[0]['projectId'] == $remark1->surveyId)
                                <a href=# title="{{$remark1->remark}}">{{$remark1->sign}}</a>
                            @endif
                        @endforeach 
                    </td>
                    <td><small>({{count($item['info'])}})<a target="_blank" href="{{route('dynata')}}/project/{{$item[0]['projectId']}}" >{{$item[0]['projectId']}}</a></small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/{{$item[0]['lineItemId']}}/{{json_decode($item[0]['quotaGroups'])[0]->id}}" >{{$item[0]['lineItemId']}}</a>-<a href="#" onclick="openDialog('{{$item[0]['lineItemId']}}')">L({{count(json_decode($item[0]['quotaGroups']))}})</a></small></td>
                    <td><small>${{$item[0]['incentive']}}</small></td>
                    <td><small>{{$item[0]['total']}}</small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/remain/{{$item[0]['projectId']}}/{{$item[0]['lineItemId']}}" >{{$item[0]['remain']}}</a></small></td>
                    <td><small>{{$item[0]['lengthOfInterview']}}.0</small></td>
                    <td><small>{{$item[0]['indicativeIncidence']}}%</small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/check/{{$item[0]['lineItemId']}}" >Check</a></small></td>
                    {{-- <td><small>{{$item[0]['count']}}</small></td> --}}
                    <td><small>{{$item[0]['title']}}</small></td>
                    {{-- <td><small>{{$item[0]['countryISOCode']}}</small></td> --}}
                    <td><small><small>{{$item[0]['ctime']}}</small></small></td>
                    <td><small><small>{{$item[0]['mtime']}}</small></small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/filter/{{$item[0]['lineItemId']}}" ><small>Filter</small></a></small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/quota/{{$item[0]['lineItemId']}}" ><small>Quota</small></a></small></td>
                </tr>
                @endforeach
        </tbody>
    </table>
</div>
@endforeach
{{-- 美国结束 --}}
@endsection
@section('hideContent')
@foreach ($list as $key => $item)
<div id="{{$item['lineItemId']}}" class="white_content">
    <table id="tableSort" class="myTable">
        <thead>
            <tr>
                <th ><small><small>ID</small></small></th>
                <th ><small><small>State</small></small></th>
                <th ><small><small>Count</small></small></th>
            </tr>
        </thead>
        <tbody>
            @foreach (json_decode($item['quotaGroups']) as $sig)
            <tr>
                <td><small><a target="_blank" href="{{route('dynata')}}/{{$item['lineItemId']}}/{{$sig->id}}" >Link {{$sig->id}}</a></small></td>
                <td><small>{{$sig->state}}</small></td>
                <td><small>{{$sig->count}}</small></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <button onclick = "closeDialog('{{$item['lineItemId']}}')">点这里关闭本窗口</button>
</div>
@endforeach
@endsection