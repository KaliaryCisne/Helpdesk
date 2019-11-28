<?php


namespace Root\Adapter;



$db = [
    'develop' => [
        'driver' => 'pdo_mysql',
        'user' => 'kaliary',
        'password' => '12345678',
        'dbname' => 'helpdesk',
    ],
    'production' => [
        'driver' => 'pdo_mysql',
        'user' => 'root',
        'password' => '123456',
        'dbname' => 'helpdesk',
    ],
];

return $db;




