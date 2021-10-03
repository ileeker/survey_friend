@extends('layouts.dashboard')

@section('country')
<ul>
    <li><a href="#news" onclick="openCity('us')" id="us1" class="country active">US</a></li>
    <!-- <li><a href="#news" onclick="openCity('ca')" id="ca1" class="country">CA</a></li> -->
    <!-- <li><a href="#news" onclick="openCity('gb')" id="gb1" class="country">GB</a></li> -->
    <!-- <li><a href="#news" onclick="openCity('au')" id="au1" class="country">AU</a></li> -->
    <!-- <li><a href="#news" onclick="openCity('cn')" id="cn1" class="country">CN</a></li> -->
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
                <th ><small>Check</small></th>
                {{-- <th ><small>Count</small></th> --}}
                <th ><small>Title</small></th>
                <th ><small>Time</small></th>
                <th ><small>Diff</small></th>
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
                        @if (in_array($item['projectId'],$best))
                        W
                        @endif
                        @if (in_array($item['projectId'],$black))
                            B
                        @endif  
                    </td>
                    <td><small><a target="_blank" href="#" >{{$item['projectId']}}</a></small></td>
                    {{-- <td><small><a href="#" onclick="openDialog('{{$item['lineItemId']}}')">{{$item['lineItemId']}}</a></small></td> --}}
                    <td><small><a target="_blank" href="{{route('dynata')}}/{{$item['lineItemId']}}/{{json_decode($item['quotaGroups'])[0]->id}}" >{{$item['lineItemId']}}</a>-<a href="#" onclick="openDialog('{{$item['lineItemId']}}')">L({{count(json_decode($item['quotaGroups']))}})</a></small></td>
                    <td><small>${{$item['incentive']}}</small></td>
                    <td><small>{{$item['total']}}</small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/remain/{{$item['projectId']}}/{{$item['lineItemId']}}" >{{$item['remain']}}</a></small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/check/{{$item['lineItemId']}}" >Check</a></small></td>
                    {{-- <td><small>{{$item['count']}}</small></td> --}}
                    <td><small>{{$item['title']}}</small></td>
                    <td><small>{{$item['lengthOfInterview']}}Min</small></td>
                    <td><small>{{$item['indicativeIncidence']}}%</small></td>
                    {{-- <td><small>{{$item['countryISOCode']}}</small></td> --}}
                    <td><small><small>{{$item['ctime']}}</small></small></td>
                    <td><small><small>{{$item['mtime']}}</small></small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/filter/{{$item['lineItemId']}}" ><small>Filter</small></a></small></td>
                    <td><small><a target="_blank" href="{{route('dynata')}}/quota/{{$item['lineItemId']}}" ><small>Quota</small></a></small></td>
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