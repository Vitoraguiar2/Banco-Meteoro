<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre
    if(array_key_exists("conf",$_SESSION))//se o login é valido matenha o no código se não volte para o login
        $conf=$_SESSION["conf"];
    else{
        $conf=0;
        session_unset();
        header("location:login.php");
        die();
    }
    $session_life = time() - $_SESSION["temp"];
    if(!$conf || ($session_life > 600)){//se demorar tempo demais feche a sessão e volte para o login
        session_unset();
        header("location:login.php");
        die();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Envio para o Banco</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
        <link rel="icon" href="assets/logo.png"/>
    </head>
    <body>
        <div id="body">
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
                        <a class="nav-link active" href="enviar.php">Envio de dados ao banco</a>
                    </div>
                    <div class="navbar-nav">
                        <a class="nav-link" href="login.php">Voltar para tela de login</a>
                    </div>
                </div>
            </nav>
            <br>
            <p><b class="destaque">Insira o arquivo de texto</b> (Tamanho máximo de 8Mb):</p>
            <p><b class="destaque">Padrão dos dados:</b> Data e Hora, DC, DP, HI, PA, PR1HS, RH, SR, TA, WD1HA, WS1HA.</p>
            <form method="POST" enctype="multipart/form-data" action="enviar.php"><!--dados enviados para si mesmo-->
                <p><b class="destaque">Selecione a estação de origem dos dados.</b></p>
                <div class='esq'>
                    <label>Estação 1.</label>
                    <input type="radio" name="estacao" value="1">
                </div>
                <div class='esq'>
                    <label>Estação 2.</label>
                    <input type="radio" name="estacao" value="2">
                </div>
                <br>
                <input type="hidden" name="MAX_FILE_SIZE" value="8000000"><!--determina tamanho do arquivo-->
                <input class="botao" id="file" type="file" name="userfile" accept=".txt">
                <br>
                <br>
                <input class="botao" type="submit">
            </form>
            <div style="padding-left:0;"><?php if(isset($_FILES['userfile']))echo Enviar();?></div><!--se exisitir dados enviados execute o php-->
        </div>
    </body>
    <footer id="footer">Desenvolvedor: Estagiário Vitor Aguiar da Gama</footer>
    <script>
        var uploadField = document.getElementById("file");

        uploadField.onchange = function() {//se o tamanho do arquivo for grande demais avise
            if(this.files[0].size > 8000000){
            alert("Arquivo inválido, tamanho grande demais!");
            this.value = "";
            };
        };
    </script>
</html>
<?php
    function Enviar(){
        echo "<br>";
        if(isset($_FILES['userfile'])==false){//se exisitr um arquivo
            $_SESSION["temp"]=time();
            return;
        }
        if(isset($_POST['estacao'])==false){//se for selecionada uma estação
            $_SESSION["temp"]=time();
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Favor selecione uma estação.</p>";
            return;
        }
        $est=$_POST['estacao'];
        ini_set("memory_limit", "800M");//tentando mudar o php.ini para suportar o arquivo (não tende a funcionar)
        ini_set("max_execution_time", "360");//tentando mudar o php.ini para suportar o arquivo (não tende a funcionar)
        if($_FILES['userfile']['error']==4){//se nenhum arquivo for inserido
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Nenhum arquivo inserido.</p>";        
            $_SESSION["temp"]=time();
            return;
        }
        $TipoArq = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));//pega a extensão do arquivo
        if($TipoArq != 'txt'){//se ele não for um arquivo .txt reclame
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Arquivo de tipo ilegal, favor inserir um arquivo válido.";
            $_SESSION["temp"]=time();
            return;
        }
        if($_FILES['userfile']['error']==2){//arquivo de tamanho maior que o limite
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Tamanho de arquivo grande demais.</p>";
            $_SESSION["temp"]=time();
            return;
        }
        $host = "localhost";
        $usuario = "root";
        $senha = "";
        $banco = "estacaometeoro";
        $c = mysqli_connect($host,$usuario,$senha,$banco);
        if(!$c)//conectando ao banco
        {
            echo "Erro de conexao: ";
            echo mysql_error();
        }
        $er = 0;
        $count = 0;
        $tipoer = array();//preparando variáveis controle
        $arq=file_get_contents($_FILES['userfile']['tmp_name']);//passa o conteudo do arquivo para a variável
        if($arq == false){
            $_SESSION["temp"]=time();
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Houve um erro na leitura do arquivo.</p>";
            return;
        }
        $linha = explode("\n",$arq);//pega todas as linhas do arquivo
        $tam = sizeof($linha);//pega a quantidade de linhas do arquivo
        $l = explode("	", $linha[0]);//pega a primeira linha do arquivo
        //e testa se o arquivo é válido
        if(isset($l[1])==false || isset($l[2])==false || isset($l[3])==false || isset($l[4])==false || isset($l[5])==false || isset($l[6])==false || isset($l[7])==false || isset($l[8])==false || isset($l[9])==false || isset($l[10])==false ){
            $_SESSION["temp"]=time();
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Arquivo ilegal, insira um arquivo válido.</p>";
            return;
        }
        //e testa se o arquivo é válido
        if(isset($l[11])==true){
            $_SESSION["temp"]=time();
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Arquivo ilegal, insira um arquivo válido.</p>";
            return;
        }
        //e testa se o arquivo é válido
        if($l[1] != "DC" || $l[2] != "DP" || $l[3] != "HI" || $l[4] != "PA" || $l[5] != "PR1HS" || $l[6] != "RH" || $l[7] != "SR" || $l[8] != "TA" || $l[9] != "WD1HA"){
            $_SESSION["temp"]=time();
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Arquivo ilegal, insira um arquivo válido.</p>";
            return;
        }
        $sql = "INSERT IGNORE INTO info_esta VALUES"; //LOAD DATA INFILE 'LOG1_20210520.txt' IGNORE INTO TABLE info_esta; poderia talvez ser uma maneira melhor de fazer
        //IGNORE é importante pois ele ignora duplicatas
        $aux_sql = array();//caso haja necessidade de partir a consulta ao meio
        $aux_count = 0;
        $controle = strlen($sql);
        $count2 = 0;
        for($i=1; $i < $tam; $i++){//enquanto i for menor que o tamanho
            $l=explode("	",$linha[$i]);//separe os dados da linha
            //teste se a linha não está vazia
            if(isset($l[1])==true && isset($l[2])==true && isset($l[3])==true && isset($l[4])==true && isset($l[5])==true && isset($l[6])==true && isset($l[7])==true && isset($l[8])==true && isset($l[9])==true && isset($l[10])==true){
                for($k=1;$k<11;$k++){//para o tamanho da linha
                    if($l[$k]=='///'||$l[$k]=="///\r")//dados com /// se tornam nulos
                        $l[$k]='null';
                }
                if($count == 0)//se for o primeiro dado
                    $sql .="('$l[0]',$l[1],$l[2],$l[3],$l[4],$l[5],$l[6],$l[7],$l[8],$l[9],$l[10],$est)";
                else
                    $sql .=", ('$l[0]',$l[1],$l[2],$l[3],$l[4],$l[5],$l[6],$l[7],$l[8],$l[9],$l[10],$est)";
                $count2++;
                $count++;
                $controle = strlen($sql);
            }
            if($controle > 3900000){//se o $sql estiver grande demais separe guarde o em aux_sql e siga fazendo
                $aux_sql[$aux_count]=$sql;
                $sql = "INSERT IGNORE INTO info_esta VALUES";
                $count=0;
                $controle = strlen($sql);
                $aux_count++;
            }
        }
        $count3=0;//guardara os warnings (dados duplicados e dados nulos)
        if($aux_count != 0){//se for necessário ter várias consultas
            $aux_sql[$aux_count]=$sql;//insira o dados mais recente em aux_sql
            $tam=sizeof($aux_sql);
            for($i=0;$i<$tam;$i++){//e pelo tamanho de aux_sql
                if ($c->query($aux_sql[$i]) === FALSE){//faça o envio dos dados
                    $er=mysqli_errno($c);
                    echo "<p>".mysqli_error($c)."</p>";//e avise dos erros
                }
                $count3+=$c->warning_count;
            }
        }
        else{//senão
            if ($c->query($sql) === FALSE){//faça o envio dos dados
                $er=mysqli_errno($c);
                echo "<p>".mysqli_error($c)."</p>";//e avise dos erros
            }
            $count3=$c->warning_count;
        }
        $nomeArq = $_FILES['userfile']['name'];
        if($er==0){//se não houver nenhum erro
            if($count3==0)//e não aconteceram dados repetidos
                echo "<p class = 'sucesso' style='padding-left:10px;'>Todos os dados inseridos corretamente do arquivo $nomeArq.</p>";
            else{
                if($count3==$count2)//se todos os dados forem repetidos
                    echo "<p class = 'sucesso' style='padding-left:10px; text-align:left;'>Nenhuma linha inserida, o arquivo já existe no sistema.</p>";
                else
                    echo "<p class = 'sucesso' style='padding-left:10px; text-align:left;'>Certas linhas não foram inseridas, pois já existiam no sistema.</p>";
            }
        }
        else{
            echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Houve um erro número: $er durante a inserção de dados, por favor tente novamente mais tarde.</p>";
        }
    }
?>