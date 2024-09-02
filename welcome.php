<?php

	session_start();
	
	if (!isset($_SESSION['register_success']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['register_success']);
	}
	
	//Usuwanie zmiennych pamiętających wartości wpisane do formularza
	if (isset($_SESSION['fr_name'])) unset($_SESSION['fr_name']);
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	
	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_name'])) unset($_SESSION['e_name']);
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_password'])) unset($_SESSION['e_password']);
	if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
	
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
    </head>

    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="./index.php">Budget Manager</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                        <li class="nav-item"><a class="nav-link" href="./register.php"><i class="bi bi-person-plus"></i> Zarejestruj się</a></li>
                        <li class="nav-item"><a class="nav-link" href="./login.php"><i class="bi bi-box-arrow-in-right"></i> Zaloguj się</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <header class="masthead">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="form-signin col-10 col-md-6 col-xl-4 m-auto">
                        <form>
                          <h2 class="text-white font-weight-bold mb-5 mt-0">Konto założone pomyślnie!</h2>
                          <hr class="divider">
                          <a class="btn btn-primary btn-xl col-12 col-sm-6 py-3 my-3" href="./login.php">Strona logowania</a>
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
