<?php
session_start();
$nivel = "../../";
require_once($nivel."includes/config.inc.php");
global $c;
	
	$sql = "SELECT senha FROM perfil WHERE idPerfil = " . $_SESSION['s_id'];
	$row = $c->sqlObj($sql);
	echo 0;
	
	if(isset($_POST['senha'])){
		$senha = mysql_real_escape_string($_POST['senha'], $c->resource);
		if($row->senha != md5($senha)){
			echo 1;
		}
	}
	if(isset($_POST['nova'])){
		$senha = mysql_real_escape_string($_POST['nova'], $c->resource);
		if($row->senha != md5($senha)){
			
			$c->sqlExec("UPDATE perfil SET senha = MD5('" . $senha . "'), ativo = 1 WHERE idPerfil = " . $_SESSION['s_id']);
			header("Location: ./");		
		}
	}
?>