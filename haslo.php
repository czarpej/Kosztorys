<?php

session_start();

if(!isset($_SESSION['which_user']))
{
	header('Location: index.php');
	exit();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Kosztorys <?php if(isset($_SESSION['which_user'])) {echo $_SESSION['which_user'];} ?></title>
	<meta charset="utf-8">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type='text/css' href='fontello/css/fontello.css'/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/tlo_kosztorys.css">
	<link rel="shortcut icon" type="image/x-icon" href="img/icon.png">

	<style>
	.table { text-align: center; }
	</style>

</head>
<body>

<nav class="navbar navbar-expand-sm">
	<ol class="navbar-nav">
		<li><a href="kosztorys.php" class="with_icons">Strona główna <i class="icon-home"></i></a></li>
		<li class="with_icons">Zarządzanie <i class="icon-cog"></i>
			<ul>
				<li><a href="haslo.php">Zmień hasło <i class="icon-key"></i></a></li>
				<li><a href="wydatki.php">Wydatki</a></li>
				<li><a href="typy.php">Typy wydatków</a></li>
			</ul>
		</li>
		<li class="with_icons">Sortowanie wydatków <i class="icon-calc"></i>
			<ul>
				<li><a href="sort_nazwa.php">Według nazwy <i class="icon-sort-name-up"></i></a></li>
				<li><a href="sort_typ.php">Według typu <i class="icon-sort-alt-up"></i></a></li>
				<li><a href="sort_data.php">Według daty <i class="icon-calendar"></i></a></li>
			</ul>
		</li>
		<li><a class="with_icons"><form method="post" action="logout.php"><input type="submit" name="logout" value="Wyloguj  &#xe800;"></form></a></li>
	</ol>
</nav>

<section class="haslo">
<div class="container">
	<div class="row">
		<div class="col-12">

			<?php
			require_once 'dbconnect.php';
			mysqli_report(MYSQLI_REPORT_STRICT);
			try
			{
				$connect=new mysqli($address, $db_login, $db_password, $db_name);
				if($connect->connect_errno!=0)
					throw new Exception($connect->connect_errno());
				else
				{
					$connect->query("SET CHARSET utf8");
					$connect->query("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");

					$password_from_db=$connect->query("SELECT haslo FROM uzytkownicy WHERE id='".$_SESSION['which_user_id']."' ");
					$what_password=$password_from_db->fetch_row();

					echo '<h2>Zmiana hasła</h2><form method="post"><div class="table-responsive">
					<table class="table">
					<tr><td><label for="actual_password">Obecne hasło:</label></td><td><input type="password" name="actual_password" id="actual_password"></td></tr>
					<tr><td><label for="new_password">Nowe hasło:</label></td><td><input type="password" name="new_password" id="new_password"></td></tr>
					<tr><td><label for="repeat_password">Powtórz hasło:</label></td><td><input type="password" name="repeat_password" id="repeat_password"></td></tr>
					</table>
					<p><input type="submit" name="change" value="Zmień" class="btn btn-dark"></p>
					</div></form>';
			?>

		</div>

		<?php
						if(isset($_SESSION['info']))
						{
							echo '<div class="col-12">';
							echo $_SESSION['info'];
							unset($_SESSION['info']);
							echo '</div>';
						}

						if(isset($_POST['change']))
						{
							if(isset($_POST['actual_password']))
								$_SESSION['actual_password']=$_POST['actual_password'];
							$_SESSION['new_password']=$_POST['new_password'];
							$_SESSION['repeat_password']=$_POST['repeat_password'];

							if(!$password_from_db)
								throw new Exception($connect->error);	
							else if($_SESSION['new_password']=='' || $_SESSION['repeat_password']=='')
							{
								$_SESSION['info']='<p>Nie wprowadzono wszystkich danych!</p>';
								header('Location: haslo.php');
								exit();
							}
							else if(!password_verify($_SESSION['actual_password'], $what_password[0]))
							{
								$_SESSION['info']='<p>Obecne hasło jest nieprawdziwe!</p>';
								header('Location: haslo.php');
								exit();
							}
							else if($_SESSION['new_password']!==$_SESSION['repeat_password'])
							{
								$_SESSION['info']='<p>Podane hasła są niezgodne!</p>';
								header('Location: haslo.php');
								exit();
							}
							else
							{
								if($connect->query("UPDATE uzytkownicy SET haslo='".password_hash($_SESSION['repeat_password'], PASSWORD_DEFAULT)."' WHERE id='".$_SESSION['which_user_id']."' "))
								{
									unset($_SESSION['actual_password']);
									unset($_SESSION['new_password']);
									unset($_SESSION['repeat_password']);
									$_SESSION['info']='<p>Pomyślnie zmieniono hasło.</p>';
									header('Location: haslo.php');
									exit();
								}
								else
									throw new Exception($connect->error);
							}
						}
				}
			}
			catch(Exception $e)
			{
				echo '<p>Serwer niedostępny.</p>';
			}
		?>

	</div>
</div>
</section>

<script defer src="https://use.fontawesome.com/releases/v5.1.1/js/all.js" integrity="sha384-BtvRZcyfv4r0x/phJt9Y9HhnN5ur1Z+kZbKVgzVBAlQZX4jvAuImlIz+bG7TS00a" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="bootstrap/js/popper.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/navbar.js"></script>
<script>
var obecne=$("#for_new").html();
var checked=function() {
	if($("input:checked").val())
		$("#for_new").html('<input type="text" name="new_type">');
	else
		$("#for_new").html(obecne);
};

$("input[type=checkbox]").on("click", checked);
</script>

</body>
</html>