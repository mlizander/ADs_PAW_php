<?php
   $vetor = array(array(1,3,6,7,2,5),
   	              array(4,4,6,2,2,4),
   	              array(2,3,5,1,1,5),
   	              array(5,3,5,1,5,7),
   	              array(6,2,4,2,1,3),
   	              array(3,2,3,1,7,4)
   	              );
   //echo count($vetor) . "<br>";
   //echo count($vetor[0]);

   Function acheSequencia($tabuleiro,$soma){
       $horizontal = "";
       $vertical = "";

       //Fazendo a verificação Horizontal
       for($li = 0; $li < count($tabuleiro) ; $li++){
       	  for ($col = 0; $col < count($tabuleiro[$li]); $col++){
          	  $total = 0; 
        	  $ind = $col;
          	  $seq = "";
       	  	  while ($total < $soma and $ind < count($tabuleiro[$li])){
                  $total += $tabuleiro[$li][$ind]; 
                  if ($seq<>""){
                  	$seq .=",";
                  }
                  $seq .= $tabuleiro[$li][$ind];
                  $ind++;
       	  	  }
       	  	  if ($total == $soma) {
       	  	  	//Verifica se já existe a sequencia e caso não exista, adiciona 
       	  	  	if (strpos($horizontal, $seq) === false) {
       	  	  		if ($horizontal <> "") {
       	  	  			$horizontal .= " - ";
       	  	  		}
       	  	  		$horizontal .= $seq;
       	  	  	}

       	  	  }

            } 
       	}
        //O código abaixo funcionará apenas para entradas onde o número de itens do array mais interno
        //é igual em cada linha. Ou seja cada linha do array é um array de "n" posições

	   //Fazendo a verificação Vertical
       for($col = 0; $col < count($tabuleiro[0]) ; $col++){
       	  for ($li = 0; $li < count($tabuleiro) ;	 $li++){
          	  $total = 0; 
        	  $ind = $li;
          	  $seq = "";
       	  	  while ($total < $soma and $ind < count($tabuleiro)) {
               	$total += $tabuleiro[$ind][$col]; 
               	if ($seq<>""){
               		$seq .=",";
               	}
               	$seq .= $tabuleiro[$ind][$col];
                $ind++;
       	  	  }
       	  	  if ($total == $soma) {
       	  	  	//Verifica se já existe a sequencia e caso não exista, adiciona 
       	  	  	if (strpos($vertical, $seq) === false) {
       	  	  		if ($vertical <> "") {
       	  	  			$vertical .= " - ";
       	  	  		}
       	  	  		$vertical .= $seq;
       	  	  	}

       	  	  }

            } 
       	}
       	
       	return "Horizontal: ". $horizontal . "<br/>". "Vertical: ".$vertical;
    } 

    echo "Rodando a funcao <br/>";
    echo acheSequencia($vetor,9);
?>