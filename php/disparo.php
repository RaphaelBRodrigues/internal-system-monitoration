<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


    require dirname(__FILE__)."/../vendor/autoload.php";
    require "templates/templates.php";
    require 'config.php';

     class Disparo extends config{

        private $template;
        private $mail;
        function __construct()
        {
             
            $this->template = new Templates();
            $this->mail = new PHPMailer(true);
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'raphaelsmilinux@gmail.com';
            $this->mail->Password = 'linuxsmi1.0';
            $this->mail->SMTPSecure = PHPmailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            $this->mail->SMTPSecure = 'tls';
            $this->mail->SMTPDebug = 1;
            $this->mail->CharSet = 'UTF-8';
            $this->mail->setFrom('raphaelsmilinux@gmail.com', 'Notebook');
            $this->mail->addAddress('raphaelbarbosa.rodrigues@gmail.com', 'Raphael');
            $this->mail->isHTML(true);
            
           
        }

      

        public function Acesso(){

                $this->mail->Subject = 'Acesso recente no notebook';
                $this->mail->Body = $this->template->Acesso();
                $this->mail->send();

        }

        

        static function teste(){
            echo "Teste estático";
        }
    }

?>