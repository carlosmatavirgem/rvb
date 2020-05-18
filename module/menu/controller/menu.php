<?php

Class Menu extends Action {

    public $view = 'listagem.html';
    public $module = 'menu';
    public $dir = '../module';
    public $table = 'menu';
    public $title = 'Menu';
    public $_maxLevel = 0;
    
    public function __construct() {

        if (!$_SESSION['s_logado']) {
            Crud::_redirect(WWWROOT . "/admin/login");
        }

        $this->menu();
    }

    public function menu() {

        $this->headTitle(BREADCRUMB . $this->title);

        $this->replaceCode('title_page', $this->title);

        $src = array(
            '/css/ui_custom.css'
        );
        $this->headLink($src);

        $href = array(
            '/js/dataTables/jquery.dataTables.js',
            '/js/custom_lista.js',
        );
        $this->headScript($href);

        /*
         * Monta a listagem
         */
        $result = $this->select()
                ->column("m1.idMenu, m2.descricao AS relacionado, m1.descricao AS menu")
                ->from("menu m1")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->query();

        $this->replaceInterval('lista', $this->replaceList('lista', $result));

        $this->_run(true);
    }

}
