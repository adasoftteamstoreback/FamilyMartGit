<?php 
   
   //Autoload Helper
   $autoload['helper'] = array(
      'language',
      'getbranch',
      'generatecode',
      'rowlength',
      'prorate',
      'getCompanyInfo',
      'report'
      // 'jRabbitMQ'
   );

   include('application/config/config.php');
   require_once('system/core/Input.php');

?>