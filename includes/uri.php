<?php

class Uri extends Crud {

    public $dir = 'module';
    public $module;
    public $level;
    public $action;

    public function uri($admin = null) {

        $result = array();
        $param = self::_getParam();
        $url = !isset($param[1]) || is_numeric($param[1]) ? $param[0] : $param[1];

        if (is_null($admin)) {

            if (isset($param[1]) && $param[1] == 'download') {

                /**
                 * Força o DOWNLOAD de arquivos
                 */
                Crud::download($param[2], $param[0]);
            } elseif ($param[0] == 'email') {
                $this->module = $param[0];
                $this->action = 'sendmail';
            } elseif ($param[0] == 'midia') {
                $this->module = $param[0];
                $this->action = $param[1];
            } elseif ($param[0] != 'erro') {

                /**
                 * Retorna o MODULO a ser usado
                 */
                list($url) = explode('?', $url);
                $result = $this->select()
                        ->column("md.url as module")
                        ->from("modulo md")
                        ->join("LEFT JOIN modulomenu mm USING(idModulo)")
                        ->join("LEFT JOIN menu m USING(idMenu)")
                        ->where("WHERE m.url = '$url' OR md.url = '$url'")
                        ->query();

                /**
                 * Acesso direto de URI pelo jQuery
                 */
                if (count($param) > 2 && $param[2] == 'direct') {
                    $this->module = $param[0];
                    $this->action = $param[1];
                } elseif (count($result) > 0 && $result[0]['module'] == 'conteudo' && !empty($param[0])) {
                    $this->module = $result[0]['module'];
                    $this->action = 'index';
                } else {
                    $this->module = (count($result) > 0 && !empty($param[0])) ? $result[0]['module'] : 'index';
                    $this->action = (isset($param[1]) && ($result[0]['module'] != 'conteudo' && $result[0]['module'] != 'pacote')) ? $param[1] : 'index';
                }
            } else {
                $this->module = $param[0];
            }

        // Admin
        } else {
            $this->module = $param[0];
            $this->dir = '../module';

            if (count($param) > 1) {
                $this->module = $param[0];
                $this->action = $param[1];
            }

            $actions = array("excluir", "send");
            if (isset($param[1]) && $param[1] === 'excluir') {
                $this->action = $param[0];
            }
        }

        $this->controller = isset($this->action) ? $this->action : $this->module;

        //self::debug($param);

        if (!isset($param[2]) || $param[2] != 'js') {
            if (file_exists($this->dir . '/' . $this->module . '/controller/' . $this->controller . '.php') && $param[0] != 'admin') {
                require_once($this->dir . '/' . $this->module . '/controller/' . $this->controller . '.php');
            } elseif ((count($param) == 1 && empty($param[0])) && strpos($_SERVER['REQUEST_URI'], 'admin')) {
                Crud::_redirect(WWWROOT . "/admin/login");
            } else {
                if(!file_exists(DOCUMENTROOT . PATH . BAR . 'admin')){
                    Crud::_redirect(WWWROOT);
                }else{
                    Crud::p(2, true);
                    //Crud::_redirect(WWWROOT . "/erro/404");
                    //exit;
                }
            }

            if (count($param) <= 2 || end($param) != 'direct') {
                $className = self::className(ucfirst($this->controller));
                new $className();
            }
        }

    }

    private function className($str) {

        $param = array();
        $array = explode('-', $str);
        foreach ($array as $value) {
            $param[] .= ucfirst($value);
        }
        return implode('', $param);
    }

    public static function removeAcento($str, $enc = 'UTF-8') {

        $accent = array(
            'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
            'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
            'C' => '/&Ccedil;/',
            'c' => '/&ccedil;/',
            'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
            'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
            'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
            'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
            'N' => '/&Ntilde;/',
            'n' => '/&ntilde;/',
            'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
            'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
            'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
            'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
            'Y' => '/&Yacute;/',
            'y' => '/&yacute;|&yuml;/',
            '' => '/\|/',
            ' ' => '/\s+/',
            '' => '/\?/',
            'a.' => '/&ordf;/',
            'o.' => '/&ordm;/'
            );

        return strtolower(str_replace(' ', '-', preg_replace($accent, array_keys($accent), htmlentities(str_replace('-', ' ', $str), ENT_NOQUOTES, $enc))));
    }

    public function _getParam($key = null) {

        if (preg_match("/\/$/", $_SERVER['REQUEST_URI'])) {
            $uri = explode('/', substr($_SERVER['QUERY_STRING'], 2, -1));
        } else {
            $uri = explode('/', substr($_SERVER['QUERY_STRING'], 2));
        }
        return (is_null($key) || $key === true ? $uri : $uri[$key]);
    }

    private function debug($param){
        Crud::p($param);
        Crud::p($this->dir . '/' . $this->module . '/controller/' . $this->controller . '.php');
        Crud::p($this);
        Crud::vd(file_exists($this->dir . '/' . $this->module . '/controller/' . $this->controller . '.php'));
        Crud::p(end($param));
        Crud::p($_SERVER);
    }

}
