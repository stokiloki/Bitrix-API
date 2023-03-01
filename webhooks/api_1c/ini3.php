<?php

$data = array(
    'id' => $_GET["id"],
    'code_sklad' => $_GET["code_sklad"]
);
if($data['code_sklad'] == 'НФ-000006'){
	$sklad = 'Розы Люксембург';
}else{
	$sklad = 'Омск';

}

function execRest($method,$queryData){
    $queryUrl = 'https://live.tinel.kz/rest/1/*/'.$method.'.json';
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


$crm_item_prod_list = execRest("crm.item.productrow.list",
    ["filter" => [
    "=ownerType" => "D",
        "=ownerId" => $data["id"]
    ]]);

//var_dump($crm_item_prod_list['result']['productRows']);
$mess = array();
foreach ($crm_item_prod_list['result']['productRows'] as $result){
    $prod = $result['productName'];
    $prod_id = $result['id'];
    $prod_name = strval($prod);
    $str=strpos($prod_name, ":");
    $prod_name=substr($prod_name, 0, $str);
    $prod_name=substr($prod_name, 0, -1);
    $prod_x= strval($prod);
    $prod_x = substr($prod_x, strpos($prod_x, ':') + 3, strlen($prod_x));
     $data_for_1c = array(
            "id_product" => strval($prod_name),
            "x" => strval($prod_x),
            "code" => strval($data['code_sklad']),
        );
    $content = json_encode($data_for_1c);

    $ch = curl_init('http://192.168.0.10/base1c/hs/balance/');                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($content),
        'Authorization: Basic '. base64_encode("rest:123"))                                                                       
    );                                                                                                                   
    $json_response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response = json_decode($json_response, true);
    $result = json_decode($response);
    if($response[0]['balance'] == 0){
        $prod_mess = "Товар: " . $prod_name .". " . "\n" . "Остаток: Нет на складе " . $sklad . "\n" . "\n";
    }else{
        $prod_mess = "Товар: " . $prod_name .". " . "\n" . "Остаток: " . $response[0]['balance']  . " на складе " . $sklad . "\n" . "\n";
    }
    array_push($mess,array($prod_mess));
}

print_r($response[0]);
$message = '';
foreach ($mess as $m) {
    $message .= implode(" \n",$m);
}

//var_dump($message);

$crm_livefeedmessage_add = execRest("crm.livefeedmessage.add",
    [
        "fields" => [
                "POST_TITLE" => "Немного о сервисе",
                    "MESSAGE" => $message,

                    "ENTITYTYPEID" => 2,
                    "ENTITYID" => $data["id"]
    ]
]);
var_dump($crm_livefeedmessage_add);

?>