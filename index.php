<?php 
session_start();
include './functions.php';
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
	    <!---
        TODO :
            Admin - 
            *_* Vyhledávání na accounts.php pro admina (jménem nebo emailem)                                                       
            *_* Udělat stránkování na accounts.php                                                                              
            *_* Tvorba nových jídelen -> přidělení operátora                                                                                

            Operátor -
            *_* Měnit nazev své jídelny, přidávat města dovozu ke své jídelně
            *_* Přidávat nová jídla
            *_* Měnit jídelníček své jídelny
            *_* Přidělovat zakázky řidičům

            Řidič -
            *_* Procházet své zakázky
            *_* Měnit stav zakázek

            Konzument -
            *_* Procházet jídelny a jejich jídelníčky
            *_* Objednávat si jídla - z db se předvyplní adresa
            *_* Sledovat objednávku
            *_* Změnit svůj účet
            *_* Smazat svůj účet

            Uživatel bez účtu -
            *_* Procházet jídelny a jejich jídelníčky
            *_* Objednat si jídlo - bude muset zadat adresu, email (v db se vytvoří provizorní účet bez hesla)
            *_* Nějak aby mohl sledovat objednávku ?? asi kód do db kterej musí zadat
            *_* Předělat registraci, aby se kontroloval email -> jestli pass prázdný jen doplnit informace jinak vytvořit nový účet

                Projít isset($_POST['submity']) a dát je nad html pokud to pujde
                Nepsát headry na index ale chybový hlášky (jen někde)


            Stavy
                - 1. Čekání
                - 2. Potvrzeno
                - 3. Na cestě
                - 4. Dodáno
		-->
    <main>
        <?php
            if(isset($_SESSION['jmeno'])){
                echo "Přihlášen jako : <a href=\"./account/user?user=".$_SESSION['id']."\"><b>".$_SESSION['jmeno']."</b></a><br>";
                echo "<a href='./account/login.php?action=off'>Odhlásit se</a><br>";
                echo "<a href='./account/moje_objednavky.php'>Moje objednávky</a><br>";
                if($_SESSION['prava'] == 2){
                    echo "<a href='./op/moje_jidelny.php'>Moje jídelny</a><br>";
                    echo "<a href='./op/dat_zakazky.php'>Nové zakázky</a><br>";
                }
                if($_SESSION['prava'] <= 2){
                    echo "<a href='./op/add_jidlo.php'>Vložení jídla do DB</a><br>";
                    if($_SESSION['prava'] == 1){
                        echo "<a href='./admin/accounts.php'>Účty</a><br>";
                        echo "<a href='./admin/add_jidelna.php'>Vložení jídelny do DB</a><br>";
                        echo "<a href='./admin/jidelny.php'>Jídelny</a><br>";
                    }
                }
                if($_SESSION['prava'] == 3)
                    echo "<a href='./ridic/zakazky.php?search=&f_akt=akt'>Zakázky</a><br>";
            }else{
                echo "<a href='./account/register.php'>Registrace</a><br>";
                echo "<a href='./account/login.php'>Login</a><br>";
            }
            echo "<a href='./jidlo/objednavka.php'>Najít objednávku</a>";
            $db = dbconnect();
            $sql = "SELECT id, nazev, adresa, mesto FROM jidelna WHERE stav = 1";
            $jidelny = $db->query($sql);
            if($jidelny->num_rows>0){
    			while($row = $jidelny->fetch_assoc()){
                    echo "<a href='./jidlo/jidelnicek.php?jidelna=".$row['id']."' style='text-decoration : none; color: black;'><div style='border : 1px solid black;'>";
                    echo "<b>".$row['nazev']."</b>";
                    echo "<p>Města dovozu - ".getMestaDovozu($row['id'])."</p>";
                    echo "<p>Adresa - ".$row['mesto']." ".$row['adresa']."</p>";
                    echo "</div></a>";
                }               
            }else{
                echo "Nepodařilo se načíst žádné jídelny";
            }

        ?>
    </main>
    <p>Admin účet : email - admin@jidelna.cz, heslo - admin</p>
    <p>Oparátor účet : email - LadNov@jidelna.cz, heslo - heslo</p>
    <p>Řidič účet : email - novak@jidelna.cz, heslo - heslo</p>

	</body>
</html>