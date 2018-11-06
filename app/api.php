<?php

// Inclusão de bibliotecas internas/externas
include_once "boot.php";
include_once "../src/mycurl.php";
include_once "../src/githubapi.php";
include_once "../src/repositorio.php";
include_once "../src/programador.php";
include_once "../src/persistencia.php";

$api 					= new githubapi();
$repositorio 			= new repositorio($api);
$programador 			= new programador($api);
$persistencia			= new persistencia();

//Definição do repositório semente
//$nome_repositorio 	= "keystone";
//$usuario_repositorio 	= "openstack";



/*************************************************************************************************
		PRIMEIRA ETAPA - LISTAR TODOS OS PROGRAMADORES DO REPOSITORIO 
**************************************************************************************************/

/*

//Listar todos os Programadores (ids) de um repositório
$programadores    					= $repositorio->listarProgramadores($nome_repositorio, $usuario_repositorio, $qtda_programadores);

//Acrescentar novos dados básicos (especifico dos programadores na lista)
$repositorios_programadores_dados 	= $programador->completarDadosProgramadores($programadores);

//Grava os dados básicos dos programadores na base
$persistencia->gravarDados($repositorios_programadores_dados);

unset($programadores);
unset($repositorios_programadores_dados);

echo "O processo [primeira etapa] foi concluído corretamente. Repositório: ".$nome_repositorio;
die();

*/


/*************************************************************************************************
		SEGUNDA ETAPA - COLETAR OS DADOS ESTATÍSTICOS DOS PROGRAMADORES
**************************************************************************************************/
/*

for ($i=0; $i <10 ; $i++) { 
	
	$qtda_programadores_para_coleta = 30;

	//Busca os programadores
	$listaProgramadores 			= $persistencia->obterListaProgramadoresColeta($qtda_programadores_para_coleta);

	//Para cada programador
	foreach ($listaProgramadores as $key => $value) {

		//Obtem a quantidade de commits
		$listaProgramadores[$key]['commits'] 	= $programador->buscarQuantidadeTotalCommits($listaProgramadores[$key]['login']);
		$listaProgramadores[$key]['issues']  	= $programador->buscarQuantidadeTotalIssues($listaProgramadores[$key]['login']);
		$listaProgramadores[$key]['pr']  		= $programador->buscarQuantidadeTotalPR($listaProgramadores[$key]['login']);
	}

	//Gravar as estatísticas na base de dados
	$persistencia->gravarEstatisticasProgramadores($listaProgramadores);

	echo "O processo [segunda etapa: ".$i."] foi concluído corretamente.";

}

*/



/*************************************************************************************************
		TERCEIRA ETAPA - COLETAR OS DADOS DOS REPOSITÓRIOS DOS PROGRAMADORES
**************************************************************************************************/
/*

for ($i=0; $i <10 ; $i++) { 
	
	$qtda_programadores_para_coleta = 25;

	//Busca os programadores
	$listaProgramadores 			= $persistencia->obterListaProgramadoresColeta($qtda_programadores_para_coleta);

	//Busca os repositórios dos programadores
	$listaProgramadoresRepositorios = $programador->listarRepositoriosPorProgramador($listaProgramadores);

	//Gravar as estatísticas na base de dados
	$persistencia->gravarRepositoriosDosProgramadores($listaProgramadoresRepositorios);

	echo "O processo [terceira etapa: ".$i."] foi concluído corretamente.";

}
*/



/*************************************************************************************************
		QUINTA ETAPA - COLETAR OS DADOS DOS REPOSITORIOS
**************************************************************************************************/
/*
for ($i=0; $i <11 ; $i++) { 
	
	$qtda_coleta = 1000;

	//Busca os programadores
	$listaRepositorios	= $persistencia->obterRepositorios($qtda_coleta);

	foreach ($listaRepositorios as $key => $value) {

		$nome = $repositorio->buscarNomeRepositorioPorId($listaRepositorios[$key]['id_repositorio'], $listaRepositorios[$key]['usuario_dono']);
		$persistencia->gravarNomeRepositorio($listaRepositorios[$key]['id_repositorio'], $nome);
		
	}


	echo "O processo [quarta etapa: ".$i."] foi concluído corretamente.";


}

*/

/*************************************************************************************************
		QUARTA ETAPA - COLETAR OS DADOS DE ATUAÇÃO DOS PROGRAMADORES NOS REPOSITÓRIOS
**************************************************************************************************/
/*
for ($i=0; $i <20 ; $i++) { 
	
	$qtda_programadores_para_coleta = 600;

	//Busca os programadores
	$listaProgramadoresRepositorios	= $persistencia->obterProgramadoresRepositorios($qtda_programadores_para_coleta);

	foreach ($listaProgramadoresRepositorios as $key => $value) {
		
		$listaProgramadoresRepositorios[$key]['qtda_commits'] 	= $programador->buscarCommitsProgramadorPorRepositorio($listaProgramadoresRepositorios[$key]['login'],$listaProgramadoresRepositorios[$key]['nome']);
		$listaProgramadoresRepositorios[$key]['qtda_issues']  	= $programador->buscarIssuesProgramadorPorRepositorio($listaProgramadoresRepositorios[$key]['login'],$listaProgramadoresRepositorios[$key]['nome']);
		$listaProgramadoresRepositorios[$key]['qtda_pr']  		= $programador->buscarPRProgramadorPorRepositorio($listaProgramadoresRepositorios[$key]['login'],$listaProgramadoresRepositorios[$key]['nome']);

		//Atualiza atuação do programador
		$persistencia->atualizarAtuacaoProgramador($listaProgramadoresRepositorios[$key]);

	}


	echo "O processo [quarta etapa: ".$i."] foi concluído corretamente.";

}
*/

/*************************************************************************************************
		SEXTA ETAPA - NORMALIZAÇÃO DOS DADOS
**************************************************************************************************/
	/*
	$qtda_programadores_para_coleta = 500;

	//Busca os valores máximos / mínimos para realizar os cálculos - posteriormente
	$valoresBase 		= $persistencia->obterValoloresLimiteProgramadores();

	//Busca os programadores
	$listaProgramadores	= $persistencia->obterProgramadoresNormalizacao($qtda_programadores_para_coleta);

	//Para cada programador
	foreach ($listaProgramadores as $key => $value) {

		//Realiza o cálculo da normalização:  valor_normalizado = ((valor - valor_minimo) / (valor_maximo - valor_minimo))
		$valor 													= $listaProgramadores[$key]['qtda_seguidores'];
		$listaProgramadores[$key]['qtda_seguidores_n'] 			= (($valor - $valoresBase['min_qtda_seguidores']) / ($valoresBase['max_qtda_seguidores'] - $valoresBase['min_qtda_seguidores']));

		$valor 													= $listaProgramadores[$key]['qtda_seguindo'];
		$listaProgramadores[$key]['qtda_seguindo_n'] 			= (($valor - $valoresBase['min_qtda_seguindo']) / ($valoresBase['max_qtda_seguindo'] - $valoresBase['min_qtda_seguindo']));

		$valor 													= $listaProgramadores[$key]['qtda_rep_publicos'];
		$listaProgramadores[$key]['qtda_rep_publicos_n']		= (($valor - $valoresBase['min_qtda_rep_publicos']) / ($valoresBase['max_qtda_rep_publicos'] - $valoresBase['min_qtda_rep_publicos']));

		$valor 													= $listaProgramadores[$key]['qtda_p_requests_incluidos'];
		$listaProgramadores[$key]['qtda_p_requests_incluidos_n']= (($valor - $valoresBase['min_qtda_p_requests_incluidos']) / ($valoresBase['max_qtda_p_requests_incluidos'] - $valoresBase['min_qtda_p_requests_incluidos']));

		$valor 													= $listaProgramadores[$key]['qtda_issues_incluidos'];
		$listaProgramadores[$key]['qtda_issues_incluidos_n'] 	= (($valor - $valoresBase['min_qtda_issues_incluidos']) / ($valoresBase['max_qtda_issues_incluidos'] - $valoresBase['min_qtda_issues_incluidos']));

		$valor 													= $listaProgramadores[$key]['qtda_commits_incluidos'];
		$listaProgramadores[$key]['qtda_commits_incluidos_n'] 	= (($valor - $valoresBase['min_qtda_commits_incluidos']) / ($valoresBase['max_qtda_commits_incluidos'] - $valoresBase['min_qtda_commits_incluidos']));

		// Formata o valor para 4 casas decimais 
		$listaProgramadores[$key]['qtda_seguidores_n'] 			= round($listaProgramadores[$key]['qtda_seguidores_n'], 9);
		$listaProgramadores[$key]['qtda_seguindo_n'] 			= round($listaProgramadores[$key]['qtda_seguindo_n'], 9);
		$listaProgramadores[$key]['qtda_rep_publicos_n']		= round($listaProgramadores[$key]['qtda_rep_publicos_n'], 9);
		$listaProgramadores[$key]['qtda_p_requests_incluidos_n']= round($listaProgramadores[$key]['qtda_p_requests_incluidos_n'], 9);
		$listaProgramadores[$key]['qtda_issues_incluidos_n'] 	= round($listaProgramadores[$key]['qtda_issues_incluidos_n'], 9);
		$listaProgramadores[$key]['qtda_commits_incluidos_n'] 	= round($listaProgramadores[$key]['qtda_commits_incluidos_n'], 9); 

		//Incluir na base os valores normalizados do programador - não é possível atualizar em lote
		$persistencia->atualizarNormalizacaoProgramador($listaProgramadores[$key]);

	}

	echo "O processo [sexta etapa] foi concluído corretamente. <br>";
*/



/*************************************************************************************************
		SETIMA ETAPA - COLETAR OS DADOS DOS REPOSITORIOS POR PROGRAMADOR
**************************************************************************************************/

/* 

	$listaProgramadoresRepositorios = null;
	$listaRepositorios = null;
	
	$qtda_programadores_para_coleta = 100;

	//Busca os programadores
	$listaProgramadoresRepositorios	= $persistencia->obterProgramadoresRestantes($qtda_programadores_para_coleta);

	// Para cada programador, faz a busca de seus repositórios
	foreach ($listaProgramadoresRepositorios as $key => $value) {
		
		$listaRepositorios 		= $repositorio->buscarInformacoesRepositorio($listaProgramadoresRepositorios[$key]['login']);

		if($listaRepositorios != 0)
		{
			foreach ($listaRepositorios as $key2 => $value2) {
					$persistencia->criarRegistroRepositorio($listaRepositorios[$key2]);
			}
		}
		
		// atualiza o indicador de coleta dos repositórios para o programador 
		$persistencia->atualizaIndicadorColetaRepositorios($listaProgramadoresRepositorios[$key]['id']);
		$listaRepositorios = null;

	}


	echo "O processo [SETIMA ETAPA] foi concluído corretamente.";
*/


/*************************************************************************************************
		OITAVA ETAPA - COLETAR A QTDA DE INCRITOS DE CADA REPOSITORIO
*************************************************************************************************



	$listaProgramadoresRepositorios = null;
	$listaRepositorios = null;
	
	$qtda_programadores_para_coleta = 6000;

	//Busca os programadores
	$listaProgramadoresRepositorios	= $persistencia->obterRepositoriosRestantes($qtda_programadores_para_coleta);

	// Para cada programador, faz a busca de seus repositórios
	foreach ($listaProgramadoresRepositorios as $key => $value) {
		
		$qtda 		= $repositorio->buscarQtdaInscritos($listaProgramadoresRepositorios[$key]['nome'], $listaProgramadoresRepositorios[$key]['user_dono']);
		
		$persistencia->atualizaQtdaInscritos($qtda, $listaProgramadoresRepositorios[$key]['id_repositorio']);
		$listaRepositorios = null;

	}


	echo "O processo [OITAVA ETAPA] foi concluído corretamente.";



/*************************************************************************************************
		NONA ETAPA - COLETAR A QTDA DE DIAS DE CRIAÇÃO DO REPOSITÓRIO
**************************************************************************************************/
	/*
	$qtda_repositorios_para_coleta = 6000;

	//Busca os programadores
	$listaRepositorios	= $persistencia->obterRepositoriosColetaDias($qtda_repositorios_para_coleta);

	// Para cada programador, faz a busca de seus repositórios
	foreach ($listaRepositorios as $key => $value) {
		
		$qtdaDias	 = $repositorio->buscarQtdaDias($listaRepositorios[$key]['nome'], $listaRepositorios[$key]['user_dono']);

		if($listaRepositorios[$key]['qtda_estrelas'] == 0)
			$estatistica = 0;
		else
		{
			$estatistica = $listaRepositorios[$key]['qtda_estrelas'] / $qtdaDias;
			$estatistica=sprintf("%2.10f",$estatistica); 
		}
	
		$persistencia->atualizaDiasCriacaoEstatistica($listaRepositorios[$key]['id_repositorio'], $qtdaDias, $estatistica);
	}


	echo "O processo [NONA ETAPA] foi concluído corretamente. 6 mil registros submetidos ";*/


/*************************************************************************************************
		NONA ETAPA - COLETAR A QTDA DE DIAS DE CRIAÇÃO DO REPOSITÓRIO
**************************************************************************************************
	
	$qtda_repositorios_para_coleta = 6000;

	//Busca os programadores
	$listaRepositorios	= $persistencia->obterRepositoriosColetaSoma($qtda_repositorios_para_coleta);

	// Para cada programador, faz a busca de seus repositórios
	foreach ($listaRepositorios as $key => $value) {
		
		//$qtdaDias	 = $repositorio->buscarQtdaDias($listaRepositorios[$key]['nome'], $listaRepositorios[$key]['user_dono']);

		if ($listaRepositorios[$key]['dias_criacao'] == 0)
			$listaRepositorios[$key]['dias_criacao'] = 1;

		$soma = $listaRepositorios[$key]['qtda_estrelas'] + $listaRepositorios[$key]['qtda_forks'];
		if($soma == 0)
			$estatistica = 0;
		else
		{
			$estatistica =  $soma / $listaRepositorios[$key]['dias_criacao'];
			$estatistica=sprintf("%2.10f",$estatistica); 
		}
	
		$persistencia->atualizaEstatistica($listaRepositorios[$key]['id_repositorio'], $estatistica);
	}


	echo "O processo [NONA ETAPA] foi concluído corretamente.";
*/


/*************************************************************************************************
		DECIMA ETAPA - TRATAMENTO E INCLUSAO DE REGISTROS NA TABELA REPOSITÓRIOS PERFIS
**************************************************************************************************
	

	$conn = mysqli_connect("localhost","root","","cinpro");

	// Para cada linha da tabela REPOSITORIOS_PERFIS
	for ($i=1; $i <= 4 ; $i++) { 
		
		$porcentagem = array();

		// Para cada perfil
		for ($j=1; $j<=6 ; $j++) { 
			
			// Pegar o count de todo mundo que seja do perfil J
			//$sql  		= "select count(*) as contador from programadores where id_perfil = ".$j;
			$sql  		= "select count(*) as contador from atuacao a, programadores b where a.id_programador = b.id_programador and b.id_perfil = ".$j;
			$resultado 	= $conn->query($sql) or die (mysqli_error());
			$linha		= mysqli_fetch_array($resultado);
			$count1		= $linha['contador'];

			// Pegar o count de todo mundo que seja do perfil J que tenha atuado em projetos de nivel I
			$sql = "
						select
							count(*) contador
						from
							programadores a,
							atuacao b,
							repositorios c
						where
							a.id_perfil = ".$j." 
							and a.id_programador = b.id_programador
							and b.id_repositorio = c.id_repositorio
							and c.id_nivel_sucesso = ".$i;

			$resultado 	= $conn->query($sql) or die (mysqli_error());
			$linha		= mysqli_fetch_array($resultado);
			$count2		= $linha['contador'];

			// Calculo da porcentagem de participação
			//$porcentagem[$j] = (( 100 * $count2 ) / $count1);
			$porcentagem[$j]=sprintf("%2.10f",(( 100.00 * $count2 ) / $count1)); 

		}

		// Grava na tabela a porcentagem de participação
        $sql = "update repositorios_perfis set porcentagem_partic_perfil_1 = ".$porcentagem[1].", porcentagem_partic_perfil_2 = ".$porcentagem[2].", porcentagem_partic_perfil_3 = ".$porcentagem[3].", porcentagem_partic_perfil_4 = ".$porcentagem[4].", porcentagem_partic_perfil_5 = ".$porcentagem[5].", porcentagem_partic_perfil_6 = ".$porcentagem[6]." where id_nivel_sucesso = ".$i;
        $conn->query($sql);			


	}

	echo "O processo [DECIMA ETAPA] foi concluído corretamente."; */

	/*************************************************************************************************
		NONA ETAPA - COLETAR A QTDA DE DIAS DE CRIAÇÃO DO REPOSITÓRIO
**************************************************************************************************
	
	$qtda_repositorios_para_coleta = 6000;

	//Busca os programadores
	$listaRepositorios	= $persistencia->obterRepositoriosColetaSoma($qtda_repositorios_para_coleta);

	// Para cada programador, faz a busca de seus repositórios
	foreach ($listaRepositorios as $key => $value) {
		
		//$qtdaDias	 = $repositorio->buscarQtdaDias($listaRepositorios[$key]['nome'], $listaRepositorios[$key]['user_dono']);

		if ($listaRepositorios[$key]['dias_criacao'] == 0)
			$listaRepositorios[$key]['dias_criacao'] = 1;

		$soma = $listaRepositorios[$key]['qtda_estrelas'] + $listaRepositorios[$key]['qtda_forks'];
		if($soma == 0)
			$estatistica = 0;
		else
		{
			$estatistica =  $soma / $listaRepositorios[$key]['dias_criacao'];
			$estatistica=sprintf("%2.10f",$estatistica); 
		}
	
		$persistencia->atualizaEstatistica($listaRepositorios[$key]['id_repositorio'], $estatistica);
	}


	echo "O processo [NONA ETAPA] foi concluído corretamente.";
*/


/*************************************************************************************************
		DECIMA PRIMEIRA ETAPA - TRATAMENTO E INCLUSAO DE REGISTROS NA TABELA PERFIS REPOSITÓRIOS
**************************************************************************************************/
	

	$conn = mysqli_connect("localhost","root","","cinpro");

	// Para cada linha da tabela PERFIS_REPOSITORIOS
	for ($i=1; $i <= 6 ; $i++) { 
		
		$porcentagem = array();

		// Para cada nível de sucesso
		for ($j=1; $j<=4 ; $j++) { 
			
			// Pegar o count de todo mundo que seja do nível de sucesso J
			$sql  		= "select count(*) as contador from atuacao a, repositorios b where a.id_repositorio = b.id_repositorio and b.id_nivel_sucesso = ".$j;
			$resultado 	= $conn->query($sql) or die (mysqli_error());
			$linha		= mysqli_fetch_array($resultado);
			$count1		= $linha['contador'];

			// Pegar o count de todo mundo que seja do perfil I que tenha atuado em projetos de nivel J
			$sql = "
						select
							count(*) contador
						from
							programadores a,
							atuacao b,
							repositorios c
						where
							a.id_perfil = ".$i." 
							and a.id_programador = b.id_programador
							and b.id_repositorio = c.id_repositorio
							and c.id_nivel_sucesso = ".$j;

			$resultado 	= $conn->query($sql) or die (mysqli_error());
			$linha		= mysqli_fetch_array($resultado);
			$count2		= $linha['contador'];

			// Calculo da porcentagem de participação
			$porcentagem[$j]=sprintf("%2.10f",(( 100.00 * $count2 ) / $count1)); 

		}

		// Grava na tabela a porcentagem de participação
        $sql = "update perfis_repositorio set excepcional = ".$porcentagem[1].", muito_sucesso = ".$porcentagem[2].", sucesso = ".$porcentagem[3].", pouco_sucesso = ".$porcentagem[4]." where id_perfil = ".$i;


        $conn->query($sql);			


	}

	echo "O processo [11ª ETAPA] foi concluído corretamente.";


?>