<?php

$data = array(
    'id' => $_GET['id'],
    'services' => $_GET['services'],
    'phone' => $_GET['phone'],
    'fullname' => $_GET['fullname'],
    'email' => $_GET['email'],
    'staff_id' => $_GET['staff_id'],
    'datetime' => $_GET['datetime'],
);
$sdata = date("c",strtotime($data["datetime"]));

$crm_item_get = execRest("crm.item.get",
        array(
            "entityTypeId" => 144,
            "id" => $data['id'],
        )
    );
//print_r($crm_item_get['result']['item']);

$book = createBook($data,$sdata);
if($book['success'] == 1){
	$crm_item_update_add = execRest("crm.item.update",
        array(
            "entityTypeId" => 144,
            "id" => $data['id'],
            "fields" => array(
                "ufCrm3_1667820586" => "Успешно записано запись. Запись ID: " . $book['data'][0]['record_id'],
                "ufCrm3_1667891718" => $book['data'][0]['record_id']
            )
        )
    );
    $crm_deal_update = execRest("crm.deal.update",
        array(
            "id" => $crm_item_get['result']['item']['parentId2'],
            "fields" => array(
                "UF_CRM_1667893093" =>$crm_item_get['result']['item']['ufCrm3_1667892581']
            )
        )
    );
}else{
	$crm_item_update_add2 = execRest("crm.item.update",
        array(
            "entityTypeId" => 144,
            "id" => $data['id'],
            "fields" => array(
                "ufCrm3_1667820586" => $book['meta']['message']
            )
        )
    );
}
print_r($book);

function execRest($method,$queryData){
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

function createBook($data,$sdata){ 
  $url = 'https://api.alteg.io/api/v1/book_record/724765';
  $data2 = array(
      'phone' => $data['phone'],
      'fullname' => $data['fullname'],
      'email' => $data['email'],
      'appointments' => array(array(
        'id' => 1,
        'services' => array($data['services']),
        'staff_id' => $data['staff_id'],
        'datetime' => $sdata,
      )
      ),
  );
  $content = json_encode($data2);
  $headers[] = 'Content-Type:application/json';
  $token = "mxc2hh9j4pkyx9nzbs3w";
  $headers[] = "Authorization: Bearer ".$token.", User 35bf58259f8b383b3bc12b8921bd6f62";
  $headers[] = "Accept: application/vnd.api.v2+json";


  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
  $json_response = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 
  curl_close($curl);
  $response = json_decode($json_response, true);
  return $response;
}