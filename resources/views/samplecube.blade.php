@extends('layouts.dashboard')

@section('country')
<ul>
    <li><a href="#news" onclick="openCity('us')" id="us1" class="country active">US</a></li>
    <li><a href="#news" onclick="openCity('ca')" id="ca1" class="country">CA</a></li>
    <li><a href="#news" onclick="openCity('uk')" id="uk1" class="country">UK</a></li>
    <li><a href="#news" onclick="openCity('au')" id="au1" class="country">AU</a></li>
    <li><a href="#news" onclick="openCity('hk')" id="hk1" class="country">HK</a></li>
    <li><a href="#news" onclick="openCity('best')" id="best1" class="country">Best</a></li>
    <li><a href="#" class="country">{{$last_time}}</a></li>
</ul>
<hr>
@endsection
@section('table')
@foreach ($total as $keyName => $value)
{{-- 多国开始 --}}
<div id="{{$keyName}}" class="w3-container city" @if ($keyName != 'us') style="display: none" @endif>
    <h2>{{$keyName}}({{count($value)}})</h2>
    <table id="table_id_example" class="myTable">
        <thead>
            <tr>
                <th type="number"><small>M</small></th>
                <th type="number"><small>ID</small></th>
                <th type="string"><small>JobId</small></th>
                <th type="number"><small>CPI</small></th>
                <th type="number"><small>Tot</small></th>
                <th type="number"><small>Rem</small></th>
                <th type="number"><small>Min</small></th>
                <th type="number"><small>IR</small></th>
                <th ><small>T</small></th>
                <th type="number"><small>Country</small></th>
                <th type="number"><small>Name</small></th>
                <th type="number"><small>Type</small></th>
                <th type="number"><small>Ctime</small></th>
                <th type="number"><small>Mtime</small></th>
                <th ><small>Q</small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($value as $key => $item)
            <tr>
                <td class="sequence"><small>
                    @foreach ($remark as $remark1)
                        @if ($item['jobId'] == $remark1->surveyId)
                            <a href=# title="{{$remark1->remark}}">{{$remark1->sign}}</a>
                        @endif
                    @endforeach
                </small></td>
                @if ($keyName == 'best')
                    <td><small><a target="_blank" href="{{route('innovate')}}/sub/{{$item['surveyId']}}" >{{$item['surveyId']}}</a></small></td>
                @else
                    <td><small><a target="_blank" href="{{route('innovate')}}/{{$item['surveyId']}}" >{{$item['surveyId']}}</a></small></td>
                @endif
                <td><small><a href="#" onclick="openDialog('{{$item['jobId']}}')">({{count($item['info'])}}){{$item['jobId']}}</a></small></td>
                <td><small>${{$item['CPI']}}</small></td>
                <td><small>{{$item['N']}}</small></td>
                <td><small>{{$item['remainingN']}}</small></td>
                <td><small>{{$item['LOI']}}.0</small></td>
                <td><small>{{$item['IR']}}%</small></td>
                <td><small><a target="_blank" href="{{route('innovate')}}/target/{{$item['surveyId']}}" >target</a></small></td>
                <td><small>{{$item['Country']}}</small></td>
                <td><small><small>{{$item['surveyName']}}</small></small></td>
                <td><small><small>{{$item['groupType']}}-{{$item['jobCategory']}}</small></small></td>
                <td><small><small>{{$item['ctime']}}</small></small></td>
                <td><small><small>{{$item['mtime']}}</small></small></td>
                <td><small><a target="_blank" href="{{route('innovate')}}/quota/{{$item['surveyId']}}" >quota</a></small></td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
</div>
{{-- 多国结束 --}}
@endforeach
@endsection
@section('hideContent')
@foreach ($projectNew as $key => $item)
<div id="{{$key}}" class="white_content">
    <table id="tableSort" class="myTable">
        <thead>
            <tr>
                <th type="number"><small><small>ID</small></small></th>
                <th type="number"><small><small>CPI</small></small></th>
                <th type="number"><small><small>Tot</small></small></th>
                <th type="number"><small><small>Rem</small></small></th>
                <th type="number"><small><small>LOI</small></small></th>
                <th type="number"><small><small>IR</small></small></th>
                <th type="string"><small><small>Country</small></small></th>
                <th type="string"><small><small>Name</small></small></th>
                <th type="string"><small><small>Type</small></small></th>
                <th type="string"><small><small>Ctime</small></small></th>
                <th type="string"><small><small>Mtime</small></small></th>
                <th><small><small>Target</small></small></th>
                <th><small><small>Quota</small></small></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($item['info'] as $sig)
            <tr>
                <td><small><a target="_blank" href="{{route('innovate')}}/{{$sig['surveyId']}}">{{$sig['surveyId']}}</small></a></td>
                <td><small>${{$sig['CPI']}}</small></td>
                <td><small>{{$sig['N']}}</small></td>
                <td><small>{{$sig['remainingN']}}</small></td>
                <td><small>{{$sig['LOI']}}Min</small></td>
                <td><small>{{$sig['IR']}}%</small></td>
                <td><small>{{$sig['Country']}}</small></td>
                <td><small>{{$sig['surveyName']}}</small></td>
                <td><small>{{$sig['groupType']}}-{{$sig['jobCategory']}}</small></td>
                <td><small><small>{{$sig['ctime']}}</small></small></td>
                <td><small><small>{{$sig['mtime']}}</small></small></td>
                <td><small><a target="_blank" href="{{route('innovate')}}/target/{{$sig['surveyId']}}">target</a></small></td>
                <td><small><a target="_blank" href="{{route('innovate')}}/quota/{{$sig['surveyId']}}">quota</a></small></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <button onclick = "closeDialog('{{$key}}')">点这里关闭本窗口</button>
</div>
@endforeach
@endsection