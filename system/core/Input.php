<?php

class Input{

    public function post($element){
        if(isset($_POST[$element])){
            $tValue = $_POST[$element];
            return trim(htmlspecialchars($tValue,ENT_COMPAT, 'UTF-8'));
        }else{
            return 'null';
        }
    }

}

?>