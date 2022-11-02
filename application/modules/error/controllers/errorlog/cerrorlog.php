<?php

class cerrorlog extends Controller{

    public function __construct(){

    }

    public function index(){
        echo $this->RequestView('error','errorlog/werrorlog');
    }

}

?>