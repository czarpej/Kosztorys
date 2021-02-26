<?php

session_start();

require_once 'dbconnect.php';
mysqli_report(MYSQLI_REPORT_STRICT);
try
{
	$connect=new mysqli($address, $db_login, $db_password, $db_name);
	if($connect->connect_errno!=0)
	{
		$_SESSION['connect_error']=$connect->error;
		header('Location: typy.php');
		exit();
	}
	else
	{
		$connect->query("SET CHARSET utf8");
		$connect->query("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");

		if(isset($_POST['delete']))
		{
			if(isset($_POST['typ']))
			{
				if(isset($_POST['razem']))
				{
					if(($connect->query("DELETE FROM wydatki WHERE typ='".$_POST['typ']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")) && ($connect->query("DELETE FROM typy WHERE typ='".$_POST['typ']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
					{
						$_SESSION['delete_ok']='<p>Pomyślnie usunięto typ wydatku wraz oraz wydatki, do których przypisany był ten typ.</p>';
						header('Location: typy.php');
						exit();
					}
					else
					{
						$_SESSION['connect_error']=$connect->error;
						header('Location: typy.php');
						exit();
					}
				}
				else
				{
					if($isset_inne=$connect->query("SELECT typ FROM typy WHERE typ='Inne' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($isset_inne->num_rows>0)
						{
							if(($connect->query("UPDATE wydatki SET typ='Inne' WHERE typ='".$_POST['typ']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")) && ($connect->query("DELETE FROM typy WHERE typ='".$_POST['typ']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
							{
								$_SESSION['delete_ok']='<p>Pomyślnie usunięto typ wydatku. Wydatki, do których przypisany był ten typ wydatku zostały zmienione na typ "Inne".</p>';
								header('Location: typy.php');
								exit();
							}
							else
							{
								$_SESSION['connect_error']=$connect->error;
								header('Location: typy.php');
								exit();
							}
						}
						else
						{
							if($connect->query("INSERT INTO typy VALUES('', 'Inne', '".$_SESSION['which_user_id']."')"))
							{
								if(($connect->query("UPDATE wydatki SET typ='Inne' WHERE typ='".$_POST['typ']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")) && ($connect->query("DELETE FROM typy WHERE typ='".$_POST['typ']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
								{
									$_SESSION['delete_ok']='<p>Pomyślnie usunięto typ wydatku. Wydatki, do których przypisany był ten typ wydatku zostały zmienione na typ "Inne".</p>';
									header('Location: typy.php');
									exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: typy.php');
									exit();
								}
							}
							else
							{
								$_SESSION['connect_error']=$connect->error;
								header('Location: typy.php');
								exit();
							}
						}
					}
				}
			}
			else
			{
				$_SESSION['error']='<p>Nie wybrano typu wydatku!</p>';
				header('Location: typy.php');
				exit();
			}
		}	
		else if(isset($_POST['change']))
		{
			if(!isset($_POST['typ_do_zmiany']))
			{
				$_SESSION['error']='<p>Nie wybrano typu do zmiany!</p>';
				header('Location: typy.php');
				exit();
			}
			else
			{
				if(isset($_POST['typ_po_zmianie']))
				{
					if(isset($_POST['delete_type']))
					{
						if(($connect->query("UPDATE wydatki SET typ='".$_POST['typ_po_zmianie']."' WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")) && ($connect->query("DELETE FROM typy WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
						{
							$_SESSION['change_ok']='<p>Pomyślnie zmieniono typ wydatków na wybrany.<br>Usunięto wskazany typ.</p>';
							header('Location: typy.php');
							exit();
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: typy.php');
							exit();
						}
					}
					else
					{
						if($connect->query("UPDATE wydatki SET typ='".$_POST['typ_po_zmianie']."' WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
						{
							$_SESSION['change_ok']='<p>Pomyślnie zmieniono typ wydatków na wybrany.</p>';
							header('Location: typy.php');
							exit();
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: typy.php');
							exit();
						}
					}
				}
				else if(isset($_POST['new_type']))
				{
					if($isset_type=$connect->query("SELECT typ FROM typy WHERE typ='".$_POST['new_type']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
					{
						if($isset_type->num_rows>0)
						{
							if(isset($_POST['delete_type']))
							{
								if(($connect->query("UPDATE wydatki SET typ='".$_POST['new_type']."' WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")) && ($connect->query("DELETE FROM typy WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
								{
									$_SESSION['change_ok']='<p>Wprowadzony typ już istnieje, więc nie został ponownie dodany.<br>Pomyślnie zmieniono typ wydatków na wybrany.<br>Usunięto wskazany typ.</p>';
									header('Location: typy.php');
									exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: typy.php');
									exit();
								}
							}
							else
							{
								if($connect->query("UPDATE wydatki SET typ='".$_POST['new_type']."' WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
								{
									$_SESSION['change_ok']='<p>Wprowadzony typ już istnieje, więc nie został ponownie dodany.<br>Pomyślnie zmieniono typ wydatków na wybrany.</p>';
									header('Location: typy.php');
									exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: typy.php');
									exit();
								}
							}
						}
						else
						{
							if(isset($_POST['delete_type']))
							{
								if(($connect->query("INSERT INTO typy VALUES('', '".$_POST['new_type']."', '".$_SESSION['which_user_id']."')")) && ($connect->query("UPDATE wydatki SET typ='".$_POST['new_type']."' WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")) && ($connect->query("DELETE FROM typy WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
								{
									$_SESSION['change_ok']='<p>Dodano nowy typ wydatków.<br>Pomyślnie zmieniono typ wydatków na wprowadzony.<br>Usunięto wskazany typ.</p>';
									header('Location: typy.php');
									exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: typy.php');
									exit();
								}
							}
							else
							{
								if(($connect->query("INSERT INTO typy VALUES('', '".$_POST['new_type']."', '".$_SESSION['which_user_id']."')")) && ($connect->query("UPDATE wydatki SET typ='".$_POST['new_type']."' WHERE typ='".$_POST['typ_do_zmiany']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' ")))
								{
									$_SESSION['change_ok']='<p>Dodano nowy typ wydatków.<br>Pomyślnie zmieniono typ wydatków na wprowadzony.</p>';
									header('Location: typy.php');
									exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: typy.php');
									exit();
								}
							}
						}
					}
					else
					{
						$_SESSION['connect_error']=$connect->error;
						header('Location: typy.php');
						exit();
					}
				}
				else 
				{
					$_SESSION['error']='<p>Nie podano typu, na który wydatki mają zostać zmienione!<p>';
					header('Location: typy.php');
					exit();
				}
			}
		}	
		else if(isset($_POST['dodaj']))
		{
			if($_POST['typ_nowy']!='')
			{
				if($isset_type=$connect->query("SELECT typ FROM typy WHERE typ='".$_POST['typ_nowy']."' AND id_uzytkownika='".$_SESSION['which_user_id']."' "))
				{
					if($isset_type->num_rows>0)
					{
							$_SESSION['error']='<p>Wprowadzony typ wydatku nie został dodany, ponieważ jest już przypisany do tego konta.</p>';
							header('Location: typy.php');
							exit();
					}
					else
					{
						if($connect->query("INSERT INTO typy VALUES('', '".$_POST['typ_nowy']."', '".$_SESSION['which_user_id']."')"))
						{
							$_SESSION['adding_ok']='<p>Pomyślnie dodano nowy typ wydatku.</p>';
							header('Location: typy.php');
							exit();
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: typy.php');
							exit();
						}
					}
				}
				else
				{
					$_SESSION['connect_error']=$connect->error;
					header('Location: typy.php');
					exit();
				}
			}
			else
			{
				$_SESSION['error']='<p>Nie podano nazwy dla nowego typu wydatku!</p>';
				header('Location: typy.php');
				exit();
			}
		} 
		else
		{
			header('Location: typy.php');
			exit();
		}
	}
}
catch(Exception $e)
{
	echo '<p>Serwer niedostępny.</p>';
}