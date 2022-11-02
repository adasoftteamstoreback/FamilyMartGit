<?php

class Autoloader {

    public function __construct(){

        $this->loadHelper();

    }

    public function loadHelper(){

        include('autoload.php');

        foreach($autoload['helper'] as $value){

            include('application/helpers/'. $value .'_helper.php');

        }

        return $this;

    }

}

?>