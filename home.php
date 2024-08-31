<?php

	session_start();
	
	if (!isset($_SESSION['logged_in']))
	{
		header('Location: index.php');
		exit();
	}

	//Usuwanie zmiennych pamiętających wartości wpisane do formularza
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	
	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_wrong'])) unset($_SESSION['e_wrong']);

    require_once 'database.php';
		
    $query = $db->query("SELECT COUNT(*) FROM quotes");
    $totalRows = $query->fetchColumn();

    $today = date('Y-m-d');
    srand(strtotime($today));
    $randomId = rand(1, $totalRows); 

    $query = $db->prepare("SELECT quote, author FROM quotes WHERE id = :id");
    $query->bindValue(':id', $randomId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $quote = $result['quote'];
        $author = $result['author'];
    } else {        
        $quote = "Pieniądze są dobrym sługą, lecz złym panem.";
        $author = "Francis Bacon";
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
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="#page-top">Budget Manager</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                        <li class="nav-item"><a class="nav-link" href="./home.php"><i class="bi bi-house"></i> Strona główna</a></li>
                        <li class="nav-item"><a class="nav-link">+ Dodaj przychód</a></li>
                        <li class="nav-item"><a class="nav-link" href="./expense.html">- Dodaj wydatek</a></li>
                        <li class="nav-item"><a class="nav-link" href="./balance.html"><i class="bi bi-graph-up"></i> Przeglądaj bilans</a></li>
                        <li class="nav-item"><a class="nav-link" href="./logout.php"><i class="bi bi-box-arrow-right"></i> Wyloguj się</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <header class="masthead">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold mb-0 ">Cześć 
                          <?php
						  if (isset($_SESSION['logged_user_name']))
						  {
							  echo $_SESSION['logged_user_name'];
						  }
						?>!</h1>
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <h2 class="text-white-75">Twój bilans w tym miesiącu wynosi 1234,56 zł</h2>
                        <hr class="divider">
                        <p class="text-white fs-2">Cytat na dzisiaj:</p>
                        <p class="text-start text-white-75 fst-italic fs-4"><?php echo $quote;?></p>
                        <p class="text-end text-white-75 mb-5"><?php echo $author;?></p>
                        <a class="btn btn-success btn-xl me-sm-4 mb-3 mb-sm-0">+ Dodaj przychód</a>
                        <a class="btn btn-danger btn-xl me-sm-4 mb-3 mb-sm-0" href="./expense.html">- Dodaj wydatek</a>
                        <a class="btn btn-primary btn-xl mb-3 mb-sm-0" href="./balance.html"><i class="bi bi-graph-up"></i> Przeglądaj bilans</a>
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
