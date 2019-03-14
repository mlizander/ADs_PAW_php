<?php
	//letra a
	function isFormatoCPF($cpf){
		$regex1 = '/^([0-9]){3}\.([0-9]){3}\.([0-9]){3}-([0-9]){2}$/';
		$regex2 = '/^([0-9]){11}$/';
		if (preg_match($regex1, $cpf) || preg_match($regex2, $cpf)){
			return 1;
		} else {
			return 0;
		}
	}
	//letra b
	function somenteNumeros($cpf){
		$retorno = str_replace(".", "", $cpf);
		$retorno = str_replace("-", "", $retorno);
		return $retorno;
	}
	//letra c
	function calculaDV($numero){
		$peso = strlen($numero)+1;
		$soma = 0;
		$tamanho = strlen($numero);
		for ($i=0; $i < $tamanho; $i++) { 
			$soma += intval(substr($numero,$i,1))* $peso;
			$peso--;
		}
		$resto = $soma % 11;
		if ($resto < 2) {
			$dv = 0;
		} else {
			$dv = 11 - $resto;
		}
		return $dv;
	}
	function validaCPF($cpf){
		if (isFormatoCPF($cpf)==1) {
			$cpf = somenteNumeros($cpf);
			$corpoCPF = substr($cpf,0,9);
			$dvCPF = substr($cpf,9,2);

			$primDV = calculaDV($corpoCPF);
			$segDV = calculaDV($corpoCPF.$primDV);

			if ($dvCPF == ($primDV.$segDV)){
				echo "CPF Válido!";
			} else {
				echo "CPF Inválido!";
			}
		} else {
			echo "CPF com formato inválido!";
		}
	}

	//Testes
	$meuCPF = '';
	validaCPF($meuCPF);












?>