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

// Função principal
function maiorMedia($ano, $periodo){

	@$conexao = mysqli_connect(host, usuario, senha, nomeBanco);

    if (!$conexao){
    	ExibeErro("Não foi possível conectar ao BD.");
    }
    //tratamento de caracteres acentuados dentro do banco de dados
    mysqli_query($conexao,"SET NAMES 'utf8'");
	mysqli_query($conexao,'SET character_set_connection=utf8');
	mysqli_query($conexao,'SET character_set_client=utf8');
	mysqli_query($conexao,'SET character_set_results=utf8');

    // VERIFICA A MATRÍCULA COM A MAIOR NOTA_FINAL, IDENTIFICANDO QUAL É O ALUNO E A DISCIPLINA
    $consulta = "SELECT M.ID_MATRICULA, A.NOME, D.TITULO, M.NOTA_FINAL FROM MATRICULA M, ALUNO A, TURMA T, DISCIPLINA D ";
    $consulta .= "WHERE M.ID_ALUNO = A.ID_ALUNO AND M.ID_TURMA = T.ID_TURMA AND T.ID_DISCIPLINA = D.ID_DISCIPLINA AND ";
    $consulta .= "T.ANO = '" . $ano . "' AND T.PERIODO = '" . $periodo ."' AND A.CR > 80 ";
    $consulta .= "ORDER BY NOTA_FINAL DESC, ID_MATRICULA DESC LIMIT 1 ";

	$res = mysqli_query($conexao, $consulta);
	$linhas = intval(mysqli_num_rows($res));

	if ($linhas > 0 ) {
		$result = mysqli_fetch_array($res);
		$nome = $result["NOME"];
		$nota = $result["NOTA_FINAL"];
		$id = $result["ID_MATRICULA"];
		$disc = $result["TITULO"];

        //EXIBE UMA MENSAGEM COM O NOME, DISCIPLINA E NOTA MAIOR
		echo "<script>alert('Aluno ". $nome . " na disciplina ". $disc." com nota final igual a ".number_format($nota, 2, ',', ' ')." ');</script>";		
		return $id;

	} else {
		echo "<script>alert('Não foram encontradas matrículas referente ao ano e período informados!');</script>";	
	}
	mysqli_close($conexao);
}

//Testes
    echo "Matrícula com maior Nota Final = " . maiorMedia("2017","1") . "<br/>"

?>

</body>
</html>
