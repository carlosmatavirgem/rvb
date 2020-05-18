<?php

Class Menu extends Action {

    public static function montaMenu($tipo = null, $view = null) {
        
        Crud::p($this);

        try {
            $select = new Select();

            $result = $select->select()
                    ->column("m.idMenu AS value, m.descricao AS text, m.url AS module, m.restrito, cm.idConteudo AS conteudo, c.ativo")
                    ->from("menu AS m")
                    ->join("LEFT JOIN conteudomenu AS cm ON (cm.idMenu = m.idMenu)")
                    ->join("LEFT JOIN conteudo AS c ON (c.idConteudo = cm.idConteudo)")
                    ->where("WHERE m.idMenuRelacionado = 0 AND m.idMenuTipo = " . $tipo . " AND m.ativo = 1 GROUP BY m.idMenu ORDER BY m.ordem, m.descricao ASC")
                    ->query();
                    
            $html = $this->openFile('module/menu/view/' . $view . '.html');

            return self::arrayRecursive($html, $result, $tipo);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    /**
     * Monta menu relacionado
     * @return array
     */
    private function arrayRecursive($html, array $result, $tipo) {

        $part = $this->selectCode('menu', true, $html);

        $_parse = $part;
        $code = '';

        foreach ($result as $array) {

            foreach ($array as $key => $val) {

                if ($key == 'value') {

                    $resultSub = $this->select()
                            ->column("m.idMenu as subvalue, m.descricao as subtext, m.restrito, (SELECT url FROM menu  WHERE idMenu = " . $val . ") AS submodule, m.url AS submodulesub")
                            ->from("menu AS m")
                            ->join("LEFT JOIN conteudomenu AS cm ON (cm.idMenu = m.idMenu)")
                            ->where("WHERE m.idMenuRelacionado = " . $val . " AND m.idMenuTipo = " . $tipo . " AND m.ativo = 1 GROUP BY subvalue ORDER BY m.ordem ASC")
                            ->query();

                    if (count($resultSub) === 0) {
                        $_parse = $this->replaceInterval('sub', '', $_parse);
                    }
                }

                $lnk = $array['module'] != 'sobre' ? ' href="' . WWWROOT . '/' . $array['module'] . '"' : null;

                $a = '<a' . $lnk . '>';

                if ($array['ativo'] == 1) {
                    $a = is_null($array['conteudo']) 
                        ? '<a' . $lnk . '>' . $array['text'] . '</a>' 
                            : '<a href="' . WWWROOT . '/' . $array['module'] . '">';//'/' . $array['conteudo'] . 
                }

                $text = $a . $array['text'] . '</a>';
                
                $param = $this->_getParam(0);
                 
                $class = $param == $array['module'] 
                    ? ' class="current-menu-ancestor"' 
                        : (empty($param) ? $this->replaceCode('home', ' class="current-menu-ancestor"') : null);
                
                $_parse = $this->replaceCode('class', $class, $_parse);
                $_parse = $this->replaceCode('text', $text, $_parse);

                $partSub = $this->selectCode('submenu', true, $html);
                $_parseSub = $partSub;
                $codeSub = '';

                foreach ($resultSub as $arraySub) {
                    $subtext = '<a href="' . WWWROOT . '/' . $arraySub['submodule'] . '/' . $arraySub['submodulesub'] . '">' . $arraySub['subtext'] . '</a>';

                    $_parseSub = $this->replaceCode('subtext', $subtext, $_parseSub);

                    $codeSub .= $_parseSub;
                    $_parseSub = $partSub;
                }
            }
            $code .= $_parse;
            $_parse = $part;

            $code = str_replace($partSub, $codeSub, $code);
        }

        return $this->replaceInterval('menu', $code, $html);
    }

}

