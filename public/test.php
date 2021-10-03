<?php

// 测试文件
$project_url = "http://final.test/dynata.json";
$aHTTP['http']['method']  = 'GET';
$context = stream_context_create($aHTTP);
$response = file_get_contents($project_url, false, $context);
$project_all = json_decode($response, true)['data'];

return $project_all;

?>