<?php

session_start();

if(!isset($_SESSION['which_user']))
{
	header('Location: index.php');
	exit();
}

if(isset($_POST['manage']))
{
	$_SESSION['manage']=true;
	$_SESSION['ktory_wydatek_id']=$_POST['ktory_wydatek_id'];
	header('Location: wydatki_zarzadzanie.php');
	exit();
}
else if(isset($_SESSION['manage']))
{
	header('Location: wydatki_zarzadzanie.php');
	exit();
}

if(isset($_POST['delete']))
{
	$_SESSION['wydatek']=Array();
	for($i=0; $i<$_SESSION['ile_usunac']; $i++)
	{
		if(isset($_POST['wydatek'.$i]))
		{
			$_SESSION['wydatek'][$i]=$_POST['wydatek'.$i];
		}
	}
	$_SESSION['delete_now']=true;
	header('Location: wydatki_dzialania.php');
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

<section class="wydatki">
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

					echo '<center><input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#dodawanie" value="Nowy wydatek &#xf217;"> <input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#manage" value="Edycja i usuwanie &#xe806;"></center>';

					echo '<center><div id="manage" class="collapse"><hr><input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#automatic" value="Wyszukanie po nazwie z listy"> <input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#manual" value="Ręczne wyszukanie"></div></center>';

					echo '<div id="dodawanie" class="collapse"><fieldset><legend>Nowy wydatek</legend><form method="post" action="wydatki_dzialania.php">
					    <div class="table-responsive">
					    <table class="table" style="text-align: left;">
					    <tr><td><label for="nazwa">Nazwa wydatku</label></td><td><input type="text" id="nazwa" name="nazwa"></td></tr>
					    <tr><td><label for="typ">Typ wydatku</label> <span class="nowy_typ"><input type="checkbox" val="cos" id="new"><label for="new"> (nowy)</label></span></td><td id="for_new">';
					    if($type=$connect->query("SELECT DISTINCT typ FROM typy WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					    {
					    	if($type->num_rows>0)
					    	{
					    		echo '<select name="typ" id="typ"><option selected disabled hidden style="display: none" value=""> -- wybierz typ -- </option>';
					    		while($type_results=$type->fetch_row())
					    			echo '<option>'.$type_results[0].'</option>';
					    		echo '</select>';
					    	}
					    	else
					    		echo 'Brak zapisanych typów';			    	
					    }
					    else
					    	throw new Exception($connect->error);
					    echo '</td></tr>
					    <tr><td><label for="cena">Cena</label></td><td><input type="number" id="cena" name="cena" step="any"> zł</td></tr>
					    <tr><td><label for="sztuk">Ilość sztuk</td><td><input type="number" id="sztuk" name="sztuk"</td></tr>
					    <tr><td><label for="data">Data wydatku</td><td><input type="date" id="data" name="data" value="'.date("Y-m-d").'"></td></tr>
					    </table>
					    <p><input type="submit" name="zatwierdz" value="Zatwierdź" class="btn btn-dark"></p>
					    </div>
					    </form></fieldset></div>

					<div id="automatic" class="collapse"><fieldset><legend>Wyszukanie po nazwie z listy</legend><form method="post"><div class="table-responsive">
					<table class="table">
					<tr><td><label for="nazwa">Wybierz nazwę</label></td><td>';
					if($nazwy=$connect->query("SELECT DISTINCT wydatek FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($nazwy->num_rows>0)
						{
							echo '<select name="nazwa" id="nazwa"><option selected disabled hidden style="display: none" value=""> -- wybierz nazwę -- </option>';
							while($nazwy_results=$nazwy->fetch_row())
								echo '<option>'.$nazwy_results[0].'</option>';
							echo '</select>';
						}
						else
							echo 'Brak zapisanych wydatków.';
					}
					else
						throw new Exception($connect->error);
					echo '</td></tr></table>
					<p><input type="submit" name="szukaj" value="Wyszukaj &#xe801;" class="btn btn-dark with_icons"></p>
					</div></form></fieldset></div>';

					echo '<div id="manual" class="collapse"><fieldset><legend>Ręczne wyszukanie</legend><form method="post">
					<div class="table-responsive"><table class="table">
					<tr><td><label for="reczna_nazwa">Wprowadź szukaną frazę lub litery</label></td>
						<td><input type="text" name="reczna_nazwa" id="reczna_nazwa"></td></tr>
					</table>
					<p><input type="submit" name="manual_find" value="Wyszukaj &#xe801;" class="btn btn-dark with_icons"></p>
					</div></form></fieldset></div>';
			?>

		</div>

		<?php
					if(isset($_SESSION['connect_error']))
					{
						unset($_SESSION['connect_error']);
						echo '<div class="col-12">';
						throw new Exception($connect->error);
						echo '</div>';				
					}

					if(isset($_SESSION['error']))
					{
						echo '<div class="col-12">'.$_SESSION['error'].'</div>';
						unset($_SESSION['error']);
					}

					if(isset($_SESSION['delete_ok']))
					{
						echo '<div class="col-12">'.$_SESSION['delete_ok'].'</div>';
						unset($_SESSION['delete_ok']);
					}

					if(isset($_SESSION['add_ok']))
					{
						echo '<div class="col-12">'.$_SESSION['add_ok'].'</div>';
						unset($_SESSION['add_ok']);
					}
					
					if(isset($_POST['szukaj']))
					{
						echo '<div class="col-12"><p>';
						if(isset($_POST['nazwa']))
						{
							if($wyszukane=$connect->query("SELECT * FROM wydatki WHERE wydatek='".$_POST['nazwa']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
							{
								if($wyszukane->num_rows>0)
								{
									echo '<h4>Wyniki wyszukań dla wydatku "'.$_POST['nazwa'].'":</h4>
									<div class="table-responsive"><form method="post"><table class="table table-bordered">
									<tr><th>Rodzaj</th><th>Cena</th><th>Sztuk</th><th>Data</th><th>Zarządzanie</th><th>Do usunięcia</th></tr>';
									$cena=0;
									$_SESSION['ile_usunac']=0;
									while($wyszukane_results=$wyszukane->fetch_assoc())
									{
										echo '<tr><form method="post"><td>'.$wyszukane_results['typ'].'</td><td>'.$wyszukane_results['cena'].' zł</td><td>'.$wyszukane_results['sztuk'].'</td><td>'.$wyszukane_results['data_wydatku'].'</td><td><input type="hidden" name="ktory_wydatek_id" value="'.$wyszukane_results['id'].'"><input type="submit" name="manage" value="Zarządzaj" class="btn btn-dark"></td></form><td><input type="checkbox" name="wydatek'.$_SESSION['ile_usunac'].'" value="'.$wyszukane_results['id'].'"</td></tr>';
										$cena+=$wyszukane_results['cena']*$wyszukane_results['sztuk'];
										$_SESSION['ile_usunac']++;
									}
									echo '<tr><td colspan="6"><center>Łącznie na ten wydatek wydano: '.$cena.' zł</center></td></tr></table>
									<p><input type="submit" name="delete" value="Usuń wybrane &#xe80a;" class="btn btn-dark with_icons"></p></form></div>';
								}
								else
									echo 'Błąd, nie znaleziono zapisów dla tego wydatku.';
							}
							else
								throw new Exception($connect->error);
						}
						else
							echo 'Nic nie wybrano!';
						echo '</p></div>';
					}

					if(isset($_POST['manual_find']))
					{
						echo '<div class="col-12"><p>';
						if($_POST['reczna_nazwa']!='')
						{
							if($wyszukane_recznie=$connect->query("SELECT * FROM wydatki WHERE wydatek LIKE '%".$_POST['reczna_nazwa']."%' AND id_uzytkownika='".$_SESSION['which_user_id']."' ORDER BY data_wydatku DESC"))
							{
								if($wyszukane_recznie->num_rows>0)
								{
									echo '<h4>Wyniki wyszukań dla frazy "'.$_POST['reczna_nazwa'].'":</h4>
									<div class="table-responsive"><table class="table table-bordered">
									<tr><th>Wydatek</th><th>Cena</th><th>Sztuk</th><th>Data</th></tr>';
									while($wyszukane_recznie_results=$wyszukane_recznie->fetch_assoc())
										echo '<tr><td>'.$wyszukane_recznie_results['wydatek'].'</td><td>'.$wyszukane_recznie_results['cena'].' zł</td><td>'.$wyszukane_recznie_results['sztuk'].'</td><td>'.$wyszukane_recznie_results['data_wydatku'].'</td></tr>';
									echo '</table></div>';
								}
								else
									echo 'Brak rezultatów wyszukania dla tej frazy.';
							}
							else
								throw new Exception($connect->error);
						}
						else
							echo 'Nic nie wprowadzono!';
						echo '</p></div>';
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