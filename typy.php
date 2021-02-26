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

<section class="typy">
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

					echo '<center><input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#dodawanie" value="Dodawanie typu wydatków"> <input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#zmiana" value="Zmiana typu wydatków"> <input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#usuwanie" value="Usuwanie typu wydatku"></center>';

					echo '<div id="usuwanie" class="collapse"><fieldset><legend>Usuwanie typu wydatków</legend><form method="post" action="typy_dzialania.php"><div class="table-responsive">
					<table class="table">
					<tr><td><label for="typ">Typ wydatku:</label></td><td>';
					if($typ=$connect->query("SELECT DISTINCT typ FROM typy WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($typ->num_rows>0)
						{
							echo '<select name="typ" id="typ"><option selected disabled hidden style="display: none" value=""> -- wybierz typ -- </option>';
							while($typ_results=$typ->fetch_row())
								echo '<option>'.$typ_results[0].'</option>';
							echo '</select>';
						}
						else
							echo 'Brak zapisanych typów w bazie danych.';
					}
					else
						throw new Exception($connect->error);
					echo '</td></tr><tr><td><label for="razem">Usunięcie wraz z wydatkami</label></td><td><input type="checkbox" name="razem" id="razem" value="cos"></td></tr></table>
					<p><input type="submit" name="delete" value="Usuń &#xe80a;" class="btn btn-dark with_icons"></p>
					<span class="hidden_info">Wydatki z usuniętym typem zmienią typ na "Inne".</span>
					</div></form></fieldset></div>

					<div id="zmiana" class="collapse"><fieldset><legend>Zmiana typu wydatków</legend><form method="post" action="typy_dzialania.php"><div class="table-responsive">
					<table class="table">
					<tr><td><label for="typ_do_zmiany">Zmień typ:</label><span class="nowy_typ"><input type="checkbox" val="cos" id="delete_type" name="delete_type"><label for="delete_type"> (usuń po zmianie)</label></span></td><td>';
					if($typ=$connect->query("SELECT DISTINCT typ FROM typy WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($typ->num_rows>0)
						{
							echo '<select name="typ_do_zmiany" id="typ_do_zmiany"><option selected disabled hidden style="display: none" value=""> -- wybierz typ -- </option>';
							while($typ_results=$typ->fetch_row())
								echo '<option>'.$typ_results[0].'</option>';
							echo '</select>';
						}
						else
							echo 'Brak zapisanych typów w bazie danych.';
					}
					else
						throw new Exception($connect->error);
					echo '</td></tr><tr><td><label for="typ_po_zmianie">Na typ:</label><span class="nowy_typ"><input type="checkbox" val="cos" id="new"><label for="new"> (nowy)</label></span></td><td id="for_new">';
					if($typ=$connect->query("SELECT DISTINCT typ FROM typy WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($typ->num_rows>0)
						{
							echo '<select name="typ_po_zmianie" id="typ_po_zmianie"><option selected disabled hidden style="display: none" value=""> -- wybierz typ -- </option>';
							while($typ_results=$typ->fetch_row())
								echo '<option>'.$typ_results[0].'</option>';
							echo '</select>';
						}
						else
							echo 'Brak zapisanych typów w bazie danych.';
					}
					else
						throw new Exception($connect->error);
					echo '</td></tr></table>
					<p><input type="submit" name="change" value="Zmień" class="btn btn-dark"></p>
					</div></form></fieldset></div>

					<div id="dodawanie" class="collapse"><fieldset><legend>Dodawanie typu wydatku</legend><form method="post" action="typy_dzialania.php"><div class="table-responsive">
					<table class="table">
					<tr><td><label for="typ_nowy">Nazwa nowego typu:</label></td><td><input type="text" name="typ_nowy" id="typ_nowy"></td></tr>
					</table>
					<p><input type="submit" name="dodaj" value="Dodaj &#xe809;" class="btn btn-dark with_icons"></p>
					</div></form></fieldset></div>';
			?>

		</div>

		<?php
					if(isset($_SESSION['error']))
					{
						echo '<div class="col-12">'.$_SESSION['error'].'</div>';
						unset($_SESSION['error']);
					}

					if(isset($_SESSION['connect_error']))
					{
						unset($_SESSION['connect_error']);
						echo '<div class="col-12">';
						throw new Exception($connect->error);
						echo '</div>';				
					}

					if(isset($_SESSION['delete_ok']))
					{
						echo '<div class="col-12">';
						echo $_SESSION['delete_ok'];
						unset($_SESSION['delete_ok']);
						echo '</div>';
					}

					if(isset($_SESSION['change_ok']))
					{
						echo '<div class="col-12">';
						echo $_SESSION['change_ok'];
						unset($_SESSION['change_ok']);
						echo '</div>';
					}

					if(isset($_SESSION['adding_ok']))
					{
						echo '<div class="col-12">';
						echo $_SESSION['adding_ok'];
						unset($_SESSION['adding_ok']);
						echo '</div>';
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

<script src="js/jquery-3.3.1.min.js"></script>
<script src="bootstrap/js/popper.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/navbar.js"></script>
<script>
var info_o_usunieciu=function() {
	if($("#razem:checked").val())
		$(".hidden_info").css({"visibility":"hidden"});
	else
		$(".hidden_info").css({"visibility":"visible"});
};

$("#razem").on("click", info_o_usunieciu);

var obecne=$("#for_new").html();
var checked=function() {
	if($("#new:checked").val())
		$("#for_new").html('<input type="text" name="new_type">');
	else
		$("#for_new").html(obecne);
};

$("#new").on("click", checked);
</script>

</body>
</html>