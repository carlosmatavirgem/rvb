<?php

Class Alterar extends Action {

    public $view = "form.html";
    public $module = 'conteudo';
    public $table = 'conteudo';
    public $title = 'Conteúdo';
    public $dir = '../module';

    public function __construct() {

        if (!$_SESSION['s_logado']) {
            Crud::_redirect(WWWROOT . "/admin/login");
        }

        $this->alterar();
    }

    public function alterar() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Alterar');

        $src = array(
            '/css/back_end/jquery-ui.min.css'
        );
        $this->headLink($src);

        $href = array(
            '/js/forms/jquery.validate.min.js',
            '/js/forms/jquery.validate.fckeditor.js',
            '/js/forms/jquery.limit-1.2.js',
            '/js/forms/jquery.maskedinput.js',
            '/js/lang/jquery.validate.pt-br.js',
            '/js/lang/jquery.datepicker.pt-br.js',
            '/js/jquery.ToTop.js',
            '/js/custom_form.js'
        );
        $this->headScript($href);

        $this->replaceCode('title_page', $this->title);
        $this->replaceCode('iClass', 'iPencil');
        $this->replaceCode('iAcao', 'Alterar');

        /**
         *  Monta conteudo da PAGINA
         */
        $id = $this->_getParam(2);
        $result = $this->select()
                ->column("c.*, DATE_FORMAT(dataIni,'%d/%m/%Y') AS dataIni, DATE_FORMAT(dataFim,'%d/%m/%Y') AS dataFim, imagem as arquivo, cm.idMenu")
                ->from("$this->table c")
                ->join("JOIN conteudomenu cm USING(idConteudo)")
                ->where("WHERE c.idConteudo = $id")
                ->query();
        $this->replaceInterval('alterar', $this->replaceList('alterar', $result));

        if (empty($result[0]['arquivo'])) {
            $this->replaceInterval('img', null);
        }

        if ($result[0]['mostrarData'] == 1) {
            $this->replaceCode('checkedM', ' checked="checked"');
        }

        if ($result[0]['ativo'] == 1) {
            $this->replaceCode('checkedA', ' checked="checked"');
        }

        if (isset($result[0]['dataFim']) && is_null($result[0]['dataFim']) || empty($result[0]['dataFim'])) {
            $this->replaceCode('checkedE', ' checked="checked"');
            $this->replaceCode('dtFimRowElem', ' style="display: none"');
            $this->replaceCode('dtFimDisabled', ' disabled="disabled"');
        }

        /**
         *  Monta combo da PAGINA
         */
        $combo = $this->select()
                ->column("m1.idMenu as value, IF(m1.idMenuRelacionado = 0, m1.descricao, CONCAT(m2.descricao,' > ',m1.descricao)) as text")
                ->from("menu m1")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->where("ORDER BY text")
                ->query();
        $this->replaceCombo('pagina', $combo, $result[0]['idMenu']);

        /**
         *  Instanciacao da classe FCKEditor em substituicao ao HTMLEditor
         */
        require_once("../includes/fckeditor/fckeditor.php");

        $fck = new FCKeditor('descricao');
        $fck->BasePath = WWWROOT . '/includes/fckeditor/';
        $fck->Value = $result[0]['descricao'];
        $fck->Width = "541";
        $fck->Height = "400";
        $this->replaceCode('fckeditor', $fck->Create());

        /**
         *  Salva os dados do formulario
         */
        if (isset($_POST['titulo'])) {

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
            $data['dataIni'] = Crud::formatDate($data['dataIni'], 1);
            if (isset($data['dataFim'])) {
                $data['dataFim'] = Crud::formatDate($data['dataFim'], 1);
            } else {
                $data['dataFim'] = null;
            }

            $data['resumo'] = isset($data['resumo']) ? $data['resumo'] : null;
            $data['mostrarData'] = isset($data['mostrarData']) ? 1 : 0;
            $data['ativo'] = isset($data['ativo']) ? 1 : 0;

            $conteudomenu['idMenu'] = $data['idMenu'];
            unset($data['idMenu']);
            unset($data['expira']);

            $del['idConteudo'] = $conteudomenu['idConteudo'] = $id;
//Crud::p($data, true);
            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            if ($this->update($this->table, $data, "idConteudo = $id")) {

                $this->delete('conteudomenu', $del)
                        ->insert('conteudomenu', $conteudomenu);

                if (isset($_FILES['arquivo']['tmp_name']) && !empty($_FILES['arquivo']['tmp_name'])) {

                    $ext = explode('.', $_FILES['arquivo']['name']);

                    $column['imagem'] = $id . '-' . date("YmdHis") . '-' . rand(000000, 999999) . '.' . end($ext);

                    $newName = $column['imagem'];
                    $path = DOCUMENTROOT . BAR . PATH . "/images/arquivos/";

                    if (!file_exists($path)) {
                        mkdir($path, 0777);
                    }

                    unlink($path . '/' . $result[0]['arquivo']);
                    $this->update($this->table, $column, "idConteudo = $id");

                    umask(0000);
                    move_uploaded_file($_FILES['arquivo']['tmp_name'], $path . $newName);
                }
            }

            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}
