<?php
    session_start();//sessão é a primeira coisa a ser iniciada sempre
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
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
					<a class="nav-link disabled" href="enviar.php">Envio de dados ao banco</a>
				</div>
			</div>
		</nav>
		<form method="post" action="valida.php"><!--manda os dados inseridos para valida.php-->
			<br>
			<div class="card">
				<div class="head">
					<p class="cabecalho">Iniciar sessão</p>
				</div>
				<label class="esquerda" >Usuário</label>
				<input class="input" type="text" name="usuario">
				<label class="esquerda">Senha</label>
				<input class="input" type="password" name="senha">
				<br>
				<button type="submit" style="width:30%; margin: 0 auto;">
					<img src="assets/seta.png" alt="enviar" style="width:100%; height:100%;">
				</button>
				<br>
				<a class="link" href="check.php">Gerencie as entradas</a>
			</div>
		</form>
	</body>			
</html>
<?php
	if(array_key_exists("cont",$_SESSION)) //se a variavel de controle existe pegue seu valor
		$cont=$_SESSION["cont"];
	else
		$cont=0; //ignora, pois não deveira voltar para login.php caso a senha esteja correto ou fosse a primeira vez
	if($cont)//valor da variavel de controle
		echo "<p class='aviso'>*Senha incorreta*</p>";
	session_unset(); //reinicia a sessão
?>