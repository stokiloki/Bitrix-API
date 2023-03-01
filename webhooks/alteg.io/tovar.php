<?php
$data = array(
    'id' => $_GET['id'],
);
    

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

$id_yclients = [];
$bitrix_array =[];
$crm_deal_productrows_get = execRest("crm.deal.productrows.get",
    array(
        "id" => $data["id"],
));
print_r($crm_deal_productrows_get);
foreach ($crm_deal_productrows_get['result'] as $productrow) {
    $crm_product_get = execRest("crm.product.list",
        array(
            "filter" => array("PROPERTY_72" => $productrow['PRODUCT_NAME'])
    ));
    //print_r($productrow);
    $rows = array("PRODUCT_ID" => $crm_product_get['result'][0]['ID'], "PRICE" => $productrow['PRICE'], "QUANTITY"=> $productrow['QUANTITY'],"TAX_RATE" => 12,"TAX_INCLUDED" => "Y",);
    array_push($bitrix_array,$rows);
}
//print_r(count($bitrix_array));
$crm_deal_productrows_set = execRest("crm.deal.productrows.set",
        array(
            "id" => $data["id"],
            "rows" => $bitrix_array
    ));
print_r($crm_deal_productrows_set);

$crm_deal_productrows_get2 = execRest("crm.deal.productrows.get",
    array(
        "id" => $data["id"],
));
print_r($crm_deal_productrows_get2);
?>
