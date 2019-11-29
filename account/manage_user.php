<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['jmeno']) && isset($_SESSION['prava']) && isset($_GET['user'])){
    if(!($_SESSION['prava'] == 1 || $_SESSION['id'] == $_GET['user']))
        header("Location:../index.php");
    
}else{
    header("Location:../index.php"); 
}
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
        <title>Užívatelia | Jidelna IS</title>
	</head>
	<body>
    <main class="container">
        <a href="../index.php">Home</a><br>
        <?php 
            if($_SESSION['prava'] == 1)
                echo "<a href=\"../admin/accounts.php\">Všechny uživatelé</a><br>";
            $db = dbconnect();
            $user_id = filter_input(INPUT_GET, "user", FILTER_SANITIZE_NUMBER_INT);           
            
            
            if(isset($_POST['submit'])){
                $jmeno = filter_input(INPUT_POST, "jmeno", FILTER_SANITIZE_STRING);
                $prijmeni = filter_input(INPUT_POST, "prijmeni", FILTER_SANITIZE_STRING);
                $mesto = filter_input(INPUT_POST, "mesto", FILTER_SANITIZE_STRING);
                $adresa = filter_input(INPUT_POST, "adresa", FILTER_SANITIZE_STRING);
                $telefon = filter_input(INPUT_POST, "telefon", FILTER_SANITIZE_STRING);
                if($_SESSION['prava'] == 1)
                    $prava = filter_input(INPUT_POST, "prava", FILTER_SANITIZE_STRING);
                if($email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
                    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                    if($mesto == "")
                        $mesto = NULL;
                    if($adresa == "")
                        $adresa = NULL;
                    if($telefon == "")
                        $telefon = NULL;
                    if($_SESSION['prava'] == 1)
                        $sql_update = "UPDATE user SET jmeno = ?, prijmeni = ?, email = ?, mesto = ?, adresa = ?, telefon = ?, prava = ? WHERE id = $user_id";
                    else
                        $sql_update = "UPDATE user SET jmeno = ?, prijmeni = ?, email = ?, mesto = ?, adresa = ?, telefon = ? WHERE id = $user_id";
                    if($updt = $db->prepare($sql_update)){
                        if($_SESSION['prava'] == 1)
                            $updt->bind_param("sssssii", $jmeno, $prijmeni, $email, $mesto, $adresa, $telefon, $prava);
                        else
                            $updt->bind_param("sssssi", $jmeno, $prijmeni, $email, $mesto, $adresa, $telefon);
                        $updt->execute();
                        $updt->close();
                        if($_SESSION['prava'] == 1){
                            echo "Úprava uživatele byla úspěšná";
                            ?>
                            <script>
                                var refresh = setTimeout(Home, 1000, "../admin/accounts.php", refresh);
                            </script>
                            <?php
                        }else{
                           ?>
                        <script>
                            var refresh = setTimeout(Home, 0, "./user.php?user=<?php echo $user_id; ?>&message=success", refresh);
                        </script>
                        <?php
                        }
                    }

                }else{
                    echo "Špatný formát emailu";
                }
            }
            if(isset($_POST['subdel'])){
                $sql = "DELETE FROM user WHERE id = $user_id";
                if($try_del = $db->prepare($sql)){
                    $try_del->execute();
                    if($try_del->affected_rows > 0){
                        ?><script>
                        var refresh = setTimeout(Home, 0, "../account/login.php?action=odhlasit", refresh);
                        </script><?php
                    }
                    $try_del->close();
                }
                $sql = "UPDATE user SET jmeno = null, prijmeni = null, password = null, mesto = null, adresa = null, prava = 5 WHERE id = $user_id";
                if($set_pleb = $db->prepare($sql)){
                    $set_pleb->execute();
                    if($set_pleb->affected_rows > 0){
                        ?><script>
                        var refresh = setTimeout(Home, 0, "../account/login.php?action=odhlasit", refresh);
                        </script><?php
                    }
                    $set_pleb->close();
                }
            }
            
            $sql = "SELECT jmeno, prijmeni, email, mesto, adresa, telefon, prava FROM user WHERE id = $user_id";
            $user = $db->prepare($sql);
            $user->execute();
            $user->bind_result($jmeno, $prijmeni, $email, $mesto, $adresa, $telefon, $prava);
            if($user->fetch()){
                $user->close();
            }
            if($jmeno != NULL){
                echo "<form method=\"post\" action=\"\" enctype=\"multipart/form-data\" name=\"userform\" onsubmit=\"return checkInput()\">";
                echo "<input type=\"text\" value=\"$jmeno\" name=\"jmeno\" required=\"required\">";
                echo "<input type=\"text\" value=\"$prijmeni\" name=\"prijmeni\" required=\"required\">";
                echo "<input type=\"text\" value=\"$email\" name=\"email\" required=\"required\">";
                echo "<select name=\"mesto\">";
                echo "<option value=''>";
                    $sql_mesta = "SELECT nazev FROM mesta";
                    $mesta = $db->query($sql_mesta);
                    if ($mesta->num_rows > 0) {
                        while ($row = $mesta->fetch_assoc()) {
                            if($row['nazev'] == $mesto)
                                echo "<option value=\"".$row["nazev"]."\" selected>".$row["nazev"];
                            else
                                echo "<option value=\"".$row["nazev"]."\">".$row["nazev"];
                        }
                    }
                    $mesta->close();
                echo "</select>";
                echo "<input type=\"text\" value=\"$adresa\" name=\"adresa\">";
                echo "<input type=\"text\" value=\"$telefon\" name=\"telefon\" placeholder='Formát - 666555444'>";
                if($_SESSION['prava'] == 1)
                    echo "<input type=\"number\" min='1' max='4' value=\"$prava\" name=\"prava\" required=\"required\">";
                echo "<input type=\"submit\" name=\"submit\" value=\"Upravit uživatele\">";
                echo "</form>";

                echo "<form onsubmit='return confirm(\"Opravdu chcete smazat účet?\")' action='manage_user.php?user=$user_id' method='post'>";
                echo "<input type=\"submit\" name=\"subdel\" value=\"Odstranit účet\">";
                echo "</form>";
            }else{
                echo "Uživatel s tímto id neexistuje";
            }
            
            $db->close();
        ?>
    </main>
    <section>
        <?php
            if(isset($_GET['message'])){
                echo "Upravení uživatele bylo úspěšné";
            }
        ?>
        <script>
            function checkInput(){
                var tel = document.forms["userform"]["telefon"].value;
                if(tel == "")
                    return true;
                var telefon = Number(document.forms["userform"]["telefon"].value);
                if(telefon < 100000000 || telefon > 999999999){
                    alert("Špatný formát telefonu, korektní - 777586996");
                    return false;
                }
                return true;
            }
        </script>
    </section>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>

	</body>
</html>