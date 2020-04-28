<?php
$conf = array(
    'simple_auth' => array(
        'valid' => false,
        'user' => 'user',
        'password' => 'password'
    ),
    'IP_restriction' => array(
        'valid' => false,
        'IPs' => array(
            '127.0.0.1'
        )
    )
);

define('BASE_PATH', realpath(__DIR__ . '/../'));