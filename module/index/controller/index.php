<?php

Class Index extends Action {

    public $module = 'index';
    public $table = 'conteudo';

    public function index() {

        parent::action(false);

        $this->headTitle();

        $this->replaceCode('homepage', ' class="active"');

        /*
         * Monta a listagem DICAS
         */
        $resultDicas = $this->select()
                ->column("c.idConteudo AS id, titulo, c.resumo, descricao")
                ->from("$this->table c")
                ->join("JOIN conteudomenu cm USING (idConteudo)")
                ->where("WHERE cm.idMenu = 3 AND c.ativo = 1 ORDER BY RAND() DESC LIMIT 3")
                ->query();

        $this->replaceCode('dicas1', $resultDicas[0]['resumo']);
        $this->replaceCode('dicas2', $resultDicas[1]['resumo']);
        $this->replaceCode('dicas3', $resultDicas[2]['resumo']);

        if (count($resultDicas) == 0) {
            $this->replaceInterval('dicas', null);
        }

        $this->_run(true);
    }

}
