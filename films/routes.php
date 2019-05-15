<?php
/*массив содержит информацию о имеющихся маршрутах:
контроллер => [
    действие1 => [необходимый_атрибут1, необходимый_атрибут2]
    действие2 => [необходимый_атрибут1, необходимый_атрибут2]
]*/
$routes = [
    'film' => [
        'create' => [],
        'view' => ['id'],
        'delete' => ['id'],
        'search' => [],
        'getExampleFile' => []
    ]
];
return $routes;