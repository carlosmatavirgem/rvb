<?php

Class Usuario extends Action {

    public $view = 'listagem.html';
    public $module = 'usuario';
    public $dir = '../module';
    public $table = 'usuario';
    public $title = 'Usuário';
    public $_maxLevel = 0;

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

        $this->usuario();
    }

    public function usuario() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title);

        $src = array(
            '/css/back_end/ui_custom.css'
        );
        $this->headLink($src);

        $href = array(
            '/js/dataTables/jquery.dataTables.js',
            '/js/ui/jquery.alerts.js',
            '/js/custom_lista.js',
        );
        $this->headScript($href);

        $this->replaceCode('title_page', $this->title);
        /*
         * Monta a listagem
         */
        $result = $this->select()
                ->column("*")
                ->from($this->table)
                ->where("WHERE idUsuario <> 1 ORDER BY nome")
                ->query();

        $this->replaceInterval('lista', $this->replaceList('lista', $result));

        /**
         * Acao para EXCLUIR registro
         */
        $param = $this->_getParam();
        if (isset($param[1]) && $param[1] == 'excluir') {
            $this->_excluir($_POST);
        }

        $this->_run(true);
    }

    private function _excluir(array $param) {

        $data = array();
        $data['idUsuario'] = $param['id'];
        $this->delete($this->table, $data);
    }

}
