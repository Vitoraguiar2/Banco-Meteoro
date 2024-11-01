<?php
    
    $dir = "C:/Users/HILÁRIO/Downloads/LOG1_20210626";
    if(file_exists($dir)){
        $files = scandir($dir);
        $recente=0;
        foreach($files as $file){
            if(is_file("$dir/"."$file")){
                $tempo = filemtime("$dir/"."$file");
                if($recente==0 || $recente < $tempo){
                    $recente=$tempo;
                    $arqrecente="$dir/"."$file";
                }
            }
        }
        $arq = file_get_contents($arqrecente);
        if($arq==false)
            exit;

        $linha = explode("\n",$arq);
        $tam = sizeof($linha);
        $l = explode("	", $linha[0]);
        if(isset($l[1])==false || isset($l[2])==false || isset($l[3])==false || isset($l[4])==false || isset($l[5])==false || isset($l[6])==false || isset($l[7])==false || isset($l[8])==false || isset($l[9])==false || isset($l[10])==false ){
           exit;
        }
        if(isset($l[11])==true){
           exit;
        }
        if($l[1] != "DC" || $l[2] != "DP" || $l[3] != "HI" || $l[4] != "PA" || $l[5] != "PR1HS" || $l[6] != "RH" || $l[7] != "SR" || $l[8] != "TA" || $l[9] != "WD1HA"){
           exit;
        }
        $er = 0;
        $sql = "INSERT IGNORE INTO info_esta VALUES";
        $aux_sql = array();
        $aux_count = 0;
        $controle = strlen($sql);
        $count = 0;
        $count2 = 0;
        for($i=1; $i < $tam; $i++){
            $l=explode("	",$linha[$i]);
            if(isset($l[1])==true && isset($l[2])==true && isset($l[3])==true && isset($l[4])==true && isset($l[5])==true && isset($l[6])==true && isset($l[7])==true && isset($l[8])==true && isset($l[9])==true && isset($l[10])==true){
                for($k=1;$k<11;$k++){
                    if($l[$k]=='///'||$l[$k]=="///\r")
                        $l[$k]='null';
                }
                if($count == 0)
                    $sql .="('$l[0]',$l[1],$l[2],$l[3],$l[4],$l[5],$l[6],$l[7],$l[8],$l[9],$l[10],1)";
                else
                    $sql .=", ('$l[0]',$l[1],$l[2],$l[3],$l[4],$l[5],$l[6],$l[7],$l[8],$l[9],$l[10],1)";
                $count2++;
                $count++;
                $controle = strlen($sql);
            }
            if($controle > 3900000){
                $aux_sql[$aux_count]=$sql;
                $sql = "INSERT IGNORE INTO info_esta VALUES";
                $count=0;
                $controle = strlen($sql);
                $aux_count++;
            }
        }
        $host = "localhost";
        $usuario = "root";
        $senha = "";
        $banco = "estacaometeoro";
        $c = mysqli_connect($host,$usuario,$senha,$banco);
        if(!$c)
        {
            echo "Erro de conexao: ";
            echo mysql_error();
            exit;
        }
        $count3=0;
        if($aux_count != 0){
            $aux_sql[$aux_count]=$sql;
            $tam=sizeof($aux_sql);
            for($i=0;$i<$tam;$i++){
                if ($c->query($aux_sql[$i]) === FALSE){
                    $er=mysqli_errno($c);
                    echo "<p>".mysqli_error($c)."</p>";
                    exit;
                }
                $count3+=$c->warning_count;
            }
        }
        else{
            if ($c->query($sql) === FALSE){
                $er=mysqli_errno($c);
                echo "<p>".mysqli_error($c)."</p>";
                exit;
            }
            $count3=$c->warning_count;
        }
        if($er==0){
            if($count3==0)
                echo "Todos os dados inseridos corretamente do arquivo $arqrecente.";
            else{
                if($count3==$count2)
                    echo "Nenhuma linha inserida, o arquivo $arqrecente já existe no sistema.";
                else
                    echo "Certas linhas não foram inseridas, pois já existiam no sistema.";
            }
        }
    }
?>