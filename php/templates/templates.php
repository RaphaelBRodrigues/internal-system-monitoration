<?php

class Templates{
    function Acesso(){
       $template = file_get_contents("../php/templates/acesso.html");
        return $template;
    }


}

?>