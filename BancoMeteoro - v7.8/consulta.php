<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre

    $erro=0;

    if(isset($_POST["data1"])==false){
        $erro=1;
        $_SESSION["data_er"]=1;
    }
    $data1 = $_POST["data1"];
    if(isset($_POST["data2"])==false){
        $erro=1;
        $_SESSION["data_er"]=1;
    }
    $data2 = $_POST["data2"];
    if($data1 == null || $data2 == null){//se alguma das datas não foram selecionadas retorne a index.php
        $erro=1;
        $_SESSION["data_er"]=1;
        header("location:index.php");
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Consulta</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
        <link rel="icon" href="assets/logo.png"/>
        <script>
            function reqListener () {
                console.log(this.responseText);
            }

            var oReq = new XMLHttpRequest();
            oReq.onload = function() {
                if(this.responseText!="" && this.responseText!="erro")
                    desenhar(this.responseText);
            };
            oReq.open("get", "grafico.php", true); //pegas as informações de grafico.php via o reqListener, o true não para o funcionamento até a resposta
            oReq.send();
        </script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script>
            function procura($inf, $ind, $size){
                $x=0;
                while($ind[$x]!=$inf && $x<$size){//procura a posição do indice onde x está
                    $x++;
                }
                return $x;
            }
            function desenhar($dado){
                //"Penerando" os dados para uso
                //Considerando dado como: {"DataHora":"2021-07-27T08:30:00","TA":"27.5"}{"DataHora":"2021-07-27T08:29:00","TA":"27.5"}
                $str=$dado.replace(/"/g,'');//{DataHora:2021-07-27T08:30:00,TA:27.5}{DataHora:2021-07-27T08:29:00,TA:27.5}
                $str=$str.replace(/{/g,'');//DataHora:2021-07-27T08:30:00,TA:27.5}DataHora:2021-07-27T08:29:00,TA:27.5}
                $str=$str.replace(/}/g,',');//DataHora:2021-07-27T08:30:00,TA:27.5,DataHora:2021-07-27T08:29:00,TA:27.5,
                $str=$str.replace(/:/g,';');//DataHora;2021-07-27T08;30;00,TA;27.5,DataHora;2021-07-27T08;29;00,TA;27.5,
                $str=$str.split(',');//0 -> DataHora;2021-07-27T08;30;00 1-> TA;27.5 2-> DataHora;2021-07-27T08;29;00 3-> TA;27.5 4->
                
                $tam=$str.length;

                $aux_str=[];
                $size=0;
                do{//pegando quantos indices tem.
                    $aux_str[$size]=$str[$size].replace(/;/,':');//aux_str|0 -> DataHora:2021-07-27T08;30;00 1-> TA:27.5 2-> DataHora:2021-07-27T08;29;00 3-> TA:27.5 4->
                    $leitor=$aux_str[$size].split(':');//leitor|0 -> DataHora 1->2021-07-27T08;30;00
                    $size++;
                    $aux_str[$size]=$str[$size].replace(/;/,':');//de novo para não para o while
                    if($aux_str[$size] == "")//está vazio o array, logo fez um ciclo único e completo. evitando loops infinitos
                        break;
                    $leitor=$aux_str[$size].split(':');
                }while($leitor[0]!='DataHora');//um ciclo de indices

                for($i=0;$i<$tam-1;$i=$i+$size){
                    $str[$i]=$str[$i].replace(/;/g,':'); //str| 0->DataHora:2021-07-27T08:30:00 2-> DataHora:2021-07-27T08:29:00
                    $str[$i]=$str[$i].replace(/:/,';'); //str| 0->DataHora;2021-07-27T08:30:00 2-> DataHora;2021-07-27T08:29:00
                }
                $valores=[];
                $indice=[];
                $size=0;
                do{
                    $leitor=$str[$size].split(';');//leitor | 0->DataHora 1->2021-07-27T08:30:00 
                    $indice[$size]=$leitor[0];//indice | 0->DataHora 1->TA
                    $size++;
                    if($str[$size] == "")
                        break;
                    $leitor=$str[$size].split(';');
                }while($leitor[0]!="DataHora");
                
                for($i=0;$i<$tam-1;$i++){
                    $leitor=$str[$i].split(';'); //leitor | 0->DataHora 1->2021-07-27T08:30:00 
                    $valores[$i]=$leitor[1];//valores| 0->2021-07-27T08:30:00 1-> 27.5 2-> 2021-07-27T08:29:00 3-> 27.5
                }
                
                $estl=procura('codigo',$indice,$size);
                if($estl != 2 && $estl != 3)//se houverem mais que 2 dados 
                    return;
                $tempo=procura('controle',$indice,$size);
                if($tempo==$size)
                    $tempo=1
                else
                    $tempo=3;
                $est=0;
                for($i=$estl;$i<$tam;$i=$i+$estl+$tempo){
                    if($est==3)//caso existam as duas estações e já tenham sidos encontradas saia do loop
                        break;
                    if($est==0 && ($valores[$i]==1 || $valores[$i]==2))//Se nenhuma estação foi encontrada entre.
                        $est=$valores[$i];
                    else{
                        if($est==1 && $valores[$i]==2) //Se encontrar a contrapartida entre
                            $est=3;
                        if($est==2 && $valores[$i]==1) //Se encontrar a contrapartida entre
                            $est=3;
                    }
                }

                $tempo=procura('controle',$indice,$size);
                $tempe=1;//pega a primeira variavel que não é datahora
                
                if($tempe==$size)//se por algum motivo estranho só existam dois dados saia
                    return;
                
                google.charts.load('current', {'packages':['corechart', 'bar', 'line']});//carega os pacotes de gráficos
                google.charts.setOnLoadCallback(drawStuff);
                
                function grafico($div,$info,$valores,$indice,$vars){//testa se a combinação existe e então desenha
                    $seta=procura($info[0], $indice, $vars[1]);

                    if($seta!=$vars[1]&&$seta!=$tempe)//se o indice procurado existe na consulta
                        draw($div,$info,$valores,$indice,$vars,$seta);
                    else{
                        if($seta==$tempe && $estl == 2){
                            $info_prim=$info;
                            draw($div,$info,$valores,$indice,$vars,$seta);
                        }
                        if($seta==$tempe)
                            $info_prim=$info;
                    }
                }
                function drawStuff() {//prepara as variaveis
                    $vars=[$tam,$size,$est,$tempe,$estl,$tempo];

                    $info_prim=[];

                    $info=['DC','Bateria','Bateria (V)', 'orangered'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['DP','Temperatura do Ponto de Orvalho','Pto. Orvalho (°C)', 'yellow'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['HI','Indíce de Calor','Índice de calor (°C)', 'darkviolet'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['PA','Pressão Atmosférica','Pressão (hPa)', 'olive'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['PR1HS','Chuva','Chuva (mm)', 'skyblue'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['RH','Umidade Relativa','Umidade (%)', 'steelblue'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['SR','Radiação Solar','Radiação (W/m²)','firebrick'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['TA','Temperatura do Ar','Temperatura (°C)', 'green'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['WD1HA','Direção do Vento','Direção do vento (°)','tomato'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['WS1HA','Velocidade do Vento','Vento (m/s)','black'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['TA_X','Temperatura Máxima','Temperatura Max. (°C)','chocolate'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['TA_m','Temperatura Minima','Temperatura Min. (°C)','cadetblue'];
                    grafico('chart',$info,$valores,$indice,$vars);

                    $info=['WS_X','Velocidade Máxima','Velocidade Max. (m/s)','darkred'];
                    grafico('chart',$info,$valores,$indice,$vars);
                };
                function draw($id,$info,$valores,$indice,$vars,$seta){

                    var chartDiv = document.getElementById($id); //pega o div no html
                    if($info[0]=='TA' && $estl!=2){
                        $aux_seta = $seta;
                        $seta = $vars[3];
                        $vars[3] = $aux_seta;
                        $aux_info = $info;
                        $info = $info_prim;
                        $info_prim = $aux_info;
                    }
                    if($estl==2){
                        var data = google.visualization.arrayToDataTable([
                            ['', $info_prim[1]],
                            ['Teste', 10.1] //placeholder para o grafico funcionar e produzir as variaveis
                        ]);
                    }
                    else{
                        var data = google.visualization.arrayToDataTable([
                            ['', $info_prim[1], $info[1]],
                            ['Teste', 10.1, 10.3] //placeholder para o grafico funcionar e produzir as variaveis
                        ]);
                    }
                    $tam=($vars[0]-1)/$vars[1]; //dividindo o tamanho da consulta pelos indices sabendo quantas repetições serão feitas
                    $loc=0;
                    
                    $min1='ab';
                    $max1='ab';

                    $min2='ab';
                    $max2='ab';
                    $j=0;
                    for($i=0;$i<$tam;$i++){
                        
                        $loc=$i*$vars[1]; //pulando de quanto em quanto baseado na quantidade de indices.
                        $valores[$loc+$vars[3]]=parseFloat($valores[$loc+$vars[3]]); //transformando a variavel principal em numero
                        $valores[$loc+$seta]=parseFloat($valores[$loc+$seta]); //transformando outra variavel qualquer em numero
                        
                        if($i!=0) //se não for o primeiro dado inserido
                            data["Wf"].push({c: Array(3), p: undefined}); //abra espaço para mais dados
                        if($vars[2]==3){//caso as duas estações existam na consulta
                            $data = $valores[$loc];
                            $data = $data.slice(0, 16);
                            $data = $data.replace("T"," ");
                            if($valores[$tempo]==10){
                                $aux_data = $data.split("T");
                                $aux=0;
                                $aux_data2 = $valores[$loc+$vars[1]+$aux];
                                while($valores[$loc+$vars[4]]!=$valores[$loc+$vars[1]+$vars[4]+$aux]&&($valores[$loc+$vars[1]+$aux]!=null)){
                                    $aux_data2 = $valores[$loc+$vars[1]+$aux].split("T");
                                    $aux=$aux+$vars[1];
                                }
                            }
                            else{
                                $aux_data = $data;
                                $aux=0;
                                $aux_data2 = $valores[$loc+$vars[1]+$aux];
                                while($valores[$loc+$vars[4]]!=$valores[$loc+$vars[1]+$vars[4]+$aux]&&($valores[$loc+$vars[1]+$aux]!=null)){
                                    $aux_data2 = $valores[$loc+$vars[1]+$aux];
                                    $aux=$aux+$vars[1];
                                }
                            }
                            $estdata= $data+" est-"+$valores[$loc+$vars[4]];
                            data["Wf"][$i+$j]["c"][0]={v: $estdata}; //insira o valor do nome da datahora com estação
                        }
                        else{//senão
                            $data = $valores[$loc];
                            $data = $data.slice(0, 16);
                            if($valores[$tempo]==10){
                                $aux_data = $data.split("T");
                                if($valores[$loc+$vars[1]]!=null){
                                    $aux_data2 = $valores[$loc+$vars[1]].split("T");
                                }
                            }
                            else{
                                $aux_data = $data;
                                $aux_data2 = $valores[$loc+$vars[1]];
                            }
                            $data = $data.replace("T"," ");
                            data["Wf"][$i+$j]["c"][0]={v: $data}; //insira o valor do nome da datahora
                        }
                        
                        if($info_prim[0]=='WD1HA'||$info_prim[0]=='SR'||$info_prim[0]=='RH'){
                            $y=$valores[$loc+$vars[3]];//arredondando
                            $y=Math.round($y);
                            if($min1=='ab'||$max1=='ab'){//determina o tamanho dos valores no gráfico
                                $min1=$y;
                                $max1=$y;
                            }
                            if($y < $min1)
                                $min1=$y;
                            if($y > $max1)
                                $max1=$y;
                            data["Wf"][$i+$j]["c"][1]={v: $y};//insirindo outra variavel qualquer
                        }
                        else{
                            if($info_prim[0]=='DP'||$info_prim[0]=='HI'||$info_prim[0]=='PA'||$info_prim[0]=='PR1HS'||$info_prim[0]=='WS1HA'|| $info_prim[0]=='TA'||$info_prim[0]=='TA_X'||$info_prim[0]=='TA_m'|| $info_prim[0]=='WS_X'){
                                $y=$valores[$loc+$vars[3]];//arredondando
                                $y=$y*10;
                                $y=Math.round($y);
                                $y=$y/10;
                                if($min1=='ab'||$max1=='ab'){//determina o tamanho dos valores no gráfico
                                    $min1=$y;
                                    $max1=$y;
                                }
                                if($y < $min1)
                                    $min1=$y;
                                if($y > $max1)
                                    $max1=$y;
                                data["Wf"][$i+$j]["c"][1]={v: $y};//insirindo outra variavel qualquer
                            }
                            else{
                                $y=$valores[$loc+$vars[3]];//arredondando
                                $y=$y*100;
                                $y=Math.round($y);
                                $y=$y/100;
                                if($min1=='ab'||$max1=='ab'){//determina o tamanho dos valores no gráfico
                                    $min1=$y;
                                    $max1=$y;
                                }
                                if($y < $min1)
                                    $min1=$y;
                                if($y > $max1)
                                    $max1=$y;
                                data["Wf"][$i+$j]["c"][1]={v: $y};//insirindo outra variavel qualquer
                            }
                        }

                        if($info[0]=='WD1HA'||$info[0]=='SR'||$info[0]=='RH'){
                            $y=$valores[$loc+$seta];//arredondando
                            $y=Math.round($y);
                            if($min2=='ab'||$max2=='ab'){//determina o tamanho dos valores no gráfico
                                $min2=$y;
                                $max2=$y;
                            }
                            if($y < $min2)
                                $min2=$y;
                            if($y > $max2)
                                $max2=$y;
                            data["Wf"][$i+$j]["c"][2]={v: $y};//insirindo outra variavel qualquer
                        }
                        else{
                            if($info[0]=='DP'||$info[0]=='HI'||$info[0]=='PA'||$info[0]=='PR1HS'||$info[0]=='WS1HA' || $info[0]=='TA'||$info[0]=='TA_X'||$info[0]=='TA_m'|| $info[0]=='WS_X'){
                                $y=$valores[$loc+$seta];//arredondando
                                $y=$y*10;
                                $y=Math.round($y);
                                $y=$y/10;
                                if($min2=='ab'||$max2=='ab'){//determina o tamanho dos valores no gráfico
                                    $min2=$y;
                                    $max2=$y;
                                }
                                if($y < $min2)
                                    $min2=$y;
                                if($y > $max2)
                                    $max2=$y;
                                data["Wf"][$i+$j]["c"][2]={v: $y};//insirindo outra variavel qualquer
                            }
                            else{
                                $y=$valores[$loc+$seta];//arredondando
                                $y=$y*100;
                                $y=Math.round($y);
                                $y=$y/100;
                                if($min2=='ab'||$max2=='ab'){//determina o tamanho dos valores no gráfico
                                    $min2=$y;
                                    $max2=$y;
                                }
                                if($y < $min2)
                                    $min2=$y;
                                if($y > $max2)
                                    $max2=$y;
                                data["Wf"][$i+$j]["c"][2]={v: $y};//insirindo outra variavel qualquer
                            }
                        }
                        if($valores[$tempo]!=7){
                            if($valores[$tempo]==10){
                                var data_inicial = new Date($aux_data[0]);
                                var data_prox = new Date($aux_data2[0]);
                            }
                            else{
                                var data_inicial = new Date($aux_data);
                                var data_prox = new Date($aux_data2);
                            }
                            if($valores[$tempo]==10)
                                $tempo_soma=86400000
                            else
                                $tempo_soma=$valores[$tempo]*1000;
                            var data_aux= new Date(data_inicial.getTime()+$tempo_soma);
                            data_aux_num=data_aux.getTime();
                            data_prox_num=data_prox.getTime();
                            while(data_aux_num < data_prox_num && data_aux_num!=null){
                                $j++;
                                if(data_aux!=null){
                                    data["Wf"].push({c: Array(3), p: undefined});
                                    $data_aux_format = data_aux.toISOString();
                                    $data_aux_format = $data_aux_format.slice(0, 16);
                                    $data_aux_format = $data_aux_format.replace("T"," ");
                                    if($vars[2]==3)
                                        $data_aux_format=  $data_aux_format+" est-"+$valores[$loc+$vars[4]];
                                    data["Wf"][$i+$j]["c"][0]={v: $data_aux_format};
                                    data["Wf"][$i+$j]["c"][1]={v: null};
                                    data["Wf"][$i+$j]["c"][2]={v: null};
                                    var data_aux = new Date(data_aux.getTime()+$tempo_soma);
                                    data_aux_num=data_aux.getTime();
                                }
                                else
                                    data_aux_num=null;
                            }
                        }
                    }
                    if($j+$i > 576){
                        var aviso = document.getElementById('java_aviso');
                        aviso.style.display='';
                        return;
                    }
                    if($estl==2){//caso seja uma variavel sozinha
                        if($info_prim[0] == 'PR1HS' || $info_prim[0]=='PA' || $info_prim[0]=='RH'){//e o grafico seja de barra
                            if($info_prim[0] == 'PA'){//para pressão atmosférica
                                var materialOptions = {
                                    width: '100%',
                                    chartArea:{width: '90%'},
                                    vAxis: {
                                        format: 'decimal',
                                        textStyle: {
                                            fontSize: 11
                                        },
                                        viewWindow: {
                                            min: ($min1-5),
                                            max: ($max1+5)
                                        }
                                    },
                                    chart: {
                                        title: 'Gráfico da '+$info_prim[1],
                                        subtitle: 'em '+$info_prim[2]
                                    },
                                    colors: [$info_prim[3]]
                                };
                            }
                            else{//para chuva
                                var materialOptions = {
                                    width: '95%',
                                    chartArea:{width: '90%'},
                                    vAxis: {
                                        format: 'decimal',
                                        viewWindow: {
                                            min: 0
                                        }
                                    },
                                    chart: {
                                        title: 'Gráfico da '+$info_prim[1],
                                        subtitle: 'em '+$info_prim[2]
                                    },
                                    colors: [$info_prim[3]]
                                };
                            }
                        }
                        else{//gráfico de linha
                            if($min1 > 0)
                                $min1=$min1-1;
                            var options = {
                                title: 'Gráfico da '+$info_prim[1],
                                chartArea:{width: '90%'},
                                curveType: 'function',
                                fontSize: 11,
                                width: '95%',
                                vAxis: {title: $info_prim[2], viewWindow:{min: $min1, max: $max1+1}, titleTextStyle:{color: $info_prim[3]}},
                                hAxis: {title: ''},
                                colors: [$info_prim[3]]
                            };
                        }
                    }
                    else{//para 2 variáveis
                        if(($info[0] == 'PR1HS' ||  $info[0]=='RH' ||  $info[0]=='PA') && ($info_prim[0]=='PA' || $info_prim[0]=='RH' || $info_prim[0]=='PR1HS')){//se for com pressão e chuva
                            if($info_prim[0]=='PR1HS'){
                                $min1=0;
                                if($max2==0)
                                    $max1=10;
                            }
                            if($info_prim[0]=='PA'){
                                $min1=$min1-2;
                                $max1=$max1+2;
                            }
                            if($info[0]=='PR1HS'){
                                $min2=0;
                                if($max2==0)
                                    $max2=10;
                            }
                            if($info[0]=='PA'){
                                $min2=$min2-2;
                                $max2=$max2+2;
                            }
                            var materialOptions = {
                                    width: '95%',
                                    chartArea:{width: '80%'},
                                    vAxis: {
                                        format: 'decimal',
                                    },
                                    chart: {
                                        title: $info_prim[1]+' x '+$info[1],
                                        subtitle: $info_prim[1]+' na esquerda, '+$info[1]+' na direita'
                                    },
                                    series: {
                                        0: { axis: 'index1', targetAxisIndex: 0}, // Bind series 0 to an axis named 'distance'.
                                        1: { axis: 'index2', targetAxisIndex: 1} // Bind series 1 to an axis named 'brightness'.
                                    },
                                    axes: {
                                        y: {
                                        0: {label: $info_prim[2]}, // Left y-axis.
                                        1: {side: 'right', label: $info[2]} // Right y-axis.
                                        }
                                    },
                                    vAxes: { 
                                        0: {viewWindow: { min: $min1, max: $max1},titleTextStyle:{color: $info_prim[3]}},
                                        1: {viewWindow: { min: $min2, max: $max2},titleTextStyle:{color: $info[3]}}
                                    },
                                    colors: [$info_prim[3],$info[3]]
                            };
                        }
                        else{
                            if($info[0] == 'PR1HS' || $info[0]=='PA' || $info[0]=='RH'){//se for combo chart (barra e linha) e as variaveis são secundárias
                                if($info[0] == 'PR1HS'){//se for com chuva
                                    var options = {                                    
                                        title: $info_prim[1]+' x '+$info[1],
                                        chartArea:{width: '90%'},
                                        curveType: 'function',
                                        fontSize: 11,
                                        width: '95%',
                                        seriesType: 'line',
                                        series: {
                                            0: {targetAxisIndex: 0}, // Bind series 0 to an axis named 'distance'.
                                            1: {targetAxisIndex: 1, type: 'bars'}
                                        },
                                        axes: {
                                            y: {
                                            index1: {label: $info_prim[2]}, // Left y-axis.
                                            index2: {side: 'right', label: $info[2]} // Right y-axis.
                                            }
                                        },
                                        vAxes: {
                                            0:{title: $info_prim[2], viewWindow: {min: $min1},titleTextStyle:{color: $info_prim[3]}},
                                            1: {title: $info[2], viewWindow: {min: 0, max: $max2+100},titleTextStyle:{color: $info[3]}}
                                        },
                                        hAxis: {title: ''},
                                        colors: [$info_prim[3], $info[3]]
                                    };
                                }
                                else{//se for com pressão
                                    var options = {
                                        title: $info_prim[1]+' x '+$info[1],
                                        chartArea:{width: '90%'},
                                        curveType: 'function',
                                        fontSize: 11,
                                        width: '95%',
                                        seriesType: 'line',
                                        series: {
                                            0: {targetAxisIndex: 0}, // Bind series 0 to an axis named 'distance'.
                                            1: {targetAxisIndex: 1, type: 'bars'}
                                        },
                                        axes: {
                                            y: {
                                            index1: {label: $info_prim[2], viewWindow: {min: $min1}}, // Left y-axis.
                                            index2: {side: 'right', label: $info[2]} // Right y-axis.
                                            }
                                        },
                                        vAxes: {
                                            0:{title: $info_prim[2],titleTextStyle:{color: $info_prim[3]}},
                                            1: {title: $info[2], format: 'decimal',titleTextStyle:{color: $info[3]}}
                                        },
                                        hAxis: {title: ''},
                                        colors: [$info_prim[3], $info[3]]
                                    };
                                }
                            }
                            else{
                                if($info_prim[0] == 'PR1HS' || $info_prim[0]=='PA' || $info_prim[0]=='RH'){//se for combo chart (barra e linha) e as variaveis são primárias
                                    if($info_prim[0] == 'PR1HS'){//se for com chuva
                                        var options = {
                                            title: $info_prim[1]+' x '+$info[1],
                                            chartArea:{width: '90%'},
                                            curveType: 'function',
                                            fontSize: 11,
                                            width: '95%',
                                            seriesType: 'bars',
                                            series: {
                                                0: {targetAxisIndex: 0}, // Bind series 0 to an axis named 'distance'.
                                                1: {targetAxisIndex: 1, type: 'line'}
                                            },
                                            axes: {
                                                y: {
                                                index1: {label: $info_prim[2]}, // Left y-axis.
                                                index2: {side: 'right', label: $info[2]} // Right y-axis.
                                                }
                                            },
                                            vAxes: {
                                                0:{title: $info_prim[2], viewWindow: {min: 0, max: $max1+10},titleTextStyle:{color: $info_prim[3]}},
                                                1: {title: $info[2], viewWindow: {min: $min2},titleTextStyle:{color: $info[3]}}
                                            },
                                            hAxis: {title: ''},
                                            colors: [$info_prim[3], $info[3]]
                                        };
                                    }
                                    else{//se for com pressão
                                        var options = {
                                            title: $info_prim[1]+' x '+$info[1],
                                            chartArea:{width: '90%'},
                                            curveType: 'function',
                                            fontSize: 11,
                                            width: '95%',
                                            seriesType: 'bars',
                                            series: {
                                                0: {targetAxisIndex: 0}, // Bind series 0 to an axis named 'distance'.
                                                1: {targetAxisIndex: 1, type: 'line'}
                                            },
                                            axes: {
                                                y: {
                                                index1: {label: $info_prim[2]}, // Left y-axis.
                                                index2: {side: 'right', label: $info[2]} // Right y-axis.
                                                }
                                            },
                                            vAxes: {
                                                0:{title: $info_prim[2],titleTextStyle:{color: $info_prim[3]}},
                                                1: {title: $info[2], viewWindow: {min: $min2},titleTextStyle:{color: $info[3]}}
                                            },
                                            hAxis: {title: ''},
                                            colors: [$info_prim[3], $info[3]]
                                        };
                                    }
                                }
                                else{
                                    if(((
                                        ($info_prim[0]== 'HI' || $info_prim[0]== 'DP' || $info_prim[0]== 'TA' || $info_prim[0]== 'TA_X' || $info_prim[0]== 'TA_m')&&($info[0]== 'HI' || $info[0]== 'DP' || $info[0]== 'TA' || $info[0]== 'TA_X' || $info[0]== 'TA_m'))||(($info_prim[0]=="WS1HA"||$info_prim[0]=="WS_X")&&($info[0]=="WS1HA"||$info[0]=="WS_X")))&&$estl!=2){//se for de linha e as variaveis dividem tipos
                                        
                                        if($min1 < $min2)
                                            $min = $min1;
                                        else
                                            $min = $min2;
                                        if($max1 > $max2)
                                            $max = $max1;
                                        else
                                            $max = $max2;
                                        $max=$max+1;
                                        var options = {                                    
                                            title: $info_prim[1]+' x '+$info[1],
                                            chartArea:{width: '90%'},
                                            curveType: 'function',
                                            fontSize: 11,
                                            width: '95%',
                                            series: {
                                                0: {targetAxisIndex: 0}, // Bind series 0 to an axis named 'distance'.
                                                1: {targetAxisIndex: 1}
                                            },
                                            axes: {
                                                y: {
                                                index1: {label: $info_prim[2]}, // Left y-axis.
                                                index2: {side: 'right', label: $info[2]} // Right y-axis.
                                                }
                                            },
                                            vAxes: {
                                                0:{title: $info_prim[2], viewWindow: {min: $min, max: $max},titleTextStyle:{color: $info_prim[3]}},
                                                1: {title: $info[2], viewWindow: {min: $min, max: $max},titleTextStyle:{color: $info[3]}}
                                            },
                                            hAxis: {title: ''},
                                            colors: [$info_prim[3], $info[3]]
                                        };
                                    }
                                    else{//se não for nenhum dos outros casos será de linha
                                        if($min1 > 0)
                                            $min1=$min1-1;
                                        if($min2 > 0)
                                            $min2=$min2-1;
                                        var options = {                                    
                                            title: $info_prim[1]+' x '+$info[1],
                                            chartArea:{width: '90%'},
                                            curveType: 'function',
                                            fontSize: 11,
                                            width: '95%',
                                            series: {
                                                0: {targetAxisIndex: 0},
                                                1: {targetAxisIndex: 1}
                                            },
                                            axes: {
                                                y: {
                                                index1: {label: $info_prim[2], textStyle:{color: $info_prim[3]}}, // Left y-axis.
                                                index2: {side: 'right', label: $info[2], textStyle:{color: $info[3]}} // Right y-axis.
                                                }
                                            },
                                            vAxes: {
                                                0:{title: $info_prim[2],viewWindow: {min: $min1}, titleTextStyle:{color: $info_prim[3]}},
                                                1: {title: $info[2], viewWindow: {min: $min2}, titleTextStyle:{color: $info[3]}}
                                            },
                                            hAxis: {title: ''},
                                            colors: [$info_prim[3], $info[3]]
                                        };
                                    }
                                }
                            }
                        }
                    }
                    chartDiv.style.display='';//mostra o div
                    if(($info[0] == 'PR1HS' ||  $info[0]=='RH' ||  $info[0]=='PA') && ($info_prim[0]=='PA' || $info_prim[0]=='RH' || $info_prim[0]=='PR1HS') || (($info[0] == 'PR1HS' || $info_prim[0]=='PA'|| $info_prim[0]=='RH') && $estl==2)){//se for gráfico de barra
                        var materialChart = new google.charts.Bar(chartDiv);
                        materialChart.draw(data, google.charts.Bar.convertOptions(materialOptions));
                    }
                    else{
                        if(($info[0] == 'PR1HS' || $info[0]=='PA' || $info[0]=='RH')||($info_prim[0] == 'PR1HS' || $info_prim[0]=='PA' || $info_prim[0]=='RH')){//se for combochart
                            var chart = new google.visualization.ComboChart(chartDiv);
                            chart.draw(data, options);
                        }
                        else{//se for de linha
                            var chart = new google.visualization.LineChart(chartDiv);
                            chart.draw(data, options);
                        }
                    }
                }
            }
        </script>
    </head>
    <body>
        <?php   
            function tratarVar($var,$post,$inte){//prepara as variáveis
                if(isset($_POST[$post])){
                    $var=$_POST[$post];
                    if($inte != 1)
                        $var="avg(".$var.") as $post";//caso não seja uma tabela de 1 minuto
                    $var = ", ".$var;
                }
                else
                    $var = "";
                $_SESSION[$post]=$var;//cria a variavel na sessão
                return $var;
            }

            $files = scandir("arqs/");
            $now  = time();

            foreach ($files as $file) {//apagas os arquivos em ars que estão lá mais de 1 hora
                if (is_file("arqs/"."$file")) {
                    if ($now - filemtime("arqs/"."$file") >= 60*60*1) { // 1 hora
                        unlink("arqs/"."$file");
                    }
                }
            }

            if($data1 > $data2){//se a data1 for maior que a data2, inverta seus valores
                $aux_data = $data1;
                $data1 = $data2;
                $data2 = $aux_data;
            }

            $_SESSION["data1"]=$data1;
            $_SESSION["data2"]=$data2;

            if(isset($_POST["tabela"])==false)//se não for pedido tabela
                $tabela=0;
            else
                $tabela = $_POST["tabela"];
            if(isset($_POST["grafico"])==false)//se não for pedido gráfico
                $grafico=0;
            else
                $grafico = $_POST["grafico"];
            $_SESSION["grafico"]=$grafico;

            //pegando o resto das variaveis e colacando elas na sessão
            
            $est = $_POST["est"];
            $_SESSION["est"] = $est;

            $inter = $_POST["intervalo"];
            $_SESSION["inter"]=$inter;
            
            echo "<header style='margin: 0; padding-top:5px; margin-bottm: 5px;'>Consulta de $data1 a $data2 de ";//cria o cabeçalho
            if($inter==3600)
                echo "1 em 1 hora</header>";
            if($inter==1)
                echo "1 em 1 minuto</header>";
            if($inter==600)
                echo "10 em 10 minutos</header>";
            if($inter==1800)
                echo "30 em 30 minutos</header>";
            if($inter==10)
                echo "1 em 1 dia</header>";
            if($inter==7)
                echo "1 em 1 mês</header>";
            //tratando todas as variáveis ambientais
            $contr=0;
            $DC = 0;
            $DC=tratarVar($DC,"DC",$inter);
            if($DC!="")
                $contr++;
            $DP = 0;
            $DP=tratarVar($DP,"DP",$inter);
            if($DP!="")
                $contr++;
            $HI = 0;
            $HI=tratarVar($HI,"HI",$inter);
            if($HI!="")
                $contr++;
            $PA = 0;
            $PA=tratarVar($PA,"PA",$inter);
            if($PA!="")
                $contr++;
            $PR1HS = 0;
            if(isset($_POST["PR1HS"])){
                $PR1HS=$_POST["PR1HS"];
                if($inter != 1)
                    $PR1HS="sum(".$PR1HS.") as PR1HS";
                $PR1HS = ", ".$PR1HS;
                $contr++;
            }
            else
                $PR1HS = "";
            $_SESSION["PR1HS"]=$PR1HS;
            $RH = 0;
            $RH=tratarVar($RH,"RH",$inter);
            if($RH!="")
                $contr++;
            $SR = 0;
            $SR=tratarVar($SR,"SR",$inter);
            if($SR!="")
                $contr++;
            $TA = 0;
            $TA=tratarVar($TA,"TA",$inter);
            if($TA!="")
                $contr++;
            $WD1HA = 0;
            $WD1HA=tratarVar($WD1HA,"WD1HA",$inter);
            if($WD1HA!="")
                $contr++;
            $WS1HA = 0;
            $WS1HA=tratarVar($WS1HA,"WS1HA",$inter);
            if($WS1HA!="")
                $contr++;
            $TA_X = 0;
            if(isset($_POST["TA_X"])){
                $TA_X=$_POST["TA_X"];
                $contr++;
            }
            else
                $TA_X = "";
            $_SESSION["TA_X"]=$TA_X;
            $TA_m = 0;
            if(isset($_POST["TA_m"])){
                $TA_m=$_POST["TA_m"];
                $contr++;
            }
            else
                $TA_m = "";
            $_SESSION["TA_m"]=$TA_m;
            $WS_X = 0;
            if(isset($_POST["WS_X"])){
                $WS_X=$_POST["WS_X"];
                $contr++;
            }
            else
                $WS_X = "";
            $_SESSION["WS_X"]=$WS_X;
            $host = "localhost";
            $usuario = "root";
            $senha = "";
            $banco = "estacaometeoro";
            $c = mysqli_connect($host,$usuario,$senha,$banco);//conetando ao banco
            if(!$c)
            {
                echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Ocorreu um erro de conexao: ";
                echo mysql_error();
                echo "</p>";
                die();
            }
            //preparando a consulta
            if($inter == 1){//se for de 1 em 1 minuto
                $sql = "SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X $TA_m $WS_X, codigo FROM info_esta WHERE DataHora >= '$data1"."T00:00:00' AND DataHora <= '$data2"."T23:59:00'";
                if($est != 3)//e for para as duas estções
                    $sql .= " AND codigo = $est";
            }   
            else{
                if($inter == 7 || $inter == 10){//se for de 1 dia ou mensal
                    if($inter == 7){//se for mensal
                        $data1=substr($data1,0,-3);
                        $data2=substr($data2,0,-3);
                        $data2.="-32";
                        $data1.="-01";
                    }
                    
                    $sql = "SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X $TA_m $WS_X, codigo, SUBSTRING(DataHora,1,$inter) as tempo, $inter as controle  FROM info_esta WHERE DataHora >= '$data1%' AND DataHora <= '$data2"."T23:59:00'";
                    
                    if($est != 3)
                        $sql .= " AND codigo = $est";
                    else{//se forem as duas estações
                        $sql = "SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X $TA_m $WS_X, codigo, SUBSTRING(DataHora,1,$inter) as tempo, $inter as controle  FROM info_esta WHERE DataHora >= '$data1%' AND DataHora <= '$data2"."T23:59:00' AND codigo = 1 GROUP BY tempo";
                        $sql .= " UNION ";
                        $sql .= "SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X $TA_m $WS_X, codigo, SUBSTRING(DataHora,1,$inter) as tempo, $inter as controle  FROM info_esta WHERE DataHora >= '$data1%' AND DataHora <= '$data2"."T23:59:00' AND codigo = 2";
                    }
                }
                else{//10 minutos, 30 minutos e 1 hora
                    $sql = "SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X $TA_m $WS_X, codigo, floor(unix_timestamp(DataHora)/$inter) as tempo, $inter as controle  FROM info_esta WHERE DataHora >= '$data1"."T00:00:00' AND DataHora <= '$data2"."T23:59:00' ";
                    if($est != 3)
                        $sql .= " AND codigo = $est";
                    else{//se forem as duas estações
                        $sql="SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X $TA_m $WS_X, codigo, floor(unix_timestamp(DataHora)/$inter) as tempo, $inter as controle  FROM info_esta WHERE DataHora >= '$data1"."T00:00:00' AND DataHora <= '$data2"."T23:59:00' AND codigo=1 GROUP BY tempo";
                        $sql .= " UNION ";
                        $sql .="SELECT DataHora $DC $DP $HI $PA $PR1HS $RH $SR $TA $WD1HA $WS1HA $TA_X$TA_m $WS_X, codigo, floor(unix_timestamp(DataHora)/$inter) as tempo, $inter as controle FROM info_esta WHERE DataHora >= '$data1"."T00:00:00' AND DataHora <= '$data2"."T23:59:00' AND codigo=2";
                    }
                }
                $sql .=   " GROUP BY tempo";
            }
            $_SESSION["sql"]=$sql;//envia para sessão
            $sql .="  ORDER BY DataHora DESC";//ordem dos decrescente
            $sql_cont="SELECT count(DataHora) as contagem from (".$sql.")src";//serve para quantos dados tem na consulta
            $resp = mysqli_query($c, $sql_cont);
            if(!$resp)
            {
                $er = mysqli_errno($c);
                echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Houve um erro de número $er, durante a consulta.</p>";
                mysqli_close($c);
                die();
            }
            $cont = mysqli_fetch_assoc($resp);
            
            if($cont["contagem"]>2880 || $contr > 2){//se a consulta for grande demais ou for mais de 2 variaveis
                $_SESSION["grafico"]=0;
            }

            $resp = mysqli_query($c, $sql);
            if(!$resp)
            {
                $er = mysqli_errno($c);
                echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Houve um erro de número $er, durante a consulta.</p>";
                mysqli_close($c);
                die();
            }
            $linha = mysqli_fetch_assoc($resp);
            if(isset($linha)==false){//se não tiver nenhum dado na consulta feita
                echo "<p class='aviso' style='text-align:left; padding-left:10px;'>Sua consulta não retornou nenhum resultado.</p>";
                die();
            }
            if($inter == 7){//se for mensal
                $data2=substr($data2,0,-3);
                $data1=substr($data1,0,-3);
            }
            $arq = "arqs/$data1"."a$data2.csv";//crie o nome do arquivo
            $csv = fopen($arq, "w") or die("Não foi possível criar arquivo");//crie o arquivo
            //preparando o cabeçalho do csv
            if($TA_X != "")
                $TA_X = ", TAX TAX ";
            if($TA_m != "")
                $TA_m = ", TAm TAm ";
            if($WS_X != "")
                $WS_X = ", WSX WSX ";
            if($est == 3){//se forem duas estações
                if($inter!=1)// e não for uma consulta de 1 em 1 minuto
                    $header = "Data e Hora$DC$DP$HI$PA$PR1HS$RH$SR$TA$WD1HA$WS1HA$TA_X$TA_m$WS_X, Estacao Estacao ";
                else
                    $header = "Data e Hora$DC$DP$HI$PA$PR1HS$RH$SR$TA$WD1HA$WS1HA$TA_X$TA_m$WS_X, Estacao";
            }
            else//senão
                $header = "Data e Hora$DC$DP$HI$PA$PR1HS$RH$SR$TA$WD1HA$WS1HA$TA_X$TA_m$WS_X";
            $x = array("avg", "(", "as", ")", "sum", "MAX", "MIN");
            $header=str_replace($x,"",$header);//vai apagar todos os "avg", "(", "as", ")", "sum" do cabeçalho
            $header = explode(", ",$header);//vai dividir o cabeçalho baseado em virgulas
            $tam = sizeof ($header);
            if($inter!=1){//vai apagar duplicatas como "DC DC"
                for ($i=1; $i < $tam; $i++){
                    $tam2=strlen($header[$i]);
                    $tam2=($tam2-2)/2;
                    $header[$i]=substr($header[$i],0,$tam2);
                }
            }
            fputcsv ($csv, $header);//colaca o cabeçalho no csv
            //determina o limite para a tabela
            if($inter==1)
                $limite=8641; //3 dias para 2 estações ou 6 dias para 1 estação
            else
                $limite=4501;

            while($linha){//prepara os dados arredondando eles
                if($inter != 7 && $inter != 10){
                    $datahora=explode("T",$linha["DataHora"]);
                }
                if($TA != ""){
                    $linha["TA"]=round($linha["TA"],1);
                }
                if($RH != ""){
                    $linha["RH"]=round($linha["RH"],0);
                };
                if($DP != ""){
                    $linha["DP"]=round($linha["DP"],1);
                }
                if($HI != ""){
                    $linha["HI"]=round($linha["HI"],1);
                }
                if($PA != "") {
                    $linha["PA"]=round($linha["PA"],1);
                }
                if($SR != ""){
                    $linha["SR"]=round($linha["SR"],0);
                }
                if($PR1HS != ""){ 
                    $linha["PR1HS"]=round($linha["PR1HS"],1);
                }
                if($WS1HA != ""){
                    $linha["WS1HA"]=round($linha["WS1HA"],1);
                }
                if($WD1HA != ""){
                    $linha["WD1HA"]=round($linha["WD1HA"],0);
                };
                if($DC != ""){
                    $linha["DC"]=round($linha["DC"],2);
                }
                if($TA_X != ""){ 
                    $linha["TA_X"]=round($linha["TA_X"],1);
                }
                if($TA_m != ""){ 
                    $linha["TA_m"]=round($linha["TA_m"],1);
                }
                if($WS_X != ""){ 
                    $linha["WS_X"]=round($linha["WS_X"],1);
                }
                if (array_key_exists("tempo",$linha)){ unset($linha["tempo"]); unset($linha["controle"]);}//apaga o dado tempo
                fputcsv ($csv, $linha);//coloca os dados no csv
                $linha = mysqli_fetch_assoc($resp);//próximos dados
            }
            fclose($csv);
            
            echo "<p style = 'margin:0; padding-left:10px; padding-top:5px;'>LAPA: - Estação Meterólogica - <a href='http://meteoro.cefet-rj.br/'>Maracanã A 201";
            if($est == 3)
                echo " 1 e 2";
            else
                echo " $est";
            echo ".</a></p>";
            echo "<p class = 'sucesso' style='text-align:left; padding-left:10px; margin-bottom: 0px'>Sua consulta foi realizada com sucesso! <a href='index.php'>Nova Consulta.</a></p>";
            echo"<p style='margin-bottom: 0px'>Baixe o csv da consulta <a href='$arq' download>aqui</a>.</p>";//coloca o arquivo para download
            if($contr<3 && $inter != 1 && $grafico !=0){//trata a parte dos gráficos
                if($cont["contagem"]>576){
                    echo "<p class='aviso' style='padding-left:10px; text-align:left; margin-bottom: 5px;'>*Tamanho de consulta grande demais para gerar gráficos.*</p>";
                }
                else{
                    echo "<div id=".'"'."chart".'"'." style=".'"'."width: 1300px; height: 500px; padding-left:20px; padding-top: 5px; display: none; ".'"'."></div>";
                    echo "<p id='java_aviso' class='aviso' style='padding-left:10px; display: none; text-align:left;' margin-bottom: 5px;>*Tamanho de consulta grande demais para gerar gráficos.*</p>";
                }
            }
            if($contr>2 && $grafico !=0){//trata a parte dos gráficos
                echo "<p class='aviso' style='padding-left:10px; text-align:left; margin-bottom: 5px;'>*Não é possível gerar gráficos para mais de 2 variáveis*</p>";
            }
            if($grafico !=0 && $inter == 1){//trata a parte dos gráficos
                echo "<p class='aviso' style='padding-left:10px; text-align:left; margin-bottom: 5px;'>*Não é possível gerar gráficos para esse intervalo*</p>";
            }
            if($tabela !=0){//dá o link para ver a tabela
                if($grafico==0)
                    echo "<br>";
                echo "<p style='text-align:center; margin:0; padding-top:5px;'>Veja a tabela gerada <a href='tabela.php' target='_blank'>aqui</a>.</p>";
            }
        ?>
    </body>
</html>