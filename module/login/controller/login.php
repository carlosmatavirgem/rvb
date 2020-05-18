<?php

Class Login extends Action {

    public $view = 'index.html';
    public $module = 'login';
    public $dir = '../module';
    public $table = 'usuario';
    public $title = 'Login';
    public $layout = '../module/login/view/index.html';

    public function login() {
        
        parent::action(false);

        $this->headTitle(BREADCRUMB . $this->title);

        // Valida os dados do formulario
        if ($_POST) {

            $db = new Db();

            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $result = $this->select()
                    ->column("idUsuario, nome, email, primeiroAcesso, ativo")
                    ->from($this->table)
                    ->where(" WHERE senha = MD5('" . $senha . "') AND email = LOWER('" . $email . "')")
                    ->query();
            
            if (count($result) == 1) {
                
                $_SESSION['s_logado'] = true;
                $_SESSION['s_moderador'] = $result[0]['ativo'];
                $_SESSION['s_id'] = $result[0]['idUsuario'];
                $_SESSION['s_nome'] = $result[0]['nome'];

                //if ($result[0]['primeiroAcesso'] == 0) {
                    //Crud::_redirect(WWWROOT . "/admin/primeiroacesso");
                //} else {
                    Crud::_redirect(WWWROOT . "/admin/" . MODULO_INICIAL);
                //}
            }
        }

        $this->_run(true);
    }

}