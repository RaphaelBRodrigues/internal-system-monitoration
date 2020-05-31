<?php

/* Exporta o template dos emails */

require '/home/raphael/Variados/SMI/php/config.php';

class Templates extends config {
    
   public function Acesso(){
       $path = CAMINHO_SMI.'/php/templates/html/';
    exec("php ".$path."acesso.phtml > ".$path."acesso.html");

    $template = file_get_contents($path."acesso.html");
   

    return $template;
    }


}
