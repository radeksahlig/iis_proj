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
		<title>Jidelna</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <meta name="Jidelna" content="IIS Project Jidelna">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/styles.css">
		<link rel="icon" href="../pic/ico.ico" type="image/x-icon">
	</head>
	<body>
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
                    echo "Nepodařilo se načíst žádné objednávky";
                }
                $load_my_objednavka->close();
                $db->close();
            }
            //Stránkování
            if(ceil($number/10) > 1){
                if($offset == 0){
                    $podm = ceil($number/10) < 3 ? ceil($number/10) : 3;
                    echo "<a href=\"".$stranka."page=1\"><b>1</b></a>";
                    for ($i=2; $i <= $podm; $i++)
                        echo "<a href=\"".$stranka."page=$i\">$i</a>";
                    if(1 < $podm)
                        echo "<a href=\"".$stranka."page=2\">&gt</a>";
                }else{
                    $cur_page = $offset/10;
                    echo "<a href=\"".$stranka."page=$cur_page\">&lt</a>";               
                    $podm = $cur_page > 3 ? $cur_page-2 : 1;
                    for ($i=$podm; $i < $cur_page+1; $i++)
                        echo "<a href=\"".$stranka."page=$i\">$i</a>";
                    echo "<a href=\"".$stranka."page=".($cur_page+1)."\"><b>".($cur_page+1)."</b></a>";
                    $podm = ceil($number/10) < $cur_page+3 ? ceil($number/10) : $cur_page+4;
                    for ($i=$cur_page+2; $i <= $podm; $i++)
                        echo "<a href=\"".$stranka."page=$i\">$i</a>";
                    if($cur_page+1 < $podm)
                        echo "<a href=\"".$stranka."page=".($cur_page+2)."\">&gt</a>";
                }
            }
        ?>
    </main>
	</body>
</html>