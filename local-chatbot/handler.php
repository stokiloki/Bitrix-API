<?php

require_once(__DIR__ . '/crest.php');

switch(strtoupper($_REQUEST['event']))
{
	case 'ONIMBOTJOINCHAT':
		// отправляем сообщения
		CRest::call(
			'imbot.message.add',
			[
				'DIALOG_ID' => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
				'MESSAGE' => 'Шалом я Чат бот ))',
			]
		);

		break;
	case 'ONIMBOTMESSAGEADD':
		// response from our bot
		$report = getAnswer($_REQUEST['data']['PARAMS']['MESSAGE']);

		// send answer message
		CRest::call(
			'imbot.message.add',
			[
				"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
				"MESSAGE" => $report['title'] . "\n" . $report['report'] . "\n",
			]
		);

		break;
}

function getAnswer($command = '')
{
	return [
		'title' => 'Вы сказали: ',
		'report' => $command,
	];
}