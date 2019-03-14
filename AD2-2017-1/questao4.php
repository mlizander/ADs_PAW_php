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

function RemoveMatricula($idMatricSai, $nomeSai, $conexao){
	//DELETA O REGISTRO DE MATRÍCULA	
	$consulta = "DELETE FROM MATRICULA WHERE ID_MATRICULA = " . $idMatricSai;
	if (!(@mysqli_query($conexao, $consulta))) {
	   	echo "<script>alert('Houve um erro e a matrícula de ". $nomeSai . " não foi deletada!');</script>";
	   	ExibeErro("Houve um erro e a matrícula de ". $nomeSai . " não foi deletada!");
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
		$coefrend = $result["CR"];

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
						//AQUI COMEÇA A DIFERENÇA ENTRE A QUESTÃO 1 E A QUESTÃO 2 DESSA AD. QUANDO NÃO HÁ VAGAS!
						//NÃO HÁ MAIS VAGAS. VERIFICAR ENTRE OS MATRICULADOS SE HÁ ALGUÉM COM CR MENOR
					    $consulta = "SELECT M.ID_MATRICULA, M.ID_TURMA, M.ID_ALUNO, A.CR, A.NOME  FROM MATRICULA M, ALUNO A, TURMA T  WHERE M.ID_ALUNO = A.ID_ALUNO AND M.ID_TURMA = T.ID_TURMA AND T.ID_DISCIPLINA = ".  $idDisc . " AND T.ANO = '". $ano."' AND T.PERIODO = '".$semestre."' AND A.CR < ". $coefrend . " ORDER BY A.CR";

						$res = mysqli_query($conexao,$consulta);
						$linhas = intval(mysqli_num_rows($res));
 						if ($linhas > 0) {
 							//HÁ PELO MENOS UM ALUNO COM CR MENOR QUE O DO ALUNO QUE ESTÁ TENTANDO MATRICULAR.
 							//MOVE ESSA MATRÍCULA DE MENOR CR PARA A LISTA DE ESPERA
							$result = mysqli_fetch_array($res);
							$idMatricSai = $result["ID_MATRICULA"];
							$idAlunoSai = $result["ID_ALUNO"];
							$nomeSai =  $result["NOME"];
							$idturma =  $result["ID_TURMA"];
 							mysqli_free_result($res);

 							InsereListaEspera($idAlunoSai, $nomeSai, $idDisc, $nomeDisciplina, $conexao);

 							//DELETA O REGISTRO DA MATRÍCULA DO ALUNO QUE FOI PARA A LISTA DE ESPERA
 							RemoveMatricula($idMatricSai, $nomeSai, $conexao);	

 							//MATRICULA O ALUNO DE CR MAIOR NESSA VAGA.
 							InsereMatricula($idAluno, $Nome, $idturma, $nomeDisciplina, $conexao);

 						} else {	
							// NÃO TEM ALUNO COM CR MENOR QUE O DO ALUNO QUE ESTÁ TENTANDO MATRICULAR. ENVIA ELE PARA A LISTA DE ESPERA	
							mysqli_free_result($res);
							InsereListaEspera($idAluno, $Nome, $idDisc, $nomeDisciplina, $conexao);
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
	mysqli_close($conexao);
}

function matriculaEmLote($arrayMatriculas) {

     foreach ($arrayMatriculas as $matricula) {
     	$id = $matricula[0];
     	$disc = $matricula[1];
     	MATRICULA_ALUNO($id, $disc);
     }

}

//Testes
    $meuArray = array(
    	array(1,"ÁLGEBRA LINEAR"),
    	array(3,"ÁLGEBRA LINEAR"),
    	array(5,"ÁLGEBRA LINEAR"),
    	array(7,"ÁLGEBRA LINEAR"),
    	array(9,"ÁLGEBRA LINEAR"),
    	array(11,"ÁLGEBRA LINEAR"),
    	array(13,"ÁLGEBRA LINEAR"),
    	array(15,"ÁLGEBRA LINEAR"),
    	array(17,"ÁLGEBRA LINEAR"),
    	array(19,"ÁLGEBRA LINEAR"),
    	array(2,"INGLÊS INSTRUMENTAL"),
    	array(4,"INGLÊS INSTRUMENTAL"),
    	array(6,"INGLÊS INSTRUMENTAL"),
    	array(8,"INGLÊS INSTRUMENTAL"),
    	array(10,"INGLÊS INSTRUMENTAL"),
    	array(12,"INGLÊS INSTRUMENTAL"),
    	array(14,"INGLÊS INSTRUMENTAL"),
    	array(16,"INGLÊS INSTRUMENTAL"),
    	array(18,"INGLÊS INSTRUMENTAL"),
    	array(20,"INGLÊS INSTRUMENTAL"),
    	);
	matriculaEmLote($meuArray);

?>

</body>
</html>
