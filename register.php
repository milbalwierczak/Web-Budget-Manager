<?php
session_start();

if (isset($_SESSION['logged_in'])) {
	header('Location: home.php');
	exit();
}


if (isset($_POST['email'])) {
	$validation_OK = true;

	$name = $_POST['name'];

	if ((strlen($name) < 3) || (strlen($name) > 50)) {
		$validation_OK = false;
		$_SESSION['e_name'] = "Imię musi posiadać od 3 do 50 znaków!";
	} else if (!preg_match('/(?![×÷])[A-Za-zÀ-ÿ]/', $name)) {
		$validation_OK = false;
		$_SESSION['e_name'] = "Imię może składać się tylko z liter";
	}

	$email = $_POST['email'];
	$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

	if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
		$validation_OK = false;
		$_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
	}

	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];

	if ((strlen($password1) < 8) || (strlen($password1) > 20)) {
		$validation_OK = false;
		$_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
	}

	if ($password1 != $password2) {
		$validation_OK = false;
		$_SESSION['e_password'] = "Podane hasła nie są identyczne!";
	}

	$password_h = password_hash($password1, PASSWORD_DEFAULT);

	$_SESSION['fr_name'] = $name;
	$_SESSION['fr_email'] = $email;

	require_once 'database.php';

	$result = $db->prepare("SELECT * FROM users WHERE email=:mail");
	$result->execute(array(':mail' => $email));


	if ($result->rowCount() > 0) {
		$validation_OK = false;
		$_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
	}

	if ($validation_OK == true) {
		try {
			$query = $db->prepare('INSERT INTO users VALUES (NULL, :username, :password, :email)');
			$query->bindValue(':username', $name, PDO::PARAM_STR);
			$query->bindValue(':password', $password_h, PDO::PARAM_STR);
			$query->bindValue(':email', $email, PDO::PARAM_STR);
			$query->execute();
		} catch (PDOException $e) {
			echo 'Błąd zapytania: ' . $e->getMessage();
		}

		try {
			$result = $db->prepare("SELECT * FROM users WHERE email=:mail");
			$result->bindValue(':mail', $email, PDO::PARAM_STR);
			$result->execute();
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$user_id = $row['id'];
		} catch (PDOException $e) {
			echo 'Błąd zapytania: ' . $e->getMessage();
		}

		try {
			$query = $db->prepare('INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT :user_id, name FROM expenses_category_default');
			$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
			$query->execute();
		} catch (PDOException $e) {
			echo 'Błąd zapytania: ' . $e->getMessage();
		}

		try {
			$query = $db->prepare('INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT :user_id, name FROM incomes_category_default');
			$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
			$query->execute();
		} catch (PDOException $e) {
			echo 'Błąd zapytania: ' . $e->getMessage();
		}

		try {
			$query = $db->prepare('INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT :user_id, name FROM payment_methods_default');
			$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
			$query->execute();
		} catch (PDOException $e) {
			echo 'Błąd zapytania: ' . $e->getMessage();
		}

		$_SESSION['register_success'] = true;
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
					<form method="post">
						<h2 class="text-white font-weight-bold my-0">Rejestracja</h2>
						<hr class="divider">
						<div class="form-floating mt-3">
							<input type="text" class="form-control" id="floatingName" placeholder=""
								<?php
								if (isset($_SESSION['fr_name'])) {
									echo 'value="' . $_SESSION['fr_name'] . '"';
									unset($_SESSION['fr_name']);
								}
								?> name="name">
							<label for="floatingName"><i class="bi bi-person"></i> Imię</label>
						</div>

						<?php
						if (isset($_SESSION['e_name'])) {
							echo '<div class="error">' . $_SESSION['e_name'] . '</div>';
							unset($_SESSION['e_name']);
						}
						?>


						<div class="form-floating mt-3">
							<input type="email" class="form-control" id="floatingEmail" placeholder=""
								<?php
								if (isset($_SESSION['fr_email'])) {
									echo 'value="' . $_SESSION['fr_email'] . '"';
									unset($_SESSION['fr_email']);
								}
								?> name="email">
							<label for="floatingEmail"><i class="bi bi-envelope"></i> Email</label>
						</div>

						<?php
						if (isset($_SESSION['e_email'])) {
							echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
							unset($_SESSION['e_email']);
						}
						?>

						<div class="form-floating mt-3">
							<input type="password" class="form-control" id="floatingPassword" placeholder="" name="password1">
							<label for="floatingPassword"><i class="bi bi-key"></i> Hasło</label>
						</div>

						<?php
						if (isset($_SESSION['e_password'])) {
							echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
							unset($_SESSION['e_password']);
						}
						?>

						<div class="form-floating mt-3">
							<input type="password" class="form-control" id="floatingPasswordRepeat" placeholder="" name="password2">
							<label for="floatingPasswordRepeat"><i class="bi bi-key"></i> Powtórz Hasło</label>
						</div>

						<input type="submit" value="Załóż konto" class="btn btn-primary btn-xl col-12 col-sm-6 py-3 my-3" />
					</form>
				</div>
			</div>
		</div>
	</header>

	<footer class="bg-light py-5">
		<div class="container px-4 px-lg-5">
			<div class="small text-center text-muted">Copyright &copy; 2024 - Miłosz Balwierczak</div>
		</div>
	</footer>
	<!-- Bootstrap core JS-->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	<!-- SimpleLightbox plugin JS-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
	<!-- Core theme JS-->
	<script src="js/scripts.js"></script>
	<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
</body>

</html>