<?php

	session_start();
	
	if (!isset($_SESSION['logged_user_id']))
	{
		header('Location: index.php');
		exit();
	}
    else {
        $logged_user_id = $_SESSION['logged_user_id'];

		require_once 'database.php';

        $query = $db->prepare('SELECT name FROM incomes_category_assigned_to_users WHERE user_id = :user_id');
        $query->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
        $query->execute();
        $categories = $query->fetchAll(PDO::FETCH_ASSOC);

    }

    if (isset($_POST['value']))
	{
		$validation_OK=true;

		$value = $_POST['value'];
		$date = $_POST['date'];
        $description = $_POST['description'];
        if (isset($_POST['category'])){
            $category_name = $_POST['category'];  
        }
		
        if (!preg_match('/^\d+(\.\d{2})?$/', $value))
		{
			$validation_OK=false;
			$_SESSION['e_value']="Podaj poprawną wartość z dwoma miejscami po przecinku";
		}

        if (preg_match('/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-(\d{4})$/', $date))
		{
            list($day, $month, $year) = explode('-', $date);

            if (!checkdate($month, $day, $year)) {              
                $validation_OK=false;
                $_SESSION['e_date']="Podaj poprawną datę w formacie dd-mm-rrrr";
            } 
            else {
                $formattedDate = DateTime::createFromFormat('d-m-Y', $date)->format('Y-m-d'); 
            }
		}
        else {            
			$validation_OK=false;
			$_SESSION['e_date']="Podaj poprawną datę w formacie dd-mm-rrrr";
        }
		
        if (!preg_match('/^[A-Za-zÀ-ÿ0-9\s.,!?()\'-]*$/', $description))
		{
			$validation_OK=false;
			$_SESSION['e_description']="Opis zawiera niedozwolone znaki!";
		}

		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_value'] = $value;
		$_SESSION['fr_date'] = $date;      
		$_SESSION['fr_description'] = $description;
        
		if (isset($_POST['category'])){
            $_SESSION['fr_category'] = $_POST['category'];  
        }
        else {            
			$validation_OK=false;
			$_SESSION['e_category']="Wybierz kategorię z listy";
        }
		
		if ($validation_OK==true)
		{            
            $result = $db->prepare('SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :category_name');
            $result->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
            $result->bindValue(':category_name', $category_name, PDO::PARAM_STR);       
            $result->execute();
    
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $category_id = $row['id'];

            $query = $db->prepare('INSERT INTO incomes VALUES (NULL, :user_id, :category_id, :value, :date, :description)');
            $query->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
            $query->bindValue(':category_id', $category_id, PDO::PARAM_INT);
            $query->bindValue(':value', $value, PDO::PARAM_STR);
            $query->bindValue(':date', $formattedDate, PDO::PARAM_STR);
            $query->bindValue(':description', $description, PDO::PARAM_STR);
            $query->execute();
    
            $_SESSION['income_success']=true;
            header('Location: income_success.php');
			
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
    </head>

    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="./home.php">Budget Manager</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                        <li class="nav-item"><a class="nav-link" href="./home.php"><i class="bi bi-house"></i> Strona główna</a></li>
                        <li class="nav-item"><a class="nav-link" href="./income.php">+ Dodaj przychód</a></li>
                        <li class="nav-item"><a class="nav-link" href="./expense.php">- Dodaj wydatek</a></li>
                        <li class="nav-item"><a class="nav-link" href="./balance.html"><i class="bi bi-graph-up"></i> Przeglądaj bilans</a></li>
                        <li class="nav-item"><a class="nav-link" href="./logout.php"><i class="bi bi-box-arrow-right"></i> Wyloguj się</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <header class="masthead">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="form-signin col-10 col-md-6 col-xl-4 m-auto">
                        <form method="post">
                          <h2 class="text-white font-weight-bold mb-5 mt-0">Wprowadź dane</h2>
                      
                          <div class="form-floating">
                            <input type="number" class="form-control" id="floatingValue" placeholder=""  
								<?php
									if (isset($_SESSION['fr_value']))
									{
										echo 'value="'.$_SESSION['fr_value'].'"';
										unset($_SESSION['fr_value']);
									}
								?> name="value">
                            <label for="floatingValue"><i class="bi bi-currency-dollar"></i> Wartość</label>
                          </div>

                          <?php
						  if (isset($_SESSION['e_value']))
						  {
							  echo '<div class="error">'.$_SESSION['e_value'].'</div>';
							  unset($_SESSION['e_value']);
						  }
						  ?>	

                          <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="floatingDate" placeholder=""  
								<?php
									if (isset($_SESSION['fr_date']))
									{
										echo 'value="'.$_SESSION['fr_date'].'"';
										unset($_SESSION['fr_date']);
									}
								?> name="date">
                            <label for="floatingDate"><i class="bi bi-calendar3"></i> Data</label>
                          </div>

                          <?php
						  if (isset($_SESSION['e_date']))
						  {
							  echo '<div class="error">'.$_SESSION['e_date'].'</div>';
							  unset($_SESSION['e_date']);
						  }
                          ?>	

                          <div class="form-floating mt-3">
                            <select class="form-select <?php echo isset($_SESSION['fr_category']) ? 'has-value' : ''; ?>" id="floatingCategory" name="category">
                                <option hidden disabled selected value></option>
                                <?php foreach ($categories as $category): 
                                    $selected = '';
                                    if (isset($_SESSION['fr_category']) && $_SESSION['fr_category'] == $category['name']) {
                                        $selected = 'selected';
                                        unset($_SESSION['fr_category']);
                                    }
                                    echo '<option value="' . htmlspecialchars($category['name']) . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
                                endforeach; ?>
                            </select>                            
                            <label for="floatingCategory"><i class="bi bi-tag"></i> Kategoria</label>
                          </div>

                          <?php
						  if (isset($_SESSION['e_category']))
						  {
							  echo '<div class="error">'.$_SESSION['e_category'].'</div>';
							  unset($_SESSION['e_category']);
						  }
                          ?>

                          <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="floatingDescription" placeholder=""  
								<?php
									if (isset($_SESSION['fr_description']))
									{
										echo 'value="'.$_SESSION['fr_description'].'"';
										unset($_SESSION['fr_description']);
									}
								?> name="description">
                            <label for="floatingDescription"><i class="bi bi-pencil"></i> Opis (opcjonalnie)</label>
                          </div>

                          <?php
						  if (isset($_SESSION['e_description']))
						  {
							  echo '<div class="error">'.$_SESSION['e_description'].'</div>';
							  unset($_SESSION['e_description']);
						  }
						  ?>	
                      
                          <input type="submit" value="Dodaj przychód" class="btn btn-primary btn-xl col-12 col-sm-6 py-3 my-3"/>

                        <?php
						  if (isset($_SESSION['income_added']))
						  {
							  echo '<div class="success">Przychód dodano pomyślnie!</div>';
							  unset($_SESSION['income_added']);
						  }
                          ?>	
                        </form>
                    </div>
                </div>
            </div>
        </header>
        <footer class="bg-light py-5">
            <div class="container px-4 px-lg-5"><div class="small text-center text-muted">Copyright &copy; 2024 - Miłosz Balwierczak</div></div>
        </footer>

        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src= 
    "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"> 
        </script>    
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet">
        <script src="./js/bootstrap-datepicker.pl.min.js"></script>
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
        <script src="./js/select.js"></script>

        <script>
            $('#floatingDate').datepicker({
                format: "dd-mm-yyyy",
                maxViewMode: 0,
                language: "pl",
                todayHighlight: true
            });

        </script>
    </body>


</html>
