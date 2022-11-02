<?php
/*
|--------------------------------------------------------------------------
| RabbitMQ Config
|--------------------------------------------------------------------------
|
*/
$config['mq_host']      = 'localhost';
$config['mq_username']  = 'myUser';
$config['mq_password']  = 'myPass';
$config['mq_vhost']     = 'AdaStoreBack';
$config['mq_port']      = '5672';
$config['mq_exchange']  = '';

$config['documentname'] = [
    'PURCNNEW',
    'PURCNNEWDEL', 
    'PDTADJSTKCHK',
    'PDTADJSTKCHKDEL'
];

define('AMQP_DEBUG', false);
define('CHANNEL_MAX', 100);




