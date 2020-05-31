<?php


require 'config.php';
class Dados extends config
{
    /* Pega os dados dos .txt */

    private $computador;
    private $acesso;

    function __construct()
    {
        $path = CAMINHO_SMI."/log/";
        $this->computador = file_get_contents($path."computador.txt");
        $this->acesso = file_get_contents($path."acesso.txt");
        $this->computador = explode("\n", $this->computador);
        $this->acesso = explode(",", $this->acesso);
    }

    public function getHost()
    {
        return $this->acesso[0];
    }
    public function getUser()
    {
        return $this->acesso[1];
    }
    public function getData()
    {
        return $this->acesso[2];
    }
    public function getHora()
    {
        return $this->acesso[3];
    }
    public function getIp()
    {
        return $this->acesso[4];
    }
    public function getTipoComputador()
    {
        $tipo = $this->computador[2];
        $tipo = explode(":", $tipo);
        return $tipo[1];
    }
    public function getOS()
    {
        $tipo = $this->computador[5];
        $tipo = explode(":", $tipo);
        return $tipo[1];
    }
}

