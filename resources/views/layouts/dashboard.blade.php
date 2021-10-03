<!DOCTYPE HTML>
<html> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <title>Surveys</title>
    <style type="text/css">
        body{
            font-family: Arial, Helvetica, sans-serif;
        }
        div{
            width: 1024px;
            margin: 0 auto;/*div相对屏幕左右居中*/
        }
        table{
            border: solid 1px #666;
            border-collapse: collapse;
            width: 100%;
            cursor: default;
        }
        tr{
            border: solid 1px #666;
            height: 30px;
        }
        table thead tr{
            background-color: #f3f3f3;
        }
        td{
            border: solid 1px #666;
			color: #666;
			padding: 5px 5px;
            text-align: center;
        }
		td a{
			text-decoration: none;
		}
        th{
            border: solid 1px #666;
            text-align: center;
            cursor: pointer;
        }
        .sequence{
            text-align: center;
        }
        .hover{
            background-color: #ddd;
        }
    </style>
    <!-- 表格的CSS结束 -->
    <!-- 导航的CSS开始 -->
	<style>
		ul {
			list-style-type: none;
			margin: 0;
			padding: 0;
			overflow: hidden;
			border: 1px solid #e7e7e7;
			background-color: #f3f3f3;
		}

		li {
			float: left;
		}

		li a {
			display: block;
			color: #666;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
		}

		li a:hover:not(.active) {
			background-color: #ddd;
		}

		li a.active {
			color: white;
			background-color: #4CAF50;
		}
	</style>
    <!-- 导航CSS的结束 -->
    <!-- 悬浮框的CSS开始 -->
    <style> 
        .black_overlay{ 
            display: none; 
            position: absolute; 
            top: 0%; 
            left: 0%; 
            width: 100%; 
            height: 100%; 
            background-color: black; 
            z-index:1001; 
            -moz-opacity: 0.8; 
            opacity:.80; 
            filter: alpha(opacity=88); 
        } 
        .white_content { 
            display: none; 
            position: absolute; 
            top: 25%; 
            left: 10%; 
            right: 10%; 
            width: 75%; 
            height: 60%; 
            padding: 25px; 
            border: 5px solid orange; 
            background-color: white; 
            z-index:1002; 
            overflow: auto;
        }
    </style>
    <!-- 悬浮框CSS的结束 -->
</head> 
<body>
    <!-- 导航开始 -->
    <div>
	<ul>
	  <li><a href="{{route('home')}}" @if (\Route::current()->getName() == 'home') class="active" @endif>Home</a></li>
	  <li><a href="{{route('dynata')}}" @if (\Route::current()->getName() == 'dynata') class="active" @endif>Dynata</a></li>
	  <li><a href="{{route('samplecube')}}" @if (\Route::current()->getName() == 'samplecube') class="active" @endif>SampleCube</a></li>
    </ul>
    <hr>
    @yield('country')
    </div>
    <!-- 导航结束 -->
	<!-- tab键的切换开始 -->
    <!-- 伦敦开始 -->
    @yield('table')   
    <script>
        function openCity(cityName) {
            var i;
            var x = document.getElementsByClassName("city");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            document.getElementById(cityName).style.display = "block";

            var x = document.getElementsByClassName("country");
            for (i = 0; i < x.length; i++) {
                x[i].className = "country";
            }
            var cityName1 = cityName + "1";
            document.getElementById(cityName1).className = "country active";
        }
    </script>
	<!-- tab键的切换结束 -->
    <!-- 隐藏的对话框开始 -->
    @yield('hideContent')
    <div id="fade" class="black_overlay"></div>
    <script type="text/javascript">
        $(function(){
        })
        function openDialog(surveyid){
            document.getElementById(surveyid).style.display='block';
            document.getElementById('fade').style.display='block'
        }
        function closeDialog(surveyid){
            document.getElementById(surveyid).style.display='none';
            document.getElementById('fade').style.display='none'
        }
    </script>
    <!-- 隐藏的对话框结束 -->
    <!-- 表格化 -->
    <script>
        $(document).ready(function(){
            $('.myTable').DataTable({
                paging: false,
                searching: false,
            });
        });
    </script>
    <!-- 表格化over -->
</body> 
</html>