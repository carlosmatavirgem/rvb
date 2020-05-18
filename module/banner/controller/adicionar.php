<?php

Class Adicionar extends Action {

    public $view = "form.html";
    public $module = 'banner';
    public $table = 'banner';
    public $title = 'Banner';
    public $dir = '../module';

    public function __construct() {

        if (!$_SESSION['s_logado'])
            Crud::_redirect(WWWROOT . "/admin/login");

        $this->__main();
    }

    public function __main() {

        parent::action();

        $this->headTitle(BREADCRUMB . $this->title . BREADCRUMB . 'Adicionar');

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
        $this->replaceCode('checkedA', 'checked="checked"');
        $this->replaceInterval('img', null);

        $this->replaceCode('required', "required");

        /**
         *  Salva os dados do formulario
         */
        if (isset($_POST['titulo'])) {

            /**
             *  Remove os campos vazios
             */
            foreach ($_POST as $key => $val) {
                if (!empty($val)) {
                    $data[$key] = $val;
                }
            }

            /**
             *  Tratamento do POST para gravar na base
             */
            $data['dataRegistro'] = date('Y-m-d H:i:s');
            $data['dataIni'] = Crud::formatDate($data['dataIni']);

            if (isset($data['dataFim'])) {
                $data['dataFim'] = Crud::formatDate($data['dataFim'], 1);
            }

            $data['mostrarTodos'] = isset($data['mostrarTodos']) ? 1 : 0;
            $data['novaJanela'] = isset($data['novaJanela']) ? 1 : 0;
            $data['ativo'] = isset($data['ativo']) ? 1 : 0;
            
            unset($data['expira']);
            unset($data['todas']);

            /**
             *  Salva na base Conteudo e ConteudoMenu
             */
            $id = $menubanner['idBanner'] = $this->insert($this->table, $data);
            $menubanner['idMenu'] = 0;

            if ($id) {

                $this->insert('bannermenu', $menubanner);

                if (isset($_FILES['url']['tmp_name']) && !empty($_FILES['url']['tmp_name'])) {

                    $ext = explode('.', $_FILES['url']['name']);

                    $column['url'] = $newName = $id . '-' . date("YmdHis") . '-' . rand(000000, 999999) . '.' . end($ext);

                    $path = DOCUMENTROOT . BAR . PATH . "/images/arquivos/banner/";

                    if (!file_exists($path)) {
                        mkdir($path, 0777);
                    }

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
