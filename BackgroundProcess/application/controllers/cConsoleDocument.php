<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require_once APPPATH.'third_party/vendor/autoload.php';
// require_once APPPATH.'libraries/rabbitmq/vendor/autoload.php';
// require_once APPPATH.'config/rabbitmq.php';

// use PhpAmqpLib\Connection\AMQPStreamConnection;
// use PhpAmqpLib\Message\AMQPMessage;
// use Spatie\Async\Pool;

require APPPATH.'controllers/cMQ.php';
class cConsoleDocument extends CI_Controller {

        public function __construct() {
                parent::__construct();
	}
	
        public function FSxMQListener() {
                $oMQ = new cMQ();
                $oMQ->FSxMQConsumer();
        }
}
