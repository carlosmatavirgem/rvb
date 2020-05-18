<?php

Class Adicionar extends Action {

    public $view = "form.html";
    public $module = 'menu';
    public $dir = '../module';
    public $table = 'menu';
    public $title = 'Menu';

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

        Crud::_redirect(WWWROOT . "/admin/menu");

        $this->Action();
    }

    public function action() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Adicionar');

        $this->replaceCode('title_page', $this->title);

        $href = array(
            '/js/forms/jquery.validate.min.js',
            '/js/lang/jquery.validate.pt-br.js',
            '/js/custom_form_menu.js',
        );
        $this->headScript($href);

        $this->replaceCode('ativo', 1);

        /**
         *  Monta combo da POSICAO MENU
         */
        $comboT = $this->select()
                ->column("idMenuTipo as value, descricao as text")
                ->from("menutipo")
                ->query();
        $this->replaceCombo('tipos', $comboT);

        /**
         *  Monta combo da MENU RELACIONADO
         */
        $comboR = $this->select()
                ->column("m1.idMenu as value, IF(m1.idMenuRelacionado = 0, m1.descricao, CONCAT(m2.descricao,' > ',m1.descricao)) as text")
                ->from("menu m1")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->query();
        $this->replaceCombo('relacionados', $comboR);

        /**
         *  Salva os dados do formulario
         */
        if (isset($_POST['descricao'])) {

            /**
             *  Remove os campos vazios
             */
            foreach ($_POST as $key => $val) {
                if ($val) {
                    $data[$key] = trim($val);
                }
            }

            /**
             *  Tratamento do POST para gravar na base
             */
            $data['dataRegistro'] = date('Y-m-d H:i:s');
            $data['url'] = Uri::removeAcento($data['descricao']);
            $modulomenu['idModulo'] = 1;

            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            $modulomenu['idMenu'] = $this->insert($this->table, $data);
            $this->insert('modulomenu', $modulomenu);

            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}