<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="30"/>
        <title>Home</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
        <link rel="icon" href="assets/logo.png"/>
    </head>
    <body> 
        <div id="body" >
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <h2 class="navbar-brand"><a href="http://meteoro.cefet-rj.br/" style="color: white;">CoMet - LAPA - Monitoramento Ambiental</a><br>Banco de dados - Estação Maracanã<br><span style="font-size: 16px;">Lat.: <b>-22,91°</b> Lon.: <b>-43,22°</b> Alt.: <b>32m</b></span></h2>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
					    <a class="nav-link active" href="home.php">Dados recentes</a>
                    </div>
                    <div class="navbar-nav">
                        <a class="nav-link" href="index.php">Consulta ao banco de dados</a>
                    </div>
                </div>
            </nav>
            <?php
                $host = "localhost";
                $usuario = "root";
                $senha = "";
                $banco = "estacaometeoro";
                $c = mysqli_connect($host,$usuario,$senha,$banco);
                if($c != false){
                    $sql="SELECT * FROM info_esta WHERE DataHora = (SELECT MAX(DataHora) as MAXI FROM info_esta WHERE codigo=1) AND codigo = 1";
                    $resp = mysqli_query($c, $sql);
                    if($resp!=false){
                        $linha = mysqli_fetch_assoc($resp);
                        $max = explode("T",$linha["DataHora"]);
                        echo "<header>Estação A201 1. Data mais recente: ".$max[0]." ".$max[1]."</header>";
                    }
                    else{
                        $linha=null;
                    }
                }
            ?>
            <div style="margin: auto;">
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Temperatura do Ar</p>
                        </div>
                        <?php  
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["TA"]."° C</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Umidade Relativa</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["RH"]."%</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left; background-color: #e9da0c">
                        <div class="head" style="background-color: #B88702;">
                            <p class="cabecalho" style="font-size:20px; ">Data e Hora Local</p>
                        </div>  
                        <?php
                        if($linha != null){
                            $hora = substr($max[1], 0, 5);
                            echo "<p class='textcard'>".$max[0]." ".$hora."</p>";
                        }
                        ?>
                        
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Temp. do Pto. de Orvalho</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["DP"]."° C</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Índice de Calor</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["HI"]."° C</p>";
                            }
                        ?>
                    </div>
                </div>
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Pressão Atmosférica</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["PA"]." hPa</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Radiação Solar Global</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["SR"]." W/m²</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Chuva</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["PR1HS"]." mm</p>";
                            }
                        ?>
                    </div>
                </div>
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Velocidade do Vento</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["WS1HA"]." m/s</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Direção do Vento</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["WD1HA"]."°</p>";
                            }
                        ?>
                    </div>
                    <div class="cart" style="float: left;">
                        <div class="head">
                            <p class="cabecalho">Bateria</p>
                        </div>
                        <?php
                            if($linha != null){
                                echo "<p class='textcard'>".$linha["DC"]." volts</p>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <footer id="footer">Desenvolvedor: Estagiário Vitor Aguiar da Gama</footer>
    </body>
</html>