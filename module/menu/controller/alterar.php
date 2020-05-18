<?php

Class Alterar extends Action {

    public $view = "form.html";
    public $module = 'menu';
    public $dir = '../module';
    public $table = 'menu';
    public $title = 'Menu';

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

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

        /**
         *  Monta conteudo
         */
        $id = $this->_getParam(3);
        $result = $this->select()
                ->column("m.*, idModulo")
                ->from("$this->table m")
                ->join("JOIN modulomenu mm ON mm.idMenu = m.idMenu")
                ->where("WHERE m.idMenu = $id")
                ->query();

        if ($result[0]['ativo'] == 1)
            $this->replaceCode('checkedA', ' checked="checked"');
        
        $this->replaceInterval('alterar', $this->replaceList('alterar', $result));

        /**
         *  Monta combo da POSICAO MENU
         */
        $comboT = $this->select()
                ->column("idMenuTipo as value, descricao as text")
                ->from("menutipo")
                ->query();
        $this->replaceCombo('tipos', $comboT, $result[0]['idMenuTipo']);

        /**
         *  Monta combo da PAGINA
         */
        $comboR = $this->select()
                ->column("m1.idMenu as value, IF(m1.idMenuRelacionado = 0, m1.descricao, CONCAT(m2.descricao,' > ',m1.descricao)) as text")
                ->from("menu m1")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->query();
        $this->replaceCombo('relacionados', $comboR, $result[0]['idMenuRelacionado']);

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
            $data['url'] = Uri::removeAcento($data['descricao']);
            $data['ativo'] = isset($data['ativo']) ? (boolean) $data['ativo'] : null;
            
            $del['idMenu'] = $modulomenu['idMenu'] = $id;
            $modulomenu['idModulo'] = $result[0]['idModulo'];
            

            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            $this->update($this->table, $data, "idMenu = $id");
            $this->delete('modulomenu', $del)
                    ->insert('modulomenu', $modulomenu);

            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}