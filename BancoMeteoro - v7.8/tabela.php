<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Tabela</title>
        <link rel="icon" href="assets/logo.png"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            if(array_key_exists("sql",$_SESSION))//se a variavel utilizada não existir termine o codigo
                $sql=$_SESSION["sql"];
            else
                die();
            $sql .="  ORDER BY DataHora DESC";//dados em ordem descrescente
            $host = "localhost";
            $usuario = "root";
            $senha = "";
            $banco = "estacaometeoro";
            $c = mysqli_connect($host,$usuario,$senha,$banco);
            if(!$c)
            {
                echo "<p class='aviso' style='text-align:left; padding-left:10px; padding-top:5px;'>Ocorreu um erro de conexao: ";
                echo mysql_error();
                echo "</p>";
                die();
            }
            $resp = mysqli_query($c, $sql);

            $data1 = $_SESSION["data1"];//pegando os dados passados em consulta.php
            $data2 = $_SESSION["data2"];
            echo "<header style='margin: 0; padding-top:5px; margin-bottm: 5px;'>Consulta de $data1 a $data2</header>";//criando o cabeçalho
            echo "<p style='text-align:center; padding-top:5px;'><a href='index.php' >Consultar novamente.</a></p>";
            if(!$resp)
            {
                $er = mysqli_errno($c);
                echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Houve um erro de número $er, durante a consulta.</p>";
                mysqli_close($c);
                die();
            }
            $linha = mysqli_fetch_assoc($resp);
            $TA = $_SESSION["TA"];
            $RH = $_SESSION["RH"];
            $DP = $_SESSION["DP"];
            $HI = $_SESSION["HI"];
            $PA = $_SESSION["PA"];
            $SR = $_SESSION["SR"];
            $PR1HS = $_SESSION["PR1HS"];
            $WS1HA = $_SESSION["WS1HA"];
            $WD1HA = $_SESSION["WD1HA"];
            $TA_X = $_SESSION["TA_X"];
            $TA_m = $_SESSION["TA_m"];
            $WS_X = $_SESSION["WS_X"];
            $DC = $_SESSION["DC"];
            $est = $_SESSION["est"];
            $inter = $_SESSION["inter"];//pegando os dados passados em consulta.php
            //baseados nos dados preencha a tabela
            echo "<table id='tabela'>";
            echo "<tr>";
            echo "<th>Data</th>";
            if($inter != 7 && $inter != 10) echo "<th>Hora Local</th>";
            if($TA != "") echo "<th>Temp. do Ar <br>(° C)</th>";
            if($RH != "") echo "<th>Umidade Relativa <br>(%)</th>";
            if($DP != "") echo "<th>Temp. do Pto. de Orvalho <br>(° C)</th>"; 
            if($HI != "") echo "<th>Índice de Calor <br>(° C)</th>";
            if($PA != "") echo "<th>Pressão Atmosférica <br>(hPA)</th>";
            if($SR != "") echo "<th>Radiação Solar Global <br>(W/m²)</th>";
            if($PR1HS != "") echo "<th>Chuva<br>(mm)</th>";
            if($WS1HA != "") echo "<th>Vel. do Vento <br>(m/s)</th>";
            if($WD1HA != "") echo "<th>Direção do Vento (°)</th>";
            if($DC != "") echo "<th>Bateria <br>(volts)</th>";
            if($TA_X != "") echo "<th>Temp. Max.<br>(° C)</th>";
            if($TA_m != "") echo "<th>Temp. Min.<br>(° C)</th>";
            if($WS_X != "") echo "<th>Vel. Max.<br>(m/s)</th>";
            if($est == 3) echo "<th>Estação</th>";
            while($linha){
                echo "<tr>";
                if($inter != 7 && $inter != 10){//se a hora for irrelevante
                    $datahora=explode("T",$linha["DataHora"]);
                    echo "<td>$datahora[0]</td>";
                    echo "<td>$datahora[1]</td>";
                }
                else{
                    echo "<td>{$linha["tempo"]}</td>";
                }
                if($TA != ""){
                    $linha["TA"]=round($linha["TA"],1);
                    echo "<td>".$linha["TA"]."</td>";
                }
                if($RH != ""){
                    $linha["RH"]=round($linha["RH"],0);
                    echo "<td>".$linha["RH"];
                };
                if($DP != ""){
                    $linha["DP"]=round($linha["DP"],1);
                    echo "<td>".$linha["DP"]."</td>";
                }
                if($HI != ""){
                    $linha["HI"]=round($linha["HI"],1);
                    echo "<td>".$linha["HI"]."</td>";
                }
                if($PA != "") {
                    $linha["PA"]=round($linha["PA"],1);
                    echo "<td>".$linha["PA"]."</td>";
                }
                if($SR != ""){
                    $linha["SR"]=round($linha["SR"],0);
                    echo "<td>".$linha["SR"]."</td>";
                }
                if($PR1HS != ""){ 
                    $linha["PR1HS"]=round($linha["PR1HS"],1);
                    echo "<td>".$linha["PR1HS"]."</td>";
                }
                if($WS1HA != ""){
                    $linha["WS1HA"]=round($linha["WS1HA"],1);
                    echo "<td>".$linha["WS1HA"]."</td>";
                }
                if($WD1HA != ""){
                    $linha["WD1HA"]=round($linha["WD1HA"],0);
                    echo "<td>".$linha["WD1HA"];
                };
                if($DC != ""){
                    $linha["DC"]=round($linha["DC"],2);
                    echo "<td>".$linha["DC"]."</td>";
                }
                if($TA_X != ""){ 
                    $linha["TA_X"]=round($linha["TA_X"],1);
                    echo "<td>".$linha["TA_X"]."</td>";
                }
                if($TA_m != ""){ 
                    $linha["TA_m"]=round($linha["TA_m"],1);
                    echo "<td>".$linha["TA_m"]."</td>";
                }
                if($WS_X != ""){ 
                    $linha["WS_X"]=round($linha["WS_X"],1);
                    echo "<td>".$linha["WS_X"]."</td>";
                }
                if($est == 3)echo "<td>{$linha["codigo"]}</td>";
                    echo "</tr>";
                $linha = mysqli_fetch_assoc($resp);
            }
            echo "</table>";
            echo "<br>";
        ?>
    </body>
</html>