<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre
    if(array_key_exists("cond",$_SESSION))//se o usuário for liberados matenha no codigo se não volte para tela de login
        $conf=$_SESSION["cond"];
    else{
        $conf=0;
        session_unset();
        header("location:check.php");
        die();
    }
    $er=0;
    $session_life = time() - $_SESSION["tempo"];
    if(!$conf|| ($session_life > 600)){//se o usuário permaneça inativo por tempo de mais retorne-o para tela de login
        session_unset();
        header("location:check.php");
        die();
    }
?>
<!DOCTYPE html>
<html>
	<head>
        <title>Gerenciamento de Logins</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="style.css">
        <link rel="icon" href="assets/logo.png"/>
    </head>
    <body class="login">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <h2 class="navbar-brand">CoMet - LAPA - Monitoramento Ambiental<br>Banco de dados - Maracanã</h2>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="home.php">Dados recentes</a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link" href="index.php">Consulta ao banco de dados</a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link" href="enviar.php">Envio de dados ao banco</a>
                </div>
            </div>
        </nav>
        <form method="post" action="gerencia.php">
            <br>
            <div class="card">
                <div class="head">
                    <p class="cabecalho">Gerencie as permissões</p>
                </div>
                <label class="esquerda" id="usuariotext">Usuário</label>
                <input class="input" type="text" name="usuario" id="usuario">
                <label class="esquerda" id="senhatext">Senha</label>
                <input class="input" type="password" name="senha" id="senha">
                <div class='esq'>
                    <label>Adicionar permissão:</label>
                    <input type="radio" name="escolha" value="adicionar" onclick="var element = document.getElementById('senha'); element.style.display=''; element = document.getElementById('senhatext'); element.style.display=''; element = document.getElementById('usuario'); element.style.display=''; element = document.getElementById('usuariotext'); element.style.display='';">
                    <!--Mostrar as entradas de usuário e senha-->
                </div>
                <div class='esq'>
                    <label>Remover permissão:</label>
                    <input type="radio" name="escolha" value="remover" onclick="var element = document.getElementById('senha'); element.style.display='none'; element = document.getElementById('senhatext'); element.style.display='none'; element = document.getElementById('usuario'); element.style.display=''; element = document.getElementById('usuariotext'); element.style.display='';">
                    <!--Mostrar as entradas de usuário e esconder a de senha-->
                </div>
                <div class='esq'>    
                    <label>Adicionar gerente:</label>
                    <input type="radio" name="escolha" value="permissao" onclick="var element = document.getElementById('senha'); element.style.display=''; element = document.getElementById('senhatext'); element.style.display=''; element = document.getElementById('usuario'); element.style.display='none'; element = document.getElementById('usuariotext'); element.style.display='none';">
                    <!--Mostrar as entradas de senha e esconder a de usuário-->
                </div>
                <br>
                <div class='esq'>
                    <input type="submit" value="Enviar">
                </div>
                <a class="link direita" href="check.php">Voltar</a>
            </div>
        </form>
    </body>
</html>
<?php
    if(isset($_POST["escolha"])==false){//se nenhum opção tenha sido e escolhida e o usuário não tenha acabado de entrar na página
        $_SESSION["tempo"]=time();
        $er = 1;
        if($session_life > 1)
            echo "<p class='aviso'>*Escolha uma opção!*</p>";//avise que ele não escolheu nada
        $escolha ="";
    }
    else
        $escolha=$_POST["escolha"];//senão pegue a escolha dele
    if((isset($_POST["usuario"])==false || $_POST["usuario"] == null) && $escolha !="permissao"){//se a escolha precisa de usuário e não foi inserido
        $_SESSION["tempo"]=time();
        $er=1;
        if($session_life > 1)
            echo "<p class='aviso'>*Insira um usuario!*</p>";//avise
    }
    else
        $usuario=$_POST["usuario"];//senão pegue o usuário
    if((isset($_POST["senha"])==false || $_POST["senha"] == null) && $escolha !="remover"){//se a escolha precisa de uma senha e não foi inserida
        $_SESSION["tempo"]=time();
        $er=1;
        if($session_life > 1)
            echo "<p class='aviso'>*Insira uma senha!*</p>";//avise
    }
    else{//senão
        if($escolha !="remover"){//se a escolha não for remover um usuário
            $senha=$_POST["senha"];//pegue a senha inserida
            if(preg_match('/\s/',$senha)){//verifique se não tem espaço em branco na senha
                $er=1;
                echo "<p class='aviso'>*A senha não pode conter espaços em branco!*</p>";
            }
            if(strlen(utf8_decode($senha)) < 5){//verifique o tamanho da senha
                $er=1;
                echo "<p class='aviso'>*A senha não pode ser menor que 5 caracteres!*</p>";
            }
            if(strlen(utf8_decode($senha)) > 15){//verifique o tamanho da senha
                $er=1;
                echo "<p class='aviso'>*A senha não pode ser maior que 15 caracteres!*</p>";
            }
        }
    }
    if($escolha=="adicionar" && $er!=1){//se for escolhido adicionar um usuário e a senha for válida
        $f=fopen("assets/bd.csv","r+");//abra o arquivos de usuários e senhas para leitura e escrita
        $dados=fgetcsv($f);//pegue seus dados
        while($dados){//e enquanto existir dados
            if($dados[0]==$usuario){//procure por um usuário igual ao inseirdo
                $_SESSION["tempo"]=time();
                echo "<p class='aviso'>*Tal dado já existe no sistema!*</p>";
                $er=1;
                break;
            }
            $dados=fgetcsv($f);
        }
        if($er!=1){//se não exisitir ensirar o usuário e a senha no arquivo
            echo "<p class='sucesso'>Usuario inserido com sucesso!</p>";
            fputcsv($f,[$usuario, sha1("$usuario-$senha")]);//a senha é codificada por motivos de segurança
        }
        fclose($f);
    }
    if($escolha=="remover" && $er!=1){//se a escolha for remover
        $cont=0;
        $f=fopen("assets/bd.csv","r");//abra o arquivo para leitura
        $g=fopen("assets/bdtemp.csv","w");//e crie um arquivo temporário para escrita
        $dados=fgetcsv($f);
        while($dados){//enquanto exisitir dados
            if($dados[0]!=$usuario){//e o dado não for igual a usuário 
                fputcsv($g,[$dados[0],$dados[1]]);//copie eles para bdtemp
            }
            if($dados[0]==$usuario)
                $cont++;//se for igual marque
            $dados=fgetcsv($f);
        }
        fclose($f);
        fclose($g);
        if($cont==0){//se não existir tal usuário mantenha o arquivo original e apague o temporario
            unlink('assets/bdtemp.csv');
            $er=1;
            echo "<p class='aviso'>*Tal dado não existe no sistema!*</p>";
        }
        else{//se existir o usuário, apague o arquivo original e renomeie o temporário
            unlink("assets/bd.csv");
            rename('assets/bdtemp.csv','assets/bd.csv');
            echo "<p class='sucesso'>Usuario excluido com sucesso!</p>";
        }
    }
    if($escolha=="permissao" && $er!=1){//se for adicionar um gerente
        $f=fopen("assets/ab.csv","r+");//abra o arquivo para escrita e leitura
        $dados=fgetcsv($f);
        while($dados){
            if($dados[0]==sha1($senha)){//se a senha inserida for igual a uma existente
                $_SESSION["tempo"]=time();
                echo "<p class='aviso'>*Tal dado já existe no sistema!*</p>";//avise do erro e pare a ação
                $er=1;
                break;
            }
            $dados=fgetcsv($f);
        }
        if($er!=1){//senão insira a senha no arquivo
            echo "<p class='sucesso'>Gerente inserido com sucesso!</p>";
            fputcsv($f,[sha1("$senha")]);
        }
        fclose($f);
    }
    $_SESSION["tempo"]=time();
    $f=fopen("assets/bd.csv","r");//abre o arquivo para leitura
    echo "<br>";
    echo "<table class='lista'>";
    echo "<tr>";
    echo "<th style='font-size:medium;'>Usuários cadastrados no sistema</th>";
    $dados=fgetcsv($f);
    if($dados==false){
        echo "<tr>";
        echo "<td style='background-color:white;'></td>";
    }
    while($dados){//insere os dados de usuários numa tabela
        echo "<tr>";
        echo "<td style='background-color:white;'>$dados[0]</td>";
        $dados=fgetcsv($f);
    }
    echo "</table>";
    if($er==1){
        $_SESSION["tempo"]=time();
        die();
    }
?>