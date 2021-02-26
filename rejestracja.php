<?php
session_start();

if(isset($_SESSION['which_user']))
{
	header('Location: kosztorys.php');
	exit();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Kosztorys</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/tlo_index.css">
	<link rel="shortcut icon" type="image/x-icon" href="img/icon.png">
	
</head>
<body>

<section class='index'>
	<div class='container'>
		<div class='row'>
		<div class='col-12'>
			<div class='info'>
				<header>
					Rejestracja
				</header>
			</div>
			<div class='logon'>

			<main>
				<section>
					<form action='register.php' method='post'>
					<label>Podaj login:<br> <input type='text' name='login' class='log' <?= isset($_SESSION['login']) ? 'value="' .$_SESSION['login']. '"' : '' ?>> </label><br>
					<label>Podaj hasło:<br> <input type='password' name='password1' class='log'> </label><br>
					<label>Powtórz hasło:<br> <input type='password' name='password2' class='log'> </label><br>
					<input type="submit" value="Zarejestruj się" name='zarejestruj' class="btn btn-dark">
					<a href="index.php"><input type="button" value="Wstecz" class="btn btn-dark"></a>
					</form>
				</section>
			</main>

			<?php
			if(isset($_SESSION['isset_login']))
			{
				echo '<hr>'.$_SESSION['isset_login'];
				unset($_SESSION['isset_login']);
			}

			if(isset($_SESSION['wrong_password']))
			{
				echo '<hr>'.$_SESSION['wrong_password'];
				unset($_SESSION['wrong_password']);
			}

			if(isset($_SESSION['brak']))
			{
				echo '<hr>'.$_SESSION['brak'];
				unset($_SESSION['brak']);
			}
			?>

			</div>
		</div>
		</div>
		
	</div>
	
</section>

<script src="js/jquery-3.3.1.min.js"></script>
<script src="bootstrap/js/popper.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>