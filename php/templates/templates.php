<?php

class Templates{
    function Acesso(){
       $template = file_get_contents(dirname(__FILE__)."/html/acesso.phtml");
        return $template;
    }


}

?>