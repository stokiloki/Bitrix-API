<?php

$data = array(
	'id_invoice' => $_GET["id_invoice"],
    'number' => $_GET["number"],
);
print_r($data);
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

$crm_deal_update = execRest("crm.item.update",
        array(
            "entityTypeId" => "31",
            "id" => $data["id_invoice"],
            "fields" => array(
                "accountNumber" => $data["number"]
            )
        )
    );
print_r($crm_deal_update);
?>