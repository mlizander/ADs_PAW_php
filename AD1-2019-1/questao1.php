<?php
	function somaClientes($vetorClientes, $vetorValores){
		$somaCompras = array();
		$maxVet = count($vetorClientes);
		for ($i=0; $i < $maxVet ; $i++) { 
			if (array_key_exists($vetorClientes[$i], $somaCompras)){
				$somaCompras[$vetorClientes[$i]] += $vetorValores[$i];
			} else {
				$somaCompras[$vetorClientes[$i]] = $vetorValores[$i];
			}
		}
		ksort($somaCompras);
		return $somaCompras;
	}
	//Testes
	$arrayCli = array(100, 325, 36, 22, 1477, 100, 36);
	$arrayVal = array( 50, 110, 35, 20, 15,    70, 15);
	$resultado = somaClientes($arrayCli, $arrayVal);
	print_r($resultado);
?>