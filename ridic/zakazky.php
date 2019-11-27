<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['id']) && isset($_SESSION['prava'])){
    if($_SESSION['prava'] > 3)
        header("Location:../index.php");
}else{
    header("Location:../index.php"); 
}
$db = dbconnect();
if(isset($_GET['obj']) && isset($_GET['stav'])){
    $obj = filter_input(INPUT_GET, "obj", FILTER_SANITIZE_NUMBER_INT);
    $stav = filter_input(INPUT_GET, "stav", FILTER_SANITIZE_STRING);
    if($stav == "dd")
        $stav = "Dodáno";
    elseif($stav == "rs")
        $stav = "Potvrzeno";
    else
        $stav = "Na cestě";
    $sql = "UPDATE objednavka SET stav = ? WHERE id = $obj";
    var_dump($sql, $obj, $stav);
    if($updt = $db->prepare($sql)){
        $updt->bind_param("s", $stav);
        $updt->execute();
        $updt->close();
        
    }
    $db->close();
    header("Location:./zakazky.php?search=&f_akt=akt");
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
        <title>Zákazky | Jidelna IS</title>
	</head>
	<body class="container">
    <main>
        <a href="../index.php">Home</a>
        <a href="./zakazky.php">Všechny Zakázky</a>
        <form method="get" action="./zakazky.php" enctype="multipart/form-data">
                <input type="text" name="search">
                <input type="checkbox" name="f_akt" value="akt" <?php if(isset($_GET['f_akt'])) echo "checked";?>>Pouze aktuální objednávky
                <input type="submit" value="Hledat">
        </form>
        <?php
            $stranka = "./zakazky.php?";
            $offset = 0;
            if(isset($_GET['page'])){
                if($page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT))
                    $offset = ($page * 10) - 10;
                if($offset < 0)
                    $offset = 0;
            }
            echo "Výpis zakázek $offset - ".($offset+10);
            $moje_id = $_SESSION['id'];            
            if(isset($_GET['search'])){
                $search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
                $stranka = "./zakazky.php?search=$search&";
                if(isset($_GET['f_akt'])){
                    $den = date('Y-m-d');
                    if($search == ""){
                        $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa FROM objednavka WHERE ridic = $moje_id AND (den_dodani > '$den' OR stav LIKE 'Potvrzeno' OR stav LIKE 'Na Cestě') ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                        $sql2 = "SELECT COUNT(*) FROM objednavka WHERE ridic = $moje_id AND den_dodani >= '$den' ";
                    }else{
                        $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa FROM objednavka WHERE ridic = $moje_id AND (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND (den_dodani > '$den' OR stav LIKE 'Potvrzeno' OR stav LIKE 'Na Cestě') ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                        $sql2 = "SELECT COUNT(*) FROM objednavka WHERE ridic = $moje_id AND (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND (den_dodani > '$den' OR stav LIKE 'Potvrzeno' OR stav LIKE 'Na Cestě')";
                    }
                }else{
                    $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa FROM objednavka WHERE (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND ridic = $moje_id ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                    $sql2 = "SELECT COUNT(*) FROM objednavka WHERE (den_dodani = '$search' OR adresa LIKE '%$search%' OR mesto LIKE '%$search%') AND ridic = $moje_id";
                }
            }else{
                $sql = "SELECT id, stav, cena, den_dodani, mesto, adresa  FROM objednavka WHERE ridic = $moje_id ORDER BY den_dodani LIMIT 10 OFFSET $offset";
                $sql2 = "SELECT COUNT(*) FROM objednavka WHERE ridic = $moje_id";
            }
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
                    echo "<tr><td>Pořadí</td><td>Den</td><td>Cena</td><td>Mesto</td><td>Adresa</td><td>Podrobnosti</td><td>Stav</td><td>Na cestě</td><td>Dodáno</td><td>Reset stavu</td></tr>";
                    while($row = $load_my_objednavka->fetch_assoc()){
                        echo "<tr>";
                        echo "<td>".$porad++."</td>";
                        echo "<td>".dateDTH($row['den_dodani'])."</td>";
                        echo "<td>".$row['cena']." Kč</td>";
                        echo "<td>".$row['mesto']."</td>";
                        echo "<td>".$row['adresa']."</td>";
                        echo "<td><a href='../jidlo/objednavka?obj=".$row['id']."'>Podrobnosti</a></td>";
                        echo "<td>".$row['stav']."</td>";
                        echo "<td><a href='./zakazky.php?obj=".$row['id']."&stav=nc'>Stav - 'Na Cestě'</a></td>";
                        echo "<td><a href='./zakazky.php?obj=".$row['id']."&stav=dd'>Stav - 'Dodáno'</a></td>";
                        echo "<td><a href='./zakazky.php?obj=".$row['id']."&stav=rs'>Reset stavu</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";                
                }else{
                    echo "Nepodařilo se načíst žádné objednávky";
                }
                $load_my_objednavka->close();
                $db->close();
            }
            strankovani($number, $offset, $stranka);
        ?>
    </main>
	</body>
</html>