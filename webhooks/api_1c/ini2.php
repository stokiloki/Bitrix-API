<?php

$data = array(
    'id' => $_GET["id"],
	'code' => $_GET["code"],
);
if($data["code"]=="00-000001" || $data["code"]=="НФ-000019"|| $data["code"]=="НФ-000015"|| $data["code"]=="НФ-000012"|| $data["code"]=="НФ-000010"|| $data["code"]=="НФ-000006"|| $data["code"]=="НФ-000001"|| $data["code"]=="НФ-000009" ){
    $currency = "KZT";
}elseif($data["code"]=="НФ-000018"){
    $currency = "EUR";
}
elseif($data["code"]=="НФ-000017" || $data["code"]=="НФ-000008" || $data["code"]=="НФ-000005" || $data["code"]=="НФ-000004"){
    $currency = "USD";
}else{
     $currency = "RUB";
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

//print_r($crm_item_prod_list['result']['productRows']);
$crm_item_prod_update = execRest("crm.deal.update",
        array("id" => $data["id"],
            "fields" => array(
                "CURRENCY_ID" => $currency
            )
        )
    );
print_r($crm_item_prod_update);
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
            "code" => strval($data['code']),
        );

    $content = json_encode($data_for_1c);

    $ch = curl_init('http://192.168.0.10/base1c/hs/price/');                                                                      
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
    //print_r($response[0]['price']);

    $crm_item_prod_update = execRest("crm.item.productrow.update",
        array("id" => $prod_id,
            "fields" => array(
                "price" => $response[0]['price']
            )
        )
    );
    $crm_update = execRest("crm.item.productrow.update",
        array("id" => $prod_id,
            "fields" => array(
                "price" => $response[0]['price']
            )
        )
    );
    //print_r($crm_item_prod_update);
}

?>