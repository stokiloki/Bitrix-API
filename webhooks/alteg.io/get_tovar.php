<?php

$data = array(
    'id' => $_GET['id'],
    'personal' => $_GET['personal']
);

$personal = $data['personal'];

function execRest($method,$queryData){
    $queryUrl = 'https://portal.siamcenter.kz/rest/1/m0alxae7a53rplmd/'.$method.'.json';
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

foreach ($crm_deal_productrows_get['result'] as $productrow) {
    $crm_product_get = execRest("crm.product.get",
        array(
            "id" => $productrow['PRODUCT_ID'],
    ));
    array_push($bitrix_array,$crm_product_get['result']['PROPERTY_72']['value']);
    array_push($id_yclients,array('id_tovar'=>$crm_product_get['result']['PROPERTY_72']['value'],'name_tovar' =>$crm_product_get['result']['NAME'],'price_tovar' =>$crm_product_get['result']['PRICE']));
}

$altegio = '';
$altegio = implode(',', $bitrix_array);
//print_r($altegio);

$crm_deal_update = execRest("crm.deal.update",
        array(
            "id" => $data["id"],
            "fields" => array(
                "UF_CRM_1666724728" => $altegio
            )
        )
    );
print_r($crm_deal_update);

function getService($id_tovar,$personal,$id,$name_tovar,$price_tovar){
  $url = 'https://api.alteg.io/api/v1/services/724765/'.$id_tovar;
  $i = 0;
  $k = array();
  $mask = array();
  $headers[] = 'Content-Type:application/json';
  $token = "mxc2hh9j4pkyx9nzbs3w";
  $headers[] = "Authorization: Bearer ".$token.", User 35bf58259f8b383b3bc12b8921bd6f62";
  $headers[] = "Accept: application/vnd.api.v2+json";


  $curl = curl_init($url);
  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_HEADER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POST => false,
  ]);


  $json_response = curl_exec($curl);

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  if ( $status != 200 ) {
      die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
  }

    curl_close($curl);
    $response = json_decode($json_response, true);
    $proverka = [];
  foreach ($response['data']['staff'] as $staff) {
    if($staff['id'] == $personal){
        $i = $i + 1;
    }
    else{
        $i = $i + 0;
    }
  }
  if($i == 1){
    array_push($proverka, array('id_toval'=>$id_tovar,'status'=>'true'));
  }else{
    array_push($proverka, array('id_toval'=>$id_tovar,'status'=>'false'));
  }

 foreach ($proverka as $prov) {
    if($prov['status'] == 'false'){
     $crm_livefeedmessage_add = execRest("crm.livefeedmessage.add",
         array(
             "fields" => array(
                    "POST_TITLE" => "Ошибка",
                        "MESSAGE" => "Название услуги: ".$name_tovar ." Цена: ". $price_tovar ." на этом услуги нету соответствующего персонала",

                        "ENTITYTYPEID" => 2,
                        "ENTITYID" => $id
        )
    ));
   array_push($k, 0);
  }else{
    array_push($k, 1);
  }
    }
    for ($j=0; $j <count($proverka) ; $j++) { 
        array_push($mask, 1);
    }

    $exp = $k===$mask;
    return $exp;

}

$array = [];
foreach ($id_yclients as $id_client) {
    $service = getService($id_client['id_tovar'],$personal,$data['id'],$id_client['name_tovar'],$id_client['price_tovar']);
    array_push($array,$service);
}
if(array_search('false',$array)){
    print_r("error");
}
else{
    $get_book_staff = getBookStaffSeances($personal);
    $seance_data = $get_book_staff['data']['seance_data'];
    $seance_array = []; 
    foreach ($get_book_staff['data']['seances'] as $seance) {
        array_push($seance_array,array('time'=>'Время: ' . $seance['time'] . "\n"));
    }
    $message = '';
    foreach ($seance_array as $m) {
        $message .= implode("\n",$m);
    }
    //print_r($message);
    $crm_livefeedmessage_add_seance = execRest("crm.livefeedmessage.add",
        array(
            "fields" => array(
                    "POST_TITLE" => "Список ближайших доступных сеансов",
                        "MESSAGE" => "Список ближайших доступных сеансов: " . $message,
                        "ENTITYTYPEID" => 2,
                        "ENTITYID" => $data['id']
        )
    ));
    //print_r($crm_livefeedmessage_add_seance);
}

function getBookStaffSeances($personal){
    $url = 'https://api.alteg.io/api/v1/book_staff_seances/724765/'.$personal;
    $headers[] = 'Content-Type:application/json';
    $token = "mxc2hh9j4pkyx9nzbs3w";
    $headers[] = "Authorization: Bearer ".$token.", User 35bf58259f8b383b3bc12b8921bd6f62";
    $headers[] = "Accept: application/vnd.api.v2+json";
    $curl = curl_init($url);
    curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_HEADER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POST => false,
    ]);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ( $status != 200 ) {
      die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }
    curl_close($curl);
    $response = json_decode($json_response, true);
   return $response;
}