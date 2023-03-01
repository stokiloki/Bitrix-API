<?php

$data = array(
    'id' => $_GET["id"],
	'date' => $_GET["date"],
	'code'=> $_GET["code"]
);  

$sdata = date("Y-m-d",strtotime($data["date"]));

$data = array(
    'id' => $_GET["id"],
    'date' => $sdata,
    'code'=> $_GET["code"]
);  

print_r($data);
$content = json_encode($data);
$ch = curl_init('http://192.168.0.10/base1c/hs/order');                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: Application/json',                                                                                
        'Authorization: Basic '. base64_encode("rest:123"))                                                                       
    );    

    $json_response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response = json_decode($json_response, true);
    $result = json_decode($response);
print_r($response);
function execRest($method,$queryData){
    $queryUrl = 'https://live.tinel.kz/rest/1/0g633o5rs1v3en8b/'.$method.'.json';
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

$crm_livefeedmessage_add = execRest("crm.livefeedmessage.add",
    [
        "fields" => [
                "POST_TITLE" => "Создано расходная накладная",
                    "MESSAGE" => "Создано расходная накладная по сделке " . $data["id"] . " " . $sdata . " ". $data["code"],

                    "ENTITYTYPEID" => 2,
                    "ENTITYID" => $data["id"]
    ]
]);
?>
