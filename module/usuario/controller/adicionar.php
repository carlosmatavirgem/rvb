<?php

Class Adicionar extends Action {

    public $view = "form.html";
    public $module = 'usuario';
    public $table = 'usuario';
    public $title = 'Usuário';
    public $dir = '../module';

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

        $this->adicionar();
    }

    public function adicionar() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Adicionar');

        $src = array(
            '/css/jquery-ui-1.8.21.custom.css'
        );
        $this->headLink($src);

        $href = array(
            '/js/forms/jquery.validate.min.js',
            '/js/lang/jquery.validate.pt-br.js',
            '/js/jquery.ToTop.js',
            '/js/custom_form.js',
        );
        $this->headScript($href);

        $this->replaceCode('title_page', $this->title);
        $this->replaceCode('iClass', 'iAdd');
        $this->replaceCode('iAcao', 'Adicionar');

        $this->replaceCode('checkedA', ' checked="checked"');
        $this->replaceCode('required', 'required ');
        
        /**
         *  Salva os dados do formulario
         */
        if (isset($_POST['nome'])) {

            /**
             *  Remove os campos vazios
             */
            foreach ($_POST as $key => $val) {
                if ($val) {
                    $data[$key] = $val;
                }
            }
            
            /**
             *  Tratamento do POST para gravar na base
             */
            $data['dataRegistro'] = date('Y-m-d H:i:s');
            $data['senha'] = MD5($data['senha']);
            $data['ativo'] = isset($data['ativo']) ? (boolean) $data['ativo'] : null;

            /**
             *  Salva na base USUARIO
             */
            $this->insert($this->table, $data);
            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}