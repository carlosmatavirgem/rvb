<?php

Class Banner extends Action {

    public function montaBanner() {
        Crud::p(1,1);
        try {
            $uri = new Uri();
            $param = $uri->_getParam();

            $where = empty($param[0]) ? "mb.idMenu = 0" : "m.url = '" . $param[0] . "'";
            $or = (count($param) > 1) ? "OR m.url = '" . $param[1] . "'" : null;

            $result = $this->select()
                    ->column("b.descricao, b.url")
                    ->from("banner AS b")
                    ->join("LEFT JOIN bannermenu AS mb USING(idBanner)")
                    ->join("LEFT JOIN menu AS m USING(idMenu)")
                    ->where("WHERE $where $or OR b.mostrarTodos = 1 AND b.ativo = 1")
                    ->query(1);

            $html = $this->openFile('module/banner/view/index.html');
            /*
              $part = $this->selectCode('banner', true, $html);

              $_parse = $part;
              $code = '';

              foreach ($result as $row) {
              $_parse = $this->replaceCode('url', $row['url'], $_parse);
              $_parse = $this->replaceCode('descricao', $row['descricao'], $_parse);

              if (empty($row['descricao']))
              $_parse = $this->replaceInterval('txtBanner', null, $_parse);

              $code .= $_parse;
              $_parse = $part;
              }
              return $this->replaceInterval('banner', $code, $html);
             */
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
