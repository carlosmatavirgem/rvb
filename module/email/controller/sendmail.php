<?php

Class Sendmail extends Action {

    public $view = 'index.html';
    public $module = 'email';
    public $dir = 'module';
    public $table = 'email';

    public function sendmail() {


        parent::action(false);

        $param = $this->_getParam();

        if (isset($param[1]) && $param[1] == 'enviado') {
            $this->replaceInterval('form', null);
        } else {
            $this->replaceInterval('msg', null);
        }

        if (isset($_POST['nome'])) {

            include('includes/PHPMailer-master/PHPMailerAutoload.php');

            $mail = new PHPMailer();

            $mail->isSMTP();
            $mail->Host = 'mail.benxs.com.br';
            $mail->SMTPAuth = true;
            $mail->Username = 'benxs@benxs.com.br';
            $mail->Password = '8us9l2cgat';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            //$mail->SMTPDebug = 2;

            $mail->addReplyTo($_POST['email'], $_POST['nome']);
            $mail->setFrom('benxs@benxs.com.br', PROJECT);
            $mail->addAddress('benxs@benxs.com.br', PROJECT);
            $mail->addAddress('ematavirgem@gmail.com', PROJECT);

            $subject = 'Email vindo do site';

            $message = "\n\r\n\r\n\r";
            foreach ($_POST as $key => $value) {
                if ($key != 'g-recaptcha-response') {
                    $message .= ucfirst($key) . ": " . utf8_decode($value) . "\n\r";
                }
            }

            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($mail->send()) {
                echo true;
            }
        }
    }

}
