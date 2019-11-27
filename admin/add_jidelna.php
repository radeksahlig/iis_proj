<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['jmeno']) && isset($_SESSION['prava'])){
    if($_SESSION['prava'] != 1)
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
        <title>Pridať jídelnu | Jidelna IS</title>
	</head>
	<body class="container">
    <main>
		<h1>Vložení jídelny do DB</h1>
		<a href="../index.php">Home</a>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="text" placeholder="Nazev" name="nazev" required="required"><br>
			<select name="operator">
                <option value='' selected>
                <?php
				    $db = dbconnect();                
                    $sql_op = "SELECT id, jmeno, prijmeni FROM user WHERE prava = 2";
                    $operators = $db->query($sql_op);
                    if ($operators->num_rows > 0) {
                        while ($row = $operators->fetch_assoc()) {
                                echo "<option value=\"".$row["id"]."\">".$row["jmeno"]." ".$row['prijmeni'];
                        }
                    }
                    $operators->close();
                ?>
            </select>
            <select name="mesto">
                <option value=''>
                <?php
                    $sql_mesta = "SELECT nazev FROM mesta";
                    $mesta = $db->query($sql_mesta);
                    if ($mesta->num_rows > 0) {
                        while ($row = $mesta->fetch_assoc()) {
                                echo "<option value=\"".$row["nazev"]."\">".$row["nazev"];
                        }
                    }
                    $mesta->close();
                ?>
            </select>
            <input type="text" name="adresa">
            <input type="submit" name="submit" value="Vložit jídelnu">
        </form>
		<?php 
			if(isset($_POST['submit'])){
				$nazev = filter_input(INPUT_POST, "nazev");
				$mesto = filter_input(INPUT_POST, "mesto");
                $operator = filter_input(INPUT_POST, "operator");
                $adresa = filter_input(INPUT_POST, "adresa");
                $sql = "INSERT INTO jidelna (nazev, mesto, adresa, operator) VALUES (?, ?, ?, ?)";
				if($stat = $db->prepare($sql)){
					$stat->bind_param("sssi", $nazev, $mesto, $adresa, $operator);
					$stat->execute();
                    $stat->close();
                echo "Jidelna byla úspěšně vložena";
				}
			}
		    $db->close();
		?>
    </main>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>

	</body>
</html>