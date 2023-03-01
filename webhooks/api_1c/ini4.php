<?php

$data = array(
    'id' => $_GET["id"],
	'date' => $_GET["date"],
    'bin' => $_GET["bin"],
    'name' => $_GET["name"],
    'kbe' => $_GET["kbe"],
    'okpo' => $_GET["okpo"],
    'seria' => $_GET["seria"],
    'nomer' => $_GET["nomer"],
    'data_nds' => $_GET["data_nds"]
);

$sdata = date("Y-m-d",strtotime($data["date"]));
$ndata = date("Y-m-d",strtotime($data["data_nds"]));
$data = array(
    'id' => $_GET["id"],
    'date' => $sdata,
       'bin' => $_GET["bin"],
       'name' => $_GET["name"],
    'kbe' => $_GET["kbe"],
    'okpo' => $_GET["okpo"],
    'seria' => $_GET["seria"],
    'nomer' => $_GET["nomer"],
    'data_nds' => $ndata,
    'rez' => $_GET["rez"],
    'code' => $_GET["code"]
);  

// $crm_livefeedmessage_add = execRest("crm.livefeedmessage.add",
//     [
//         "fields" => [
//                 "POST_TITLE" => "Немного о сервисе",
//                     "MESSAGE" => $data["date"] . $data["bin"] .$data["kbe"] . $data["okpo"] . $data["seria"]. $data["nomer"] . $data["data_nds"]  . $data["name"] . $data["rez"], 

//                     "ENTITYTYPEID" => 2,
//                     "ENTITYID" => $data["id"]
//     ]
// ]);

$content = json_encode($data);
$ch = curl_init('http://192.168.0.10/base1c/hs/invoice/');                                                                      
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
print_r($response);
// $crm_livefeedmessage_add = execRest("crm.livefeedmessage.add",
//     [
//         "fields" => [
//                 "POST_TITLE" => "Немного о сервисе",
//                     "MESSAGE" => $response, 

//                     "ENTITYTYPEID" => 2,
//                     "ENTITYID" => $data["id"]
//     ]
// ]);
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
// $crm_livefeedmessage_add2 = execRest("crm.livefeedmessage.add",
//     [
//         "fields" => [
//                 "POST_TITLE" => "Немного о сервисе",
//                     "MESSAGE" => $response, 

//                     "ENTITYTYPEID" => 2,
//                     "ENTITYID" => $data["id"]
//     ]
// ]);
$crm_deal_update = execRest("crm.deal.update",
        array("id" => $data["id"],
            "fields" => array(
                "UF_CRM_1666859263" => $response
            )
        )
    );

?>