<?php

Class AcessoRestrito extends Action {

    public $view = 'acesso-restrito.html';
    public $module = 'login';
    public $dir = 'module';
    public $table = 'membro';
    public $title = 'Acesso restrito';

    public function __construct() {
        $this->Action();
    }

    public function action() {

        parent::action();
        $auth = new Auth();

        $this->headTitle(BREADCRUMB . $this->title);

        $href = array(
            '/js/jquery-1.8.2.min.js',
            '/js/forms/jquery.maskedinput.js',
            '/js/forms/jquery.validate.min.js',
            '/js/lang/jquery.validate.pt-br.js',
            '/js/custom_form_auth.js',
        );
        $this->headScript($href);

        $param = $this->_getParam();
        
        if (isset($_SESSION['inscricao_id'])) {
            $this->replaceInterval('aRestrito', null);
        } else {
            $this->replaceInterval('inscrevase', null);
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            if (!isset($_SESSION['http_referer']))
                $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];
        }

        if (isset($_POST['login']['email']) && isset($_POST['login']['senha'])) {
            $auth->authenticate($_POST, $_SESSION['http_referer']);
            $this->replaceCode('msgErrorA', 'E-mail ou Senha inválido.');
        }

        if (isset($param[1]) && $param[1] == 'esqueci-senha') {
            $this->replaceInterval('formSenha', null);
        } else {
            $this->replaceInterval('sucessoSenha', null);
        }

        if (isset($_POST['password']['email']) && isset($_POST['password']['cpf'])) {
            if (!$auth->passwordGenerator($_POST))
                $this->replaceCode('msgErrorS', 'E-mail ou CPF inválido.');
        }
        $this->_run(true);
    }

}