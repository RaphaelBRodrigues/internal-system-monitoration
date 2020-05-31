<?php

/* Configurações do sistema */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class config{

    protected $nome = "Raphael";
    protected $remetente = "raphaellinuxsmi@gmail.com";
    protected $senha = "linux@smi1.0";
    protected $destinatario = "raphaelbarbosa.rodrigues@gmail.com";
    protected $smtp = "smtp.gmail.com";

}
define("CAMINHO_SMI", "/home/raphael/Variados/SMI");
?>