<?php
    $senha=$_REQUEST["entrada"];//pega a senha de gerente
    $f=fopen("assets/ab.csv","r"); //abre o arquivo de senhas de gerente
	$confirmacao=0;
    $dados=fgetcsv($f);
    while($dados){//lê os dados no arquivo e se a senha existir confirme
        if($dados[0]==sha1($senha)){
            $confirmacao=1;
            break;
        }
        $dados=fgetcsv($f);
    }
    fclose($f);
    if($confirmacao){//caso seja válido leve para gerencia.php
        session_start();
        $_SESSION["cond"]=$confirmacao;
        $_SESSION["tempo"]=time();
        header("location:gerencia.php");
    }
    else{//senão retorne para a área de login
        session_start();
        $_SESSION["con"]=1;
        header("location:check.php");
    }
?>