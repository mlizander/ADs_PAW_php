<?php
   
   Function cifraTexto($texto){
       $trocaLetras = array ("A"=>"N","B"=>"O","C"=>"P","D"=>"Q","E"=>"R","F"=>"S","G"=>"T","H"=>"U",
        "I"=>"V","J"=>"W","K"=>"X","L"=>"Y","M"=>"Z","N"=>"A","O"=>"B","P"=>"C","Q"=>"D","R"=>"E","S"=>"F",
        "T"=>"G","U"=>"H","V"=>"I","W"=>"J","X"=>"K","Y"=>"L","Z"=>"M");
       
       $textoCifrado = "";

       for($letra = 0; $letra < strlen($texto) ; $letra++){
       	  if (!empty($trocaLetras[strtoupper(substr($texto, $letra, 1))])) {
             $textoCifrado .=  $trocaLetras[strtoupper(substr($texto, $letra, 1))];
          } else {
             $textoCifrado .=  substr($texto, $letra, 1); 
          }
        }
        return $textoCifrado ; 
    } 

    echo "Rodando a funcao <br/>";
    $meuTexto = "Meu nome é Daniel";
    echo "Meu texto é: " . $meuTexto . "<br/>";
    echo "O Texto Convertido é: " . cifraTexto($meuTexto). "<br/>";
?>