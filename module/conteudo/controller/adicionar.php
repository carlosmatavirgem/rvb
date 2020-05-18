<?php

Class Adicionar extends Action {

    public $view = "form.html";
    public $module = 'conteudo';
    public $table = 'conteudo';
    public $title = 'Conteúdo';
    public $dir = '../module';

    public function __construct() {

        if (!$_SESSION['s_logado']) {
            Crud::_redirect(WWWROOT . "/admin/login");
        }

        $this->adicionar();
    }

    public function adicionar() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Adicionar');

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
            '/js/custom_form.js',
        );
        $this->headScript($href);

        $this->replaceCode('title_page', $this->title);
        $this->replaceCode('iClass', 'iAdd');
        $this->replaceCode('iAcao', 'Adicionar');

        $this->replaceInterval('imagem', null);

        /**
         *  Instanciacao da classe FCKEditor em substituicao ao HTMLEditor
         */
        require_once("../includes/fckeditor/fckeditor.php");

        $fck = new FCKeditor('descricao');
        $fck->BasePath = '../../includes/fckeditor/';
        $fck->Width = "541";
        $fck->Height = "400";
        $this->replaceCode('fckeditor', $fck->Create());

        $this->replaceCode('checkedA', ' checked="checked"');
        $this->replaceCode('checkedM', ' checked="checked"');

        /**
         *  Monta combo da PAGINA
         */
        $combo = $this->select()
                ->column("m1.idMenu as value, IF(m1.idMenuRelacionado = 0, m1.descricao, CONCAT(m2.descricao,' > ',m1.descricao)) as text")
                ->from("menu m1")
                ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                ->where("ORDER BY text")
                ->query();
        $this->replaceCombo('pagina', $combo);

        $this->replaceInterval('img', null);

        /**
         *  Salva os dados do formulario
         */
        if (isset($_POST['titulo'])) {

            /**
             *  Remove os campos vazios
             */
            foreach ($_POST as $key => $val) {
                if ($val) {
                    /* Campo alterado em 09/07/2014 */
                    /* $data[$key] = $val */
                    /* Campo apresentava problemas quando alguns caracteres mysql eram inseridos */
                    $data[$key] = mysql_escape_string($val);
                }
            }

            /**
             *  Tratamento do POST para gravar na base
             */
            $data['dataRegistro'] = date('Y-m-d H:i:s');
            $data['dataIni'] = Crud::formatDate($data['dataIni'], 1);

            if (isset($data['dataFim'])) {
                $data['dataFim'] = Crud::formatDate($data['dataFim'], 1);
            }

            $data['resumo'] = isset($data['resumo']) ? $data['resumo'] : null;
            $data['mostrarData'] = isset($data['mostrarData']) ? 1 : 0;
            $data['ativo'] = isset($data['ativo']) ? 1 : 0;

            $conteudomenu['idMenu'] = $data['idMenu'];
            unset($data['idMenu']);
            unset($data['expira']);

            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            $id = $this->insert($this->table, $data);
            $conteudoarquivo['idConteudo'] = $conteudomenu['idConteudo'] = $id;

            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            if ($id) {

                $this->insert('conteudomenu', $conteudomenu);

                if ($_FILES['arquivo']['tmp_name'] && !empty($_FILES['arquivo']['tmp_name'])) {

                    $ext = explode('.', $_FILES['arquivo']['name']);

                    $column['imagem'] = $id . '-' . date("YmdHis") . '-' . rand(000000, 999999) . '.' . end($ext);

                    $newName = $column['imagem'];
                    $path = DOCUMENTROOT . BAR . PATH . "/images/arquivos/";

                    if (!file_exists($path)) {
                        mkdir($path, 0777);
                    }

                    $this->update('conteudo', $column, "idConteudo = $id");

                    umask(0000);
                    move_uploaded_file($_FILES['arquivo']['tmp_name'], $path . $newName);
                }
            }

            Crud::_redirect(WWWROOT . "/admin/" . $this->module);
        }

        $this->_run(true);
    }

}
