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
	.btn
	{
		vertical-align: top;
	}
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

					echo '<h2>Wydatki</h2><div class="table-responsive">';
					$today=0;
					$in_this_month=0;
					$in_this_year=0;
					$all=0;
					if($how_spend=$connect->query("SELECT cena, sztuk, data_wydatku FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($how_spend->num_rows>0)
						{
							while($how_spend_results=$how_spend->fetch_assoc())
							{
								if($how_spend_results['data_wydatku']==date('Y-m-d'))
								{
									$today+=$how_spend_results['cena']*$how_spend_results['sztuk'];
									$in_this_month+=$how_spend_results['cena']*$how_spend_results['sztuk'];
									$in_this_year+=$how_spend_results['cena']*$how_spend_results['sztuk'];
									$all+=$how_spend_results['cena']*$how_spend_results['sztuk'];
								}
								else if(substr($how_spend_results['data_wydatku'], 5, 2)==date('m'))
								{
									$in_this_month+=$how_spend_results['cena']*$how_spend_results['sztuk'];
									$in_this_year+=$how_spend_results['cena']*$how_spend_results['sztuk'];
									$all+=$how_spend_results['cena']*$how_spend_results['sztuk'];
								}
								else if(substr($how_spend_results['data_wydatku'], 0, 4)==date('Y'))
								{
									$in_this_year+=$how_spend_results['cena']*$how_spend_results['sztuk'];
									$all+=$how_spend_results['cena']*$how_spend_results['sztuk'];
								}
								else
									$all+=$how_spend_results['cena']*$how_spend_results['sztuk'];
							}
						}
					}
					else
						throw new Exception($connect->error);

					echo '<table class="table">
							<tr><td>Dzisiaj wydano: ';
							echo '</td><td>'.$today.' zł</td></tr>
							<tr><td>W tym miesiącu wydano:</td><td>'.$in_this_month.' zł</td></tr>
							<tr><td>W tym roku wydano:</td><td>'.$in_this_year.' zł</td></tr>
							<tr><td>Łącznie wydano:</td><td>'.$all.' zł</td></tr>
							</table></div>';

					echo '<form method="post" action="kosztorys_dzialania.php"><input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#wydatek" value="Szybkie wprowadzenie &#xf217;"> ';
					if($today>0)
						echo '<input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#dzisiaj" value="Dzisiejsze wydatki &#xe807;">';

						if(isset($_SESSION['last_expense']))
						{
							if($last_expense=$connect->query("SELECT id FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
							{
								if($last_expense->num_rows>0)
								{
									$i=0;
									while($last_expense_results=$last_expense->fetch_row())
										$i=$last_expense_results[0];
									echo ' <input type="hidden" name="last" value="'.$i.'"><input type="submit" class="btn btn-dark with_icons" value="Cofnij ostatni wydatek &#xe80c;" name="back">';
								}
							}
							else
								throw new Exception($connect->error);
						}

					echo '</form>';
					echo '<div id="wydatek" class="collapse">
					    <form method="post" action="kosztorys_dzialania.php">
					    <div class="table-responsive">
					    <table class="table">
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
					    </form>
					  </div>';

					  echo '<div id="dzisiaj" class="collapse">';
					  if($dzisiaj=$connect->query("SELECT * FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' AND data_wydatku='".date('Y-m-d')."' "))
					  {
					  	if($dzisiaj->num_rows>0)
					  	{
					  		echo '<div class="table-responsive"><table class="table table-bordered">
					  		<tr><th>Wydatek</th><th>Typ</th><th>Cena</th><th>Sztuk</th><th>Łącznie</th></tr>';
					  		$lacznie=0;
					  		while($dzisiaj_results=$dzisiaj->fetch_assoc())
					  		{
					  			echo '<tr><td>'.$dzisiaj_results['wydatek'].'</td><td>'.$dzisiaj_results['typ'].'</td><td>'.$dzisiaj_results['cena'].' zł</td><td>'.$dzisiaj_results['sztuk'].'</td><td>'.($dzisiaj_results['cena']*$dzisiaj_results['sztuk']).' zł</td></tr>';
					  			$lacznie+=$dzisiaj_results['cena']*$dzisiaj_results['sztuk'];
					  		}
					  		echo '<tr><td colspan="5">Łączny wydatek: '.$lacznie.' zł</td></tr></table></div>';
					  	}
					  	else
					  		echo 'Dzisiaj nic nie wydano.';
					  }
					  else
					  	throw new Exception($connect->error);
					  echo '</div>';
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

					if(isset($_SESSION['add_ok']))
					{
						echo '<div class="col-12">'.$_SESSION['add_ok'].'</div>';
						unset($_SESSION['add_ok']);
					}

					if(isset($_SESSION['back_ok']))
					{
						echo '<div class="col-12">'.$_SESSION['back_ok'].'</div>';
						unset($_SESSION['back_ok']);
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