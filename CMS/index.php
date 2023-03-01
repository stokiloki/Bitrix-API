<?php
header('Content-Type: text/html; charset=UTF-8');

$protocol = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
$host = explode(':', $_SERVER['HTTP_HOST']);
$host = $host[0];

define('BP_APP_HANDLER', $protocol.'://'.$host.explode('?', $_SERVER['REQUEST_URI'])[0]);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="//api.bitrix24.com/api/v1/"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body>
        <h1 style="text-align: center;margin-bottom: 2rem;width: 100%">Активити оптим</h1>
        <div style="margin-left: 30px;max-width: 26rem;">
            <div class="item">
                <h3 style="text-align: center;">"Выборка элементов СП"</h3>
                <button class="btn btn-primary" style="margin-right: 8px;" onclick="installActivity();"><i class="bi bi-download"></i> Установить действие БП</button>
                <button class="btn btn-primary" onclick="uninstallActivity('getspel');"><i class="bi bi-x-square"></i> Удалить действие</button>
            </div>
        </div>
        <script type="text/javascript">
            function installActivity()
            {
                var params = {
					'CODE': 'getspel', //код, уникальный для портала
					'HANDLER': 'https://example.com/example.app/handler.php',//ваш обработчик
					'AUTH_USER_ID': 1,
					'USE_SUBSCRIPTION': '',
					'NAME': 'Получить элементы СП',
                    'USE_PLACEMENT': 'Y',
                    'PLACEMENT_HANDLER': 'https://example.com/example.app/setting.php',//ваш файл настроек
					'DESCRIPTION': 'Принимает тип СП, категорию и стадию, выдаёт массив id элементов на стадии',
					'PROPERTIES': { //здесь параметры, которые будут задаваться через setting, чтобы не отлавливать символьные коды руками
						'typeSP': {
							'Name': 'Тип СП',
							'Type': 'string',
							'Required': 'Y',
							'Multiple': 'N'
						},
						'categoryID': { 
							'Name': 'Категория',
							'Type': 'string',
							'Required': 'Y',
							'Multiple': 'N'
						},
						'statusID': { 
							'Name': 'Статус',
							'Type': 'string',
							'Required': 'Y',
							'Multiple': 'N'
						},
                        'sTypeSP': {
							'Name': 'Тип СП',
							'Type': 'string',
							'Required': 'Y',
							'Multiple': 'N'
						},
						'sCategoryID': { 
							'Name': 'Категория',
							'Type': 'string',
							'Required': 'Y',
							'Multiple': 'N'
						},
						'sStatusID': { 
							'Name': 'Статус',
							'Type': 'string',
							'Required': 'Y',
							'Multiple': 'N'
						}
					},
                    'RETURN_PROPERTIES': { //вернём массив ID привязанных СП
                        'outputString': {
                            'Name': {
                                'ru': 'IDs',
                                'en': 'IDs'
                            },
                            'Type': 'string',
                            'Multiple': 'Y',
                            'Default': null
                        }
                    }
			    };

                BX24.callMethod(
                    'bizproc.activity.add',
                    params,
                    function(result)
                    {
                        if(result.error())
                            alert("Error: " + result.error());
                        else
                            alert("Успешно: " + result.data());
                    }
                );
            }

            function uninstallActivity(code)
            {
                let params = {
                    'CODE': code
                };

                BX24.callMethod(
                    'bizproc.activity.delete',
                    params,
                    function(result)
                    {
                        if(result.error())
                            alert('Error: ' + result.error());
                        else
                            alert("Успешно: " + result.data());
                    }
                );
            }
        </script>
    </body>
</html>