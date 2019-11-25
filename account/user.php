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
                    echo "<p>$jmeno</p>";
                    echo "<p>$prijmeni</p>";
                    echo "<p>$email</p>";
                    echo "<p>$mesto</p>";
                    echo "<p>$adresa</p>";
                    echo "<p>$telefon</p>";
                    if($_SESSION['id'] == 1)
                        echo "<p>$prava</p>";
                    echo "<a href=\"./manage_user.php?user=$user_id\">Upravit</a>";
                }else{
                    echo "Uživatel s tímto id neexistuje";
                }
            }else{
                header("Location:../index.php"); 
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