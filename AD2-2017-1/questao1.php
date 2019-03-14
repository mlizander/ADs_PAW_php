<!Doctype html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
header('Content-Type: text/html; charset=utf-8');
	define("host", "localhost");
	define("usuario", "root");
	define("senha", "");
    define("nomeBanco", "ad2_web");


//Funções Auxiliares
function ExibeErro($msg) {
    echo $msg."<br>";
    echo "Erro nº: " . mysqli_connect_errno() . "<br>";
    echo "Descrição: " . mysqli_connect_error(). "<br>";
    exit;
}

function InsereMatricula($idAluno, $Nome, $idturma, $nomeDisciplina, $conexao){
	//OBTÉM O NÚMERO DE MATRÍCULA, BASEADO NO ÚLTIMO NÚMERO USADO
	$consulta = "SELECT MAX(ID_MATRICULA) AS ID FROM MATRICULA ";
	$res = mysqli_query($conexao,$consulta);
	$result = mysqli_fetch_array($res);	
	if (is_null($result["ID"])){
		$numMatricula = 1;	
	} else {
		$numMatricula = $result["ID"] + 1;	
	}
	mysqli_free_result($res);

	//ADICIONA A MATRÍCULA	
	$consulta = "INSERT INTO MATRICULA (ID_MATRICULA, ID_TURMA, ID_ALUNO, NOTA1, NOTA2, NOTA_FINAL) ";
	$consulta .= "VALUES (".$numMatricula.",".$idturma.",".$idAluno.",-1,-1,-1)";
	//P.S.: PARA NÃO DEIXAR NULL NAS NOTAS (EMBORA O CAMPO SEJA NULLABLE, INSIRO -1, COMO INDICAÇÃO DE QUE ESTÁ SEM NOTA, POIS ZERO É NOTA.
	if (!(@mysqli_query($conexao, $consulta))) {
	   	echo "<script>alert('Houve um erro e a matrícula de ". $Nome . " não foi efetuada!');</script>";
	   	ExibeErro("Houve um erro e a matrícula de ". $Nome . " não foi efetuada!");
	} else {
		echo "<script>alert('Matrícula de ". $Nome . " na disciplina " . $nomeDisciplina . " foi efetuada com sucesso!');</script>";
	}
}

function InsereListaEspera($idAluno, $Nome, $idDisc, $nomeDisciplina, $conexao){
    //OBTÉM O NÚMERO DE ID DA LISTA, BASEADO NO ÚLTIMO NÚMERO USADO
	$consulta = "SELECT MAX(ID_ESPERA) AS ID FROM LISTA_ESPERA ";
	$res = mysqli_query($conexao,$consulta);
	$linhas = intval(mysqli_num_rows($res));
	$result = mysqli_fetch_array($res);	
	if (is_null($result["ID"])){
		$numMatricula = 1;	
	} else {
		$numMatricula = $result["ID"] + 1;	
	}
	mysqli_free_result($res);

	//ADICIONA NA LISTA	
	$consulta = "INSERT INTO LISTA_ESPERA (ID_ESPERA, ID_DISCIPLINA, ID_ALUNO) ";
	$consulta .= "VALUES (".$numMatricula.",".$idDisc.",".$idAluno.")";
	if (@mysqli_query($conexao,$consulta)) {
	   	echo "<script>alert('".$Nome. " foi enviado para a Lista de Espera para a disciplina ". $nomeDisciplina."!');</script>";
	} else {
	   	echo "<script>alert('Houve um erro e a matrícula de ". $Nome . " não foi efetuada!');</script>";
	   	ExibeErro("Houve um erro e a matrícula de ". $Nome . " não foi efetuada!");
	}
}

// Função principal
function MATRICULA_ALUNO($idAluno, $nomeDisciplina){

    $ano = date("Y");
    if (intval(date("n")) < 7 ) {
     $semestre = "1";
    } else {
    	$semestre = "2";
    }

	//CONECTANDO AO BANCO DE DADOS
	@$conexao = mysqli_connect(host, usuario, senha, nomeBanco);

    if (!$conexao){
    	ExibeErro("Não foi possível conectar ao BD.");
    }
    //tratamento de caracteres acentuados dentro do banco de dados
    mysqli_query($conexao,"SET NAMES 'utf8'");
	mysqli_query($conexao,'SET character_set_connection=utf8');
	mysqli_query($conexao,'SET character_set_client=utf8');
	mysqli_query($conexao,'SET character_set_results=utf8');


    // VERIFICA SE O ID DO ALUNO EXISTE, EVITANDO IDS INEXISTENTES
    $consulta = "SELECT * FROM ALUNO ";
    $consulta .= "WHERE ID_ALUNO = ".$idAluno;

	$res = mysqli_query($conexao, $consulta);
	$linhas = intval(mysqli_num_rows($res));

	if ($linhas > 0 ) {
		$result = mysqli_fetch_array($res);
		$Nome = $result["NOME"];

		mysqli_free_result($res);

	    //COLETAR O CÓDIGO DA DISCIPLINA, EVITANDO NOMES DE DISCIPLINAS QUE NÃO EXISTEM NO BANCO
    	$consulta = "SELECT * FROM DISCIPLINA ";
    	$consulta .= "WHERE TITULO = '".$nomeDisciplina."' ";
    	$consulta .= "LIMIT 1 ";

		$res = mysqli_query($conexao,$consulta);
		$linhas = intval(mysqli_num_rows($res));
		if ($linhas > 0 ) {
			$result = mysqli_fetch_array($res);
			$idDisc = $result["ID_DISCIPLINA"];
			mysqli_free_result($res);

    		//VERIFICAR SE O ALUNO JÁ ESTÁ MATRICULADO NA DISCIPLINA NESTE SEMESTRE
    		$consulta = "SELECT M.ID_ALUNO FROM MATRICULA M, TURMA T ";
    		$consulta .= "WHERE M.ID_TURMA = T.ID_TURMA AND ";
			$consulta .= "M.ID_ALUNO = ". $idAluno." AND T.ID_DISCIPLINA = ".$idDisc." AND ";
			$consulta .= "T.ANO = '". $ano."' AND T.PERIODO = '".$semestre."' ";

			$res = mysqli_query($conexao, $consulta);
			$linhas = intval(mysqli_num_rows($res));
			mysqli_free_result($res);

			if ($linhas > 0 ) {
				echo "<script>alert('Aluno ". $Nome . " já estava matriculado na disciplina ". $nomeDisciplina."!');</script>";		
			} else { 	
	    		//VERIFICAR SE O ALUNO JÁ ESTÁ NA LISTA DE ESPERA
    			$consulta = "SELECT ID_ALUNO FROM LISTA_ESPERA ";
    			$consulta .= "WHERE ID_ALUNO = ". $idAluno." AND ID_DISCIPLINA = ".$idDisc;

				$res = mysqli_query($conexao,$consulta);
				$linhas = intval(mysqli_num_rows($res));
				mysqli_free_result($res);
				if ($linhas > 0) {
					echo "<script>alert('Aluno ". $Nome . " já estava na lista de espera para a disciplina ". $nomeDisciplina."!');</script>";			
				} else {
					//VERIFICAR SE HÁ VAGAS EM PELO MENOS UMA TURMA DA DISCIPLINA, PEGANDO A PRIMEIRA DA LISTA
				    $consulta = "SELECT * FROM TURMA T  WHERE T.ID_DISCIPLINA = ". $idDisc . " AND T.ANO = '". $ano."' AND T.PERIODO = '".$semestre."'  AND T.NUMERO_DE_VAGAS > (SELECT COUNT(*) FROM MATRICULA M WHERE M.ID_TURMA = T.ID_TURMA) ";

					$res = mysqli_query($conexao, $consulta);
					$linhas = intval(mysqli_num_rows($res));
					if ($linhas > 0) {
						//TEM PELO MENOS UMA TURMA COM VAGAS. MATRICULAR ELE NESSA TURMA
						$result = mysqli_fetch_array($res);
						$idturma = $result["ID_TURMA"];
						mysqli_free_result($res);

						InsereMatricula($idAluno, $Nome, $idturma, $nomeDisciplina, $conexao);

					} else {
						//NÃO HÁ MAIS VAGAS. ENVIAR PARA LISTA DE ESPERA
						mysqli_free_result($res);
						InsereListaEspera($idAluno, $Nome, $idDisc, $nomeDisciplina, $conexao);
					}	
				}
			}
		} else {
			echo "<script>alert('Disciplina de nome ". $nomeDisciplina . " não existe no Banco de Dados!');</script>";		
		}	
	} else {
		echo "<script>alert('Aluno de ID ". $idAluno . " não existe no Banco de Dados!');</script>";	
	}
	mysqli_close($conexao);
}

//Testes
	$disc = "INTRODUÇÃO À INFORMÁTICA";
	$aluno = 6;
	MATRICULA_ALUNO($aluno, $disc);

?>

</body>
</html>
