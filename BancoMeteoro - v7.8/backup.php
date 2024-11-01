<?php //backup de database funcional, mas o 000webhost não permite porque não posso acessar o mysqldump
    $host = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "estacaometeoro";
    if(!file_exists("docs/backups")){//se não existe uma pasta de backups crie
        mkdir("docs/backups");
    }
    $files = scandir("docs/backups");
    $temp=0;
    $tam=sizeof($files);
    foreach($files as $file){//para cada  arquivo, ele vai checar qual é o arquivo mais antigo
        if(is_file("docs/backups/"."$file")){//se é mesmo um arquivo
            if($temp==0){
                $menorarq=$file;
                $temp=filemtime("docs/backups/"."$file");
            }
            if(filemtime("docs/backups/"."$file")<$temp){
                $menorarq=$file;
                $temp=filemtime("docs/backups/"."$file");
            }
        }
    }
    if($tam>4)//vai apagar o arquivo mais antigo para manter somente os dois backups mais recentes
        unlink("docs/backups/"."$menorarq");
    $nome_arq=$banco."_".date("d_M_Y")."T".date("G_i");//da o nome para o backup
    $pasta="docs/backups/$nome_arq".".sql";

    exec("C:/xampp/mysql/bin/mysqldump --user={$usuario} --password={$senha} --host={$host} {$banco} --result-file={$pasta}");//cria o backup

    $files = scandir("docs/backups");
    $tam=sizeof($files);
    if($tam>4)//caso existam mais que 2 arquivos de backup o arquivo mais antigo é apagado
        unlink("docs/backups/"."$menorarq");
?>