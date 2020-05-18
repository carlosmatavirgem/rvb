<?php

Class Alterar extends Action {

    public $view = "form.html";
    public $module = 'banner';
    public $table = 'banner';
    public $title = 'Banner';
    public $dir = '../module';

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

        $this->alterar();
    }

    public function alterar() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Adicionar');

        $this->replaceCode('title_page', $this->title);

        $src = array(
            '/css/back_end/jquery-ui.min.css'
        );
        $this->headLink($src);

        $href = array(
            '/js/forms/jquery.validate.min.js',
            '/js/lang/jquery.validate.pt-br.js',
            '/js/forms/jquery.maskedinput.js',
            '/js/lang/jquery.datepicker.pt-br.js',
            '/js/custom_form_banner.js',
        );
        $this->headScript($href);

        $this->replaceCode('title_page', $this->title);

        $this->replaceCode('required', null);

        /**
         *  Monta conteudo da PAGINA
         */
        $id = $this->_getParam(2);
        $result = $this->select()
                ->column("b.idBanner AS id, 
                    mb.idMenu AS todas, 
                    m1.idMenu, 
                    IF(m1.idMenuRelacionado = 0, m1.descricao, CONCAT(m2.descricao,' > ',m1.descricao)) AS pagina, 
                    titulo, 
                    b.descricao, 
                    b.mostrarTodos, 
                    DATE_FORMAT(dataIni,'%d/%m/%Y') AS dataIni, 
                    DATE_FORMAT(dataFim,'%d/%m/%Y') AS dataFim, 
                    b.url,
                    b.link,
                    b.novaJanela,
                    b.ativo")
                ->from("$this->table b")
                ->join("LEFT JOIN bannermenu mb ON (mb.idBanner = b.idBanner)")
                ->join("LEFT JOIN menu m1 ON (m1.idMenu = mb.idMenu)")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->where("WHERE b.idBanner = " . $id . " GROUP BY b.idBanner ORDER BY b.dataIni DESC")
                ->query();
        $this->replaceInterval('alterar', $this->replaceList('alterar', $result));

        if (is_null($result[0]['dataFim']) || empty($result[0]['dataFim'])) {
            $this->replaceCode('checkedE', ' checked="checked"');
            $this->replaceCode('dtFimRowElem', ' style="display: none"');
            $this->replaceCode('dtFimDisabled', ' disabled="disabled"');
        }

        if (!$result[0]['url']) {
            $this->replaceInterval('imagem', '');
        }

        if ($result[0]['mostrarTodos'] == 1) {
            $this->replaceCode('checkedT', 'checked="checked"');
            $this->replaceCode('sPagina', ' style="display: none;"');
            $this->replaceCode('disabledP', ' disabled="disabled"');
        }

        if ($result[0]['novaJanela']) {
            $this->replaceCode('checkedN', 'checked="checked"');
        }

        if ($result[0]['ativo']) {
            $this->replaceCode('checkedA', 'checked="checked"');
        }
        
        if (isset($result[0]['dataFim']) && is_null($result[0]['dataFim']) || empty($result[0]['dataFim'])) {
            $this->replaceCode('checkedE', ' checked="checked"');
            $this->replaceCode('dtFimRowElem', ' style="display: none"');
            $this->replaceCode('dtFimDisabled', ' disabled="disabled"');
        }

        /**
         *  Salva os dados do formulario
         */
        if (isset($_POST['titulo'])) {

            $data = array();
            $menubanner = array();
            $del = array();
            $column = array();
            $newName = null;

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
            $data['titulo'] = isset($data['titulo']) ? $data['titulo'] : null;
            $data['link'] = isset($data['link']) ? $data['link'] : null;
            $data['descricao'] = isset($data['descricao']) ? $data['descricao'] : null;
            $data['dataIni'] = Crud::formatDate($data['dataIni']);
            if (isset($data['dataFim'])) {
                $data['dataFim'] = Crud::formatDate($data['dataFim'], 1);
            } else {
                $data['dataFim'] = null;
            }

            $data['mostrarTodos'] = isset($data['mostrarTodos']) ? 1 : 0;
            $data['novaJanela'] = isset($data['novaJanela']) ? 1 : 0;
            $data['ativo'] = isset($data['ativo']) ? 1 : 0;

            if (!isset($data['mostrarTodos'])) {
                $menubanner['idMenu'] = $data['idMenu'] != 'on' ? $data['idMenu'] : 0;
            }

            unset($data['idMenu']);
            unset($data['expira']);
            unset($data['todas']);

            $del['idBanner'] = $menubanner['idBanner'] = $id;

            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            if ($this->update($this->table, $data, "idBanner = $id")) {

                $this->delete('bannermenu', $del)
                        ->insert('bannermenu', $menubanner);

                if (isset($_FILES['url']['tmp_name']) && !empty($_FILES['url']['tmp_name'])) {

                    $ext = explode('.', $_FILES['url']['name']);

                    $column['url'] = $newName = $id . '-' . date("YmdHis") . '-' . rand(000000, 999999) . '.' . end($ext);

                    $path = DOCUMENTROOT . BAR . PATH . "/images/arquivos/banner/";

                    if (!file_exists($path)) {
                        mkdir($path, 0777);
                    }

                    unlink($path . '/' . $result[0]['url']);

                    umask(0000);
                    move_uploaded_file($_FILES['url']['tmp_name'], $path . $newName);

                    $this->update($this->table, $column, "idBanner = " . $id);
                }
            }

            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}
