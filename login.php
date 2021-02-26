<?php

session_start();

mysqli_report(MYSQLI_REPORT_STRICT);
if(isset($_POST['zaloguj']))
{
	try
	{
		require_once 'dbconnect.php';
		$connect = new mysqli($address, $db_login, $db_password, $db_name);
		if($connect->connect_errno!=0)
			throw new Exception(mysqli_connect_errno());
		else
		{
			$connect->query("SET CHARSET utf8");
			$connect->query("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");

			$login=$_POST['login'];
			$password=$_POST['password'];

			if($login_query=$connect->query(sprintf("SELECT * FROM `uzytkownicy` WHERE `login`='%s' ", mysqli_real_escape_string($connect,$login))))
			{
				if($login_query->num_rows>0)
				{
					$login_results=$login_query->fetch_assoc();
					if(password_verify($password, $login_results['haslo']) || $login_results['haslo']=='')
					{
						$_SESSION['which_user']=$login_results['login'];
						$_SESSION['which_user_id']=$login_results['id'];
						header('Location: kosztorys.php');
						exit();
					}
					else
					{
						$_SESSION['login_error']='<p>Błędne dane logowania!</p>';
						$_SESSION['login']=$login;
						header('Location: index.php');
						exit();
					}
				}
				else
				{
					$_SESSION['login_error']='<p>Błędne dane logowania!</p>';
					$_SESSION['login']=$login;
					header('Location: index.php');
					exit();
				}
			}
			else
				throw new Exception($connect->error);
			$connect->close();
		}
	}
	catch(Exception $e)
	{
		echo '<p>Błąd, serwer niedostępny.</p>';
	}
	/*
	$_SESSION['which_user']=true;
	header('Location:kosztorys.php');
	*/
}
else
	header('Location: index.php');