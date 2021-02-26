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
		header('Location: kosztorys.php');
		exit();
	}
	else
	{
		$connect->query("SET CHARSET utf8");
		$connect->query("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");

		if(isset($_POST['zatwierdz']))
		{
			if($_POST['nazwa']=='' || $_POST['cena']=='' || $_POST['sztuk']=='' || $_POST['data']=='')
			{
				$_SESSION['error']='<p>Uzupełnij wszystkie pola!</p>';
				header('Location: kosztorys.php');
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
							header('Location: kosztorys.php');
							exit();
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: kosztorys.php');
							exit();
						}

					}
					else
					{
						$_SESSION['error']='<p>Nie wybrano rodzaju wydatku!</p>';
						header('Location: kosztorys.php');
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
				  					header('Location: kosztorys.php');
				  					exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: kosztorys.php');
									exit();
								}
							}
							else
							{
								if(($connect->query("INSERT INTO wydatki VALUES('', '".$_SESSION['which_user_id']."', '".$_POST['nazwa']."', '".$_POST['new_type']."', '".$_POST['cena']."', '".$_POST['sztuk']."', '".$_POST['data']."')")) && ($connect->query("INSERT INTO typy VALUES('', '".$_POST['new_type']."', '".$_SESSION['which_user_id']."')")))
								{
									$_SESSION['add_ok']='<p>Pomyślnie dodano wydatek.</p>';
									$_SESSION['last_expense']=true;
				  					header('Location: kosztorys.php');
				  					exit();
								}
								else
								{
									$_SESSION['connect_error']=$connect->error;
									header('Location: kosztorys.php');
									exit();
								}
							}
						}
						else
						{
							$_SESSION['connect_error']=$connect->error;
							header('Location: kosztorys.php');
							exit();
						}

					}
					else
					{
						$_SESSION['error']='<p>Nie wprowadzono nowego rodzaju!</p>';
						header('Location: kosztorys.php');
						exit();
					}
				}
				else
				{
					$_SESSION['error']='<p>Brak rodzaju wydatku!</p>';
					header('Location: kosztorys.php');
					exit();
				}
			}
		}

		if(isset($_POST['back']))
		{
			if(!isset($_POST['last']) || $_POST['last']=='')
			{
				$_SESSION['error']='<p>Nie znaleziono ostatnio wprowadzonego wydatku!</p>';
				header('Location: kosztorys.php');
				exit();
			}
			else
			{
				if($connect->query("DELETE FROM wydatki WHERE id='".$_POST['last']."' "))
			  	{
			  		$_SESSION['back_ok']='<p>Ostatnio wprowadzony wydatek został wycofany.</p>';
			  		unset($_SESSION['last_expense']);
			  		header('Location: kosztorys.php');
			  		exit();
			  	}
			  	else
			  	{
					$_SESSION['connect_error']=$connect->error;
					header('Location: kosztorys.php');
					exit();
				}
			}
		}
	}
}
catch(Exception $e)
{
	echo '<p>Serwer niedostępny.</p>';
}