<?php 
session_start();
include '../functions.php';
if(isset($_POST['jidelna']))
    if(filter_input(INPUT_POST, "jidelna", FILTER_VALIDATE_INT))
        $jidelna = filter_input(INPUT_POST, "jidelna");
    else
        header("Location:../index.php");
else
    header("Location:../index.php");
if(isset($_POST['den']))
    $den = filter_input(INPUT_POST, "den", FILTER_SANITIZE_STRING);
else
    $den = date('Y-m-d');
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
                }     
                $jidelny->close();   
            }else{
                echo "Nepodařilo se načíst jídelníček";
            }
            $sql = "SELECT id FROM nabidka WHERE jidelna = $jidelna AND den = '$den' AND stav LIKE 'Otevřeno'";
            if($jidelny = $db->prepare($sql)){
                $jidelny->execute();
                $jidelny->bind_result($id);
                if($jidelny->fetch()){
                    $jidelny->close();                       
                    $sql_jidla_v_nabidce = "SELECT jidlo FROM jidla_v_nabidce WHERE nabidka = $id";
                    $jidla_v_nabidce = $db->query($sql_jidla_v_nabidce);
                    if($jidla_v_nabidce->num_rows > 0){
                        $num = 1;
                        echo "<form action=\"objednat.php\" method=\"post\" onsubmit=\"return checkNum()\" name=\"jidlo\">";
                        echo "<input type='hidden' name='jidelna' value='$jidelna'>";
                        echo "<input type='hidden' name='den' value='$den'>";
                        while($row = $jidla_v_nabidce->fetch_assoc()){
                            $sql_info_jidlo = "SELECT nazev, popis, typ, ob, cena FROM jidlo WHERE id = ".$row['jidlo'];
                            if($jidlo_info = $db->prepare($sql_info_jidlo)){
                                $jidlo_info->execute();
                                $jidlo_info->bind_result($nazev, $popis, $typ, $ob, $cena);
                                if($jidlo_info->fetch()){
                                    echo "<fieldset>";
                                    echo "<b>$nazev</b>$typ";
                                    echo "<p>$popis</p>";
                                    echo "<p>$cena Kč (jeden kus)</p>";
                                    echo "<p>$ob</p>";
                                    echo "<input type=\"hidden\" name=\"jidlo$num\" value=\"".$row['jidlo']."\">";
                                    echo "<input type=\"number\" name=\"num$num\" min=\"0\" max=\"4\" value=\"0\">0-4ks";
                                    echo "</fieldset>";
                                    $num++;
                                }else{
                                    echo "Toto jídlo neexistuje";
                                }
                                $jidlo_info->close();
                            }
                        }
                        echo "<input type=\"submit\" name=\"submit\" value=\"Objednat\">";
                        echo "</form>";
                    }else{
                        echo "V nabídce nejsou žádná jídla";
                    }
                    $jidla_v_nabidce->close();
                }else{
                    echo "V tento den nelze objednávat jídlo";
                }     
            }else{
                echo "Nepodařilo se načíst jídelníček";
                echo "<p id=\"asd\"></p>";
            }
            $db->close();
        ?>
        <script>
            function checkNum(){
                var n1 = document.forms["jidlo"]["num1"].value;
                var n2 = document.forms["jidlo"]["num2"].value;
                var n3 = document.forms["jidlo"]["num3"].value;
                var n4 = document.forms["jidlo"]["num4"].value;
                if(n1 == 0 && n2 == 0 && n3 == 0 && n4 == 0){
                    alert("Musí být vybráno více než 0 kusů");
                    return false;
                }
                return true;
            }
        </script>
    </main>
    </body>
</html>