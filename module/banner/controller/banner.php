<?php

Class Banner extends Action {

    public $view = 'listagem.html';
    public $module = 'banner';
    public $dir = '../module';
    public $table = 'banner';
    public $title = 'Banner';

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

        $this->banner();
    }

    public function banner() {

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

        /**
         * Monta a listagem
         */
        $result = $this->select()
                ->column("b.idBanner AS id, IF(m1.idMenuRelacionado = 0,m1.descricao,"
                        . "IF(mostrarTodos = 1, 'Todas as p&aacute;ginas', IF(mb.idMenu = 0,'Home',CONCAT(m2.descricao,' > ',m1.descricao)))) AS pagina, "
                        . "titulo, DATE_FORMAT(dataIni,'%d/%m/%Y') AS dataIni, DATE_FORMAT(dataFim,'%d/%m/%Y') AS dataFim, b.url")
                ->from("$this->table b")
                ->join("LEFT JOIN bannermenu mb ON (mb.idBanner = b.idBanner)")
                ->join("LEFT JOIN menu m1 ON (m1.idMenu = mb.idMenu)")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->where("ORDER BY b.dataIni DESC")
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

        $result = $this->select()
                ->column("idBanner, url")
                ->from($this->table)
                ->where("WHERE idBanner = " . $param['id'])
                ->query();

        if (count($result) > 0) {
            $path = DOCUMENTROOT . PATH . '/images/arquivos/banner/' . $param['url'];
            unlink($path . '/' . $result[0]['url']);

            $data['idBanner'] = $param['id'];
            
            $this->delete($this->table . 'menu', $data);
            $this->delete($this->table, $data);
        }
    }

}
