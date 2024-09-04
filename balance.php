<?php

session_start();

if (!isset($_SESSION['logged_in'])) {
  header('Location: index.php');
  exit();
} else {
  if (isset($_POST['dateStart'])) {
    $validation_OK = true;

    if (!empty($_POST['dateStart']) && !empty($_POST['dateEnd'])) {
      $formated_start_date = DateTime::createFromFormat('d-m-Y', $_POST['dateStart'])->format('Y-m-d');
      $formated_end_date = DateTime::createFromFormat('d-m-Y', $_POST['dateEnd'])->format('Y-m-d');

      if ($formated_start_date > $formated_end_date) {
        $validation_OK = false;
        $_SESSION['e_dateEnd'] = '"Data od" nie może być później niż "data do"';
      }
    } else if (empty($_POST['dateStart']) && empty($_POST['dateEnd'])) {
      $validation_OK = false;
      $_SESSION['e_dateStart'] = "Pole nie może być puste";
      $_SESSION['e_dateEnd'] = "Pole nie może być puste";
    } else if (empty($_POST['dateStart'])) {
      $validation_OK = false;
      $_SESSION['e_dateStart'] = "Pole nie może być puste";
    } else {
      $validation_OK = false;
      $_SESSION['e_dateEnd'] = "Pole nie może być puste";
    }

    $_SESSION['fr_dateStart'] = $_POST['dateStart'];
    $_SESSION['fr_dateEnd'] = $_POST['dateEnd'];


    if ($validation_OK == true) {
      $start_date = $formated_start_date;
      $end_date = $formated_end_date;
      if (isset($_SESSION['fr_dateStart'])) unset($_SESSION['fr_dateStart']);
      if (isset($_SESSION['fr_dateEnd'])) unset($_SESSION['fr_dateEnd']);
      if (isset($_SESSION['e_dateStart'])) unset($_SESSION['e_dateStart']);
      if (isset($_SESSION['e_dateEnd'])) unset($_SESSION['e_dateEnd']);
    } else {
      $_SESSION['show_modal'] = true;
      $start_date = date('Y-m-01');
      $end_date = date('Y-m-t');
    }
  } else {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
  }

  $total_income = 0;
  $total_expense = 0;

  $logged_user_id = $_SESSION['logged_user_id'];

  require_once 'database.php';

  try {
    $query = $db->prepare('SELECT e.id, e.amount, e.date_of_expense, c.name FROM expenses AS e, 
      expenses_category_assigned_to_users AS c WHERE e.expense_category_assigned_to_user_id = c.id 
      AND e.user_id = :user_id AND e.date_of_expense BETWEEN :start_date AND :end_date ORDER BY e.date_of_expense ASC');
    $query->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
    $query->bindValue(':start_date', $start_date, PDO::PARAM_STR);
    $query->bindValue(':end_date', $end_date, PDO::PARAM_STR);
    $query->execute();
    $expenses = $query->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo 'Błąd zapytania: ' . $e->getMessage();
  }

  try {
    $query = $db->prepare('SELECT i.id, i.amount, i.date_of_income, c.name FROM incomes AS i, 
      incomes_category_assigned_to_users AS c WHERE i.income_category_assigned_to_user_id = c.id 
      AND i.user_id = :user_id AND i.date_of_income BETWEEN :start_date AND :end_date  ORDER BY i.date_of_income ASC');
    $query->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
    $query->bindValue(':start_date', $start_date, PDO::PARAM_STR);
    $query->bindValue(':end_date', $end_date, PDO::PARAM_STR);
    $query->execute();
    $incomes = $query->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo 'Błąd zapytania: ' . $e->getMessage();
  }

  foreach ($incomes as $income) {
    $total_income += $income['amount'];
  }

  foreach ($expenses as $expense) {
    $total_expense += $expense['amount'];
  }

  $balance = $total_income - $total_expense;
  try {
    $query = $db->prepare('SELECT SUM(e.amount) AS total_amount, c.name FROM expenses AS e, 
      expenses_category_assigned_to_users AS c WHERE e.expense_category_assigned_to_user_id = c.id 
      AND e.user_id = :user_id AND e.date_of_expense BETWEEN :start_date AND :end_date GROUP BY c.name ORDER BY total_amount DESC');
    $query->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
    $query->bindValue(':start_date', $start_date, PDO::PARAM_STR);
    $query->bindValue(':end_date', $end_date, PDO::PARAM_STR);
    $query->execute();
    $expenses_categories = $query->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo 'Błąd zapytania: ' . $e->getMessage();
  }

  $expenses_labels = [];
  $expenses_data = [];

  foreach ($expenses_categories as $category) {
    $expenses_labels[] = htmlspecialchars($category['name']);
    $expenses_data[] = htmlspecialchars($category['total_amount']);
  }

  try {
    $query = $db->prepare('SELECT SUM(i.amount) AS total_amount, c.name FROM incomes AS i, 
      incomes_category_assigned_to_users AS c WHERE i.income_category_assigned_to_user_id = c.id 
      AND i.user_id = :user_id AND i.date_of_income BETWEEN :start_date AND :end_date GROUP BY c.name ORDER BY total_amount DESC');
    $query->bindValue(':user_id', $logged_user_id, PDO::PARAM_INT);
    $query->bindValue(':start_date', $start_date, PDO::PARAM_STR);
    $query->bindValue(':end_date', $end_date, PDO::PARAM_STR);
    $query->execute();
    $incomes_categories = $query->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo 'Błąd zapytania: ' . $e->getMessage();
  }
  $incomes_labels = [];
  $incomes_data = [];

  foreach ($incomes_categories as $category) {
    $incomes_labels[] = htmlspecialchars($category['name']);
    $incomes_data[] = htmlspecialchars($category['total_amount']);
  }
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

  <style>
    .datepicker {
      z-index: 1600 !important;
    }
  </style>

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
          <li class="nav-item"><a class="nav-link" href="./balance.php"><i class="bi bi-graph-up"></i> Przeglądaj bilans</a></li>
          <li class="nav-item"><a class="nav-link" href="./logout.php"><i class="bi bi-box-arrow-right"></i> Wyloguj się</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <header class="masthead">
    <div class="container px-4 px-lg-5 h-100">
      <div class="row gx-4 gx-lg-5 align-items-center justify-content-center text-center">
        <div class="col-lg-8 align-self-end">
          <?php
          echo '<h2 class="text-white">Bilans w okresie od ' . htmlspecialchars(date('d-m-Y', strtotime($start_date))) .
            ' do ' . htmlspecialchars(date('d-m-Y', strtotime($end_date))) . ': ' . htmlspecialchars(number_format($balance, 2, ',', '')) . ' zł</h2>'
          ?>
        </div>
        <div class="col-lg-4 align-self-center">
          <button type="button" class="btn btn-primary btn-xl mb-3 mb-sm-0" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
            Ustaw zakres dat
          </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Ustaw zakres dat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="post">
                <div class="modal-body">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="floatingDateStart" placeholder=""
                      <?php
                      if (isset($_SESSION['fr_dateStart'])) {
                        echo 'value="' . $_SESSION['fr_dateStart'] . '"';
                        unset($_SESSION['fr_dateStart']);
                      }
                      ?> autocomplete="off" name="dateStart">
                    <label for="floatingDateStart"><i class="bi bi-calendar3"></i> Data od</label>
                  </div>

                  <?php
                  if (isset($_SESSION['e_dateStart'])) {
                    echo '<div class="error">' . $_SESSION['e_dateStart'] . '</div>';
                    unset($_SESSION['e_dateStart']);
                  }
                  ?>

                  <div class="form-floating mt-3">
                    <input type="text" class="form-control" id="floatingDateEnd" placeholder=""
                      <?php
                      if (isset($_SESSION['fr_dateEnd'])) {
                        echo 'value="' . $_SESSION['fr_dateEnd'] . '"';
                        unset($_SESSION['fr_dateEnd']);
                      }
                      ?> autocomplete="off" name="dateEnd">
                    <label for="floatingDateEnd"><i class="bi bi-calendar3"></i> Data do</label>
                  </div>

                  <?php
                  if (isset($_SESSION['e_dateEnd'])) {
                    echo '<div class="error">' . $_SESSION['e_dateEnd'] . '</div>';
                    unset($_SESSION['e_dateEnd']);
                  }
                  ?>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                  <input type="submit" value="Zapisz zmiany" class="btn btn-primary" data-bs-dismiss="modal" />
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="row gx-4 gx-lg-5 align-items-center justify-content-center text-center">
          <div class="col-lg-6 align-self-baseline">
            <h2 class="text-white mt-3">Wydatki</h2>
            <div class="table-wrapper col-12">
              <table class="table table-striped table-sm text-white ">
                <thead class="header">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Wartość [zł]</th>
                    <th scope="col">Data</th>
                    <th scope="col">Kategoria</th>
                    <th scope="col">Szczegóły</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($expenses as $index => $expense):
                    echo '<tr>';
                    echo '<td>' . ($index + 1) . '</td>';
                    echo '<td>' . htmlspecialchars(number_format($expense['amount'], 2, ',', '')) . '</td>';
                    echo '<td>' . htmlspecialchars(date('d-m-Y', strtotime($expense['date_of_expense']))) . '</td>';
                    echo '<td>' . htmlspecialchars($expense['name']) . '</td>';
                    echo '<td><a class="text-reset text-decoration-none description" href="#">Kliknij</a></td>';
                    echo '</tr>';
                  endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="container mt-1 pie-chart">
              <canvas id="myChart"></canvas>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
              const ctx = document.getElementById('myChart');

              new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: <?php echo json_encode($expenses_labels); ?>,
                  datasets: [{
                    data: <?php echo json_encode($expenses_data); ?>
                  }]
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      display: false
                    }
                  }
                }
              });
            </script>

          </div>
          <div class="col-lg-6 align-self-baseline">
            <h2 class="text-white mt-3">Przychody</h2>
            <div class="table-wrapper col-12">
              <table class="table table-striped table-sm text-white ">
                <thead class="header">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Wartość [zł]</th>
                    <th scope="col">Data</th>
                    <th scope="col">Kategoria</th>
                    <th scope="col">Szczegóły</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($incomes as $index => $income):
                    echo '<tr>';
                    echo '<td>' . ($index + 1) . '</td>';
                    echo '<td>' . htmlspecialchars(number_format($income['amount'], 2, ',', '')) . '</td>';
                    echo '<td>' . htmlspecialchars(date('d-m-Y', strtotime($income['date_of_income']))) . '</td>';
                    echo '<td>' . htmlspecialchars($income['name']) . '</td>';
                    echo '<td><a class="text-reset text-decoration-none description" href="#">Kliknij</a></td>';
                    echo '</tr>';
                  endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="container mt-1 pie-chart">
              <canvas id="myChart2"></canvas>
            </div>
            <script>
              const ctx2 = document.getElementById('myChart2');

              new Chart(ctx2, {
                type: 'pie',
                data: {
                  labels: <?php echo json_encode($incomes_labels); ?>,
                  datasets: [{
                    data: <?php echo json_encode($incomes_data); ?>
                  }]
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      display: false
                    }
                  }
                }
              });
            </script>
          </div>
        </div>
      </div>
  </header>

  <footer class="bg-light py-5">
    <div class="container px-4 px-lg-5">
      <div class="small text-center text-muted">Copyright &copy; 2024 - Miłosz Balwierczak</div>
    </div>
  </footer>

  <script>
    Chart.defaults.color = 'white';
    Chart.defaults.font.size = 14;
  </script>

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pl.min.js"></script>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SimpleLightbox plugin JS-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
  <script src="./js/select.js"></script>

  <script>
    $('#floatingDateStart').datepicker({
      format: "dd-mm-yyyy",
      maxViewMode: 0,
      language: "pl",
      todayHighlight: true,
      autoclose: true
    });

    $('#floatingDateEnd').datepicker({
      format: "dd-mm-yyyy",
      maxViewMode: 0,
      language: "pl",
      todayHighlight: true,
      autoclose: true
    });
  </script>



  <script>
    $(document).ready(function() {
      <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
        $('#exampleModalCenter').modal('show');
        <?php unset($_SESSION['show_modal']); ?>
      <?php endif; ?>
    });
  </script>

</body>

</html>