<?php 
session_start();
include '../functions.php';
if(!isset($_SESSION['id']) && isset($_GET['obj']))
    header("Location:../index.php");

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
            if(isset($_GET['obj'])){
                if(filter_input(INPUT_GET, "obj", FILTER_VALIDATE_INT))
                    $obj = filter_input(INPUT_GET, "obj", FILTER_SANITIZE_NUMBER_INT);
                else
                    echo "Špatné číslo objednávky";
            }else if(isset($_GET['kod'])){
                if(filter_input(INPUT_GET, "kod", FILTER_VALIDATE_INT))
                    $kod = filter_input(INPUT_GET, "kod", FILTER_SANITIZE_NUMBER_INT);
                else
                    echo "Špatný kód objednávky";
            }else{
                echo "<form method='post' name='fkod' onsubmit=\"return checkKod()\">";
                echo "<input type='text' name='kod' required>";
                echo "<input type='submit' name='submitkod' value='Najít objednávku'>";
                echo "</form>";
            }
                
            if(isset($_POST['submitkod']))
                header("Location:./objednavka.php?kod=".$_POST['kod']);

            if(isset($obj)){
                $db = dbconnect();            
                $userId = $_SESSION['id'];
                $sql_kontrola = "SELECT id FROM objednavka WHERE id = $obj AND user = $userId";
                $kontrola = $db->query($sql_kontrola);
                if($kontrola->num_rows>0 || $_SESSION['prava'] == 1){
                    $kontrola->close();
                    $db->close();             
                    loadObj($obj, 0);
                }else{
                    echo "Nemáte právo zobrazovat tuto objednávku";
                }
            }else if(isset($kod)){
                loadObj(0, $kod);
            }

            function loadObj($obj, $kod){
                $db = dbconnect();
                if($kod == 0)
                    $sql_obj = "SELECT id, user, stav, ridic, cena, cas_objednani, den_dodani, mesto, adresa, jidelna FROM objednavka WHERE id = $obj";
                else
                    $sql_obj = "SELECT id, user, stav, ridic, cena, cas_objednani, den_dodani, mesto, adresa, jidelna FROM objednavka WHERE kod = $kod";
                if($obje = $db->prepare($sql_obj)){
                    $obje->execute();
                    $obje->bind_result($obj, $user, $stav, $ridic, $cena, $cas_objednani, $den_dodani, $mesto, $adresa, $jidelna);
                    if($obje->fetch()){
                        $obje->close();
                        $sql = "SELECT nazev, adresa, mesto FROM jidelna WHERE id = $jidelna AND stav = 1";
                        if($jidelny = $db->prepare($sql)){
                            $jidelny->execute();
                            $jidelny->bind_result($nazev, $adresa, $mesto);
                            if($jidelny->fetch()){
                                echo "<a href='./jidelnicek.php?jidelna=$jidelna' style='text-decoration : none; color: black;'><div style='border : 1px solid black;'>";
                                echo "<b>$nazev</b>";
                                echo "<p>Města dovozu - ".getMestaDovozu($jidelna)."</p>";
                                echo "<p>Adresa - $mesto $adresa</p>";
                                echo "</div></a>";
                            }else{
                                echo "Nepodařilo se načíst jídelnu";
                            }     
                            $jidelny->close();   
                        }else{
                            echo "Nepodařilo se načíst jídelnu";
                        }
                        echo "Objednaná jídla : celkem za $cena Kč";
                        $sql_obj_jidl = "SELECT jidlo, pocet FROM objednana_jidla WHERE objednavka = $obj";
                        $obj_jidla = $db->query($sql_obj_jidl);
                        if($obj_jidla->num_rows>0){
                            while($row = $obj_jidla->fetch_assoc()){
                                $sql_jidlo_info = "SELECT nazev FROM jidlo WHERE id = ".$row['jidlo'];
                                $jidlo_info = $db->prepare($sql_jidlo_info);
                                $jidlo_info->execute();
                                $jidlo_info->bind_result($nazev);
                                if($jidlo_info->fetch()){
                                    echo "<p>$nazev -- ".$row['pocet']." Ks</p>";
                                }
                                $jidlo_info->close();
                            }
                        }
                        $obj_jidla->close();
                        echo "Stav - $stav";
                        echo "$ridic";
                        echo "Čas objednání $cas_objednani";
                        echo "Den dodání ".dateDTH($den_dodani)."";
                        echo "Adresa : $mesto $adresa";
                    }else{
                        if($kod == 0)
                            echo "Objednávka s tímto číslem neexistuje";
                        else
                            echo "Objednávka s tímto kódem neexistuje";                    
                    }
                }
                $db->close();
            }
           
            ?>
            <script>
                function checkKod(){
                    var kod = Number(document.forms['fkod']['kod'].value);
                    if(Number.isInteger(kod)){
                        if(kod < 100000000 || kod > 999999999){
                            alert("Špatný formát kódu");
                            return false;
                        }
                        return true;
                    }
                    alert("Musíte zadat číslo");
                    return false;
                }
            </script>       
    </main>
	</body>
</html>