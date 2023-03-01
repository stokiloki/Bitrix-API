<?php
$postData = file_get_contents('php://input');
$data = parse_str($postData);
$data = json_decode($postData, true);
var_dump($data);

if($data['event'] == "event-create-record"){
    $result = execRest('crm.deal.add', array(
        'fields' => array(
            'TITLE' => "dd",
            'CATEGORY_ID' => "0",
        ),
        'params' => array("REGISTER_SONET_EVENT" => "Y")
    ));
}elseif($data['event'] == "event-update-record"){
    
}
else{
    
}
    

function execRest($method, $queryData){
    $queryUrl = 'https://portal.siamcenter.kz/rest/1/ts8qova3luwfvhr3/'.$method.'.json';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => http_build_query($queryData),
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, 1);
    return $result;
}

?>
