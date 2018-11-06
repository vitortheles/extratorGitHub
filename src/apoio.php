<?php


class apoio {


	//buscaOcorrenciaDeItemNoVetor
	public function buscaOcorrenciaDeItemNoVetor($vetor, $item)
	{
		$count = 0;

		foreach ($vetor as $key => $value) {
			if ($value == $item)
				$count++;
		}

		return $count;
	}

	//definePorcentagemCommits
	public function definePorcentagemCommits($listaAutoresCommits, $idProgramador)
	{
		$count = $this->buscaOcorrenciaDeItemNoVetor($listaAutoresCommits, $idProgramador);
		return ((($count*100)/(count($listaAutoresCommits)))/100);
	}

	//definePorcentagemInteracao
	public function definePorcentagemInteracao($itens, $idProgramador)
	{
		$countInteracao 		= 0;
		$countInteracaoTotal 	= 0;

		foreach ($itens as $key => $value) {
			$countInteracao 		= $countInteracao + $this->buscaOcorrenciaDeItemNoVetor($itens[$key]['ids_envolvidos'], $idProgramador);
			$countInteracaoTotal 	= $countInteracaoTotal + count($itens[$key]['ids_envolvidos']);
		}

		if ($countInteracaoTotal != 0)
			return (($countInteracao*100.0)/$countInteracaoTotal)/100.0;
		else 
			return 0;
	}


}
