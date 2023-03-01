<?php
header('Content-Type: text/html; charset=UTF-8');

$protocol = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
$host = explode(':', $_SERVER['HTTP_HOST']);
$host = $host[0];

define('BP_APP_HANDLER', $protocol.'://'.$host.explode('?', $_SERVER['REQUEST_URI'])[0]);

$obj = json_decode($_POST['PLACEMENT_OPTIONS']); //объект с параметрами, отрисуем сохранённые параметры
$sp       = $obj->current_values->sTypeSP;
$category = $obj->current_values->sCategoryID;
$status   = $obj->current_values->sStatusID;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script src="//api.bitrix24.com/api/v1/"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
            .select {
                min-width: 25%;
                margin-bottom: 20px;
                padding-bottom: 3px;
                padding-top: 3px;
            }
            .label {
                font-size: 0.9rem;
                margin-top: 1rem;
                margin-left: 20rem;
            }
        </style>
    </head>
    <body>
        <!-- вёрстка может быть любой, главное - вызвать BX24.placement.call и сохранить новые параметры -->
        <div id="app" style="background-color:#f5f9f9; height: 250px;width: 853px;" >
            <div style="width: 635px;padding-right: 42px; float: right; display: inline-block;margin-top: 15px;">
                <div>
                    <select class="form-select select" v-model="typeSearch">
                        <option value="" disabled selected><?=$sp ?></option>
                        <option v-for="type in types" v-bind:value="{ id: type.entityTypeId, name: type.title }"> {{ type.title }}</option>
                    </select>
                </div>
                <div>
                    <select class="form-select select" v-model="categorySearch">
                        <option value="" disabled selected><?=$category ?></option>
                        <option v-for="category in categories" v-bind:value="{ id: category.id, name: category.name }"> {{ category.name }}</option>
                    </select>
                </div>
                <div >
                    <select class="form-select select" v-model="statusSearch">
                        <option value="" disabled selected><?=$status ?></option>
                        <option v-for="status in statuses" v-bind:value="{ id: status.STATUS_ID, name: status.NAME }"> {{ status.NAME }}</option>
                    </select>
                </div>
            </div>
            <div style="float: left; display: inline-block;margin-top: 15px;margin-left: 5rem;">
                <div> 
                    <p style="font-size: 0.9rem;text-align: end;padding-top: 4px;">Выберите СП:</p>
                </div>
                <div>
                    <p style="font-size: 0.9rem;text-align: end;margin-top: 29px;">Выберите воронку:</p>
                </div>
                <div>
                    <p style="font-size: 0.9rem;text-align: end;margin-top: 29px;">Выберите статус:</p>
                </div>
            </div>
        </div>
        <script>
            let app = new Vue({
                el: '#app',
                data: {
                    types: [],
                    categories: [],
                    statuses: [],
                    typeSearch: '',
                    categorySearch: '',
                    statusSearch: ''
                },
                created: function() { //получаем список СП после отрисовки фрейма
                    BX24.resizeWindow(853, 250);
                    BX24.callMethod(
                        'crm.type.list',
                        '',
                        function(result)
                        {
                            if(result.error())
                                alert("Error: " + result.error());
                            else

                                app.types = result.data().types;
                        }
                    );
                },
                watch: {
                    typeSearch: function() { //выбрали СП - подгружаем его воронки
                        BX24.callMethod(
                            'crm.category.list',
                            {"entityTypeId": app.typeSearch.id},
                            function(result)
                            {
                                if(result.error())
                                    alert("Error");
                                else

                                app.categories = result.data().categories;
                            }
                        );
                    },
                    categorySearch: function() {//выбрали воронку - подгружаем статусы
                        BX24.callMethod(
                            'crm.status.list',
                            {'filter': { "ENTITY_ID": 'DYNAMIC_' + app.typeSearch.id + '_STAGE_' + app.categorySearch.id}},
                            function(result)
                            {
                                if(result.error())
                                    alert("Error");
                                else

                                app.statuses = result.data();
                            }
                        );
                    },
                    statusSearch: function() {
                        BX24.placement.call( //обновим параметры после заполнения каждого пункта
                            'setPropertyValue',
                            {'typeSP': app.typeSearch.id, 'categoryID': app.categorySearch.id, 'statusID': app.statusSearch.id, 'sTypeSP': app.typeSearch.name, 'sCategoryID': app.categorySearch.name, 'sStatusID': app.statusSearch.name}
                        )
                    }
                }
            })
        </script>
    </body>
</html>