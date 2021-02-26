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
	<link rel="stylesheet" type='text/css' href='fontello/css/fontello.css'/>
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
					Logowanie
				</header>
			</div>
			<div class='logon'>

			<main>
				<section>
					<form action='login.php' method='post'>
					<label>Podaj login:<br> <input type='text' name='login' class='log' <?= isset($_SESSION['login']) ? 'value="' .$_SESSION['login']. '"' : '' ?>> </label><br>
					<label>Podaj hasło:<br> <input type='password' name='password' class='log'> </label><br>
					<input type='submit' name='zaloguj' value='Zaloguj &#xe803;' class='btn btn-dark with_icons'>
					<a href="rejestracja.php"><input type="button" value="Rejestracja &#xf234;" class="btn btn-dark with_icons"></a>
					</form>
				</section>
			</main>

			<?php
			if(isset($_SESSION['login_error']))
			{
				echo '<hr>'.$_SESSION['login_error'];
				unset($_SESSION['login_error']);
				unset($_SESSION['login']);
			}

			if(isset($_SESSION['register_good']))
			{
				echo '<hr>'.$_SESSION['register_good'];
				unset($_SESSION['register_good']);
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