<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['jmeno']))
    header("Location:../index.php")

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
        <title>Registrace | Jidelna IS</title>
	</head>
	<body class="container">
    <main class="row justify-content-md-center">
    <section class="col col-md-6 mt-sm-3">
    <div class="card">
      <h5 class="card-header">Registrace</h5>
        <div class="card-body">
        
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

            <!-- REGISTRATION FORM -->
            <form method="post" action="" enctype="multipart/form-data">
              <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputName">Jméno</label>
                    <input type="text" class="form-control" id="inputName" placeholder="Zadajte jméno" name="jmeno" required="required" value="<?php echo $jmeno;?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputSurname">Příjmení</label>
                    <input type="text" class="form-control" id="inputSurname" placeholder="Zadajte příjmení" name="prijmeni" required="required" value="<?php echo $prijmeni;?>">
                </div>
              </div>
                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="Zadajte email" name="email" required="required" value="<?php echo $email;?>">
                </div>
                <div class="form-group">
                    <label for="inputPassword1">Heslo</label>
                    <input type="password" class="form-control" id="inputPassword" placeholder="Zadajte heslo" name="pass1" required="required" />
                </div>
                <div class="form-group">
                    <label for="inputPassword2">Potvrzení hesla</label>
                    <input type="password" class="form-control" id="inputPassword2" placeholder="Zadejte heslo znovu" name="pass2" required="required" />
                </div>                
                    <input type="submit" class="btn btn-primary float-right" name="submit" value="Registrovat" />
            </form>

            <?php }else{?>
            <!-- REGISTRATION FORM -->
            <form method="post" action="" enctype="multipart/form-data">
              <div class="form-row"> 
                <div class="form-group col-md-6">
                    <label for="inputName">Jméno</label>
                    <input type="text" class="form-control" id="inputName" placeholder="Zadajte jméno" name="jmeno" required="required" value="<?php echo $jmeno;?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputSurname">Příjmení</label>
                    <input type="text" class="form-control" id="inputSurname" placeholder="Zadajte příjmení" name="prijmeni" required="required" value="<?php echo $prijmeni;?>">
                </div>
              </div>
                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="Zadajte email" name="email" required="required" value="<?php echo $email;?>">
                </div>
                <div class="form-group">
                    <label for="inputPassword1">Heslo</label>
                    <input type="password" class="form-control" id="inputPassword" placeholder="Zadajte heslo" name="pass1" required="required" />
                </div>
                <div class="form-group">
                    <label for="inputPassword2">Potvrzení hesla</label>
                    <input type="password" class="form-control" id="inputPassword2" placeholder="Zadejte heslo znovu" name="pass2" required="required" />
                </div>                
                    <input type="submit" class="btn btn-primary float-right" name="submit" value="Registrovat" />
            </form>
            <?php } ?>
            <a href="../index.php" >≪ Back to Home</a>
        </div>
      </div>
      <div class="alert alert-secondary mt-md-2" role="alert">
        Již jsem registrovaný: <a href="login.php" class="badge badge-warning">LOG IN</a>
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

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>
    

	</body>
</html>