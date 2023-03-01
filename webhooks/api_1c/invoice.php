<?php

$data = array(
	'id_invoice' => $_GET["id_invoice"],
    'id_deal' => $_GET["id_deal"],
);
//print_r($data);
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

$crm_item_get = execRest("crm.item.get",
        array(
            "entityTypeId" => "31",
            "id" => $data["id_invoice"],
            "fields" => array(
                "ufCrmSmartInvoice1669960460"
            )
        )
    );
//print_r($crm_item_get['result']['item']['ufCrmSmartInvoice1669960460']);
$crm_deal_update = execRest("crm.deal.update",
        array(
            "id" => $data["id_deal"],
            "fields" => array(
                "UF_CRM_1669961750" => $crm_item_get['result']['item']['ufCrmSmartInvoice1669960460']
            )
        )
    );
?>