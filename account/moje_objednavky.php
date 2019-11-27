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
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <meta name="Jidelna" content="IIS Project Jidelna">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/styles.css">

        <!-- FAVICON -->
		<link rel="icon" href="./pic/ico.ico" type="image/x-icon">
        
        <!-- TITLE -->
        <title>Moje objednávky | Jidelna IS</title>
	</head>
	<body class="container">
    <main>
        <a href="../index.php">Home</a>
        <a href="./moje_objednavky.php">Všechny objednávky</a>
        <form method="get" action="./moje_objednavky.php" enctype="multipart/form-data">
                <input type="text" name="search">
                <input type="checkbox" name="f_akt" value="akt">Pouze aktuální objednávky
                <input type="submit" value="Hledat">
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
            echo "Výpis objednávek $offset - ".($offset+10);
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
                    echo "<table>";
                    echo "<tr><td>Pořadí</td><td>Stav</td><td>Den</td><td>Cena</td><td>Mesto</td><td>Adresa</td><td>Podrobnosti</td></tr>";
                    while($row = $load_my_objednavka->fetch_assoc()){
                        echo "<tr>";
                        echo "<td>".$porad++."</td>";
                        echo "<td>".$row['stav']."</td>";
                        echo "<td>".dateDTH($row['den_dodani'])."</td>";
                        echo "<td>".$row['cena']." Kč</td>";
                        echo "<td>".$row['mesto']."</td>";
                        echo "<td>".$row['adresa']."</td>";
                        echo "<td><a href='../jidlo/objednavka?obj=".$row['id']."'>Podrobnosti</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";                
                }else{
                    echo "Nemáte žádné objednávky";
                }
                $load_my_objednavka->close();
                $db->close();
            }
            strankovani($number, $offset, $stranka);
        ?>
    </main>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>


	</body>
</html>