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
                        <h2 class="text-white">Bilans w okresie od 01-07-2024 do 31-07-2024: 1234,56 zł</h2>
                    </div>
                    <div class="col-lg-4 align-self-center">
                        <a class="btn btn-primary btn-xl mb-3 mb-sm-0">Ustaw zakres dat</a></div>
                </div>
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
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
                                  <tr>
                                    <td>1</td>
                                    <td>15,78</td>
                                    <td>01-07-2024</td>
                                    <td>Jedzenie</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>59,99</td>
                                    <td>02-07-2024</td>
                                    <td>Rachunki</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>100</td>
                                    <td>03-07-2024</td>
                                    <td>Rachunki</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>123,15</td>
                                    <td>04-07-2024</td>
                                    <td>Rozrywka</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>5</td>
                                    <td>99,99</td>
                                    <td>05-07-2024</td>
                                    <td>Rachunki</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>6</td>
                                    <td>1024,00</td>
                                    <td>06-07-2024</td>
                                    <td>Rachunki</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>7</td>
                                    <td>57,88</td>
                                    <td>07-07-2024</td>
                                    <td>Jedzenie</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
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
                                labels: ['Rozrywka', 'Jedzenie', 'Rachunki'],
                                datasets: [{
                                  data: [100,200,1000]
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
                                  <tr>
                                    <td>1</td>
                                    <td>3315,78</td>
                                    <td>01-07-2024</td>
                                    <td>Wypłata</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>15,23</td>
                                    <td>02-07-2024</td>
                                    <td>Odsetki</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>100</td>
                                    <td>03-07-2024</td>
                                    <td>Sprzedaż</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>123,15</td>
                                    <td>04-07-2024</td>
                                    <td>Sprzedaż</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
                                  <tr>
                                    <td>5</td>
                                    <td>99,99</td>
                                    <td>05-07-2024</td>
                                    <td>Sprzedaż</td>
                                    <td><a class="text-reset text-decoration-none description">Kliknij</a></td>
                                  </tr>
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
                                labels: ['Sprzedaż', 'Odsetki', 'Wypłata'],
                                datasets: [{
                                  data: [200,50,3200]
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
            <div class="container px-4 px-lg-5"><div class="small text-center text-muted">Copyright &copy; 2024 - Miłosz Balwierczak</div></div>
        </footer>

        <script>            
            Chart.defaults.color = 'white';
            Chart.defaults.font.size = 14;
        </script>
        
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </body>
</html>
