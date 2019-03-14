<?php
   $vetor = array("Minions", "Ben-Hur", "O Poderoso Chefão","Silêncio dos Inocentes", "Avatar");
   $vetor2 = array("Milhons","O Poderoso Melão", "Avocado");
      //echo count($vetor) . "<br>";
   //echo count($vetor[0]);

   Function ehLetra($char) {
      $espectro = "ABCDEFGHIJKLMNOPQRSTUVWXYZÇÑÀÁÂÃÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝabcdefghijklmnopqrstuvwxyzçñàáâãèéêëìíîïòóôõöùúûüýÿ";
      //$codigo = ord($str);
      if ( (strpos($espectro, $char) === false)  ) {
          return false;
      } else {
          return true;
      }
   }

   Function pontuacaoTitulo($nomeFilme, $nomeFake){
       $totalPontos = 0;
       if (strlen($nomeFilme) > strlen($nomeFake)) {
          $tamanhoMaximo = strlen($nomeFilme);
       } else {
          $tamanhoMaximo = strlen($nomeFake);
       }

       for ($letra = 0; $letra < $tamanhoMaximo; $letra++){
           if (ehLetra(substr($nomeFilme,$letra,1)) && ehLetra(substr($nomeFake,$letra,1))) {
              if (substr($nomeFilme,$letra,1) == substr($nomeFake,$letra,1)){
                  $totalPontos += 10;
              } else if (strtoupper(substr($nomeFilme,$letra,1)) == strtoupper(substr($nomeFake,$letra,1))){
                  $totalPontos += 5;
              } else {
                  $totalPontos += 2;
              }
           }
       }
       return $totalPontos;
       //round($totalPontos/$tamanhoMaximo,1);
   }

   Function comparacaoFilmes($nomesFilmes,$nomesFakes){
       $compara = [];

       //Montando o array de comparações
       for($filme = 0; $filme < count($nomesFilmes) ; $filme++){
       	  for ($fake = 0; $fake < count($nomesFakes); $fake++){
              array_push($compara,array($nomesFilmes[$filme],$nomesFakes[$fake],0));
            } 
       	}

	     //Percorre  o array de comparações, comparando todos os filmes com os fakes, atribuindo uma pontuação
       for($li = 0; $li < count($compara) ; $li++){
           $compara[$li][2] = pontuacaoTitulo($compara[$li][0], $compara[$li][1]);
       	}
       	
        //Compara os maiores, preenchendo um array com a classificação final
        $classificacao = array(array("","",0),array("","",0),array("","",0));
        for($li = 0; $li < count($compara) ; $li++){
           if ($compara[$li][2] >= $classificacao[0][2]) {
              $classificacao[2] = $classificacao[1];
              $classificacao[1] = $classificacao[0];
              $classificacao[0][0] = $compara[$li][0];
              $classificacao[0][1] = $compara[$li][1];
              $classificacao[0][2] = $compara[$li][2];
           } elseif ($compara[$li][2] >= $classificacao[1][2]) {
              $classificacao[2] = $classificacao[1];
              $classificacao[1][0] = $compara[$li][0];
              $classificacao[1][1] = $compara[$li][1];
              $classificacao[1][2] = $compara[$li][2];
           } elseif ($compara[$li][2] >= $classificacao[2][2]) {
              $classificacao[2][0] = $compara[$li][0];
              $classificacao[2][1] = $compara[$li][1];
              $classificacao[2][2] = $compara[$li][2];
           }
        }
        
        //print_r( $compara);

        $retorno = "";
        for($li = 0; $li < count($classificacao) ; $li++){
            $retorno .= ($li + 1) . "o. Lugar = ". $classificacao[$li][0] . " vs " . $classificacao[$li][1] . " => "  . $classificacao[$li][2] ." pontos <br/>" ;
         }

       	return $retorno;
    } 

    echo "Rodando a funcao <br/>";
    echo comparacaoFilmes($vetor,$vetor2);
?>