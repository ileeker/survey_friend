@extends('layouts.dashboard')

@section('country')
<div>Country : <b style="color: blue">{{$country}}</b> --- State : <b>{{$region}}</b>:{{$region_code}} --- Zip : <b>{{$zip}}</b></div>
<br>
<div>SampleCube UUID: {{$innovate_uuid}}</div>
<hr>
<div>Dynata UserId: {{$dynata_user}}</div>
<hr>
@endsection
@section('table')
<div id="opinonetwork_uuid" class="w3-container city">
    <div style="width: 49%;float:left;">
        <p><b>Dynata Generator(CA,AU,UK,FR)</b></p>
        <form action="{{route('dynata_user')}}" method="post">
            @csrf
            <label for="UserName">UserName : </label>
            <input type="text" id="userId" name="userId" value="{{$dynata_username}}">
            <p></p>
            <input type="radio" id="male1" name="sex" value="1" checked>
            <label for="male1">Man</label><br>
            <input type="radio" id="female1" name="sex" value="2">
            <label for="female1">Woman</label><br>
            <p></p>
            <label for="quantity">Date of Birth : </label>
            <input type="date" id="dob" name="dob" min="1920-01-01" max="2020-01-01" value="1985-06-08">
            <p></p>
            <label for="countryISOCode">countryISOCode : </label>
            <select name="countryISOCode" id="countryISOCode">
                <option value="US" selected>US</option>
                <option value="CA">CA</option>
                <option value="AU">AU</option>
                <option value="GB">GB</option>
                <option value="CN">CN</option>
            </select>
            <p></p>
            <label for="postalCode">postalCode : </label>
            <input type="text" id="postalCode" name="postalCode" value="{{$zip}}">
            <p></p>
            <label for="profileData">profile-data : </label>
            <textarea id="profileData" name="profileData" rows="8" cols="50">9943:1</textarea>
            <p></p>
            <input type="submit" value="Submit">
        </form>
    </div>
    <div style="width: 49%;float:left;">
        <p><b>Add Remark</b></p>
        <form action="{{route('remark')}}" method="post">
            @csrf
            <label for="SurveyId">Group ID : </label>
            <input type="text" id="surveyId" name="surveyId">
            <p></p>
            <label for="Sign">Sign : </label>
            <input type="text" id="sign" name="sign">
            <p></p>
            <label for="Remark">Remark : </label>
            <input type="text" id="remark" name="remark">
            <p></p>
            <input type="submit" value="Submit">
        </form>
    </div>
</div>
 
@endsection
@section('hideContent')

@endsection