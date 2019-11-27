<?php 
session_start();
include '../functions.php';
if(isset($_SESSION['jmeno']) && isset($_SESSION['prava']) && isset($_GET['jidelna'])){
    if($_SESSION['prava'] > 2)
        header("Location:../index.php");
}else{
    header("Location:../index.php"); 
}
?>
<!DOCTYPE html>
<html>
	<head>
		<<!-- META TAGS -->
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
        <title>Jídelna | Jidelna IS</title>
	</head>
	<body class="container">
    <main>
        <a href="../index.php">Home</a><br>
        <?php 
        if($_SESSION['prava'] == 1)
            echo "<a href=\"../admin/jidelny.php\">Všechny jídelny</a><br>";
        else
            echo "<a href=\"./moje_jidelny.php\">Moje jídelny</a><br>";


            $db = dbconnect();
            if($jidelna_id = filter_input(INPUT_GET, "jidelna", FILTER_VALIDATE_INT)){
                $sql = "SELECT nazev, mesto, adresa, operator, stav FROM jidelna WHERE id = $jidelna_id";
                $user = $db->prepare($sql);
                $user->execute();
                $user->bind_result($nazev, $mesto, $adresa, $operator, $stav);
                $userdata = array();
                if($user->fetch()){
                    $userdata = array("nazev" => $nazev, "mesto" => $mesto, "adresa" => $adresa, "operator" => $operator, "stav" => $stav);
                    $user->close();
                }
                if($operator == $_SESSION['id'] || $_SESSION['prava'] == 1){
                    echo "<form method=\"post\" action=\"\" enctype=\"multipart/form-data\">";
                    echo "<input type=\"text\" value=\"$nazev\" name=\"nazev\" required=\"required\">";
                    echo "<input type=\"text\" value=\"$adresa\" name=\"adresa\" required=\"required\">";
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
                    if($_SESSION['prava'] == 1){
                        echo "<select name=\"operator\">";
                        echo "<option value=''>";
                        $sql_op = "SELECT id, jmeno, prijmeni FROM user WHERE prava = 2";
                        $operators = $db->query($sql_op);
                        if ($operators->num_rows > 0) {
                            while ($row = $operators->fetch_assoc()) {
                                if($row['id'] == $operator)
                                    echo "<option value=\"".$row["id"]."\" selected>".$row["jmeno"]." ".$row['prijmeni'];
                                else
                                    echo "<option value=\"".$row["id"]."\">".$row["jmeno"]." ".$row['prijmeni'];                
                            }
                        }
                        $operators->close();
                        echo "</select>";
                    }else{
                        echo "<input type=\"text\" value=\"".getOpName($operator)."\" readonly>";
                    }
                    echo "Připravena<select name=\"stav\">";
                    if($stav == 0){
                        echo "<option value=\"0\" selected>Ne";
                        echo "<option value=\"1\">Ano";
                    }else{
                        echo "<option value=\"0\">Ne";
                        echo "<option value=\"1\" selected>Ano";
                    }
                    echo "</select>";
                    echo "<input type=\"submit\" name=\"submit\" value=\"Upravit jídelnu\">";
                    echo "</form>";

                    $mesta_dovozu = getMestaDovozu($jidelna_id);
                    echo "Přidání města k městů dovozu <br>";
                    echo "<form method=\"post\" action=\"\" enctype=\"multipart/form-data\">";
                    echo "<select name=\"mesta\">";
                    echo "<option value='' selected>";
                        $sql_mesta = "SELECT nazev FROM mesta";
                        $mesta = $db->query($sql_mesta);
                        if ($mesta->num_rows > 0) {
                            while ($row = $mesta->fetch_assoc()) {
                                if(!(strpos($mesta_dovozu, $row['nazev']) !== false))
                                    echo "<option value=\"".$row["nazev"]."\">".$row["nazev"];
                            }
                        }
                        $mesta->close();
                    echo "<input type=\"submit\" name=\"submit_mesto\" value=\"Přidat město\">";
                    echo "</select>";
                    echo "</form>";
                    echo "Města dovozu : $mesta_dovozu";                
                }else{
                    echo "Jídelna s tímto id neexistuje";
                }

            }else{
                header("Location:../index.php"); 
            }
            if(isset($_POST['submit'])){
                $nazev = filter_input(INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
                $mesto = filter_input(INPUT_POST, "mesto", FILTER_SANITIZE_STRING);
                $adresa = filter_input(INPUT_POST, "adresa", FILTER_SANITIZE_STRING);
                $stav = filter_input(INPUT_POST, "stav");
                if($_SESSION['prava'] == 1)
                    $operator = filter_input(INPUT_POST, "operator");
                if($stav == 1 || $stav == 0){
                    $sql_update = "UPDATE jidelna SET nazev = ?, mesto = ?, adresa = ?, operator = ?, stav = ? WHERE id = $jidelna_id";
                    if($updt = $db->prepare($sql_update)){
                        $updt->bind_param("sssii", $nazev, $mesto, $adresa, $operator, $stav);
                        $updt->execute();
                        $updt->close();
                        ?>
                        <script>
                            var refresh = setTimeout(Home, 0, "./jidelna.php?jidelna=<?php echo $jidelna_id; ?>&message=success", refresh);
                        </script>
                        <?php
                    }
                }
            }
            
            if(isset($_POST['submit_mesto'])){
                $mesta = filter_input(INPUT_POST, "mesta", FILTER_SANITIZE_STRING);  
                $sql = "INSERT INTO mesta_dovozu (mesto, jidelna) VALUES (?, ?)";
                if($insrt = $db->prepare($sql)){
                    $insrt->bind_param("si", $mesta, $jidelna_id);
                    $insrt->execute();
                    $insrt->close();
                    ?>
                    <script>
                        var refresh = setTimeout(Home, 0, "./jidelna.php?jidelna=<?php echo $jidelna_id; ?>&message=mesto_success", refresh);
                    </script>
                    <?php
                }
            }
            $db->close();
        ?>
    </main>
    <section>
        <?php
            if(isset($_GET['message'])){
                $message = filter_input(INPUT_GET, "message", FILTER_SANITIZE_STRING);
                if($message == "success")
                    echo "Upravení jídelny bylo úspěšné";
                if($message == "mesto_success")
                    echo "Město dovozu bylo úspěšně vloženo";
            }
        ?>
    </section>
	</body>
</html>