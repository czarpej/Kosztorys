<?php

session_start();

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

		if(isset($_POST['zatwierdz']))
		{
			if($_POST['nazwa']=='' || $_POST['cena']=='' || $_POST['sztuk']=='' || $_POST['data']=='')
			{
				$_SESSION['error']='<p>Uzupełnij wszystkie pola!</p>';
				header('Location: wydatki.php');
				exit();
			}
			else
			{
				if(isset($_POST['typ']))
				{
					if($_POST['typ']!='')
					{
						if($connect->query("INSERT INTO wydatki VALUES('', '".$_SESSION['which_user_id']."', '".$_POST['nazwa']."', '".$_POST['typ']."', '".$_POST['cena']."', '".$_POST['sztuk']."', '".$_POST['data']."')"))
						{
							$_SESSION['add_ok']='<p>Pomyślnie dodano wydatek.</p>';
							$_SESSION['last_expense']=true;
							header('Location: wydatki.php');
							exit();
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: wydatki.php');
							exit();
						}

					}
					else
					{
						$_SESSION['error']='<p>Nie wybrano rodzaju wydatku!</p>';
						header('Location: wydatki.php');
						exit();
					}
				}
				else if(isset($_POST['new_type']))
				{
					if($_POST['new_type']!='')
					{
						if($isset_type=$connect->query("SELECT DISTINCT typ FROM typy WHERE typ='".$_POST['new_type']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
						{
							if($isset_type->num_rows>0)
							{
								if($connect->query("INSERT INTO wydatki VALUES('', '".$_SESSION['which_user_id']."', '".$_POST['nazwa']."', '".$_POST['new_type']."', '".$_POST['cena']."', '".$_POST['sztuk']."', '".$_POST['data']."')"))
								{
									$_SESSION['add_ok']='<p>Pomyślnie dodano wydatek.</p>';
									$_SESSION['last_expense']=true;
				  					header('Location: wydatki.php');
				  					exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: wydatki.php');
									exit();
								}
							}
							else
							{
								if(($connect->query("INSERT INTO wydatki VALUES('', '".$_SESSION['which_user_id']."', '".$_POST['nazwa']."', '".$_POST['new_type']."', '".$_POST['cena']."', '".$_POST['sztuk']."', '".$_POST['data']."')")) && ($connect->query("INSERT INTO typy VALUES('', '".$_POST['new_type']."', '".$_SESSION['which_user_id']."')")))
								{
									$_SESSION['add_ok']='<p>Pomyślnie dodano wydatek.</p>';
									$_SESSION['last_expense']=true;
				  					header('Location: wydatki.php');
				  					exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: wydatki.php');
									exit();
								}
							}
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: wydatki.php');
							exit();
						}

					}
					else
					{
						$_SESSION['error']='<p>Nie wprowadzono nowego rodzaju!</p>';
						header('Location: wydatki.php');
						exit();
					}
				}
				else
				{
					$_SESSION['error']='<p>Brak rodzaju wydatku!</p>';
					header('Location: wydatki.php');
					exit();
				}
			}
		}
		else if(isset($_SESSION['delete_now']))
		{
			$usuniety=false;
			for($i=0; $i<$_SESSION['ile_usunac']; $i++)
			{
				if(isset($_SESSION['wydatek'][$i]))
				{
					echo '<div class="col-12"><p>';
					if($connect->query("DELETE FROM wydatki WHERE id_uzytkownika='".$_SESSION['which_user_id']."' AND id='".$_SESSION['wydatek'][$i]."' "))
						$usuniety=true;
					else
					{
						$_SESSION['connect_error']=$connect->error;
						unset($_SESSION['ile_usunac']);
						unset($_SESSION['wydatek']);
						unset($_SESSION['delete_now']);
						header('Location: wydatki.php');
						exit();
					}
					echo '</p></div>';
				}
			}
			if($usuniety==true)
			{
				$_SESSION['delete_ok']='<p>Pomyślnie usunięto wybrane wydatki.</p>';
				unset($_SESSION['ile_usunac']);
				unset($_SESSION['wydatek']);
				unset($_SESSION['delete_now']);
				header('Location: wydatki.php');
				exit();
			}
			else
			{
				$_SESSION['error']='<p>Nie wybrano wydatków do usunięcia!<p>';
				unset($_SESSION['ile_usunac']);
				unset($_SESSION['wydatek']);
				unset($_SESSION['delete_now']);
				header('Location: wydatki.php');
				exit();
			}
		}
		else
		{
			header('Location: wydatki.php');
			exit();
		}
	}
}
catch(Exception $e)
{
	echo '<p>Serwer niedostępny.</p>';
}