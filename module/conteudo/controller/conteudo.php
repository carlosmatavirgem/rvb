<?php

Class Conteudo extends Action {

    public $view = 'listagem.html';
    public $module = 'conteudo';
    public $dir = '../module';
    public $table = 'conteudo';
    public $title = 'Conteúdo';
    public $_maxLevel = 0;

    public function __construct() {

        if (!$_SESSION['s_logado']) {
            Crud::_redirect(WWWROOT . "/admin/login");
        }

        $this->conteudo();
    }

    public function conteudo() {

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
                ->column("c.idConteudo, IF(m1.idMenuRelacionado = 0, m1.descricao, CONCAT(m2.descricao,' > ',m1.descricao)) AS pagina, titulo, DATE_FORMAT(dataIni,'%d/%m/%Y') AS dataIni, DATE_FORMAT(dataFim,'%d/%m/%Y') AS dataFim")
                ->from("$this->table c")
                ->join("JOIN conteudomenu cm ON (cm.idConteudo = c.idConteudo)")
                ->join("JOIN menu m1 ON (m1.idMenu = cm.idMenu)")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->orderby("ORDER BY c.dataIni DESC")
                ->query();

        $this->replaceInterval('lista', $this->replaceList('lista', $result));

        /**
         * Acao para EXCLUIR registro
         */
        $param = $this->_getParam();
        if (isset($param[2]) == 'exluir' && isset($_POST['id']))
            self::_excluir($_POST);

        $this->_run(true);
    }

    private function _excluir(array $param) {

        $data = array();

        $data['idConteudo'] = $param['id'];

        $result = $this->select()
                ->column("c.idConteudo, CONCAT(a.nome,'.',a.arquivo) as arquivo, a.idArquivo")
                ->from("$this->table c")
                ->join("LEFT JOIN conteudoarquivo ca ON ca.idConteudo = c.idConteudo")
                ->join("LEFT JOIN arquivo a ON a.idArquivo = ca.idArquivo")
                ->where("WHERE c.idConteudo = " . $param['id'])
                ->query();
        
        if (!is_null($result[0]['arquivo'])) {
            if ($_SERVER['SERVER_ADDR'] == IPSERVER) {
                $path = $_SERVER['DOCUMENT_ROOT'] . '/clientes/fbn/images/arquivos/';
            } else {
                $path = $_SERVER['DOCUMENT_ROOT'] . 'images/arquivos/';
            }
            unlink($path . '/' . $result[0]['arquivo']);
            $this->delete($this->table . 'arquivo', $data);
            $this->delete('arquivo', $result[0]['idArquivo']);
        }

        $this->delete($this->table . 'menu', $data);
        $this->delete($this->table, $data);
    }

}
