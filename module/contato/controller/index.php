<?php

Class Index extends Action {

    public $view = 'index.html';
    public $module = 'contato';
    public $dir = 'module';
    public $title = 'Contato';

    public function index() {

        parent::action(false);

        $this->headTitle();

        $href = array(
                //'/js/forms/jquery.validate.min.js',
                //'/js/lang/jquery.validate.pt-br.js',
        );
        $this->headScript($href);

        $param = $this->_getParam();

        $this->replaceInterval('banner', null);
        $this->replaceCode('contatopage', ' class="active"');

        if (isset($_POST['login']['email'])) {
            $auth = new Auth();
            $auth->authenticate($_POST);
        }

        $this->_run(true);
    }

}
