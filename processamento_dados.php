<?php

// Inclusão de bibliotecas internas/externas
include_once "src/persistencia.php";

$persistencia		= new persistencia();
$programadores 		= $persistencia->obterListaProgramadores();

//Para cada programador, busca as informações na base
foreach ($programadores as $key => $value) {

	$interacoes		= $persistencia->obterInteracoesProgramadores($value);	
	$persistencia->gravarInteracoesProgramadores($interacoes, $value);

}

die("Procedimento realizado com sucesso");

?>