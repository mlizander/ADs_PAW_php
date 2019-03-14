<!Doctype html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
header('Content-Type: text/html; charset=utf-8');


function matricula_Aluno($idAluno, $nomeDisciplina){
	define("host", "localhost");
	define("usuario", "root");
	define("senha", "");
    define("nomeBanco", "ad2_web");

    $ano = date("Y");
    if (intval(date("n")) < 7 ) {
     $semestre = "1";
    } else {
    	$semestre = "2";
    }

    if (!(@$conexao = mysql_connect(host, usuario, senha))){
    	die("Não foi possível conectar ao BD");
    }
    mysql_select_db(nomeBanco, $conexao);
    //tratamento de caracteres acentuados dentro do banco de dados
	mysql_query("SET NAMES 'utf8'");
	mysql_query('SET character_set_connection=utf8');
	mysql_query('SET character_set_client=utf8');
	mysql_query('SET character_set_results=utf8');


    // VERIFICA SE O ID DO ALUNO EXISTE, EVITANDO IDS INEXISTENTES
    $consulta = "SELECT * FROM ALUNO ";
    $consulta .= "WHERE ID_ALUNO = ".$idAluno;

	$res = mysql_query($consulta,$conexao);
	$linhas = intval(mysql_num_rows($res));

	if ($linhas > 0 ) {
		$result = mysql_fetch_array($res);
		$Nome = $result["NOME"];
		$coefrend = $result["CR"];

	    //COLETAR O CÓDIGO DA DISCIPLINA, EVITANDO NOMES DE DISCIPLINAS QUE NÃO EXISTEM NO BANCO
    	$consulta = "SELECT * FROM DISCIPLINA ";
    	$consulta .= "WHERE TITULO = '".$nomeDisciplina."' ";
    	$consulta .= "LIMIT 1 ";

		$res = mysql_query($consulta,$conexao);
		$linhas = intval(mysql_num_rows($res));
		if ($linhas > 0 ) {
			$result = mysql_fetch_array($res);
			$idDisc = $result["ID_DISCIPLINA"];;

    		//VERIFICAR SE O ALUNO JÁ ESTÁ MATRICULADO NA DISCIPLINA NESTE SEMESTRE
    		$consulta = "SELECT M.ID_ALUNO FROM MATRICULA M, TURMA T ";
    		$consulta .= "WHERE M.ID_TURMA = T.ID_TURMA AND ";
			$consulta .= "M.ID_ALUNO = ". $idAluno." AND T.ID_DISCIPLINA = ".$idDisc." AND ";
			$consulta .= "T.ANO = '". $ano."' AND T.PERIODO = '".$semestre."' ";

			$res = mysql_query($consulta, $conexao);
			$linhas = intval(mysql_num_rows($res));

			if ($linhas > 0 ) {
				echo "<script>alert('Aluno ". $Nome . " já estava matriculado na disciplina ". $nomeDisciplina."!');</script>";		
			} else { 	
	    		//VERIFICAR SE O ALUNO JÁ ESTÁ NA LISTA DE ESPERA
    			$consulta = "SELECT ID_ALUNO FROM LISTA_ESPERA ";
    			$consulta .= "WHERE ID_ALUNO = ". $idAluno." AND ID_DISCIPLINA = ".$idDisc;

				$res = mysql_query($consulta,$conexao);
				$linhas = intval(mysql_num_rows($res));
				if ($linhas > 0) {
					echo "<script>alert('Aluno ". $Nome . " já estava na lista de espera para a disciplina ". $nomeDisciplina."!');</script>";			
				} else {
					//VERIFICAR SE HÁ VAGAS EM PELO MENOS UMA TURMA DA DISCIPLINA, PEGANDO A DE MAIOR QUANTIDADE DE VAGAS
				    $consulta = "SELECT * FROM TURMA T  WHERE T.ID_DISCIPLINA = ". $idDisc . " AND T.ANO = '". $ano."' AND T.PERIODO = '".$semestre."'  AND T.NUMERO_DE_VAGAS > (SELECT COUNT(*) FROM MATRICULA M WHERE M.ID_TURMA = T.ID_TURMA) ";

					$res = mysql_query($consulta,$conexao);
					$linhas = intval(mysql_num_rows($res));
					if ($linhas > 0) {
						//TEM PELO MENOS UMA TURMA COM VAGAS. MATRICULAR ELE NESSA TURMA
						$result = mysql_fetch_array($res);
						$idturma = $result["ID_TURMA"];


                        //OBTÉM O NÚMERO DE MATRÍCULA, BASEADO NO ÚLTIMO NÚMERO USADO
			    		$consulta = "SELECT MAX(ID_MATRICULA) AS ID FROM MATRICULA ";
						$res = mysql_query($consulta,$conexao);
						$result = mysql_fetch_array($res);	
						if (is_null($result["ID"])){
							$numMatricula = 1;	
						} else {
							$numMatricula = $result["ID"] + 1;	
						}

						//ADICIONA A MATRÍCULA	
					    $consulta = "INSERT INTO MATRICULA (ID_MATRICULA, ID_TURMA, ID_ALUNO, NOTA1, NOTA2, NOTA_FINAL) ";
						$consulta .= "VALUES (".$numMatricula.",".$idturma.",".$idAluno.",-1,-1,-1)";
				        //P.S.: PARA NÃO DEIXAR NULL NAS NOTAS (EMBORA O CAMPO SEJA NULLABLE, INSIRO -1, COMO INDICAÇÃO DE QUE ESTÁ SEM NOTA, POIS ZERO É NOTA.
				        if (!(@mysql_query($consulta,$conexao))) {
				        	echo "<script>alert('Houve um erro e a matrícula de ". $Nome . " não foi efetuada!');</script>";
				        }
					} else {
						//NÃO HÁ MAIS VAGAS. VERIFICAR ENTRE OS MATRICULADOS SE HÁ ALGUÉM COM CR MENOR
					    $consulta = "SELECT M.ID_MATRICULA, M.ID_TURMA, M.ID_ALUNO, A.CR, A.NOME  FROM MATRICULA M, ALUNO A, TURMA T  WHERE M.ID_ALUNO = A.ID_ALUNO AND M.ID_TURMA = T.ID_TURMA AND T.ID_DISCIPLINA = ".  $idDisc . " AND T.ANO = '". $ano."' AND T.PERIODO = '".$semestre."' AND A.CR < ". $coefrend . " ORDER BY A.CR";

						$res = mysql_query($consulta,$conexao);
						$linhas = intval(mysql_num_rows($res));
 						if ($linhas > 0) {
 							//HÁ PELO MENOS UM ALUNO COM CR MENOR QUE O DO ALUNO QUE ESTÁ TENTANDO MATRICULAR.
 							//MOVE ESSA MATRÍCULA DE MENOR CR PARA A LISTA DE ESPERA, DELETA ESSE REGISTRO DA MATRÍCULA E MATRICULA O ALUNO NESSA VAGA.
							$result = mysql_fetch_array($res);
							$idMatricSai = $result["ID_MATRICULA"];
							$idAlunoSai = $result["ID_ALUNO"];
							$nomeSai =  $result["NOME"];
							$idturma =  $result["ID_TURMA"];

				    		$consulta = "SELECT MAX(ID_ESPERA) AS ID FROM LISTA_ESPERA ";
							$res = mysql_query($consulta,$conexao);
							$linhas = intval(mysql_num_rows($res));
							$result = mysql_fetch_array($res);	
							if (is_null($result["ID"])){
								$numMatricula = 1;	
							} else {
								$numMatricula = $result["ID"] + 1;	
							}

							//ADICIONA NA LISTA	
						    $consulta = "INSERT INTO LISTA_ESPERA (ID_ESPERA, ID_DISCIPLINA, ID_ALUNO) ";
							$consulta .= "VALUES (".$numMatricula.",".$idDisc.",".$idAlunoSai.")";
					        if (@mysql_query($consulta,$conexao)) {
				    	    	echo "<script>alert('".$nomeSai. " foi enviado para a Lista de Espera para a disciplina ". $nomeDisciplina."!');</script>";
				    	    	// REMOVE A MATRÍCULA DO ALUNO QUE FOI PARA A LISTA DE ESPERA.
							    $consulta = "DELETE FROM MATRICULA WHERE ID_MATRICULA = " . $idMatricSai;
						        if (@mysql_query($consulta,$conexao)) {

			                        //OBTÉM O NÚMERO DE MATRÍCULA, BASEADO NO ÚLTIMO NÚMERO USADO
						    		$consulta = "SELECT MAX(ID_MATRICULA) AS ID FROM MATRICULA ";
									$res = mysql_query($consulta,$conexao);
									$result = mysql_fetch_array($res);	
									if (is_null($result["ID"])){
										$numMatricula = 1;	
									} else {
										$numMatricula = $result["ID"] + 1;	
									}

									//ADICIONA A MATRÍCULA	
								    $consulta = "INSERT INTO MATRICULA (ID_MATRICULA, ID_TURMA, ID_ALUNO, NOTA1, NOTA2, NOTA_FINAL) ";
									$consulta .= "VALUES (".$numMatricula.",".$idturma.",".$idAluno.",-1,-1,-1)";
							        //P.S.: PARA NÃO DEIXAR NULL NAS NOTAS (EMBORA O CAMPO SEJA NULLABLE, INSIRO -1, COMO INDICAÇÃO DE QUE ESTÁ SEM NOTA, POIS 	ZERO É NOTA.
							        if (!(@mysql_query($consulta,$conexao))) {
							        	echo "<script>alert('Houve um erro e a matrícula de ". $Nome . " não foi efetuada!');</script>";
				        			}
						        }else{
					        		echo "<script>alert('Houve um erro ao remover a matrícula de ". $nomeSai . "!');</script>";
					        		echo "<script>alert('A matrícula de ". $Nome . " não foi concluída!');</script>";
						        }	
				        	} else {
				        		echo "<script>alert('Houve um erro ao mover a matrícula de ". $nomeSai . " para a Lista de Espera!');</script>";
				        		echo "<script>alert('A matrícula de ". $Nome . " não foi concluída!');</script>";
				        	}


 						} else {
 							// NÃO TEM ALUNO COM CR MENOR QUE O DO ALUNO QUE ESTÁ TENTANDO MATRICULAR. ENVIA ELE PARA A LISTA DE ESPERA	
                        	//OBTÉM O NÚMERO DE ID DA LISTA, BASEADO NO ÚLTIMO NÚMERO USADO
				    		$consulta = "SELECT MAX(ID_ESPERA) AS ID FROM LISTA_ESPERA ";
							$res = mysql_query($consulta,$conexao);
							$linhas = intval(mysql_num_rows($res));
							$result = mysql_fetch_array($res);	
							if (is_null($result["ID"])){
								$numMatricula = 1;	
							} else {
								$numMatricula = $result["ID"] + 1;	
							}

							//ADICIONA NA LISTA	
						    $consulta = "INSERT INTO LISTA_ESPERA (ID_ESPERA, ID_DISCIPLINA, ID_ALUNO) ";
							$consulta .= "VALUES (".$numMatricula.",".$idDisc.",".$idAluno.")";
					        if (@mysql_query($consulta,$conexao)) {
				    	    	echo "<script>alert('".$Nome. " foi enviado para a Lista de Espera para a disciplina ". $nomeDisciplina."!');</script>";
				        	} else {
				        		echo "<script>alert('Houve um erro e a matrícula de ". $Nome . " não foi efetuada!');</script>";
				        	}
				    	}
					}	
				}
			}
		} else {
			echo "<script>alert('Disciplina de nome ". $nomeDisciplina . " não existe no Banco de Dados!');</script>";		
		}	
	} else {
		echo "<script>alert('Aluno de ID ". $idAluno . " não existe no Banco de Dados!');</script>";	
	}
}

//Testes
	$disc = "PROJETO E DESENVOLVIMENTO DE ALGORITMOS";
	$aluno = 8;
	matricula_Aluno($aluno, $disc);

?>

</body>
</html>
