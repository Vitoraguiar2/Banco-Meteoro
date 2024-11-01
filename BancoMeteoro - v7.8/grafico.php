<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre
    if((array_key_exists("sql",$_SESSION))==false)//se a variavel utilizada não existir feche o codigo
        exit;
    $sql=$_SESSION["sql"];
    $grafico=$_SESSION["grafico"];
    $est=$_SESSION["est"];
    if($grafico==0){//se não for pedido um gráfico, envie o erro e feche o codigo
        echo json_encode("erro");
        die;
    }
    if($sql=="erro"){//se for invalido o dado vindo, envie o erro e feche o codigo
        echo json_encode($sql);
        die;
    }
    $host = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "estacaometeoro";
    $c = mysqli_connect($host,$usuario,$senha,$banco);//conectando ao banco de dados
    if(!$c)
	{
		echo "Erro de conexao: ";
        echo mysql_error();
		die();
	}
    if($est!=3)
        $sql .="  ORDER BY DataHora ASC";//ordem crescente de dados
    else
        $sql .=" ORDER BY codigo ASC, DataHora";
    $resp = mysqli_query($c, $sql);
    if(!$resp)//se não houver resposta do banco
	{
        $er = mysqli_errno($c);
		echo "<p>Houve um erro de número $er, durante a consulta.</p>";
		mysqli_close($c);
		die();
	}
    $linha = mysqli_fetch_assoc($resp);//pegas os dados
    while($linha){//enquanto existir dados
        echo json_encode($linha,JSON_HEX_QUOT);//transforme em dados capazes de javascript compreender
        $linha = mysqli_fetch_assoc($resp);//próximo dado
    }
?>