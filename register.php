<?php
	session_start();

	if (isset($_POST['email']))
	{
		$validation_OK=true;

		$name = $_POST['name'];
		
		if ((strlen($name)<3) || (strlen($name)>50))
		{
			$validation_OK=false;
			$_SESSION['e_name']="Imię musi posiadać od 3 do 50 znaków!";
		}
		
		else if (!preg_match('/(?![×÷])[A-Za-zÀ-ÿ]/', $name))
		{
			$validation_OK=false;
			$_SESSION['e_name']="Imię może składać się tylko z liter";
		}

		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$validation_OK=false;
			$_SESSION['e_email']="Podaj poprawny adres e-mail!";
		}
		
		//Sprawdź poprawność hasła
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		
		if ((strlen($password1)<8) || (strlen($password1)>20))
		{
			$validation_OK=false;
			$_SESSION['e_password']="Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if ($password1!=$password2)
		{
			$validation_OK=false;
			$_SESSION['e_password']="Podane hasła nie są identyczne!";
		}	

		$password_h = password_hash($password1, PASSWORD_DEFAULT);
		
		//Bot or not? Oto jest pytanie!
		$secret = "6Le1UiUqAAAAAAwig1K-UaQ-UF2W9vKdy7t0h2cR";
		
		$chceck_captcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
		
		$answer = json_decode($chceck_captcha);
		
		if ($answer->success==false)
		{
			$validation_OK=false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}		
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_name'] = $name;
		$_SESSION['fr_email'] = $email;
		
		require_once 'database.php';
		
		$result = $db->prepare("SELECT * FROM users WHERE email=:mail");
		$result->execute(array(':mail' => $email));


		if($result->rowCount() > 0){
			$validation_OK=false;
			$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";			
		}

		if ($validation_OK==true)
		{
			//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
							
		$query = $db->prepare('INSERT INTO users VALUES (NULL, :username, :password, :email)');
		$query->bindValue(':username', $name, PDO::PARAM_STR);
		$query->bindValue(':password', $password_h, PDO::PARAM_STR);
		$query->bindValue(':email', $email, PDO::PARAM_STR);
		$query->execute();

		$_SESSION['register_success']=true;
		header('Location: welcome.php');
			
		}
		
		$db = null;
			
		
	}
	
	
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Budget Manager</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/income.png">
        <!-- Bootstrap Icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css">
        <link href="css/styles.css" rel="stylesheet">
        <script src="https://www.google.com/recaptcha/api.js"></script>		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/gh/cowboy/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js"></script>

    </head>

    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="./index.html">Budget Manager</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                        <li class="nav-item"><a class="nav-link" href="./register.php"><i class="bi bi-person-plus"></i> Zarejestruj się</a></li>
                        <li class="nav-item"><a class="nav-link" href="./login.html"><i class="bi bi-box-arrow-in-right"></i> Zaloguj się</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <header class="masthead">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="form-signin col-10 col-md-6 col-xl-4 m-auto">
                        <form method="post">
                          <h2 class="text-white font-weight-bold my-0">Rejestracja</h2>
                          <hr class="divider">
                          <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="floatingName" placeholder="" 
								<?php
									if (isset($_SESSION['fr_name']))
									{
										echo 'value="'.$_SESSION['fr_name'].'"';
										unset($_SESSION['fr_name']);
									}
								?> name="name">
                            <label for="floatingName"><i class="bi bi-person"></i> Imię</label>
                          </div>

						  <?php
						  if (isset($_SESSION['e_name']))
						  {
							  echo '<div class="error">'.$_SESSION['e_name'].'</div>';
							  unset($_SESSION['e_name']);
						  }
						?>		
					  

                          <div class="form-floating mt-3">
                            <input type="email" class="form-control" id="floatingEmail" placeholder=""  
								<?php
									if (isset($_SESSION['fr_email']))
									{
										echo 'value="'.$_SESSION['fr_email'].'"';
										unset($_SESSION['fr_email']);
									}
								?> name="email">
                            <label for="floatingEmail"><i class="bi bi-envelope"></i> Email</label>
                          </div>

						  <?php
						  if (isset($_SESSION['e_email']))
						  {
							  echo '<div class="error">'.$_SESSION['e_email'].'</div>';
							  unset($_SESSION['e_email']);
						  }
						?>	
                          
                          <div class="form-floating mt-3">
                            <input type="password" class="form-control" id="floatingPassword" placeholder="" name="password1">
                            <label for="floatingPassword"><i class="bi bi-key"></i> Hasło</label>
                          </div>

						  <?php
						  if (isset($_SESSION['e_password']))
						  {
							  echo '<div class="error">'.$_SESSION['e_password'].'</div>';
							  unset($_SESSION['e_password']);
						  }
						?>	

                          <div class="form-floating mt-3">
                            <input type="password" class="form-control" id="floatingPasswordRepeat" placeholder="" name="password2">
                            <label for="floatingPasswordRepeat"><i class="bi bi-key"></i> Powtórz Hasło</label>
                          </div>

						  <div class="text-xs-center">
		                  	<div class="g-recaptcha mt-3" data-sitekey="6Le1UiUqAAAAAB6kjiZE_wUpJpMU3XOj4L2rezLa"></div>
						  </div>
						  <?php
						  if (isset($_SESSION['e_bot']))
						  {
							  echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
							  unset($_SESSION['e_bot']);
						  }
						?>	
                      
                          <input type="submit" value="Załóż konto"  class="btn btn-primary btn-xl col-12 col-sm-6 py-3 my-3"/>
                        </form>
                    </div>
                </div>
            </div>
        </header>
        
        <footer class="bg-light py-5">
            <div class="container px-4 px-lg-5"><div class="small text-center text-muted">Copyright &copy; 2024 - Miłosz Balwierczak</div></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SimpleLightbox plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>
