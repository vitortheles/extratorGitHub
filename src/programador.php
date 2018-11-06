<?php


class programador {


	public $api;

	function __construct($api)
	{
		$this->api = $api;	
	}

	//Listar todos os repositórios de cada programador - agregação na lista de programadores
	//A partir de uma lista de ids / logins
	public function listarRepositoriosPorProgramador($programadores)
	{

		//Para cada programador da lista
		foreach ($programadores as $key1 => $value) {
			
			$command 						= "/users/".$programadores[$key1]["login"]."/repos";

			//Chamada API GitHub
			if($programadores[$key1]["qtda"] > 100)
				$repositorios = $this->api->apiCall($command, 'GET', null, false, $programadores[$key1]["qtda"]);	
			else 
				$repositorios = $this->api->apiCall($command, 'GET', null);

			if (!$repositorios && $this->api->errors) {
				echo "Erro ao buscar os repositórios do programador<br>";
			    print_r($this->api->errors);
			    die;
			}

			//Para cada programador, armazena o id e o nome do repositório que este é colaborador
			foreach ($repositorios as $key2 => $value) {
				$programadores[$key1]["repositorios"][] = array("id" => $repositorios[$key2]["id"], "nome" => $repositorios[$key2]["name"], "usuario_dono" => $repositorios[$key2]["owner"]['login'] , "node_id" => $repositorios[$key2]["node_id"]);
			}
		}

		return $programadores;
	}

	//Acrescenta novos dados básicos na lista de programadores
	//A partir de uma lista de ids / logins
	public function completarDadosProgramadores($programadores)
	{

		//Para cada programador da lista
		foreach ($programadores as $key1 => $value) {
			
			$command 						= "/users/".$programadores[$key1]["login"];

			//Chamada API GitHub
			$dados = $this->api->apiCall($command, 'GET', null);
			if (!$dados && $this->api->errors) {
				echo "Erro ao buscar os dados básicos do programador<br>";
			    print_r($this->api->errors);
			    die;
			}

			$programadores[$key1]["qtda_seguidores"] 	= $dados["followers"];
			$programadores[$key1]["qtda_seguindo"] 		= $dados["following"];
			$programadores[$key1]["qtda_rep_publicos"] 	= $dados["public_repos"];
			$programadores[$key1]["qtda_gists_publicos"]= $dados["public_gists"];

			//Calcula a diferença entre a data atual e a data de início de atividade no github
    		$data_inicio 	= new DateTime();
    		$data_fim 		= new DateTime($dados["created_at"]);
    		$dateInterval 	= $data_inicio->diff($data_fim);
			$programadores[$key1]["dias_criacao"] 		= $dateInterval->days;

		}

		return $programadores;
	}	

	function buscarQuantidadeTotalCommits($login)
	{

		$command = "/search/commits?q=committer:".$login;

		//Chamada API GitHub
		$dados = $this->api->apiCallSearch($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['total_count']))
			return $dados['total_count'];
		else
			return 0;
	}

	function buscarQuantidadeTotalIssues($login)
	{

		$command = "/search/issues?q=type:issue+author:".$login;

		//Chamada API GitHub
		$dados = $this->api->apiCallSearch($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['total_count']))
			return $dados['total_count'];
		else
			return 0;
	}	

	function buscarQuantidadeTotalPR($login)
	{

		$command = "/search/issues?q=type:pr+author:".$login;

		//Chamada API GitHub
		$dados = $this->api->apiCallSearch($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['total_count']))
			return $dados['total_count'];
		else
			return 0;
	}		


	function buscarCommitsProgramadorPorRepositorio($login, $nome)
	{

		$command = "/search/commits?q=committer:".$login."+repo:".$login."/".$nome;
		//$command = "/search/commits?q=committer:".$login;

		//Chamada API GitHub
		$dados = $this->api->apiCallSearch($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['total_count']))
			return $dados['total_count'];
		else
			return 0;
	}		


	function buscarIssuesProgramadorPorRepositorio($login, $nome)
	{

		$command = "/search/issues?q=type:issue+author:".$login."+repo:".$login."/".$nome;

		//Chamada API GitHub
		$dados = $this->api->apiCallSearch($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['total_count']))
			return $dados['total_count'];
		else
			return 0;
	}		


	function buscarPRProgramadorPorRepositorio($login, $nome)
	{

		$command = "/search/issues?q=type:pr+author:".$login."+repo:".$login."/".$nome;

		//Chamada API GitHub
		$dados = $this->api->apiCallSearch($command, 'GET', null);
		if (!$dados && $this->api->errors) {
			echo "Erro ao buscar os dados básicos do programador<br>";
		    print_r($this->api->errors);
		    die;
		}

		if(isset($dados['total_count']))
			return $dados['total_count'];
		else
			return 0;
	}		




}