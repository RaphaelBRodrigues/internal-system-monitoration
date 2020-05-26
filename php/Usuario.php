<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$vendorPath = true ? "../vendor/autoload.php" : "../../vendor/autoload.php";

//require dirname(__FILE__)."/vendor/autoload.php";
require "/var/www/html/mixpet/vendor/autoload.php";

/* 
Arrumar o problema do vendor
index.php está fora de pasta(cad/login)
já os outros estão dentro
*/


require 'config.php';

class Usuario
{
    private $pdo;
    private $con;
    public function __construct()
    {
        $this->con = new Config();

        $this->pdo = new PDO("mysql:hostname={$this->con->hostname()};dbname={$this->con->banco()}", $this->con->username(), $this->con->password(), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }





    public function cadastroUsuario($razao, $cnpj, $email, $celular, $cep,$cidade, $lograd, $bairros, $site, $senha, $nome, $cat, $bairro, $img, $comp, $num,$atividades)
    {
        if ($cat == 1) {
            $query = "INSERT INTO Consumidor VALUES (null, '" . $nome . "', '" . $email . "', '" . $cep . "', '" . $celular . "', '" . $senha . "',2,'".$cidade."','" . $bairro . "','" . $comp . "','" . $num . "')";
            $identificador = $nome;
        }
        if ($cat == 2) {
            $identificador = $razao;
            $query = "INSERT INTO Lojista values(null,'{$razao}',{$cnpj},'{$email}',{$celular},'{$cep}','{$cidade}','{$bairro}','{$comp}','{$num}','{$lograd}',{$bairros},'{$site}','{$senha}',0,'{$img}')";
            //Lojista
        }

        $prep = $this->pdo->prepare($query);
        $prep->execute();
        $row = $prep->rowCount();
        $id = $this->pdo->lastInsertId();

        if ($row > 0) {
            //Cadastrado
            @session_start();
            if ($cat == 2) {
                foreach($atividades as $atividade){
                    $this->atualizarAtividades($id,$atividade);
                }
            }
            
            $this->confirmarEmail($identificador, $email, $cat);
        } else {
            //Falha
            echo "
        <script>
        alert('Falha ao realizar o cadastro');
        window.location.href = '../../index.php';
        </script>
        ";
        }
    }

    public function atualizarAtividades($id,$atividade){
        $query = "INSERT INTO Atividades VALUES({$id},'{$atividade}')";
        $prep = $this->pdo->prepare($query);
        $prep->execute();
    }


    public function alterarStatusUsuario($cat, $hash)
    {
        if ($cat == 1) {
            //Tutor
            //$query = "UPDATE Consumidor SET cd_Status = 1 WHERE md5(cd_razaoSocial) = '".$hash."'";
            $query = "UPDATE Consumidor SET cs_Status = 1 WHERE md5(cs_Nome) = '" . $hash . "'";
        } else {
            //Lojista
            $query = "UPDATE Lojista SET lj_Status = 1 WHERE md5(lj_razaoSocial) = '" . $hash . "'";
        }
        $prep = $this->pdo->prepare($query);
        $prep->execute();
        if ($prep->rowCount() > 0) {
            echo "
            <script>
            alert('Sua conta foi habilitada');
            window.location.href = '../index.php';
            </script>
          ";
        } else {
            echo "
            <script>
            alert('Falha ao habilitar a conta');
            window.location.href = '../index.php';
            </script>
          ";
        }
    }

    public function login($cat, $user, $pass)
    {
        if ($cat == 1) {
            //Tutor
            $prep = $this->pdo->query("SELECT * FROM Consumidor where cs_Email = '{$user}'");
            $cons =  $prep->fetch();
            if ($cons['cs_Status'] == 2) {
                if (strtolower($cons['cs_Email']) == strtolower($user)) {
                    if (strtolower($cons['cs_Senha']) == strtolower($pass)) {
                        //Logado
                        @session_start();
                        $_SESSION['logado'] = true;
                        $_SESSION['categoria'] = $cat;
                        $_SESSION['usrID'] = $cons['cs_Consumidor'];
                        echo "
                      <script>
                      alert('Logado com sucesso');
                      window.location.href = '../../consultar/index.php';
                      </script>
                      ";
                    } else {
                        echo "
                      <script>
                      alert('Senha inválida');
                      window.location.href = '../../index.php';
                      </script>
                      ";
                    }
                } else {
                    echo "
                      <script>
                      alert('Usuário inválido');
                      window.location.href = '../../index.php';
                      </script>
                      ";
                }
            } else {
                echo "
                      <script>
                      window.location.href = '../../avisos/verifiqueLogin.php';
                      </script>
                      ";
            }
        } else if ($cat == 2) {
            //Lojista
            $prep = $this->pdo->query("SELECT * FROM Lojista where lj_Email = '{$user}'");
            $cons =  $prep->fetch();
            if ($cons['lj_Status'] == 2) {
                if (strtolower($cons['lj_Email']) == strtolower($user)) {
                    if (strtolower($cons['lj_Senha']) == strtolower($pass)) {
                        //Logado
                        @session_start();
                        $_SESSION['logado'] = true;
                        $_SESSION['categoria'] = $cat;
                        $_SESSION['usrID'] = $cons['lj_lojistaID'];
                        echo "
                      <script>
                      alert('Logado com sucesso');
                      window.location.href = '../../alterarInformacoes/index.php';
                      </script>
                      ";
                    } else {
                        echo "
                      <script>
                      alert('Senha inválida');
                      window.location.href = '../index.php';
                      </script>
                      ";
                    }
                } else {
                    echo "
                      <script>
                      alert('Usuário inválido');
                      window.location.href = '../index.php';
                      </script>
                      ";
                }
            } else {
                echo "
                      <script>
                      window.location.href = '../avisos/verifiqueLogin.php';
                      </script>
                      ";
            }
        }
    }


    public function recuperarDados($categoria, $usrID)
    {
        if ($categoria == 1) {
            //tutor
            $query = "SELECT * FROM Consumidor WHERE cs_consumidor = {$usrID}";
            $prep = $this->pdo->query($query);

            $cons = $prep->fetch();

            $cons['categoria'] = "Tutor/Consumidor";
            $cons['pref'] = 'cs';
        } else if ($categoria == 2) {
            //tutor
            $query = "SELECT * FROM Lojista WHERE lj_lojistaID = {$usrID}";
            $prep = $this->pdo->query($query);
            $cons = $prep->fetch();
            $cons['categoria'] = "Lojista";
            $cons['pref'] = 'lj';
        } else {
            header("location: ../index.php");
            return false;
        }


        return $cons;
    }

    public function recuperarAtividades($usrID){
        echo $query = "SELECT at_Nome FROM Atividades WHERE at_lojistaID = {$usrID}";
        $atividades = $this->pdo->query($query);
        return $atividades;
    }

    public function apagarBairrosAssociados($usrID, $cidadeID)
    {

        /*echo $query = "
        DELETE bairrosAtendidos FROM bairrosAtendidos INNER JOIN Bairros INNER JOIN Cidades 
        WHERE ba_lojistaID = {$usrID} and 
        cd_CidadeID = {$cidadeID} and 
        br_Cidade like cd_Nome and
        ba_bairro = br_bairroID
        
        ";*/
         $query = "
            DELETE bairrosAtendidos FROM bairrosAtendidos INNER JOIN Bairros
            INNER JOIN Cidades WHERE ba_lojistaID = {$usrID}
            and cd_CidadeID = {$cidadeID} and 
            br_Cidade like cd_Nome and
            ba_bairro = br_bairroID 
        ";
        $prep = $this->pdo->prepare($query);
        $prep->execute();
        return true;
    }

    public function atualizarBairros($usrID, $bairroID)
    {

        $query = "SELECT * FROM bairrosAtendidos where ba_lojistaID = {$usrID} and ba_bairro = {$bairroID}";
        $queryExec = $this->pdo->query($query);
        $cons = $queryExec->fetch();

        if (!empty($cons)) {
            //O bairro já está associado ao estabelecimento
            return false;
        }

         $query = "INSERT INTO bairrosAtendidos values({$usrID},{$bairroID})";
        $prep = $this->pdo->prepare($query);
        $prep->execute();
        return $prep->rowCount();
    }



    public function atualizarDados($id, $razao, $cnpj, $email, $celular, $cep,$cidade, $site, $senha, $nome, $cat, $bairro, $logradouro, $comp, $num, $img)
    {
        $bairros = 0;
        if (isset($img)) {
            $query = "UPDATE Lojista SET  lj_razaoSocial = '" . $razao . "' , lj_CNPJ = " . $cnpj . " , lj_Email = '" . $email . "' , lj_Celular = '" . $celular . "', lj_CEP = '" . $cep . "', lj_Cidade = '".$cidade."' , lj_Logradouro = '" . $logradouro . "',lj_Bairro = '" . $bairro . "',lj_Complemento = '" . $comp . "',lj_Numero = '" . $num . "', lj_Site = '" . $site . "', lj_Senha = '" . $senha . "',lj_Imagem = '{$img}'  WHERE lj_lojistaID = " . $id . " ";
        } else {
            if ($cat == 1) {
           echo     $query = "UPDATE Consumidor SET cs_Nome = '" . $nome . "' ,cs_Email = '" . $email . "' , cs_Celular = '" . $celular . "', cs_CEP = '" . $cep . "',cs_Cidade = '".$cidade."',cs_Complemento = '" . $comp . "',cs_Numero = '" . $num . "', cs_Senha = '" . $senha . "' ,cs_Bairro = '" . $bairro . "' WHERE cs_Consumidor = " . $id . " ";
            };
            if ($cat == 2) {
echo                $query = "UPDATE Lojista SET  lj_razaoSocial = '" . $razao . "' , lj_CNPJ = " . $cnpj . " , lj_Email = '" . $email . "' , lj_Celular = '" . $celular . "', lj_CEP = '" . $cep . "' ,lj_Logradouro = '" . $logradouro . "',lj_Cidade = '".$cidade."',lj_Bairro = '" . $bairro . "',lj_Complemento = '" . $comp . "',lj_Numero = '" . $num . "', lj_Site = '" . $site . "', lj_Senha = '" . $senha . "'   WHERE lj_lojistaID = " . $id . " ";
            }
        }


        $prep = $this->pdo->prepare($query);
        $prep->execute();
        $row = $prep->rowCount();
        if ($row > 0) {
            echo "
            <script>
           window.location.href = '../../alterarInformacoes/index.php';
            </script>";
        } else {
            echo "
            <script>
            window.location.href = '../../alterarInformacoes/index.php';
            </script>";
        }
    }

    public function atualizarQuantidadeBairros($usrID)
    {
        $quantidadeAtual = $this->pdo->query("SELECT count(ba_lojistaID) FROM bairrosAtendidos WHERE ba_lojistaID = {$usrID}");
        $quantidadeAtual = $quantidadeAtual->fetch();
        echo $query = "UPDATE Lojista SET lj_Bairros = {$quantidadeAtual[0]} where lj_lojistaID = {$usrID}";
        $prep = $this->pdo->prepare($query);
        $prep->execute();

        return $prep->rowCount();
    }


    public function atualizarStatusUsuarioDashboard($cat, $id, $alteracao)
    {


        if ($cat == 1) {
            //Consumidor $cat = 1
            $query = "UPDATE Consumidor SET cs_Status = {$alteracao} WHERE cs_Consumidor = {$id}";
            $inf = $this->pdo->query("SELECT * FROM Consumidor WHERE cs_Consumidor = {$id}");
            $inf = $inf->fetch();

            $this->statusAlterado($inf['cs_Nome'], $inf['cs_Email'], $alteracao);
        } else if ($cat == 2) {
            //Lojista $cat = 2

            $query = "UPDATE Lojista SET lj_Status = {$alteracao} WHERE lj_lojistaID = {$id}";
            $this->statusAlterado($inf['cs_Nome'], $inf['cs_Email'], $alteracao);
        }
        $prep = $this->pdo->prepare($query);
        $prep->execute();
        return $prep->rowCount();
    }

    public function removerBairrosLojista($usrID)
    {
        echo $query = "DELETE bairrosAtendidos FROM bairrosAtendidos  WHERE ba_lojistaID = {$usrID}";
        $prep = $this->pdo->prepare($query);
        $prep->execute();


        return true;
    }


    //Emails


    public function confirmarEmail($nome, $email, $categoria)
    {

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

            /*$mail->addReplyTo('mixpetplataforma@gmail.com', 'MixPet');
          $mail->addCC('mixpetplataforma@gmail.com');
          $mail->addBCC('mixpetplataforma@gmail.com');
          

          $mail->setFrom($email, $nome);
          $mail->addAddress($email, $nome);
          $mail->addReplyTo($email, $nome);
          $mail->addCC($email, $nome);
          $mail->addBCC($email, $nome);*/


            // Attachments
            /*
          $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
      */
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Confirme seu cadastro na MIXPET';
            $mail->Body = "
       
         
        
       
         
<center>
  <div style=' width: 80%;'>
      <center>

          <head>
              <img style='width: 40%;' src='http://18.231.188.87/mixpet/assets/images/logo.png' alt=''>
          </head>

          <section style='width: 90%;font-size:1.2em;'>
          <center>


              <h2>
                  Olá  <abbr title='Senhor(a)'>Sr(a).</abbr>{$nome},
              </h2>
              <p>
                  Parece que o acabou de se registrar
                  em nossa plataforma de busca de estabelecimentos comerciais,por favor caso tenha sido você mesmo clique no botão abaixo para
                  efetivar seu cadastro.

              </p>
              <a style='color:white;text-decoration:none;' href='127.0.0.1/mixpet/controller/alterarStatusUsuario.php?ca={$categoria}&h=" . md5($nome) . "'>

              <button style='display: block;
              margin-left: auto;
              margin-right: auto;
              margin-top: 2em;
              margin-bottom: 2em;
              background-color: rgba(255, 226, 0, 1);
              border: 0;
              padding: 1em 1em 1em 1em;
              font-size: 1.5em;
              box-shadow: 0 0 10px black;'>
                  Confirmar cadastro
               </button>
               </a>               
               </center>

          </section>
          <footer style='height:5vh'>
              <hr>
          MixPet
          </footer>
      </center>
  </div>
</center>

          
          
          
          
          
          ";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            $this->confirmarEmailCopiaAdministrador($nome, $email);


            echo "
  <script>
  //alert('Verifique sua caixa de entrada do email');
  window.location.href = '../../avisos/aguardandoAprovacao.php';
  </script>";
        } catch (Exception $e) {
            echo "
          window.location.href = '../../index.php';

          Email inválido
          ";
        }
    }

    public function statusAlterado($nome, $email, $alteracao)
    {
        if ($alteracao == 0) {
            $status = "Reprovado";
            $adjetivo = "Má";
            $orien = "";
        } else if ($alteracao == 2) {
            $status = "Aprovado";
            $adjetivo = "Boa";
            $orien = ",Acesse já";
        } else {
            $status = "Entre em contato conosco,algo deu errado";
        }

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

            /*$mail->addReplyTo('mixpetplataforma@gmail.com', 'MixPet');
          $mail->addCC('mixpetplataforma@gmail.com');
          $mail->addBCC('mixpetplataforma@gmail.com');
          

          $mail->setFrom($email, $nome);
          $mail->addAddress($email, $nome);
          $mail->addReplyTo($email, $nome);
          $mail->addCC($email, $nome);
          $mail->addBCC($email, $nome);*/


            // Attachments
            /*
          $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
      */
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Atualização sobre o status da sua conta';
            $mail->Body = "
       
         
        
       
         
<center>
<link rel='stylesheet' href='../css/emails/email.css'>
<meta charset='utf-8'>
<div style=' width: 80%;'>
<center>

        <head>
        <img style='width: 40%;' src='http://18.231.188.87/mixpet/assets/images/logo.png' alt=''>
        </head>

        <section style='width: 90%;font-size:1.2em;'>
        <center>


            <h2>
                Olá <abbr title='Senhor(a)'>Sr(a).</abbr> {$nome},                Temos uma {$adjetivo} notícia!

            </h2>
            <p>
                O cadastro do senhor(a) acabou de ser {$status} em nosso portal de buscas {$orien}!
                

            </p>

            <a style='color:white;text-decoration:none;' href='http://18.231.188.87/mixpet/'>

              <button style='display: block;
              margin-left: auto;
              margin-right: auto;
              margin-top: 2em;
              margin-bottom: 2em;
              background-color: rgba(255, 226, 0, 1);
              border: 0;
              padding: 1em 1em 1em 1em;
              font-size: 1.5em;
              box-shadow: 0 0 10px black;'>
                Acessar o portal
              </button>
               </a>       
               </center>

        </section>
        <footer style='height:5vh'>
            <hr>
            MixPet
        </footer>
    </center>
</div>
</center>

          
          
          
          
          
          ";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo "
            <script>
            //alert('Verifique sua caixa de entrada do email');
            window.location.href = '../dashboard/usuarios.php?c=1';
            </script>";
        } catch (Exception $e) {
            echo "
          <script>
          alert('Falha ao atualizar o status do usuário {$nome}');
          window.location.href = '../dashboard/usuarios.php?c=1';

          </script>";
        }
    }


    public function confirmarEmailCopiaAdministrador($nome, $email)
    {

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
            $mail->addAddress('mixpetplataforma@gmail.com', 'MixPet');
            $mail->isHTML(true);
            $mail->Subject = 'Novo cadastro na plataforma';
            $mail->Body = "
       
         
        
       
         
<center>
  <div style=' width: 80%;'>
      <center>

          <head>
              <img style='width: 40%;' src='http://18.231.188.87/mixpet/assets/images/logo.png' alt=''>
          </head>

          <section style='width: 90%;font-size:1.2em;'>
          <center>


              <h2>
                  Novo usuário cadastrado :  <abbr title='Senhor(a)'>Sr(a).</abbr>{$nome},
              </h2>
              <p>
                    Um novo usuário acabou de se cadastrar e aguarda sua aprovação.

              </p>
              <a style='color:white;text-decoration:none;' href='18.231.188.87/mixpet/dashboard/'>

              <button style='display: block;
              margin-left: auto;
              margin-right: auto;
              margin-top: 2em;
              margin-bottom: 2em;a
              background-color: rgba(255, 226, 0, 1);
              border: 0;
              padding: 1em 1em 1em 1em;
              font-size: 1.5em;
              box-shadow: 0 0 10px black;'>
                Acessar o dashboard
              </button>
               </a>               
               </center>

          </section>
          <footer style='height:5vh'>
              <hr>
          MixPet
          </footer>
      </center>
  </div>
</center>

          
          
          
          
          
          ";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo "
  <script>
  //alert('Verifique sua caixa de entrada do email');
  window.location.href = '../../avisos/aguardandoAprovacao.php';
  </script>";
        } catch (Exception $e) {
            echo "
          window.location.href = '../../index.php';

          Email inválido
          ";
        }
    }
}
