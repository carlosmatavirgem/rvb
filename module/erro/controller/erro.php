<?php

Class Erro extends Action {

    public $view = 'index.html';
    public $module = 'erro';
    public $dir = 'module';
    public $table = 'erro';

    public function index() {

        parent::action(false);

        $param = $this->_getParam(true);


        $this->headTitle(BREADCRUMB . $param[1]);

        switch ($param[1]) {
            case '403':
                $this->replaceCode('erro', $param[1]);
                $msg = 'acesso restrito';
                break;
            case '404':
                $this->replaceCode('erro', $param[1]);
                $msg = 'ocorreu um erro.<br />A página solicitada não foi encontrado';
                break;
            default:
                $this->replaceCode('erro', '404');
                $msg = 'ocorreu um erro.<br />A página solicitada não foi encontrado';
                break;
        }
        $this->replaceCode('msg', $msg);

        $this->_run(true);
    }

}
