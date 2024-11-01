<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Consulta</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <link rel="icon" href="assets/logo.png"/>
  </head>
  <body > 
    <div id="body" >
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <h2 class="navbar-brand"><a href="http://meteoro.cefet-rj.br/" style="color: white;">CoMet - LAPA - Monitoramento Ambiental</a><br>Banco de dados - Estação Maracanã<br><span style="font-size: 16px;">Lat.: <b>-22,91°</b> Lon.: <b>-43,22°</b> Alt.: <b>32m</b></span></h2>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="home.php">Dados recentes</a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link active" href="index.php">Consulta ao banco de dados</a>
                </div>
            </div>
        </nav>
        
        <form action="consulta.php" method="POST"><!--enviará os dados do formulário para consulta.php-->
            <?php
                $host = "localhost";
                $usuario = "root";
                $senha = "";
                $banco = "estacaometeoro";
                $c = mysqli_connect($host,$usuario,$senha,$banco);//conecta a base de dados
                if($c != false)
                {
                    $sql="SELECT MAX(DataHora) as MAXI, MIN(DataHora) as MINI, codigo FROM info_esta WHERE DataHora != '' GROUP BY codigo;"; 
                    //pega os dados mais recentes e mais antigos do banco de dados para ambas as estações
                    $resp = mysqli_query($c, $sql);
                    if($resp != false)
                    {
                        $linha = mysqli_fetch_assoc($resp);
                        if($linha != null){//se existem existirem dados
                            if($linha["MAXI"]!=null && $linha["MINI"]!=null && $linha["codigo"]==1){//se for os dados da estação 1 e esses dados existem
                                $max1 = explode("T",$linha["MAXI"]);
                                $min1 = explode("T",$linha["MINI"]);
                            }
                            else{
                                $max1[0] = null;
                                $min1[0] = null;
                            }
                        }
                        else{
                            $max1[0] = null;
                            $min1[0] = null;
                        }
                        if($linha != null){//se existerem dados
                            if($linha["codigo"]==1)// e esses dados forem da estação 1
                                $linha = mysqli_fetch_assoc($resp);//próximo dados
                        }
                        if($linha != null){//se existerem dados na estação 2
                            if($linha["MAXI"]!=null && $linha["MINI"]!=null && $linha["codigo"] == 2){
                                $max2 = explode("T",$linha["MAXI"]);
                                $min2 = explode("T",$linha["MINI"]);
                            }
                            else{
                                $max2[0] = null;
                                $min2[0] = null;
                            }
                        }
                        else{
                            $max2[0] = null;
                            $min2[0] = null;
                        }
                        if($max1[0] > $max2[0])//pega o máximo maior
                            $max=$max1[0];
                        else
                            $max=$max2[0];
                        if($min1[0] < $min2[0] && $min1!=null)//pega o minimo menor não nulo
                            $min=$min1[0];
                        else{
                            if($min2 == null)
                                $min=$min1[0];
                            else
                                $min=$min2[0];
                        }
                        //criando o header e o javascript do header
                        echo "<header>Estação A201 <select name='est' id='est' 
                        onchange='var element = document.getElementById(`test`); 
                        var el = document.getElementById(`est`);";
                        if($max1[0] != null) //dados para estação 1
                            echo "if(el.value==1){ 
                                element.innerHTML=`  Dados desde $min1[0] a $max1[0]`;
                            }"; 
                        else
                            echo "if(el.value==1){ 
                                element.innerHTML=`  Nenhum dado na estação 1.`;
                            }";
                        if($max2[0] != null) //dados para estação 2
                            echo "if(el.value==2){
                                element.innerHTML=`  Dados desde $min2[0] a $max2[0]`;
                            }";
                        else
                            echo "if(el.value==2){
                                element.innerHTML=`  Nenhum dado na estação 2.`;
                            }";
                        if($max != null) //dados para ambas estações
                            echo "if(el.value==3){
                                element.innerHTML=`  Dados desde $min a $max`;
                            }'>";
                        else
                            echo "if(el.value==3){
                                element.innerHTML=`  Nenhum dado no banco.`;
                            }'>";
                        echo "<option value='1'>1</option><option value='2'>2</option><option value='3'>1 e 2</option></select>";
                        if($max1[0] != null) 
                            echo "<span id='test'>  Dados desde $min1[0] a $max1[0].</span></header>";//valor padrão
                        else
                            echo "<span id='test'>  Nenhum dado na estação 1.</span></header>";//valor padrão
                    }
                }
            ?>
            <div class='esq'>
                <label><b class="destaque">Intervalo:</b> Início</label>
                <input class="date" name="data1" type="date">
                <label>a </label>
                <input class="date" name="data2" type="date">
            </div>
            <br>
            <div class='esq'>
                <label><b class="destaque">Consultar via: </b></label>
                <input type="checkbox" name="tabela" value="1">
                <label><span style="color: #0a4db3;">Tabela</span></label>
                <span>| <input type="checkbox" name="grafico" value="1" class="grafico"> <label><span style="color: rgb(207, 89, 46)">Gráfico</span> (1 ou 2 variáveis)</label></span>
            </div>
            <br>
            <div class='esq'>
                <label for="intervalo"><b class="destaque">Dados médios e total:</b></label>
                <!--Mostra a mensagem relevante baseada no intervalo-->
                
                <select name="intervalo" id="intervalo" onchange="var element = document.getElementById('mensagem'); var el = document.getElementById('intervalo'); if(el.value==1){element.style.display=''; element.innerHTML=` - <b class='destaque'>Aviso:</b> Não é possível criar gráficos para esse intervalo.`; }if(el.value==600){element.style.display=''; element.innerHTML=` - <b class='destaque'>Aviso:</b> Apenas gráficos de até 4 dias para 1 estação ou 2 dias para 2 estações podem ser feitos.`; }if(el.value!=600 && el.value!=1){element.style.display='none'; }">
                    <?php
                    if(array_key_exists("inter",$_SESSION)){
                        $int=$_SESSION["inter"];
                        if($int==1)
                            echo'<option value="1" selected>1 minuto</option>';
                        else 
                            echo'<option value="1">1 minuto</option>';
                        if($int==600)
                            echo'<option value="600" selected>10 minutos</option>';
                        else 
                            echo'<option value="600">10 minutos</option>';
                        if($int==1800)
                            echo'<option value="1800" selected>30 minutos</option>';
                        else
                            echo'<option value="1800">30 minutos</option>';
                        if($int==3600)
                            echo'<option value="3600" selected>1 hora</option>';
                        else
                            echo'<option value="3600">1 hora</option>';
                        if($int==10)
                            echo'<option value="10" selected>24 horas</option>';
                        else
                            echo'<option value="10">24 horas</option>';
                        if($int==7)
                            echo'<option value="7" selected>Mensal</option>';
                        else
                            echo'<option value="7">Mensal</option>';
                    }
                    else
                        echo'<option value="1">1 minuto</option><option value="600">10 minutos</option><option value="1800">30 minutos</option><option value="3600">1 hora</option><option value="10">24 horas</option><option value="7">Mensal</option>';
                    ?>
                </select>
                <?php
                    if(array_key_exists("inter",$_SESSION)){//se a sessão já foi iniciada e passou por consulta.php mostre a mensagem relevante
                        $int=$_SESSION["inter"];
                        if($int==1)
                            echo "<span id='mensagem'> - <b class='destaque'>Aviso:</b> Não é possível criar gráficos para esse intervalo.</span>";
                        if($int==600)
                            echo "<span id='mensagem'> - <b class='destaque'>Aviso:</b> Apenas gráficos de até 4 dias para 1 estação ou 2 dias para 2 estações podem ser feitos.</span>";
                        if($int!= 1 && $int != 600)
                            echo "<span id='mensagem'></span>";
                    }
                    else{//senão mostre a mensagem padrão
                        echo "<span id='mensagem'> - <b class='destaque'>Aviso:</b> Não é possível criar gráficos para esse intervalo.</span>";
                    }
                ?>
            </div>
            <br>
            <p style="margin-bottom: 0px;"><b class="destaque">Selecionar Variáveis:</b> Marcar todas <input class="marca" type="checkbox" onclick="toggle(this)"></p>
            <div class='esq' style="width: 100%; float:left;">
                <div class="colu"  style="margin-left:0px">
                    <input class="marca" name="TA" type="checkbox" value="TA">
                    <label>Temperatura do ar</label>
                </div>
                <div class="colu">
                    <input class="marca" name="RH" type="checkbox" value="RH">
                    <label>Umidade relativa </label>
                </div>
                <div class="colu" style="width: 20%">
                    <input class="marca" name="DP" type="checkbox" value="DP">
                    <label>Temperatura do ponto de orvalho </label>
                </div>
            </div>
            <div class='esq' style="width: 100%; float:left;">
                <div class="colu" style="margin-left:0px">
                    <input class="marca" name="HI" type="checkbox" value="HI">
                    <label>Índice de calor </label>
                </div>
                <div class="colu">
                    <input class="marca" name="PA" type="checkbox" value="PA">
                    <label>Pressão atmosférica </label>
                </div>
                <div class="colu" style="width: 13%">
                    <input class="marca" name="SR" type="checkbox" value="SR">
                    <label>Radiação solar global </label>
                </div>
                <div class="colu">
                    <input class="marca" name="PR1HS" type="checkbox" value="PR1HS">
                    <label>Chuva</label>
                </div>
            </div>
            <div class='esq' style="width: 100%; float:left;">
                <div class="colu" style="margin-left:0px">
                    <input class="marca" name="WS1HA" type="checkbox" value="WS1HA">
                    <label>Velocidade do vento </label>
                </div>
                <div class="colu">
                    <input class="marca" name="WD1HA" type="checkbox" value="WD1HA">
                    <label>Direção do vento </label>	
                </div>
                <div class="colu"> 
                    <input class="marca" name="DC" type="checkbox" value="DC">
                    <label>Bateria</label>
                </div>
            </div>
            <div class='esq' style="width: 100%; float:left; margin-bottom:10px;">
                <div class="colu" style="margin-left:0px">
                    <input class="marca diff" name="TA_X" type="checkbox" value=", MAX(TA) as TA_X">
                    <label>Temperatura Máxima</label>
                </div>
                <div class="colu"> 
                    <input class="marca diff" name="TA_m" type="checkbox" value=", MIN(TA) as TA_m">
                    <label>Temperatura Mínima</label>
                </div>
                <div class="colu" style="width: 17%">
                    <input class="marca diff" name="WS_X" type="checkbox" value=", MAX(WS1HA) as WS_X" >
                    <label>Velocidade do vento Máxima</label>
                </div>
            </div>
            <br>
            <input class="botao" type="submit" value="Consultar" style="margin-top: 10px;"></input>
            <?php
                $files = scandir("arqs/");
                $now  = time();
            
                foreach ($files as $file) {//verifica os arquivos na pasta arqs e apaga aqueles que estão lá por mais de 1 hora
                    if (is_file("arqs/"."$file")) {
                        if ($now - filemtime("arqs/"."$file") >= 60*60*1) { // 1 hora
                            unlink("arqs/"."$file");
                        }
                    }
                }

                if(array_key_exists("data_er",$_SESSION))//se as datas não foram inseridas e não é a primeira entrada no site
                    $data_er=$_SESSION["data_er"];
                else
                    $data_er=0;
                if($data_er){
                    echo "<br><br>";
                    echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Favor inserir ambas as datas!</p>";//reclame
                }
                $_SESSION["data_er"]=0;//e e reinicie a variavel de erro
            ?>
            
        </form>
    </div>
    <footer id="footer">Desenvolvedor: Estagiário Vitor Aguiar da Gama</footer>
  </body>
</html>
<script>
    function toggle(source) {//procura todas a checkboxes com a class marca e as marca ou desmarca
        checkboxes = document.getElementsByClassName('marca');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<?php
    session_unset();//reinicia a sessão e suas variáveis
    session_destroy();
?>
