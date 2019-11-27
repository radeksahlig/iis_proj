<?php 
session_start();
include '../functions.php';
if(isset($_GET['jidelna']))
    if(filter_input(INPUT_GET, "jidelna", FILTER_VALIDATE_INT))
        $jidelna = filter_input(INPUT_GET, "jidelna");
    else
        header("Location:../index.php");
else
    header("Location:../index.php");
if(isset($_GET['den']))
    $den = filter_input(INPUT_GET, "den", FILTER_SANITIZE_STRING);
else
    $den = date('Y-m-d');
    
if(isset($_POST['submitjidlo'])){
    $db = dbconnect();
    $sql_sel = "SELECT id FROM nabidka WHERE jidelna = $jidelna AND den = '$den'";
    if($sel = $db->prepare($sql_sel)){
        $sel->execute();
        $sel->bind_result($id);
        $sel->fetch();
        $sel->close();
    }
    $jidla = array(filter_input(INPUT_POST, "jidlo1"), filter_input(INPUT_POST, "jidlo2"), filter_input(INPUT_POST, "jidlo3"), filter_input(INPUT_POST, "jidlo4"));
    $sql = "INSERT INTO jidla_v_nabidce (nabidka, jidlo) VALUES (?, ?)";
    $sql_del = "DELETE FROM jidla_v_nabidce WHERE nabidka = $id";
    if($del = $db->prepare($sql_del)){
        $del->execute();
        $del->close();
        for ($i=0; $i < 4 ; $i++) {
            if($insrt_jidlo = $db->prepare($sql)){
                $insrt_jidlo->bind_param("ii", $id, $jidla[$i]);
                $insrt_jidlo->execute();
                $insrt_jidlo->close();
            }
        }
    }
    $db->close();
    header("Location:./manage_jidelnicek?jidelna=$jidelna&den=$den&message=succ");
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
        <title>Upraviť jidelnicek | Jidelna IS</title>
	</head>
	<body class="container">
    <main>
        <a href="../index.php">Home</a>
        <?php 
            $db = dbconnect();
            $sql = "SELECT nazev, adresa, id FROM jidelna WHERE id = $jidelna AND stav = 1";
            if($jidelny = $db->prepare($sql)){;
                $jidelny->execute();
                $jidelny->bind_result($nazev, $adresa, $id);
                if($jidelny->fetch()){
                    echo "<a href='../jidlo/jidelnicek.php?jidelna=$jidelna' style='text-decoration : none; color: black;'><div style='border : 1px solid black;'>";
                    echo "<b>$nazev</b>";
                    echo "<p>Města dovozu - ".getMestaDovozu($jidelna)."</p>";
                    echo "<p>Adresa - $id $adresa</p>";
                    echo "</div></a>";
                }else{
                    echo "Nepodařilo se načíst jídelníček jídelny s daným id";
                }     
                $jidelny->close();   
            }else{
                echo "Nepodařilo se načíst jídelníček";
            }
        ?>
            <a href="./manage_jidelnicek.php?jidelna=<?php echo $jidelna;?>&den=<?php echo date('Y-m-d', strtotime($den . ' -1 day'));?>">Předchozí den</a>
            <form method="get" action="manage_jidelnicek.php" style="display: inline-block;">
                <input type="hidden" name="jidelna" value="<?php echo $jidelna;?>" >
                <input type="date" name="den"  value="<?php echo $den;?>">
                <input type="submit" value="Změnit den">
            </form>
            <a href="./manage_jidelnicek.php?jidelna=<?php echo $jidelna;?>&den=<?php echo date('Y-m-d', strtotime($den . ' +1 day'));?>">Následující den</a>
        <?php
            $dnes = date('Y-m-d', strtotime("+1 day"));
            if($dnes >= $den){
                echo "Na tento den již nelze upravovat jídelníček";
            }else{
                echo "<form name='jid' method='post' action='' onsubmit='return checkInputs()'>";
                $sql = "SELECT id FROM nabidka WHERE jidelna = $jidelna AND den = '$den'";
                if($jidelny = $db->prepare($sql)){
                    $jidelny->execute();
                    $jidelny->bind_result($id);
                    if($jidelny->fetch()){
                        $jidelny->close();                       
                        $sql_jidla_v_nabidce = "SELECT jidlo FROM jidla_v_nabidce WHERE nabidka = $id";
                        $jidla_v_nabidce = $db->query($sql_jidla_v_nabidce);
                        $i = 1;
                        if($jidla_v_nabidce->num_rows > 0){
                            while($row = $jidla_v_nabidce->fetch_assoc()){
                                $sql_info_jidlo = "SELECT nazev FROM jidlo WHERE id = ".$row['jidlo'];
                                if($jidlo_info = $db->prepare($sql_info_jidlo)){
                                    $jidlo_info->execute();
                                    $jidlo_info->bind_result($nazev);
                                    if(!$jidlo_info->fetch())
                                        echo "Toto jídlo neexistuje";
                                    $jidlo_info->close();
                                    echo "<select name='jidlo$i'>";
                                    echo "<option value=''>";
                                        $sql = "SELECT id, nazev FROM jidlo WHERE typ LIKE 'hlavni'";
                                        if($i == 4)
                                            $sql = "SELECT id, nazev FROM jidlo WHERE typ LIKE 'polevka'";
                                        $jidla = $db->query($sql);
                                        if($jidla->num_rows>0){
                                            while($row = $jidla->fetch_assoc()){
                                                if($row['nazev'] == $nazev)
                                                    echo "<option value=\"".$row["id"]."\" selected>".$row["nazev"];
                                                else
                                                    echo "<option value=\"".$row["id"]."\">".$row["nazev"];
                                            }
                                        }
                                        $jidla->close();
                                    echo "</select>";
                                    $i++;                                    
                                }
                            }
                        }else{
                            for ($i=1; $i <= 4; $i++) { 
                                echo "<select name='jidlo$i'>";
                                echo "<option value=''>";
                                $sql = "SELECT id, nazev FROM jidlo WHERE typ LIKE 'hlavni'";
                                if($i == 4)
                                $sql = "SELECT id, nazev FROM jidlo WHERE typ LIKE 'polevka'";
                                $jidla = $db->query($sql);
                                if($jidla->num_rows>0){
                                    while($row = $jidla->fetch_assoc()){
                                            echo "<option value=\"".$row["id"]."\">".$row["nazev"];
                                    }
                                }
                                $jidla->close();
                                echo "</select>";
                            }
                        }
                        $jidla_v_nabidce->close();
                    }else{
                        $sql = "INSERT INTO nabidka (jidelna, den, stav) VALUES (?, ?, 'Otevřeno')";
                        if($new_nabidka = $db->prepare($sql)){
                            $new_nabidka->bind_param("is", $jidelna, $den);
                            $new_nabidka->execute();
                            $new_nabidka->close();
                            ?><script>
                                var reload = setTimeout(Home, 0, "./manage_jidelnicek?jidelna=<?php echo $jidelna; ?>&den=<?php echo $den; ?>", reload);
                            </script><?php
                        }
                    }   
                }
                echo "<input type='submit' name='submitjidlo' value='Vložit jídla'>";
                echo "</form>"; 
            }


            $db->close();
        ?>
        <script>
            function checkInputs(){
                var i1 = document.forms['jid']['jidlo1'].value;
                var i2 = document.forms['jid']['jidlo2'].value;
                var i3 = document.forms['jid']['jidlo3'].value;
                var i4 = document.forms['jid']['jidlo4'].value;
                if(i1 == "" || i2 == "" || i3 == "" || i4 == ""){
                    alert("Musíš vybrat všechny 4 jídla");
                    return false;
                }
                if(i1 == i2 || i1 == i3 || i2 == i3){
                    alert("Každé jídlo musí být jiné");
                    return false;
                }
                return true;
            }
        </script>
    </main>
    <section>
            <?php
            if(isset($_GET['message'])){
                echo "Jídla byla úspěšně přidána do jídelníčku";
            }
            ?>
    </section>
	</body>
</html>