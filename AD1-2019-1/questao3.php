<?php
	class Estoque {
		private $produtos;

		function __construct(){
			$this->produtos = array();
		}

		function adicionaProduto(Produto $p, $qtd){
			$temItem = false;
			for ($i=0; $i < count($this->produtos); $i++) { 
				if ($this->produtos[$i][0] == $p){
					$this->produtos[$i][1] += $qtd;
					$temItem = true;
				}
			}
			if (!$temItem){
				$this->produtos[] = array($p, $qtd);
			}
		}

		function removeProduto(Produto $p, $qtd){
			for ($i=0; $i < count($this->produtos); $i++) { 
				if ($this->produtos[$i][0] == $p){
					if ($this->proutos[$i][1] >= $qtd){
						$this->proutos[$i][1] -= $qtd;
						return true;
					}
				}
			}
			return false;
		}

		function getEstoque(){
			return $this->produtos;
		}
	}

	class Produto{
		private $descricao;
		private $tipos = array("bebida",
							   "alimento perecível",
							   "alimento não perecível",
							   "produto de limpeza" );
		private $codTipo;
		private $prazoValidade;
		private $valorUnit;

		function __construct($descr, $codTipo, $prazo, $valor){
			$this->descricao = $descr;
			$this->prazoValidade = $prazo;
			$this->valorUnit = $valor;
			if (($codTipo >= 0) && ($codTipo <= 3)){
				$this->codTipo = $codTipo;
			} else {
				$this->codTipo = 0;
			}
		}

		function setValorUnit($valor){
			$this->valorUnit = $valor;
		}
	}


	//Testes... não faz parte da AD
	$p1 = new Produto("Cachaça", 0, 1000, 15);
	$p2 = new Produto("Frango", 1, 7, 10);
	$p3 = new Produto("Arroz 5Kg", 2, 730, 13);
	$p4 = new Produto("Desinfetante", 3, 365, 5);

	$e = new Estoque();
	$e->adicionaProduto($p1, 5);
	$e->adicionaProduto($p2, 20);
	$e->adicionaProduto($p3, 7);
	$e->adicionaProduto($p4, 40);
	$e->adicionaProduto($p2, 15);

	$r = $e->getEstoque();

	print_r($r);


?>