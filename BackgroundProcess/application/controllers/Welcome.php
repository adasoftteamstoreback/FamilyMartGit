<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'third_party/vendor/autoload.php';
use Spatie\Async\Pool;
class Welcome extends CI_Controller {

	public function index(){
		$pool 	= Pool::create();
		$things = array('B','A','C','D');
		foreach ($things as $thing) {
			$pool->add(function () use ($thing) {
				// Do a thing
				echo 'Process'.$thing.'<br>';
				
			})->then(function ($output) {
				// Handle success
			})->catch(function (Throwable $exception) {
				// Handle exception
				echo '!someting has been error';
			});
		}
		$pool->wait();
	}

	public function message($to = 'World'){
			echo "Hello {$to}!".PHP_EOL;
	}
}
