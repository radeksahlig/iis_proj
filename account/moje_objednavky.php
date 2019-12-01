<?php 
session_start();
include '../functions.php';
if(!(isset($_SESSION['jmeno']) && isset($_SESSION['prava']) && isset($_SESSION['id']))){
    header("Location:../index.php"); 
}
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- META TAGS -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	    <meta name="Jidelna" content="IIS Project Jidelna" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous" />

        <!-- REGULAR CSS -->
        <link rel="stylesheet" href="../styles/styles.css" />

        <!-- FAVICON -->
		<link rel="icon" href="./pic/ico.ico" type="image/x-icon" />
        
        <!-- TITLE -->
        <title>Moje objednávky | Jidelna IS</title>
	</head>
	<body>
        <nav class='mb-4 navbar navbar-expand-lg navbar-dark bg-dark'>
            <div class='container'>
                <a class='navbar-brand' href='../index.php'><img src='../pic/logo/logo.png' /></a>
                    <button class='navbar-toggler' type='button' data-togle='collapse' data-target='#navbarSupportedContent-4' aria-controls='navbarSupportedContent-4' aria-expanded='false' aria-label='Toggle navigation'>
                        <span class='navbar-toggler-icon'></span>
                    </button>
                    <div class='collapse navbar-collapse' id='navbarSupportedContent-4'>
                        <ul class='navbar-nav ml-auto'>
                        <?php
                            if(isset($_SESSION['jmeno'])){
                                echo "
                                <li class='nav-link dropdown'>       
                                    <span class='nav-link dropdown-toggle' id='navbarDropdownMenuLink-4' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Přihlášen jako: 
                                    <a href=\"../account/user?user=".$_SESSION['id']."\"><b>".$_SESSION['jmeno']."</b></a>
                                    </span>    
                                    <div class='dropdown-menu dropdown-menu-right' aria-labelledby='navbarDropdownMenuLink-4'>
                                    ";
                                echo "<a class='dropdown-item' href='../account/moje_objednavky.php'>Moje objednávky</a>";
                            if($_SESSION['prava'] == 2){
                                echo "<a class='dropdown-item' href='../op/moje_jidelny.php'>Moje jídelny</a>";
                                echo "<a class='dropdown-item' href='../op/dat_zakazky.php'>Nové zakázky</a>";
                            }
                            if($_SESSION['prava'] <= 2){
                                echo "<a class='dropdown-item' href='../op/add_jidlo.php'>Vložení jídla do DB</a>";
                                if($_SESSION['prava'] == 1){
                                    echo "<a class='dropdown-item' href='../admin/accounts.php'>Účty</a>";
                                    echo "<a class='dropdown-item' href='../admin/add_jidelna.php'>Vložení jídelny do DB</a>";
                                    echo "<a class='dropdown-item' href='../admin/jidelny.php'>Jídelny</a>";
                                }
                            }
                            if($_SESSION['prava'] == 3) {
                                echo "<a class='dropdown-item' href='../ridic/zakazky.php?search=&f_akt=akt'>Zakázky</a>";
                            }
                            echo "<div class='dropdown-divider'></div>";
                            echo "<a class='dropdown-item' href='../account/login.php?action=off'>Odhlásit se</a>";
                            echo "</div></nav>";
                            }else{
                            echo "
                                <li class='nav-item'>
                                    <a class='nav-link' href='../account/register.php'><button class='btn btn-outline-info'>Registrace</button></a>
                                </li>
                                <li class='nav-item'>
                                    <a class='nav-link' href='../account/login.php'><button class='btn btn-outline-warning'>Login</button></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                ";
            }
        ?>
    <main class="container">
        <section class="row justify-content-md-center">
            <div class="col col-md-9">
                <div class="card shadow-lg border-dark">
                    <h5 class="card-header">Výpis objednávek</h5>
                    <div class="col col-md-6 mt-4">
                        <a class="float-left badge badge-light" href="./moje_objednavky.php">Všechny objednávky</a>
                        <br>
                        <form method="get" class="mb-5" action="./moje_objednavky.php" enctype="multipart/form-data">
                            <div class="input-group">
                                <input class="form-control" type="text" name="search" id="srch" placeholder="Zadejte objednávku" />
                                <div class="input-group-append">
                                    <input type="submit" value="Hledat" class="btn btn-primary float-right" id="srch" />
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox mt-2">
                                <input class="custom-control-input" type="checkbox" name="f_akt" value="akt" id="check1" />
                                <label class="custom-control-label" for="check1">Pouze aktuální objednávky</label>
                            </div>
                        </form>
                        <?php
                            $stranka = "./moje_objednavky.php?";
                            $offset = 0;
                            if(isset($_GET['page'])){
                                if($page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT))
                                    $offset = ($page * 10) - 10;
                                if($offset < 0)
                                    $offset = 0;
                            }
                            echo "<span>Výpis objednávek $offset - ".($offset+10) . "</span><br>";
                            ?>
                    </div>
                    <div class="card-body">
                    
            <?php
            $moje_id = $_SESSION['id'];            
            if(isset($_GET['search'])){
                $search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
                $stranka = "./moje_objednavky.php?search=$search&";
                if(isset($_GET['f_akt'])){
                    $den = date('Y-m-d');
                    if($search == ""){
                        $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa FROM objednavka WHERE user = $moje_id AND den_dodani >= '$den' ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                        $sql2 = "SELECT COUNT(*) FROM objednavka WHERE user = $moje_id AND den_dodani >= '$den' ";
                    }else{
                        $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa FROM objednavka WHERE user = $moje_id AND (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND den_dodani > '$den' ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                        $sql2 = "SELECT COUNT(*) FROM objednavka WHERE user = $moje_id AND (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND  den_dodani > '$den' ";
                    }
                }else{
                    $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa FROM objednavka WHERE (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND user = $moje_id ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                    $sql2 = "SELECT COUNT(*) FROM objednavka WHERE (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND user = $moje_id";
                }
            }else{
                $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa  FROM objednavka WHERE user = $moje_id ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                $sql2 = "SELECT COUNT(*) FROM objednavka WHERE user = $moje_id";
            }
            $db = dbconnect();
            if($num_of_my_objednavka = $db->prepare($sql2)){
                $num_of_my_objednavka->execute();
                $num_of_my_objednavka->bind_result($count);
                $number = 0;
                if($num_of_my_objednavka->fetch())
                    $number = $count;
                $num_of_my_objednavka->close();
            }
            if($load_my_objednavka = $db->query($sql)){
                if($load_my_objednavka->num_rows>0){
                    $porad = $offset+1;
                    echo "<table class='table table-hover table-responsive mt-4'>";
                    echo "
                        <thead>
                            <tr>
                                <th scope='col'>Pořadí</th>
                                <th scope='col'>Stav</th>
                                <th scope='col'>Den</th>
                                <th scope='col'>Cena</th>
                                <th scope='col'>Mesto</th>
                                <th scope='col'>Adresa</th>
                                <th scope='col'>Podrobnosti</th>
                            </tr>
                        </thead>
                    ";
                    while($row = $load_my_objednavka->fetch_assoc()){
                        echo "<tbody>";
                        echo "<tr>";
                        echo "<th scope='row'>".$porad++."</th>";
                        echo "<td>".$row['stav']."</td>";
                        echo "<td>".dateDTH($row['den_dodani'])."</td>";
                        echo "<td>".$row['cena']." Kč</td>";
                        echo "<td>".$row['mesto']."</td>";
                        echo "<td>".$row['adresa']."</td>";
                        echo "<td><a class='badge badge-light' href='../jidlo/objednavka.php?obj=".$row['id']."'>Podrobnosti</a></td>";
                        echo "</tr>";
                        echo "</tbody>";
                    }
                    echo "</table>";                
                }else{
                    echo "<p class='mt-2 text-center border-danger alert alert-danger'>Nemáte žádné objednávky!</p>";
                }
                $load_my_objednavka->close();
                $db->close();
            }
            strankovani($number, $offset, $stranka);
        ?>              
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="mt-4">
        <div class="bg-dark p-2 text-center text-white footer">
            Zer.to IIS Projekt | 2019 FIT VUT
        </div>
    </footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>

    <!-- FONT AWESOME -->
        <script src="https://kit.fontawesome.com/9e04c8ca52.js" crossorigin="anonymous"></script>
	</body>
</html>