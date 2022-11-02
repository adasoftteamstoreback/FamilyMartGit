<?php

class clogin extends Controller{

    public function __construct(){

    }

    public function index(){
        echo $this->RequestView('common','login/wLogin');
    }

}

?>