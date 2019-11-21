<?php 
session_start();
include '../functions.php';
if(isset($_GET['jidelna']))
    if(filter_input(INPUT_GET, "jidelna", FILTER_VALIDATE_INT))
        $jidelna = filter_input(INPUT_GET, "jidelna");
    else
        header("Location:../index.php");
else
    header("Location:../index.php");
if(isset($_GET['den']))
    $den = filter_input(INPUT_GET, "den", FILTER_SANITIZE_STRING);
else
    $den = date('Y-m-d');
$kontrola = true;

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Jidelna</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <meta name="Jidelna" content="IIS Project Jidelna">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="./styles/styles.css">
		<link rel="icon" href="./pic/ico.ico" type="image/x-icon">
	</head>
	<body>
    <main>
        <a href="../index.php">Home</a>
        <?php 
            $db = dbconnect();
            $sql = "SELECT nazev, adresa, mesto FROM jidelna WHERE id = $jidelna AND stav = 1";
            if($jidelny = $db->prepare($sql)){;
                $jidelny->execute();
                $jidelny->bind_result($nazev, $adresa, $mesto);
                if($jidelny->fetch()){
                    echo "<a href='./jidelnicek.php?jidelna=$jidelna' style='text-decoration : none; color: black;'><div style='border : 1px solid black;'>";
                    echo "<b>$nazev</b>";
                    echo "<p>Města dovozu - ".getMestaDovozu($jidelna)."</p>";
                    echo "<p>Adresa - $mesto $adresa</p>";
                    echo "</div></a>";
                }else{
                    echo "Nepodařilo se načíst jídelníček jídelny s daným id";
                    $kontrola = false;
                }     
                $jidelny->close();   
            }else{
                echo "Nepodařilo se načíst jídelníček";
                $kontrola = false;
            }
        ?>
            <a href="./jidelnicek.php?jidelna=<?php echo $jidelna;?>&den=<?php echo date('Y-m-d', strtotime($den . ' -1 day'));?>">Předchozí den</a>
            <form method="get" action="jidelnicek.php" style="display: inline-block;">
                <input type="hidden" name="jidelna" value="<?php echo $jidelna;?>" >
                <input type="date" name="den"  value="<?php echo $den;?>">
                <input type="submit" value="Změnit den">
            </form>
            <a href="./jidelnicek.php?jidelna=<?php echo $jidelna;?>&den=<?php echo date('Y-m-d', strtotime($den . ' +1 day'));?>">Následující den</a>
        <?php
            $sql = "SELECT id, stav FROM nabidka WHERE jidelna = $jidelna AND den = '$den'";
            if($jidelny = $db->prepare($sql)){
                $jidelny->execute();
                $jidelny->bind_result($id, $stav);
                if($jidelny->fetch()){
                    $jidelny->close();                       
                    if($stav == "Otevřeno"){
                        $dnes = date('Y-m-d', strtotime("+1 day"));
                        if($dnes >= $den){                            
                            $sql_update_stav = "UPDATE nabidka SET stav = 'Uzavřeno' WHERE jidelna = $jidelna AND den = '$den'";
                            if($updt = $db->prepare($sql_update_stav)){                                
                                $updt->execute();
                                $updt->close();
                                $stav = "Uzavřeno";
                            }
                        }
                    }
                    echo "<b>Stav přijímání objednávek : $stav</b><br>";                    
                    $sql_jidla_v_nabidce = "SELECT jidlo FROM jidla_v_nabidce WHERE nabidka = $id";
                    $jidla_v_nabidce = $db->query($sql_jidla_v_nabidce);
                    if($jidla_v_nabidce->num_rows > 0){
                        while($row = $jidla_v_nabidce->fetch_assoc()){
                            $sql_info_jidlo = "SELECT nazev, popis, typ, ob, cena FROM jidlo WHERE id = ".$row['jidlo'];
                            if($jidlo_info = $db->prepare($sql_info_jidlo)){
                                $jidlo_info->execute();
                                $jidlo_info->bind_result($nazev, $popis, $typ, $ob, $cena);
                                if($jidlo_info->fetch()){
                                    echo "<b>$nazev</b>$typ";
                                    echo "<p>$popis</p>";
                                    echo "<p>$cena Kč</p>";
                                    echo "<p>$ob</p>";
                                }else{
                                    echo "Toto jídlo neexistuje";
                                }
                                $jidlo_info->close();
                            }
                        }
                    }else{
                        echo "V nabídce nejsou žádná jídla";
                        $kontrola = false;
                    }
                    $jidla_v_nabidce->close();
                }else{
                    echo "Jídelna na tento den nemá sestavený jídelníček";
                    $kontrola = false;
                }     
            }else{
                echo "Nepodařilo se načíst jídelníček";
                $kontrola = false;
            }
        ?>
    </main>
    <section>
        <?php
            if(isset($_SESSION['id']) && $kontrola){
                $id = $_SESSION['id'];
                $sql = "SELECT * FROM objednavka WHERE user = $id AND den_dodani = '$den' AND jidelna = $jidelna";
                $kon = $db->query($sql);
                if($kon->num_rows > 0)
                    $kontrola = false;
                $kon->close();
            }
            if($stav == "Otevřeno" && $kontrola){
                echo "<form action='./jidlo.php' method='post'>";
                echo "<input type='hidden' name='jidelna' value='$jidelna' >";
                echo "<input type='hidden' name='den' value='$den' >";
                echo "<input type='submit' name='obj' value='Objednat si jídlo'>";
                echo "</form>";
            }else if($stav == "Uzavřeno" && $kontrola){
                echo "Na tento den již nelze podávat objednávky";
            }else if($kontrola){
                echo "Na tento den již byla podána vaše objednávka";
            }
            $db->close();
        ?>
    </section>
	</body>
</html>