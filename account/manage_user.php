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
        <a href="../index.php">Home</a><br>
        <?php 
            if($_SESSION['prava'] == 1)
                echo "<a href=\"../admin/accounts.php\">Všechny uživatelé</a><br>";
            $db = dbconnect();
            if($user_id = filter_input(INPUT_GET, "user", FILTER_VALIDATE_INT)){
                $sql = "SELECT jmeno, prijmeni, email, mesto, adresa, telefon, prava FROM user WHERE id = $user_id";
                $user = $db->prepare($sql);
                $user->execute();
                $user->bind_result($jmeno, $prijmeni, $email, $mesto, $adresa, $telefon, $prava);
                $userdata = array();
                if($user->fetch()){
                    $userdata = array("jmeno" => $jmeno, "prijmeni" => $prijmeni, "email" => $email, "mesto" => $mesto,"adresa" => $adresa, "telefon" => $telefon, "prava" => $prava);
                    $user->close();
                }
                if($jmeno != NULL){
                    echo "<form method=\"post\" action=\"\" enctype=\"multipart/form-data\" id=\"userform\">";
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
                    echo "<input type=\"text\" value=\"$telefon\" name=\"telefon\">";
                    if($_SESSION['prava'] == 1)
                        echo "<input type=\"text\" value=\"$prava\" name=\"prava\" required=\"required\">";
                    else
                        echo "<input type=\"text\" value=\"$prava\" readonly>";
                    echo "<input type=\"submit\" name=\"submit\" value=\"Upravit uživatele\">";
                    echo "</form>";
                }else{
                    echo "Uživatel s tímto id neexistuje";
                }
            }else{
                header("Location:../index.php"); 
            }
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
                    if($telefon == "");
                        $telefon = NULL;
                    $sql_update = "UPDATE user SET jmeno = ?, prijmeni = ?, email = ?, mesto = ?, adresa = ?, telefon = ?, prava = ? WHERE id = $user_id";
                    if($updt = $db->prepare($sql_update)){
                        $updt->bind_param("ssssssi", $jmeno, $prijmeni, $email, $mesto, $adresa, $telefon, $prava);
                        $updt->execute();
                        $updt->close();
                        ?>
                        <script>
                            var refresh = setTimeout(Home, 0, "./user.php?user=<?php echo $user_id; ?>&message=success", refresh);
                        </script>
                        <?php
                    }

                }else{
                    echo "Špatný formát emailu";
                }
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
    </section>
	</body>
</html>