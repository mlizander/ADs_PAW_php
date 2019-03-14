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
       $linhas =  [];
       $colunas = [];

       //Preenchendo Sequencias Horizontais
       for($li = 0; $li < count($tabuleiro) ; $li++){
       	  for ($col = 0; $col < count($tabuleiro[$li]); $col++){
       	  	 $linhas[] =  $tabuleiro[$li][$col];
       	  }
       	} 	

       //Preenchendo Sequencias Verticais
       for($col = 0; $col < count($tabuleiro[0]) ; $col++){
       	  for ($li = 0; $li < count($tabuleiro) ;	 $li++){
       	  	 $colunas[] = $tabuleiro[$li][$col];
       	  }
       	} 	


       //Fazendo a verificação Horizontal
       for($li = 0; $li < count($linhas); $li++){
           $total = 0; 
           $ind = $li;
       	   $seq = "";
       	   while ($total < $soma and $ind < count($linhas)){
                $total += $linhas[$ind]; 

                if ($seq<>""){
                  	$seq .=",";
                }
                $seq .= $linhas[$ind];
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

	   //Fazendo a verificação Vertical
       for($col = 0; $col < count($colunas); $col++){
           $total = 0; 
           $ind = $col;
       	   $seq = "";
       	   while ($total < $soma and $ind < count($colunas)){
                $total += $colunas[$ind]; 
                if ($seq<>""){
                  	$seq .=",";
                }
                $seq .= $colunas[$ind];
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
       	
       	return "Horizontal: ". $horizontal . "<br/>". "Vertical: ".$vertical;
    } 

    echo "Rodando a funcao <br/>";
    echo acheSequencia($vetor,9);
?>