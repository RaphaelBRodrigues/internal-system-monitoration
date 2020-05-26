<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;


    require "vendor/autoload.php";
    require 'config.php';

    class Methods extends config{

        public function teste(){
            echo $this->nome;
        }
    }

?>