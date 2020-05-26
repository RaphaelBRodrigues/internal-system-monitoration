<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;


    require "../vendor/autoload.php";
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
            $this->mail->host = "smtp.gmail.com";
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'mixpetplataforma@gmail.com';
            $this->mail->Password = 'mixadmin';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            $this->mail->SMTPSecure = 'tls';
            $this->mail->SMTPDebug = 1;
            $this->mail->CharSet = 'UTF-8';
            $this->mail->isHTML(true);
            $this->mail->setFrom("mixpetplataforma@gmail.com","Raphael");
            $this->mail->addAddress("mixpetplataforma@gmail.com","Raphael");
           
        }

        public function teste(){
            echo $this->mail->host;
        }

        public function Acesso(){

            echo $this->template->Acesso();

            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mixpetplataforma@gmail.com';
                $mail->Password = 'mixadmin';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPSecure = 'tls';
                $mail->SMTPDebug = 1;
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('mixpetplataforma@gmail.com', 'MixPet');
                $mail->addAddress($email, $nome);


         
            $this->mail->Subject = "Alguem acessou seu notebook:";
            $this->mail->Body = "teste";
            $this->mail->send();

        }
    }

?>