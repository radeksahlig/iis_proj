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
            <form method="post" action="" enctype="multipart/form-data">
                <input type="text" placeholder="Jméno" name="jmeno" required="required">
                <input type="text" placeholder="Příjmení" name="prijmeni" required="required">
                <input type="text" placeholder="E-mail" name="email" required="required">
                <input type="password" placeholder="Heslo" name="pass1" required="required">
                <input type="password" placeholder="Potvrzení hesla" name="pass2" required="required">
                <input type="submit" name="submit" value="Register">
            </form>
            <p>I already have account : <a href="login.php">LOG IN</a></p>
        </div>
    </section>
    <section class="alert2 al2">
	<?php
        if(isset($_POST['submit'])){
            $jmeno = filter_input(INPUT_POST, "jmeno", FILTER_SANITIZE_STRING);
            $prijmeni = filter_input(INPUT_POST, "prijmeni", FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $pass1 = filter_input(INPUT_POST, "pass1");
            $pass2 = filter_input(INPUT_POST, "pass2");
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
                $db = dbconnect();
                $sql = "SELECT id, email, password FROM user WHERE email = '$email'";
                $stat = $db->query($sql);
                if($stat->num_rows > 0){
                    if($password == null)
                        //Znamená že je to pleb account, pomocný účet pro objednávky dodělat aby se jenom doplnili informace
                        echo "doplnit jenom";
                    echo "Tento e-mail se již používá !";                
                    $stat->close();               
                }else{  
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
                    $db->close();
                }
            }
        }
        ?>
        </section>
    </main>
	</body>
</html>