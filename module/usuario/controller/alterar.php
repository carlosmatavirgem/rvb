<?php

Class Alterar extends Action {

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

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Alterar');

        $this->replaceCode('title_page', $this->title);

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
        $this->replaceCode('iClass', 'iPencil');
        $this->replaceCode('iAcao', 'Alterar');

        /**
         *  Monta conteudo da PAGINA
         */
        $id = $this->_getParam(2);

        if ($id == 1) {
            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $result = $this->select()
                ->column("*")
                ->from($this->table)
                ->where("WHERE idUsuario = $id")
                ->query();
        $this->replaceInterval('alterar', $this->replaceList('alterar', $result));

        if ($result[0]['ativo'] == 1) {
            $this->replaceCode('checkedA', ' checked="checked"');
        }

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
            if (!empty($data['senha'])) {
                $data['senha'] = md5($data['senha']);
            }
            $data['ativo'] = isset($data['ativo']) ? (boolean) $data['ativo'] : null;

            $this->update($this->table, $data, "idUsuario = $id");

            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}
