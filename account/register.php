<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['jmeno']))
    header("Location:../index.php")

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
    <section>
        <div class="middle">
            <a href="../index.php">Home</a>
            <?php 
            if(isset($_POST['submit'])){
                $jmeno = filter_input(INPUT_POST, "jmeno", FILTER_SANITIZE_STRING);
                $prijmeni = filter_input(INPUT_POST, "prijmeni", FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                $pass1 = filter_input(INPUT_POST, "pass1");
                $pass2 = filter_input(INPUT_POST, "pass2");
                $email_kon = emailExists($email);
                $email = $email_kon > 0 ? "" : $email;
            ?>    
            <form method="post" action="" enctype="multipart/form-data">
                <input type="text" placeholder="Jméno" name="jmeno" required="required" value="<?php echo $jmeno;?>">
                <input type="text" placeholder="Příjmení" name="prijmeni" required="required" value="<?php echo $prijmeni;?>">
                <input type="text" placeholder="E-mail" name="email" required="required" value="<?php echo $email;?>">
                <input type="password" placeholder="Heslo" name="pass1" required="required">
                <input type="password" placeholder="Potvrzení hesla" name="pass2" required="required">
                <input type="submit" name="submit" value="Registrovat">
            </form>
            <?php }else{?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="text" placeholder="Jméno" name="jmeno" required="required">
                <input type="text" placeholder="Příjmení" name="prijmeni" required="required">
                <input type="text" placeholder="E-mail" name="email" required="required">
                <input type="password" placeholder="Heslo" name="pass1" required="required">
                <input type="password" placeholder="Potvrzení hesla" name="pass2" required="required">
                <input type="submit" name="submit" value="Registrovat">
            </form>
            <?php } ?>
            <p>I already have account : <a href="login.php">LOG IN</a></p>
        </div>
    </section>
    <section class="alert2 al2">
	<?php
        
        if(isset($_POST['submit'])){
            if($pass1!==$pass2){
                echo "Hesla se neshodují";
            }else{
                add($email, $pass1, $jmeno, $prijmeni);
            }
        }
                
        function add($email, $password, $jmeno, $prijmeni){           
            if(!($email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL))){
                echo "Špatný formát emailu";
            }else{
                $email_kon = emailExists($email);
                $db = dbconnect();                                    
                if($email_kon == 0){
                    $pass = password_hash($password, PASSWORD_DEFAULT);
                    $avatar = "/avatar/avatar1.jpg";
                    $sql1 = "INSERT INTO user(jmeno, prijmeni, email, password) VALUES (?, ?, ?, ?)";
                    if($val = $db->prepare($sql1)){
                        $val->bind_param("ssss", $jmeno, $prijmeni, $email, $pass);
                        $val->execute();
                        $val->close();
                        echo "<span class=\"green\">Registrace úspěšná</span>";
                        ?><script>
                            var home = setTimeout(Home, 1000, "../index.php", home);
                        </script><?php
                    }else{
                        echo "Registrace se nezdařila";
                    }
                }else{
                    $sql = "SELECT jmeno FROM user WHERE id = $email_kon";
                    if($ucheck = $db->prepare($sql)){
                        $ucheck->execute();
                        $ucheck->bind_result($jmeno_emp);
                        if($ucheck->fetch()){
                            $ucheck->close();
                            if($jmeno_emp == ""){
                                $sql_insrt = "UPDATE user SET jmeno = ?, prijmeni = ?, password = ?, prava = 4 WHERE id = $email_kon";
                                if($insrt = $db->prepare($sql_insrt)){
                                    $pass = password_hash($password, PASSWORD_DEFAULT);
                                    $insrt->bind_param("sss", $jmeno, $prijmeni, $pass);
                                    $insrt->execute();
                                    $insrt->close();
                                    echo "Registrace úspěšná";
                                    ?><script>
                                        var home = setTimeout(Home, 1000, "../index.php", home);
                                    </script><?php
                                }
                            }else{
                                echo "Tento email se již používá !";
                            }
                        }
                    }
                }
                $db->close();                
            }
        }
        ?>
        </section>
    </main>
	</body>
</html>