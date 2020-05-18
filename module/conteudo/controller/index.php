<?php

Class Index extends Action {
	public $view = 'index.html';
	public $module = 'conteudo';
	public $dir = 'module';
	public $table = 'conteudo';
	public $title = 'Conteúdo';

	public function index() {

		parent::action(false);

		$src = array(
              //'/css/pagination.css'
			);
		$this->headLink($src);

		$href = array(
              //'/js/jquery.quick.pagination.min.js'
			);
		$this->headScript($href);
		try {

          /**
           * Monta o conteudo de acordo com a URL
           */
          $param = $this->_getParam();

          $idC = is_numeric(end($param)) ? "c.idConteudo = " . end($param) : "m.url = '" . end($param) . "'";
          $idM = is_numeric(end($param)) ? "AND c.idConteudo <> " . end($param) : "AND c.idConteudo = -1";

          if ($idC) {
          	$resultC = $this->select()
				          	->column("c.idConteudo AS id, 
				          		c.titulo, 
				          		c.resumo, 
				          		c.descricao, 
				          		DATE_FORMAT(c.dataIni,'%d') AS dia,
				          		DATE_FORMAT(c.dataIni,'%m') AS mes,
				          		DATE_FORMAT(c.dataIni,'%Y') AS ano,
				          		imagem,
				          		c.fonte, 
				          		c.url, 
				          		c.mostrarData, 
				          		m.descricao AS pagina")
				          	->from("$this->table c")
				          	->join("LEFT JOIN conteudomenu cm USING (idConteudo)")
				          	->join("LEFT JOIN menu m USING (idMenu)")
				          	->where("WHERE $idC AND c.ativo = 1 ORDER BY dataIni DESC LIMIT 3")
				          	->query();

          	foreach ($resultC as $key => $value) {
          		$resultC[$key]['descricao'] = is_numeric(end($param)) || end($param) != 'noticias' ? $value['descricao'] : Crud::str_reduce($value['descricao'], 550, '...');
          		$resultC[$key]['mes'] = Crud::dataMesEscritoMine($value['mes']);
          	}

          	if ($resultC[0]['mostrarData'] == 0) {
          		$this->replaceInterval('data', null);
          	}

          	if (empty($resultC[0]['imagem'])) {
          		$this->replaceInterval('img', null);
          	}

          	switch (end($param)) {
          		case 'noticias':
	          		$this->replaceInterval('description', $this->replaceList('description', $resultC));
	          		$this->replaceInterval('detalhe', null);
	          		$this->replaceInterval('modulos', null);
        			$this->replaceCode('noticiaspage', ' class="active"');
          			break;
          		
          		case 'empresa':
	          		$this->replaceInterval('modulos', $this->replaceList('modulos', $resultC));
	          		$this->replaceInterval('noticiaList', null);
	          		$this->replaceInterval('detalhe', null);
        			$this->replaceCode('empresapage', ' class="active"');
          			break;

          		case 'servicos':
	          		$this->replaceInterval('modulos', $this->replaceList('modulos', $resultC));
	          		$this->replaceInterval('noticiaList', null);
	          		$this->replaceInterval('detalhe', null);
        			$this->replaceCode('servicospage', ' class="active"');
          			break;
          		
          		default:
	          		$this->replaceInterval('detalhe', $this->replaceList('detalhe', $resultC));
	          		$this->replaceInterval('noticiaList', null);
	          		$this->replaceInterval('modulos', null);
          			break;
          	}

          	$resultM = $this->select()
				          	->column("c.idConteudo AS id, 
				          		c.titulo, 
				          		c.resumo,
				          		DATE_FORMAT(c.dataIni,'%d') AS dia,
				          		DATE_FORMAT(c.dataIni,'%m') AS mes,
				          		DATE_FORMAT(c.dataIni,'%Y') AS ano, 
				          		m.url AS pagina,
				          		m.descricao AS paginamais")
				          	->from("$this->table c")
				          	->join("LEFT JOIN conteudomenu cm USING (idConteudo)")
				          	->join("LEFT JOIN menu m USING (idMenu)")
				          	->where("WHERE m.url = '" . $param[0] . "' AND c.idConteudo <> " . $resultC[0]['id'] . " AND c.ativo = 1 ORDER BY dataIni DESC")
				          	->query();

          	if (count($resultM) > 0) {
          		foreach ($resultM as $key => $value) {
          			$resultM[$key]['mes'] = Crud::dataMesEscritoMine($resultM[$key]['mes']);
          		}
          		$this->replaceInterval('list', $this->replaceList('list', $resultM));
          		$this->replaceCode('paginamais', strtolower($resultM[0]['paginamais']));
          	} else {
          		$this->replaceInterval('descriptionplus', null);
          	}
          } else {
          	Crud::_redirect(WWWROOT . "/erro/404");
          	exit;
          }
      } catch (Exception $exc) {
      	echo $exc->getTraceAsString();
      }

      $this->_run(true);
  }

}