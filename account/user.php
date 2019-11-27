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
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <meta name="Jidelna" content="IIS Project Jidelna">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/styles.css">

        <!-- FAVICON -->
		<link rel="icon" href="./pic/ico.ico" type="image/x-icon">
        
        <!-- TITLE -->
        <title>Užívateľ | Jidelna IS</title>
	</head>
	<body class="container">
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

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>


	</body>
</html>