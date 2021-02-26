<?php

session_start();

if(isset($_POST['zarejestruj']))
{
	require_once 'dbconnect.php';
	$connect = new mysqli($address, $db_login, $db_password, $db_name);

	try
	{
		if($connect->connect_errno!=0)
			throw new Exception(mysqli_connect_errno());
		else
		{
			$connect->query("SET CHARSET utf8");
			$connect->query("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");

			$login=$_POST['login'];
			$password=$_POST['password1'];
			$repeat_password=$_POST['password2'];

			if($login=='' || $password=='' || $repeat_password=='')
			{
				$_SESSION['brak']='<p>Uzupełnij wszystkie pola!</p>';
				header('Location: rejestracja.php');
				exit();
			}
			else if($isset_login=$connect->query("SELECT login FROM uzytkownicy WHERE login='".$login."' "))
			{
				if($isset_login->num_rows>0)
				{
					$_SESSION['isset_login']='<p>Wybrany login jest już zajęty!</p>';
					header('Location: rejestracja.php');
					exit();
				}
				else
				{
					if($password!=$repeat_password)
					{
						$_SESSION['wrong_password']='<p>Wprowadzone hasła nie są identyczne!</p>';
						header('Location: rejestracja.php');
						exit();
					}
					else
					{
						$password_hash=password_hash($password, PASSWORD_DEFAULT);
						if($connect->query("INSERT INTO uzytkownicy VALUES('', '".$login."', '".$password_hash."')"))
						{
							$_SESSION['register_good']='<p>Rejestracja zakończona powodzeniem. Można się zalogować.</p>';
							header('Location: index.php');
							exit();
						}
					}
				}
			}
			else
				throw new Exception($connect->error);

		}
	}
	catch(Exception $e)
	{
		echo '<p>Błąd, serwer niedostępny.</p>';
	}
}
else
{
	header('Location: rejestracja.php');
	exit();
}