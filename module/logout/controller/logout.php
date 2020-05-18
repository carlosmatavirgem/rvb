<?php

Class Logout extends Action {

    public function __construct() {
        session_destroy();
        $param = $this->_getParam(true);
        Crud::_redirect(WWWROOT . ( count($param) == 1) ? $_SERVER['HTTP_REFERER'] : "/admin/login");
    }

}