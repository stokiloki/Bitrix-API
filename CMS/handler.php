<?php
$protocol = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
$host = explode(':', $_SERVER['HTTP_HOST']);
$host = $host[0];

define('BP_APP_HANDLER', $protocol.'://'.$host.$_SERVER['REQUEST_URI']);

if (!empty($_REQUEST['workflow_id']))//добавим простые проверки - измените под себя
{
    if (!empty($_REQUEST['properties']['typeSP'])){

        $par = array( //сформируем параметры для выборки элементов нужного СП на выбранном статусе
            'entityTypeId' => $_REQUEST['properties']['typeSP'], 
            'select'       => ['id'],
            'order'        => null, 
            'filter'       => ['categoryId' => $_REQUEST['properties']['categoryID'], 'stageId' => $_REQUEST['properties']['statusID']],
        );

        //используем вебхук с правами на CRM, чтобы не отвлекаться на Crest - настраивайте под задачу
        $result = callB24Method('https://example.bitrix24.ru/rest/1/59i35rrrzqg0np/','crm.item.list', $par); //запрашиваем ID's элементов СП

        $arr = [];
        foreach($result['result']['items'] as $item){ //готовим простой массив
            $arr[] = $item['id'];
        }

        //берем авторизацию из пришедшего БП, добавляем массив, возвращаем в БП
        $params = array(
            "auth" => $_REQUEST['auth']["access_token"],
            "event_token" => $_REQUEST["event_token"],
            "log_message" => "Элементы получены",
            "return_values" => array(
                "outputString" => $arr,
            )
        );
        $r = callB24Method('https://example.bitrix24.ru/rest/','bizproc.event.send', $params);
    }

}


function callB24Method($bitrix, $method, $params){ //напишем функцию для отправки запросов через вебхук
    $c = curl_init($bitrix . $method . '.json');

    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_POST, true);
    curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($c);
    $response = json_decode($response, true);

    return $response;
}