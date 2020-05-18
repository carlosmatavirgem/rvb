<?php

Class Enviar extends Action {

    public $view = 'index.html';
    public $module = 'contato';
    public $dir = 'module';
    public $title = 'Enviar';
    public $controller = 'enviar';

    public function __construct() {
        Crud::pe(1);
        $this->Action();
    }

    public function action() {

        header('Content-type: text/html; charset=iso-8859-1');
        if (isset($_POST['nome'])) {

            Crud::p($_POST);

            $to = "Eduardo <ematavirgem@gmail.com>";
            $subject = 'Email vindo do site';

            $message = "\r\n\r\n\r\nNome: " . $_POST['nome'] . "\r\n";
            $message .= "Email: " . $_POST['email'] . "\r\n";
            $message .= $_POST['mensagem'];

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $headers .= "From: {$_POST['nome']} <{$_POST['email']}>\r\n";

            if (mail($to, $subject, $message, $headers)) {
                $msg = '<div align="center" class="erro">&bull; Mensagem enviada com sucesso &bull;</div>
                    Sua mensagem foi enviada com sucesso.


                    Agradecemos sua mensagem.
                    Entraremos em contato em breve.';
                echo 1;
            }
        }
    }

}