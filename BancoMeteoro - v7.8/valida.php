<?php
    $usuario=$_REQUEST["usuario"];//pega o usuário e a senha inserida no formulário
    $senha=$_REQUEST["senha"];
    $f=fopen("assets/bd.csv","r");//abre o arquivo com usuários e senha em modo de leitura
	$confirmacao=0;//variavel de controle é zerada.
    $dados=fgetcsv($f);//pega os dados do arquivo.
    while($dados){//enquanto existir dados
        if((($dados[0]==$usuario)&&(sha1("$usuario-$senha")==$dados[1]))){//vai procurar se o usuário e a senha existem no arquivo
            $confirmacao=1;
            break;
        }
        $dados=fgetcsv($f);//próximo dado no arquivo
    }
    fclose($f);//fecha o arquivo
    $f=fopen("assets/ab.csv","r");//abre o arquivo de senhas de gerente e faz o mesmo processo que o anterior
    $dados=fgetcsv($f);
    while($dados){
        if($dados[0]==sha1($senha)){
            $confirmacao=1;
            break;
        }
        $dados=fgetcsv($f);
    }
    fclose($f);
    if($confirmacao){//se o login for válido
        session_start();//inicie a sessão
        $_SESSION["temp"]=time();//comece o temporizador de login
        $_SESSION["conf"]=$confirmacao;//confirme que é válido o login
        header("location:enviar.php");//e leve para a página desejada
    }
    else{//senão
        session_start();//inicie a sessão
        $_SESSION["cont"]=1;
        header("location:login.php");//e retorne para login.php
    }
?>