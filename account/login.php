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
        <title>Login | Jidelna IS</title>
	</head>
	<body>
        <nav class='mb-4 navbar navbar-expand-lg navbar-dark bg-dark'>
            <div class='container'>
                <a class='navbar-brand' href='../index.php'><img src='../pic/logo/logo.png' /></a>
                    <button class='navbar-toggler' type='button' data-togle='collapse' data-target='#navbarSupportedContent-4' aria-controls='navbarSupportedContent-4' aria-expanded='false' aria-label='Toggle navigation'>
                        <span class='navbar-toggler-icon'></span>
                    </button>
                <div class='collapse navbar-collapse' id='navbarSupportedContent-4'>
                        <ul class='navbar-nav ml-auto'>
                            <li class='nav-item'>
                                <a class='nav-link' href='../account/register.php'><button class='btn btn-outline-info'>Registrace</button></a>
                            </li>
                            <li class='nav-item'>
                                <a class='nav-link' href='../account/login.php'><button class='btn btn-outline-warning'>Login</button></a>
                            </li>
                        </ul>
                </div>
            <div>
        </nav>
        <main class="container">
            <section class="row justify-content-md-center">
                <div class="col col-md-4 mt-sm-3">
                    <div class="card shadow-lg border-dark">
                        <h5 class="card-header">Prihlášení do účtu</h5>
                        <div class="card-body">
                            <form method="post" action="" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="inputEmail">Login</label>
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Zadajte email" name="email" required="required" />
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword">Heslo</label>
                                    <input type="password" class="form-control" id="inputPassword" placeholder="Zadajte heslo" name="pass" required="required" />
                                </div>
                                <input type="submit" class="btn btn-primary float-right" name="submit" value="Prihlásit">
                            </form>
                        </div>
                    </div>
                </div>
            </section>
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

    <footer class="mt-4 bg-info">
        <div class="bg-dark p-2 text-center text-white footer">
            Zer.to IIS Projekt | 2019 
        </div>
    </footer>
	</body>
</html>