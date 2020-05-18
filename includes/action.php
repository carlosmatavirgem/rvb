<?php

class Action extends Uri {

    protected $code;
    public $_parse;
    public $url;
    public $view = "index.html";
    public $conexao;
    public $module;
    public $layout = LAYOUT;
    public $template;

    public function action($menu = true) {

        //Crud::p($this);
        /**
         * Instanciando Objetos
         */
        $this->url = $this->dir . '/' . $this->module . '/view/' . $this->view;

        if (!file_exists($this->url)) {
            die("Não existe o arquivo: " . $this->url);
        }

        /**
         * Monta o layout
         */
        $this->layoutFile($this->layout);


        if (isset($_SESSION['s_filiacao'])) {
            $this->replaceCode('nome', $_SESSION['s_nome']);
        } else {
            $this->replaceInterval('login', null);
        }

        /**
         * Monta o menu
         */
        if ($menu === true && $this->dir == '../module') {

            $param = $this->_getParam(0);
            $code = $this->openFile($this->dir . '/menu/view/menu.html');
            $code = $this->replaceCode('a' . ucfirst($param), ' class="action"', $code);
            $this->replaceCode('menu', $code);

            if (isset($_SESSION['s_nome'])) {
                $saudacao = date('H') >= 18 ? 'Boa noite' : (date('H') >= 12 ? 'Boa tarde' : 'Bom dia' );
                $this->replaceCode('saudacao', $saudacao);
                $this->replaceCode('snome', $_SESSION['s_nome']);
            }
        } else {
            //$this->replaceCode('menu', Menu::montaMenu(2, "topo"));
            //$this->replaceCode('menurodape', Menu::montaMenu(2, "rodape"));

            /**
             * Monta o banner
             */
            try {
                $param = $this->_getParam();

                $where = empty($param[0]) ? "mb.idMenu = 0" : "m.url = '" . $param[0] . "'";
                $or = (count($param) > 1) ? "OR m.url = '" . $param[1] . "'" : null;

                $result = $this->select()
                        ->column("b.titulo, b.link, b.descricao, b.url AS banner")
                        ->from("banner AS b")
                        ->join("LEFT JOIN bannermenu AS mb USING(idBanner)")
                        ->join("LEFT JOIN menu AS m USING(idMenu)")
                        ->where("WHERE ($where $or OR b.mostrarTodos = 1) AND b.ativo = 1")
                        ->query();
                $result[0]['active'] = 'active';
                $resultcount[0]['activecount'] = ' class="active"';

                foreach ($result as $key => $value) {
                    $resultcount[$key]['bannercount'] = $key;
                }

                if (empty($param[0])) {
                    $this->replaceInterval('listbannerto', $this->replaceList('listbannerto', $resultcount));
                    $this->replaceInterval('listbanner', $this->replaceList('listbanner', $result));
                } else {
                    $this->replaceInterval('blocobanner', null);
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        //Crud::p($this->url,true);
        /**
         * Monta o conteudo
         */
        $this->replaceCode('conteudo', $this->openFile($this->url));
        $this->replaceCode('wwwroot', WWWROOT);
        $this->replaceCode('requesturi', REQUESTURI);

        /**
         * Monta o conteudo de acordo com a URL
         */
        if (isset($param[2]) && is_numeric($param[2])) {

            $resultD = $this->select()
                    ->column("c.titulo, c.resumo, c.imagem")
                    ->from("$this->table c")
                    ->join("LEFT JOIN conteudomenu cm USING (idConteudo)")
                    ->join("LEFT JOIN menu m1 USING (idMenu)")
                    ->join("LEFT JOIN menu m2 ON (m2.idMenu = m1.idMenuRelacionado)")
                    ->join("JOIN categoria ca USING (idCategoria)")
                    ->where("WHERE c.idConteudo = {$param[2]} AND c.ativo = 1")
                    ->query();

            $this->replaceCode('titulo', $resultD[0]['titulo']);
            $this->replaceCode('resumo', $resultD[0]['resumo']);
            $this->replaceCode('imagem', $resultD[0]['imagem']);
        }

        $this->headTitle();
    }

    protected function layoutFile($layout, $top = null, $bottom = null) {

        $this->code = $this->openFile($layout);

        $top ? $this->replaceCode('top', $top) : null;
        $bottom ? $this->replaceCode('bottom', $bottom) : null;
    }

    public static function openFile($file) {

        $htmlBuffer = "";
        $html = fopen($file, "r");
        while (!feof($html)) {
            $htmlBuffer .= fgets($html, 4096);
        }
        fclose($html);
        return $htmlBuffer;
    }

    /**
     * Antigo REPLACE/LOOP
     */
    public function replaceCode($marker, $value, $code = null) {

        if (is_null($code)) {
            $this->code = str_replace('{m:' . $marker . '}', $value, $this->code);
        } else {
            return str_replace('{m:' . $marker . '}', $value, $code);
        }
    }

    /**
     * Antigo REPLACEBLOCO/LOOP
     */
    public function replaceInterval($marker, $value, $code = null) {

        if (is_null($code)) {
            $this->code = str_ireplace($this->selectCode($marker), $value, $this->code);
        } else {
            return str_ireplace($this->selectCode($marker, true, $code), $value, $code);
        }
    }

    /**
     * Antigo SELECTTMP/LOOP
     */
    public function selectCode($marker, $return = true, $code = null) {

        $cod = is_null($code) ? $this->code : $code;

        $first = strpos($cod, '{m:' . $marker . '}')/* + strlen('{/m:' . $marker . '}') */;
        $end = strpos($cod, '{/m:' . $marker . '}');

        if ($return !== true) {
            $this->_parse = substr($cod, $first, $end - $first);
        } else {
            return substr($cod, $first, $end - $first);
        }
    }

    /**
     * Mosta lista
     */
    public function replaceList($marker, $value, $return = true, $code = null) {

        $part = self::selectCode($marker, $return, $code);
        $_parse = $part;
        $code = '';
        $line = 0;

        foreach ($value as $array) {
            foreach ($array as $key => $val) {
                $_parse = self::replaceCode($key, $val, $_parse);
            }
            switch ($line) {
                case 0: $_parse = self::replaceCode('css', "odd", $_parse);
                    $line = 1;
                    break;
                case 1: $_parse = self::replaceCode('css', "even", $_parse);
                    $line = 0;
                    break;
            }
            $code .= $_parse;
            $_parse = $part;
        }
        return $code;
    }

    /**
     * Monta combo
     */
    public function replaceCombo($marker, $value, $seleced = null) {

        $part = self::selectCode($marker, true);
        $_parse = $part;
        $code = '';

        foreach ($value as $array) {
            foreach ($array as $key => $val) {
                $_parse = self::replaceCode($key, $val, $_parse);
                $_parse = $this->replaceCode('selected', ($seleced == $val) ? 'selected="selected"' : '', $_parse);
            }
            $code .= $_parse;
            $_parse = $part;
        }
        $this->replaceInterval($marker, $code);
    }

    public function headTitle($title = null) {
        $title = TITLE . $title;
        $this->replaceCode('title', $title);
    }

    public function headLink(array $src) {
        $link = '';
        $wwwroot = '';
        if (is_array($src)) {
            foreach ($src as $val) {
                $wwwroot = '';
                if (strpos($val, 'http') === false) {
                    $wwwroot = WWWROOT;
                }
                $link .= "<link href=\"" . $wwwroot . $val . "\" rel=\"stylesheet\" type=\"text/css\">\n";
            }
        } else {
            die('O argumento deve ser uma array');
        }
        $this->replaceCode('headlink', $link);
    }

    public function headScript(array $href) {
        $script = '';
        if (is_array($href)) {
            foreach ($href as $key => $val) {

                $wwwroot = '';
                if (strstr($val, 'http') === false) {
                    $wwwroot = WWWROOT;
                }
                if ($key == 'noScript' && $key) {
                    $script .= "<script>$(document).ready(function() {" . $href[$key] . "});</script>\n";
                } else {
                    $script .= "<script src=\"" . $wwwroot . $val . "\"></script>\n";
                }
            }
        } else {
            die('O argumento deve ser uma array');
        }
        $this->replaceCode('headscript', $script);
    }

    /**
     * Limpa os marcadores e comentarios HTML do layout
     */
    private function cleanLayout() {

        $array = array();

        preg_match_all("[({/?[mMiIfFvV]:)+[[:alnum:]]+(})]", $this->code, $array, PREG_PATTERN_ORDER); //|(<!--)+.+(-->)

        foreach ($array[0] as $val) {
            $this->code = str_replace($val, "", $this->code);
        }
    }

    public function _run($run = false) {
        if ($run !== false) {
            $this->cleanLayout();
            echo $this->code;
        } else {
            return $this->code;
        }
    }

}
