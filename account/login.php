<?php 
session_start();
include '../functions.php';
if(isset($_GET['action'])){
    session_destroy();
    header("Location:../index.php");
}
if(isset($_SESSION['jmeno']))
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
		<link rel="stylesheet" href="../styles/styles.css">
		<link rel="icon" href="../pic/ico.ico" type="image/x-icon">
	</head>
	<body>
    <main>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="text" placeholder="Email" name="email" required="required">
            <input type="password" placeholder="Heslo" name="pass" required="required">
            <input type="submit" name="submit" value="Login">
        </form>
    </main>
    <?php
        function login($email, $pass){
            $db = dbconnect();
            $jmeno = "";
            $prava = 0;
            $login_sql = "SELECT id, jmeno, email, password, prava FROM user WHERE email = '$email'";
            $logina=$db->prepare($login_sql);
            $logina->execute();
            $logina->bind_result($id, $jmeno, $email, $password, $prava);
            $staty = array();
            if ($logina->fetch()) {
                    $staty = array("id" => $id, "jmeno" => $jmeno, "email" => $email, "password" => $password, "prava" => $prava);
                    $logina->close();
                if(password_verify($pass, $password)){
                    $_SESSION['jmeno'] = $jmeno;
                    $_SESSION['id'] = $id;
                    $_SESSION['prava'] = $prava;
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
            $db->close();
        }
        if(isset($_POST['submit'])){
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
            $pass = filter_input(INPUT_POST, "pass");
            if($email && $pass){
                if(login($email, $pass)){
                    header("Location:../index.php");
                }else{
                    echo "Špatný email nebo heslo";
                }
            }else{
                echo "Nastala chyba";
            }
        }
        ?>
	</body>
</html>