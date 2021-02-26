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


<section class="sort_data">
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

					echo '<center><input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#wedlug_daty" value="Sortowanie według daty"> <input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#wedlug_miesiaca" value="Sortowanie według miesiąca"></center>';

					echo '<div id="wedlug_daty" class="collapse"><fieldset><legend>Sortowanie według daty</legend><form method="post">
					<div class="table-responsive"><table class="table">
					<tr><td><label for="data">Wybierz datę</label></td><td>';
					if($nazwy=$connect->query("SELECT DISTINCT data_wydatku FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($nazwy->num_rows>0)
						{
							echo '<select name="data" id="data"><option selected disabled hidden style="display: none" value=""> -- wybierz datę -- </option>';
							while($nazwy_results=$nazwy->fetch_row())
								echo '<option>'.$nazwy_results[0].'</option>';
							echo '<option>Wszystko</option></select>';
						}
						else
							echo 'Brak zapisanych wydatków.';
					}
					else
						throw new Exception($connect->error);
					echo '</td></tr></table>
					<p><input type="submit" name="szukaj" value="Wyszukaj &#xe801;" class="btn btn-dark with_icons"></p>
					</div></form></fieldset></div>

					<div id="wedlug_miesiaca" class="collapse"><fieldset><legend>Sortowanie według miesiąca</legend><form method="post">
					<div class="table-responsive"><table class="table">
					<tr><td><label for="month">Wybierz miesiąc:</label></td><td>';
					if($miesiace=$connect->query("SELECT DISTINCT data_wydatku FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($miesiace->num_rows>0)
						{
							$help_tab=Array();
							$i=0;
							echo '<select name="month" id="month"><option selected disabled hidden style="display: none" value=""> -- wybierz miesiąc -- </option>';
							while($miesiace_results=$miesiace->fetch_row())
							{
								if(@$help_tab[$i-1]!=substr($miesiace_results[0], 0, 7))
									echo '<option>'.substr($miesiace_results[0], 0, 7).'</option>';
								$help_tab[$i]=substr($miesiace_results[0], 0, 7);
								$i++;
							}
							echo '</select>';
						}
						else
							echo 'Nie znaleziono zapisanych wydatków';
					}
					else
						throw new Exception($connect->error);
					echo '</td><//tr></table>
					<p><input type="submit" name="szukaj_miesiac" value="Wyszukaj &#xe801;" class="btn btn-dark with_icons"></p>
					</div></form></fieldset></div>';
			?>

		</div>

		<?php
					if(isset($_POST['szukaj']))
					{
						echo '<div class="col-12"><p>';
						if(isset($_POST['data']))
						{
							if($_POST['data']=='Wszystko')
							{
								if($wyszukane=$connect->query("SELECT * FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
								{
									if($wyszukane->num_rows>0)
									{
										echo '<h4>Wyniki wyszukań dla wszystkich dat:</h4>
										<div class="table-responsive"><table class="table table-bordered">
										<tr><th>Wydatek</th><th>Typ</th><th>Cena</th><th>Sztuk</th></tr>';
										$help_tab=Array();
										$i=0;
										$cena=0;
										$wyszukane_results=$wyszukane->fetch_assoc();
										$help_tab[$i]=$wyszukane_results['data_wydatku'];
										echo '<tr><td colspan="4"><center>'.$wyszukane_results['data_wydatku'].'</center></td></tr>';
										echo '<tr><td>'.$wyszukane_results['wydatek'].'</td><td>'.$wyszukane_results['typ'].'</td><td>'.$wyszukane_results['cena'].' zł</td><td>'.$wyszukane_results['sztuk'].'</td></tr>';
										$cena+=$wyszukane_results['cena']*$wyszukane_results['sztuk'];
										$i++;
										while($wyszukane_results=$wyszukane->fetch_assoc())
										{
											$help_tab[$i]=$wyszukane_results['data_wydatku'];
											if($help_tab[$i-1]!=$wyszukane_results['data_wydatku'])
											{
												echo '<tr><td colspan="4"><center>Łącznie w tym dniu wydano: '.$cena.' zł</center></td></tr>';
												$cena=0;
												echo '<tr><td colspan="4"></td></tr>';
												echo '<tr><td colspan="4"><center>'.$wyszukane_results['data_wydatku'].'</center></td></tr>';
											}
											echo '<tr><td>'.$wyszukane_results['wydatek'].'</td><td>'.$wyszukane_results['typ'].'</td><td>'.$wyszukane_results['cena'].' zł</td><td>'.$wyszukane_results['sztuk'].'</td></tr>';
											$i++;
											$cena+=$wyszukane_results['cena']*$wyszukane_results['sztuk'];
										}
										echo '<tr><td colspan="4"><center>Łącznie w tym dniu wydano: '.$cena.' zł</center></td></tr></table></div>';
									}
									else
										echo 'Błąd, nie znaleziono zapisów dla tej daty.';
								}
								else
									throw new Exception($connect->error);
							}
							else
							{
								if($wyszukane=$connect->query("SELECT * FROM wydatki WHERE data_wydatku='".$_POST['data']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
								{
									if($wyszukane->num_rows>0)
									{
										echo '<h4>Wyniki wyszukań dla daty "'.$_POST['data'].'":</h4>
										<div class="table-responsive"><table class="table table-bordered">
										<tr><th>Wydatek</th><th>Typ</th><th>Cena</th><th>Sztuk</th></tr>';
										$i=0;
										while($wyszukane_results=$wyszukane->fetch_assoc())
										{
											echo '<tr><td>'.$wyszukane_results['wydatek'].'</td><td>'.$wyszukane_results['typ'].'</td><td>'.$wyszukane_results['cena'].' zł</td><td>'.$wyszukane_results['sztuk'].'</td></tr>';
											$i+=$wyszukane_results['cena']*$wyszukane_results['sztuk'];
										}
										echo '<tr><td colspan="4"><center>Łączny wydatek: '.$i.' zł</center></td></tr></table></div>';
									}
									else
										echo 'Błąd, nie znaleziono zapisów dla tej daty.';
								}
								else
									throw new Exception($connect->error);
							}
						}
						else
							echo 'Nic nie wybrano!';
						echo '</p></div>';
					}

					if(isset($_POST['szukaj_miesiac']))
					{
						echo '<div class="col-12"><p>';
						if(isset($_POST['month']))
						{
							if($miesiac=$connect->query("SELECT * FROM wydatki WHERE data_wydatku LIKE '".$_POST['month']."%' AND id_uzytkownika='".$_SESSION['which_user_id']."' ORDER BY data_wydatku DESC "))
							{
								if($miesiac->num_rows>0)
								{
									echo '<h4>Wyniki wyszukań dla miesiąca: "'.$_POST['month'].'":</h4>
									<div class="table-responsive"><table class="table table-bordered">
									<tr><th>Wydatek</th><th>Typ</th><th>Cena</th><th>Sztuk</th></tr>';
									$help_tab=Array();
									$i=0;
									$cena=0;
									$miesiac_results=$miesiac->fetch_assoc();
									$help_tab[$i]=$miesiac_results['data_wydatku'];
									echo '<tr><td colspan="4"><center>'.$miesiac_results['data_wydatku'].'</center></td></tr>';
									echo '<tr><td>'.$miesiac_results['wydatek'].'</td><td>'.$miesiac_results['typ'].'</td><td>'.$miesiac_results['cena'].' zł</td><td>'.$miesiac_results['sztuk'].'</td></tr>';
									$cena+=$miesiac_results['cena']*$miesiac_results['sztuk'];
									$i++;
									while($miesiac_results=$miesiac->fetch_assoc())
									{
										$help_tab[$i]=$miesiac_results['data_wydatku'];
										if($help_tab[$i-1]!=$miesiac_results['data_wydatku'])
										{
											echo '<tr><td colspan="4"><center>Łącznie w tym dniu wydano: '.$cena.' zł</center></td></tr>';
											$cena=0;
											echo '<tr><td colspan="4"></td></tr>';
											echo '<tr><td colspan="4"><center>'.$miesiac_results['data_wydatku'].'</center></td></tr>';
										}
										echo '<tr><td>'.$miesiac_results['wydatek'].'</td><td>'.$miesiac_results['typ'].'</td><td>'.$miesiac_results['cena'].' zł</td><td>'.$miesiac_results['sztuk'].'</td></tr>';
										$i++;
										$cena+=$miesiac_results['cena']*$miesiac_results['sztuk'];
									}
									echo '<tr><td colspan="4"><center>Łącznie w tym dniu wydano: '.$cena.' zł</center></td></tr></table></div>';
								}
								else
									echo 'Nie znaleziono wydatków dla tego miesiąca.';
							}
							else
								throw new Exception($connect->error);
						}
						else
							echo 'Nic nie wybrano!';
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
<script src="js/width_fieldset.js"></script>

</body>
</html>