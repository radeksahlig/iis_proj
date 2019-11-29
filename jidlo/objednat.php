<?php 
session_start();
include '../functions.php';
$jidelna = filter_input(INPUT_POST, "jidelna");
$den = filter_input(INPUT_POST, "den", FILTER_SANITIZE_STRING);
$podm = true;
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
        <title>Objednat | Jidelna IS</title>
    </head>
    <body class="container">
    <main>
        <a href="../index.php">Home</a>
        <?php 
            $db = dbconnect();
            
            if(isset($_POST['submitobj'])){
                $mestoobj = filter_input(INPUT_POST, "mesto", FILTER_SANITIZE_STRING);
                $adresaobj = filter_input(INPUT_POST, "adresa", FILTER_SANITIZE_STRING);         
                if(!isset($_SESSION['id'])){
                    $telefon = filter_input(INPUT_POST, "telefon", FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                    if(filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
                        $email_kon = emailExists($email);
                        if($email_kon == ""){
                            $sql_new_acc = "INSERT INTO user (email, telefon) VALUES (?, ?)";
                            if ($new_acc = $db->prepare($sql_new_acc)){
                                $new_acc->bind_param("ss", $email, $telefon);
                                $new_acc->execute();
                                $idUser = $new_acc->insert_id;
                                $new_acc->close();
                            }
                        }
                    }else{
                        echo "Špatně zadaný email";
                    }
                }else{
                    $idUser = $_SESSION['id'];
                }

                if(isset($idUser)){
                    $sql_obj = "INSERT INTO objednavka (user, cena, cas_objednani, den_dodani, mesto, adresa, jidelna, kod) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)";
                    if($obj = $db->prepare($sql_obj)){
                        $celkem = filter_input(INPUT_POST, "celkem");
                        $kod = genNewKod();
                        $obj->bind_param("iisssii", $idUser, $celkem, $den, $mestoobj, $adresaobj, $jidelna, $kod);
                        $obj->execute();
                        $id_obj = $obj->insert_id;
                        $obj->close();
                        if($id_obj != 0){
                            if(isset($_SESSION['id']))
                                echo "Objednávku můžete sledovat <a href=\"./objednavka.php?obj=$id_obj\">zde</a> nebo pomocí kódu <b>$kod</b>";
                            else
                                echo "Objednávku můžete sledovat pomocí kódu <b>$kod</b>";
                            $podm = false;
                            for ($i=1; $i < 5; $i++) { 
                                if(isset($_POST["jidlo$i"])){
                                    $jidlo = filter_input(INPUT_POST, "jidlo$i");
                                    $num = filter_input(INPUT_POST, "num$i");
                                    $sql_jidla_v_obj = "INSERT INTO objednana_jidla (objednavka, jidlo, pocet) VALUES (?, ?, ?)";
                                    if($jidla_v_obj = $db->prepare($sql_jidla_v_obj)){
                                        $jidla_v_obj->bind_param("iii", $id_obj, $jidlo, $num);
                                        $jidla_v_obj->execute();
                                        $jidla_v_obj->close();
                                    }
                                }
                                
                            }                                        
                        }else{
                            echo "Nastala chyba";
                        }
                    }
                }
            }
            if($podm){
                $sql = "SELECT nazev, adresa, mesto FROM jidelna WHERE id = $jidelna";
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
                echo "Objednání pro den $den";
                $celkem = 0;
                $jidla = array();
                for ($i=1; $i <= 4; $i++) {
                    $num = filter_input(INPUT_POST, "num$i");
                    if($num != 0){
                        $id = filter_input(INPUT_POST, "jidlo$i");
                        $sql_info_jidlo = "SELECT nazev, popis, typ, ob, cena FROM jidlo WHERE id = $id";
                        if($jidlo_info = $db->prepare($sql_info_jidlo)){
                            $jidlo_info->execute();
                            $jidlo_info->bind_result($nazev, $popis, $typ, $ob, $cena);
                            if($jidlo_info->fetch()){
                                echo "<fieldset>";
                                echo "<b>$nazev</b>$typ";
                                echo "<p>$popis</p>";
                                echo "<p>$cena Kč (jeden kus)</p>";
                                echo "<p>$ob</p>";
                                echo "<p>$num Ks</p>";
                                echo "</fieldset>";
                                $celkem = $celkem + $cena * $num;
                                array_push($jidla, $id, $num);
                            }else{
                                echo "Toto jídlo neexistuje";
                            }
                            $jidlo_info->close();
                        }
                    }
                }
                if($celkem > 0)
                    echo "Celkem $celkem Kč";
                echo "<form method=\"post\" onsubmit='return checkMesto()' name='obj'>";
                echo "<input type='hidden' name='jidelna' value='$jidelna'>";
                echo "<input type='hidden' name='den' value='$den'>";
                echo "<input type='hidden' name='celkem' value='$celkem'>";
                for ($i=0; $i < count($jidla)/2; $i++) { 
                    echo "<input type='hidden' name='jidlo".($i+1)."' value='".$jidla[$i*2]."'>";
                    echo "<input type='hidden' name='num".($i+1)."' value='".$jidla[$i*2+1]."'>";
                }

                if(isset($_SESSION['id'])){
                    $sql_user = "SELECT email, mesto, adresa, telefon FROM user WHERE id = ".$_SESSION['id'];
                    if($user = $db->prepare($sql_user)){
                        $user->execute();
                        $user->bind_result($email, $mesto, $adresa, $telefon);
                        if($user->fetch()){
                            $user->close();
                            if(strpos(getMestaDovozu($jidelna), $mesto) !== false)
                                echo "<input type='text' name='adresa' value='$adresa' required placeholder='Adresa'>";
                            else
                                echo "<input type='text' name='adresa' required placeholder='Adresa'>";
                            echo "<select name='mesto'>";
                            echo "<option value=''>";
                                $sql = "SELECT mesto FROM mesta_dovozu WHERE jidelna = $jidelna";
                                $mesta = $db->query($sql);
                                if($mesta->num_rows>0){
                                    while($row = $mesta->fetch_assoc()){
                                        if($row['mesto'] == $mesto)
                                            echo "<option value=\"".$row["mesto"]."\" selected>".$row["mesto"];
                                        else
                                            echo "<option value=\"".$row["mesto"]."\">".$row["mesto"];
                                    }
                                }
                                $mesta->close();
                            echo "</select>";
                        }
                    }
                }else{
                    if(isset($email_kon)){
                        echo "<input type='text' name='email' required placeholder='email'>";
                        echo "<input type='text' name='telefon' required placeholder='telefon' value='$telefon'>";
                        echo "<select name='mesto'>";
                        echo "<option selected>Vyberte</option>";
                            $sql = "SELECT mesto FROM mesta_dovozu WHERE jidelna = $jidelna";
                            $mesta = $db->query($sql);
                            if($mesta->num_rows>0){
                                while($row = $mesta->fetch_assoc()){
                                    if($row['mesto'] == $mestoobj)
                                        echo "<option value=\"".$row['mesto']."\" selected>".$row['mesto'];
                                    else 
                                        echo "<option value=\"".$row["mesto"]."\">".$row["mesto"];
                                }
                                echo "</option>";
                            }
                            $mesta->close();
                        echo "</select>";
                        echo "<input type='text' name='adresa' required value='$adresaobj'>";
                    }else{
                        echo "<input type='text' name='email' required placeholder='email'>";
                        echo "<input type='text' name='telefon' required placeholder='telefon'>";
                        echo "<select name='mesto'>";
                        echo "<option value=''>";
                            $sql = "SELECT mesto FROM mesta_dovozu WHERE jidelna = $jidelna";
                            $mesta = $db->query($sql);
                            if($mesta->num_rows>0){
                                while($row = $mesta->fetch_assoc()){
                                        echo "<option value=\"".$row["mesto"]."\">".$row["mesto"];
                                }
                            }
                            $mesta->close();
                        echo "</select>";
                        echo "<input type='text' name='adresa' required>";
                    }
                }
                echo "<input type='submit' name='submitobj' value='Objednat'>";
                echo "</form>";
            }
            if(isset($email_kon))
                if($email_kon != "")
                    echo "Tento email se již používá";
            $db->close();
?>
        <script>
            function checkMesto(){
                var mesto = document.forms["obj"]["mesto"].value;
                var telefon = Number(document.forms["obj"]["telefon"].value);
                if(mesto == ""){
                    alert("Musíte vybrat město");
                    return false;
                }
                if(telefon < 100000000 || telefon > 999999999){
                    alert("Špatný formát telefonu, korektní - 777586996");
                    return false;
                }
                return true;
            }
        </script>
    </main>
    </body>
</html>