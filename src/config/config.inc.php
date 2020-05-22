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
    ),
    'command_list' => array(
        'MySQL - Dump' => 'mysqldump --single-transaction --events --skip-lock-tables -u {USER} -p{PASSWORD} {DATABASE_NAME} | gzip > dump-data.sql.gz'
    )
);
