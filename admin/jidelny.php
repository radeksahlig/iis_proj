<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['jmeno']) && isset($_SESSION['prava'])){
    if($_SESSION['prava'] != 1)
        header("Location:../index.php");
}else{
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
        <a href="./jidelny.php">Zpět</a>
        <form method="get" action="" enctype="multipart/form-data">
                <input type="text" name="search" required="required">
                <input type="submit" value="Hledat">
        </form>
        <?php
            $offset = 0;
            $stranka = "./jidelny.php?";
            if(isset($_GET['page'])){
                if($page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT))
                    $offset = ($page * 10) - 10;
                if($offset < 0)
                    $offset = 0;
            }
            echo "Výpis jídelen $offset - ".($offset+10);
            if(isset($_GET['search'])){
                $search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
                $stranka = "./jidelny.php?search=$search&";
                $sql = "SELECT id, nazev, mesto, adresa, operator, stav FROM jidelna WHERE nazev LIKE '%$search%' OR adresa LIKE '%$search%' LIMIT 10 OFFSET $offset";
                $sql2 = "SELECT COUNT(*) FROM jidelna WHERE nazev LIKE '%$search%' OR adresa LIKE '%$search%'";
            }else{
                $sql = "SELECT id, nazev, mesto, adresa, operator, stav FROM jidelna LIMIT 10 OFFSET $offset";
                $sql2 = "SELECT COUNT(*) FROM jidelna";
            }
            $db = dbconnect();
            if($num_of_accounts = $db->prepare($sql2)){
                $num_of_accounts->execute();
                $num_of_accounts->bind_result($count);
                $number = 0;
                if($num_of_accounts->fetch())
                    $number = $count;
            }
            $num_of_accounts->close();
            $load_accounts = $db->query($sql);
            $porad = $offset+1;
            if($load_accounts->num_rows>0){
                echo "<table>";
                echo "<tr><td>Pořadí</td><td>Název</td><td>Operátor</td><td>Města dovozu</td><td>Mesto</td><td>Adresa</td><td>Připravena</td><td>Upravit</td></tr>";
                while($row = $load_accounts->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".$porad++."</td>";
                    echo "<td>".$row['nazev']."</td>";
                    echo "<td><a href=\"./account.php?user=".$row['operator']."\">".(getOpName($row['operator']))."</a></td>";
                    echo "<td>".getMestaDovozu($row['id'])."</td>";
                    echo "<td>".$row['mesto']."</td>";
                    echo "<td>".$row['adresa']."</td>";
                    if ($row['stav'] == 0)
                        echo "<td>Ne</td>";
                    else
                        echo "<td>Ano</td>";
                    echo "<td><a href='../op/jidelna?jidelna=".$row['id']."'>Upravit</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }else{
                echo "Nepodařilo se načíst žádné jídelny";
            }
            $load_accounts->close();
            $db->close();
            strankovani($number, $offset, $stranka);
        ?>
    </main>
	</body>
</html>