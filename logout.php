<?php

	session_start();
	
	
	if (!isset($_SESSION['logged_in']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['logged_in']);
        unset($_SESSION['logged_user_id']);
        unset($_SESSION['logged_user_name']);
        
		header('Location: index.php');
		exit();
	}
	
?>