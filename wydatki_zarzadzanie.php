<?php

session_start();

if(!isset($_SESSION['which_user']))
{
	header('Location: index.php');
	exit();
}

if(isset($_SESSION['manage']))
{
	;
}
else
{
	header('Location: wydatki.php');
	exit();
}

if(isset($_POST['canel']))
{
	unset($_SESSION['manage']);
	unset($_SESSION['ktory_wydatek_id']);
	header('Location: wydatki.php');
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
		<li><a class="with_icons"><form method="post"><input type="submit" name="canel" value="Anuluj &#xe80b;"></form></a></li>
		<li><a class="with_icons"><form method="post" action="logout.php"><input type="submit" name="logout" value="Wyloguj  &#xe800;"></form></a></li>
	</ol>
</nav>

<section class="wydatki_zarzadzanie">
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

					if($wydatek=$connect->query("SELECT * FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' AND id='".$_SESSION['ktory_wydatek_id']."' "))
					{
						if($wydatek->num_rows>0)
						{
							$wydatek_results=$wydatek->fetch_assoc();
							echo '<h3>Wybrany wydatek: '.$wydatek_results['wydatek'].'</h3>
								<div class="table-responsive"><table class="table table-bordered">
								<tr><th>Rodzaj</th><th>Cena</th><th>Sztuk</th><th>Data</th></tr>
								<tr><td>'.$wydatek_results['typ'].'</td><td>'.$wydatek_results['cena'].'</td><td>'.$wydatek_results['sztuk'].'</td><td>'.$wydatek_results['data_wydatku'].'</td></tr>
								</table></div>
								<form method="post"><p><input type="button" class="btn btn-dark hidding with_icons" data-toggle="collapse" data-target="#edycja" value="Edycja danych wydatku &#xe806;"> <input type="submit" class="btn btn-dark with_icons" name="delete" value="Usuń wydatek &#xe80a;"></p></form>';

								echo '<div id="edycja" class="collapse">
								<fieldset><legend>Edycja danych wydatku</legend><form method="post"><div class="table-responsive"><table class="table" style="text-align: left;">
								<tr><td><label for="nazwa">Nazwa wydatku</label></td><td><input type="text" id="nazwa" name="nazwa" value="'.$wydatek_results['wydatek'].'"></td></tr>
							    <tr><td><label for="typ">Typ wydatku</label> <span class="nowy_typ"><input type="checkbox" val="cos" id="new"><label for="new"> (nowy)</label></span></td><td id="for_new">';
							    if($type=$connect->query("SELECT DISTINCT typ FROM typy WHERE id_uzytkownika='".$_SESSION['which_user_id']."' "))
							    {
							    	if($type->num_rows>0)
							    	{
							    		echo '<select name="typ" id="typ"><option selected hidden>'.$wydatek_results['typ'].'</option>';
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
							    <tr><td><label for="cena">Cena</label></td><td><input type="number" id="cena" name="cena" step="any" value="'.$wydatek_results['cena'].'"> zł</td></tr>
							    <tr><td><label for="sztuk">Ilość sztuk</td><td><input type="number" id="sztuk" name="sztuk" value="'.$wydatek_results['sztuk'].'"></td></tr>
							    <tr><td><label for="data">Data wydatku</td><td><input type="date" id="data" name="data" value="'.$wydatek_results['data_wydatku'].'"></td></tr>
							    </table>
							    <p><input type="submit" name="zatwierdz" value="Zatwierdź" class="btn btn-dark"></p>
							    </div>
							    </form></fieldset></div>';
						}
						else
							echo '<p>Błąd! Nie odnaleziono wybranego wydatku.</p>';
					}
					else
						throw new Exception($connect->error);
			?>

		</div>

		<?php
					if(isset($_SESSION['update_ok']))
					{
						echo '<div class="col-12">'.$_SESSION['update_ok'].'</div>';
						unset($_SESSION['update_ok']);
					}

					if(isset($_POST['zatwierdz']))
					{
						if($_POST['nazwa']=='' || $_POST['cena']=='' || $_POST['sztuk']=='' || $_POST['data']=='')
							echo '<div class="col-12"><p>Uzupełnij wszystkie dane!</p></div>';
						else if(isset($_POST['typ']))
						{
							if($_POST['typ']=='')
								echo '<div class="col-12"><p>Nie wybrano rodzaju wydatku!</p></div>';
							else
							{
								if($connect->query("UPDATE wydatki SET wydatek='".$_POST['nazwa']."', typ='".$_POST['typ']."', cena='".$_POST['cena']."', sztuk='".$_POST['sztuk']."', data_wydatku='".$_POST['data']."' WHERE id_uzytkownika='".$_SESSION['which_user_id']."' AND id='".$_SESSION['ktory_wydatek_id']."' "))
								{
									$_SESSION['update_ok']='<p>Pomyślnie zaktualizowano dane wydatku.</p>';
									header('Location: wydatki_zarzadzanie.php');
									exit();
								}
								else
									throw new Exception($connect->error);
							}
						}
						else if(isset($_POST['new_type']))
						{
							if($_POST['new_type']=='')
								echo '<div class="col-12"><p>Nie wprowadzono nowego rodzaju!</p></div>';
							else
							{
								if(($connect->query("INSERT INTO typy VALUES('', '".$_POST['new_type']."', '".$_SESSION['which_user_id']."')")) && ($connect->query("UPDATE wydatki SET wydatek='".$_POST['nazwa']."', typ='".$_POST['new_type']."', cena='".$_POST['cena']."', sztuk='".$_POST['sztuk']."', data_wydatku='".$_POST['data']."' WHERE id_uzytkownika='".$_SESSION['which_user_id']."' AND id='".$_SESSION['ktory_wydatek_id']."' ")))
								{
									$_SESSION['update_ok']='<p>Pomyślnie dodano nowy typ oraz zaktualizowano dane wydatku.</p>';
									header('Location: wydatki_zarzadzanie.php');
									exit();
								}
								else
									throw new Exception($connect->error);
							}
						}
					}

					if(isset($_POST['delete']))
					{
						if($connect->query("DELETE FROM wydatki WHERE id='".$_SESSION['ktory_wydatek_id']."' "))
						{
							$_SESSION['delete_ok']='<p>Wydatek usunięto pomyślnie.</p>';
							unset($_SESSION['manage']);
							unset($_SESSION['ktory_wydatek_id']);
							header('Location: wydatki.php');
							exit();
						}
						else
							throw new Exception($connect->error);
					}	
				}
			}
			catch(Exception $e)
			{
				echo '<div class="col-12"><p>Serwer niedostępny.</p></div>';
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