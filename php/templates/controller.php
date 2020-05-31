<?php

require '/home/raphael/Variados/SMI/php/dados.php';
class Controller extends Dados
{

    public function vendor(){
        $vendor = array(
            "repo" => "https://github.com/RaphaelBRodrigues/SMI",
            "link" => "https://raphaelbrodrigues.github.io",
            "autor" => "RaphaelBRodrigues"
        );
        return $vendor;
    }
  
}

