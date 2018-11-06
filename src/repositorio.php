<?php


class repositorio {


	public $api;

	function __construct($api)
	{
		$this->api = $api;	
	}

	// Listar todos os programadores de um repositório - retorna uma lista com os ids e nomes
	public function listarProgramadores($nome_repositorio, $usuario_repositorio, $totalProgramadores = null)
	{

		//Buscar todos os programadores do repositório
		$res 								= null;
		$command 							= "/repos/".$usuario_repositorio."/".$nome_repositorio."/contributors";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null, null, $totalProgramadores);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar os programadores do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}	

		$programadores		= array();

		//Para cada programador do repositório, busca as informações detalhadas
		foreach ($res as $key => $value) {
			$programadores[] = array("id" => $res[$key]['id'], "login" => $res[$key]['login'], "repositorios" => array());
		}

		//Retorna uma lista simples com os ids dos programadores
		return $programadores;
	}


	//Buscar quantidade de downloads do repositório
	public function buscarQuantidadeDownloads($nome_repositorio, $usuario_repositorio)
	{

		//Buscar todos os programadores do repositório
		$res 								= null;
		$command 							= "/repos/".$usuario_repositorio."/".$nome_repositorio."/downloads";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar a quantidade de downloads do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		return count($res);
	}	


	//Buscar informações básicas do repositório
	public function buscarInformacoesBasicas($nome_repositorio, $usuario_repositorio)
	{

		//Buscar todos os programadores do repositório
		$res 								= null;
		$command 							= "/repos/".$usuario_repositorio."/".$nome_repositorio;

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar as informações básicas do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}
		
		if(isset($res['parent']))
		{
			if(isset($res['subscribers_count']))
				$res['parent']['subscribers_count'] = $res['subscribers_count'];
			else 
				$res['parent']['subscribers_count'] = 0;
			$res['parent']['dono']				= $res['parent']['owner']['login'];
			$res['parent']['qtda_downloads']    = $this->buscarQuantidadeDownloads($nome_repositorio, $res['parent']['dono']);
			return $res['parent'];
		}
		else
		{
			$res['dono']						= $res['owner']['login'];
			$res['qtda_downloads']    			= $this->buscarQuantidadeDownloads($nome_repositorio, $usuario_repositorio);
			return $res;
		}

	}




	//Buscar comentários do repositório
	public function buscarComentarios($nome_repositorio, $usuario_repositorio, $id_usuario)
	{

		$contador_usuario 	= 0;
		$contador_reacoes 	= 0;
		$command  			= "/repos/".$usuario_repositorio."/".$nome_repositorio."/comments";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar os comentários do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		//Para cada comentário, grava um array com o ID_COMENTARIO e o ID dos usuários envolvidos ( do autor e de quem reagiu )
		foreach ($res as $key => $value) {

			//Adiciona ao contador se o autor do comentário for o usuário em questão
			if(isset($res[$key]['user']['id']))
			{
				if ($res[$key]['user']['id'] == $id_usuario)
					$contador_usuario++;				
			}

/*
			//Busca as reações do comentário
			$command = "/repos/".$usuario_repositorio."/".$nome_repositorio."/comments/".$res[$key]['id']."/reactions";

			//Chamada API GitHub
			$reacoes = $this->api->apiCall($command, 'GET', null);
			if (!$reacoes && $this->api->errors) {
				echo "Erro ao buscar as reações dos comentários de commits<br>";
			    print_r($this->api->errors);
			    die;
			}

			//Para cada reação do comentário, verifica se é do id procurado
			foreach ($reacoes as $chave => $valor) {
				if($reacoes[$chave]['user']['id'] == $id_usuario)
					$contador_usuario++;
			}

			//Adiciona ao contador de reações a qtda de reações de um determinado comentário
			$contador_reacoes = $contador_reacoes + count($reacoes); */
		} 

		//Retorna o total de comentários e o total de comentários do usuário
		return array("total" => (count($res) + $contador_reacoes), "usuario" => $contador_usuario);
	}




	//Buscar problemas do repositório
	public function buscarProblemas($nome_repositorio, $usuario_repositorio, $id_usuario)
	{

		$contador_usuario 	= 0;
		$contador_geral 	= 0;
		$command  			= "/repos/".$usuario_repositorio."/".$nome_repositorio."/issues";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar os problemas do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		//Inclui a quantidade total de problemas
		$contador_geral = $contador_geral + count($res);

		if (count($res) > 0)
		{

		//Para cada problema
		foreach ($res as $key => $value) {

			//Adiciona ao contador se o autor do problema for o usuário em questão
			if(isset($res[$key]['user']['id']))
			{
				if ($res[$key]['user']['id'] == $id_usuario)
					$contador_usuario++;				
			}

			if(isset($res[$key]['number']))
			{
				//Busca os comentários dos problemas
				$command = "/repos/".$usuario_repositorio."/".$nome_repositorio."/issues/".$res[$key]['number']."/comments";

				//Chamada API GitHub
				$comentarios = $this->api->apiCall($command, 'GET', null);
				if (!$comentarios && $this->api->errors) {
					echo "Erro ao buscar os comentarios dos problemas<br>";
				    print_r($this->api->errors);
				    die;
				}

				//Inclui a quantidade total de comentários dos problemas
				$contador_geral = $contador_geral + count($comentarios);

				if(count($comentarios) > 0)
				{

					//Para cada comentário do problema
					foreach ($comentarios as $key2 => $value2) {

						//Adiciona ao contador se o autor do comentario for o usuário em questão
						if(isset($comentarios[$key2]['user']['id']))
						{
							if ($comentarios[$key2]['user']['id'] == $id_usuario)
								$contador_usuario++;				
						}
					
		/*
						//Busca as reações do comentário
						$command = "/repos/".$usuario_repositorio."/".$nome_repositorio."/issues/comments/".$comentarios[$key2]['id']."/reactions";

						//Chamada API GitHub
						$reacoes = $this->api->apiCall($command, 'GET', null);
						if (!$reacoes && $this->api->errors) {
							echo "Erro ao buscar as reações dos comentários do problema<br>";
						    print_r($this->api->errors);
						    die;
						}

						//Inclui a quantidade total de reações dos comentários dos problemas
						$contador_geral = $contador_geral + count($reacoes);

						//Para cada reação de comentário
						foreach ($reacoes as $key3 => $value3) {

							//Adiciona ao contador se o autor do comentario for o usuário em questão
							if ($reacoes[$key3]['user']['id'] == $id_usuario)
								$contador_usuario++;									
						}
						*/
					}

				}

			}
		} 
		}

		//Retorna o total de comentários e o total de comentários do usuário
		return array("total" => $contador_geral, "usuario" => $contador_usuario);
	}




	//Buscar commits do repositório
	public function buscarCommits($nome_repositorio, $usuario_repositorio, $id_usuario)
	{

		$contador_usuario 	= 0;
		$command  			= "/repos/".$usuario_repositorio."/".$nome_repositorio."/commits";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar os commits do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		//Para cada commit
		foreach ($res as $key => $value) {

			//Confere somente para os casos em que há autor no commit
			if(isset($res[$key]['author']['id']))
			{
				if($res[$key]['author']['id'] == $id_usuario)
					$contador_usuario++;
			}
		} 

		//Retorna o total de comentários e o total de comentários do usuário
		return array("total" => count($res), "usuario" => $contador_usuario);
	}



	//Buscar pull requests do repositório
	public function buscarPullRequests($nome_repositorio, $usuario_repositorio)
	{

		$command  			= "/repos/".$usuario_repositorio."/".$nome_repositorio."/pulls";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar a quantidade total de pull requests do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		//Retorna o total de pull requests do repositório
		return count($res);
	}



	//Buscar programadores do repositório
	public function buscarProgramadores($nome_repositorio, $usuario_repositorio)
	{

		$command  			= "/repos/".$usuario_repositorio."/".$nome_repositorio."/contributors";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar a quantidade total de programadores do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		//Retorna o total de pull requests do repositório
		return count($res);
	}



	// Buscar as informações de todos os repositórios e as interações com os programadores
	public function completarDadosRepositorios($dados)
	{
		//Para cada programador
		foreach ($dados as $key1 => $value1) {
			
			//Para cada repositorio do programador
			foreach ($dados[$key1]['repositorios'] as $key2 => $value2) {

				//Buscar informações básicas do repositório
				$repositorio = $this->buscarInformacoesBasicas($dados[$key1]['repositorios'][$key2]['nome'], $dados[$key1]['login']);				

				//Monta as informações de retornadas no array de saída				
				$dados[$key1]['repositorios'][$key2]['qtda_estrelas'] 		= $repositorio['stargazers_count'];
				$dados[$key1]['repositorios'][$key2]['qtda_inscritos']		= $repositorio['subscribers_count'];
				$dados[$key1]['repositorios'][$key2]['qtda_downloads'] 		= $repositorio['qtda_downloads'];
				$dados[$key1]['repositorios'][$key2]['qtda_visualizacoes'] 	= $repositorio['watchers_count'];
				$dados[$key1]['repositorios'][$key2]['qtda_forks'] 			= $repositorio['forks_count'];
				$dados[$key1]['repositorios'][$key2]['user_dono'] 			= $repositorio['dono'];

				if(isset($repositorio['has_wiki']))
					$dados[$key1]['repositorios'][$key2]['contem_wikis']	= 'S';
				else 
					$dados[$key1]['repositorios'][$key2]['contem_wikis']	= 'N';

				//Calcula a diferença entre a data atual e a data de início do repositório
	    		$data_inicio 	= new DateTime();
	    		$data_fim 		= new DateTime($repositorio['created_at']);
	    		$dateInterval 	= $data_inicio->diff($data_fim);
				$dados[$key1]['repositorios'][$key2]['dias_criacao'] 		= $dateInterval->days;

				//Buscar estatísticas de comentarios do repositório (realizados pelo usuario e total)
				//$comentarios = $this->buscarComentarios($dados[$key1]['repositorios'][$key2]['nome'], $dados[$key1]['login'], $dados[$key1]['id']);
				$comentarios = $this->buscarComentarios($dados[$key1]['repositorios'][$key2]['nome'], $repositorio['dono'], $dados[$key1]['id']);
				$dados[$key1]['repositorios'][$key2]['qtda_comentarios'] 			= $comentarios['total'];
				$dados[$key1]['repositorios'][$key2]['qtda_comentarios_usuario'] 	= $comentarios['usuario'];

				//Buscar estatísticas de problemas do repositório (detectados pelo usuario e total)
				//$problemas   = $this->buscarProblemas($dados[$key1]['repositorios'][$key2]['nome'], $dados[$key1]['login'], $dados[$key1]['id']);
				$problemas   = $this->buscarProblemas($dados[$key1]['repositorios'][$key2]['nome'], $repositorio['dono'], $dados[$key1]['id']);
				$dados[$key1]['repositorios'][$key2]['qtda_problemas'] 				= $problemas['total'];
				$dados[$key1]['repositorios'][$key2]['qtda_problemas_usuario'] 		= $problemas['usuario'];				

				//Buscar estatísticas de commits do repositório (submetidos pelo usuario e total)
				//$commits    = $this->buscarCommits($dados[$key1]['repositorios'][$key2]['nome'], $dados[$key1]['login'], $dados[$key1]['id']);
				$commits    = $this->buscarCommits($dados[$key1]['repositorios'][$key2]['nome'], $repositorio['dono'], $dados[$key1]['id']);
				$dados[$key1]['repositorios'][$key2]['qtda_commits'] 				= $commits['total'];
				$dados[$key1]['repositorios'][$key2]['qtda_commits_usuario'] 		= $commits['usuario'];

				//Buscar pull requests do repositório
				//$dados[$key1]['repositorios'][$key2]['qtda_p_requests'] 	= $this->buscarPullRequests($dados[$key1]['repositorios'][$key2]['nome'], $dados[$key1]['login']);
				$dados[$key1]['repositorios'][$key2]['qtda_p_requests'] 	= $this->buscarPullRequests($dados[$key1]['repositorios'][$key2]['nome'], $repositorio['dono']);

				//Buscar programadores do repositório
				//$dados[$key1]['repositorios'][$key2]['qtda_programadores'] 	= $this->buscarProgramadores($dados[$key1]['repositorios'][$key2]['nome'], $dados[$key1]['login']);
				$dados[$key1]['repositorios'][$key2]['qtda_programadores'] 	= $this->buscarProgramadores($dados[$key1]['repositorios'][$key2]['nome'], $repositorio['dono']);
			}
		}

		//Retorna o vetor incrementado de dados
		return $dados;
	}



	public function obterQtdaDownloads($nome, $usuario)
	{

		$command  			= "/repos/".$usuario."/".$nome."/downloads";

		//Chamada API GitHub
		$res = $this->api->apiCall($command, 'GET', null);
		if (!$res && $this->api->errors) {
			echo "Erro ao buscar a quantidade total de programadores do repositório<br>";
		    print_r($this->api->errors);
		    die;
		}

		//Retorna o total de pull requests do repositório
		return count($res);

	}






	//Buscar pull requests do repositório
	public function buscarInformacoesRepositorio($usuario)
	{

		$command = "/users/".$usuario."/repos";

		//Chamada API GitHub
		$dados = $this->api->apiCall($command, 'GET', null, false, 1000);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(count($dados) == 0)
			return 0;

		$saida = array();

		foreach ($dados as $key => $value) {

				$item 				= array();

				$item['id'] 				= $dados[$key]["id"];
				$item['nome'] 				= $dados[$key]["name"];

				if(isset($dados[$key]["owner"]["login"]))
					$item['user_dono'] 			= $dados[$key]["owner"]["login"];
				else
					$item['user_dono'] 			= $usuario;

				$item['qtda_estrelas'] 		= $dados[$key]["stargazers_count"];
				$item['qtda_forks'] 		= $dados[$key]["forks_count"];

				$saida[] 			= $item;
		}

		return $saida;
	}



	//Buscar pull requests do repositório
	public function buscarQtdaInscritos($nome, $usuario)
	{

		$command = "/repos/".$usuario."/".$nome;

		//Chamada API GitHub
		$dados = $this->api->apiCall($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['subscribers_count']))
			return $dados['subscribers_count'];
		else
			return 0;
	}


	//Buscar qtda dias de criação
	public function buscarQtdaDias($nome, $usuario)
	{

		$command = "/repos/".$usuario."/".$nome;

		//Chamada API GitHub
		$dados = $this->api->apiCall($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['created_at']))
		{
			//Calcula a diferença entre a data atual e a data de início do repositório
    		$data_inicio 	= new DateTime();
    		$data_fim 		= new DateTime($dados['created_at']);
    		$dateInterval 	= $data_inicio->diff($data_fim);
			return $dateInterval->days;			
		}
		else
			return 0;
	}	

}