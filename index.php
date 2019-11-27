<?php 
session_start();
include './functions.php';
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
        <title>Jidelna</title>
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
                echo "<nav class=' navbar-dark bg-dark'>";
                echo "<div class='container'>";
                echo "<ul class='nav justify-content-end'>";
                echo "<li class='nav-item py-2'>";
                echo "  <a class='nav-link btn btn-outline-info' href='./account/register.php'>Registrace</a>";
                echo "</li>";
                echo " <li class='nav-item py-2 mx-2'>";
                echo "  <a class='nav-link btn btn-outline-warning' href='./account/login.php'>Login</a>";
                echo "</li>";
                echo "</ul>";
              
                
           
                echo "</div></nav>";
            }
            echo "<section class='container'>";
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
                echo "<div class='alert alert-danger text-center' role='alert'>Nepodařilo se načíst žádné jídelny!</div>";
            }
            echo "</section>";

        ?>
    </main>
    <footer class="container">
    <table class="table table-striped table-dark">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Účet</th>
                <th scope="col">Email</th>
                <th scope="col">Heslo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <th scope="row">1</th>
                <td>Admin</td>
                <td>admin@jidelna.cz</td>
                <td>admin</td>
            </tr>
            <tr>
            <th scope="row">2</th>
                <td>Operátor</td>
                <td>LadNov@jidelna.cz</td>
                <td>heslo</td>
            </tr>
            <tr>
            <th scope="row">3</th>
                <td>Řidič</td>
                <td>novak@jidelna.cz</td>
                <td>heslo</td>
            </tr>
            <tr>
            <th scope="row">4</th>
                <td>Konzument</td>
                <td>novy@jidelna.cz</td>
                <td>heslo</td>
            </tr>
        </tbody>
    </table>
</footer>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>

	</body>
</html>
