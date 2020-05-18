<?php

Class NovaSenha extends Action {

    public $view = 'nova-senha.html';
    public $module = 'login';
    public $dir = 'module';
    public $table = 'membro';
    public $title = 'Nova Senha';

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

        if (isset($param[1]) && $param[1] == 'sucesso') {
            $this->replaceInterval('formSenha', null);
        } else {
            $this->replaceInterval('sucessoSenha', null);
        }

        if (isset($_POST['senha'])) {
            $param = $this->_getParam();
            $auth->passwordNew($_POST, $param[1]);
        }
        $this->_run(true);
    }

}