<?php 
session_start();
include '../functions.php';
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
		<h1>Vložení jídla do DB</h1>
		<a href="../index.php">Home</a>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="text" placeholder="Nazev" name="nazev" required="required" style="width : 90%; height : 25px; margin-bottom : 5px;"><br>
            <input type="text" placeholder="Popis" name="popis" required="required" style="width : 90%; height : 25px; margin-bottom : 5px;"><br>
			<input type="file" name="ob" id="ob">
			<input list="type" name="typ" required="required">
				<datalist id="type">
					<option value="hlavni">
					<option value="polevka">
				</datalist><br>
            <input type="checkbox" name="al1" value="1">1<br>
            <input type="checkbox" name="al2" value="2">2<br>
            <input type="checkbox" name="al3" value="3">3<br>
            <input type="checkbox" name="al4" value="4">4<br>
            <input type="checkbox" name="al5" value="5">5<br>
            <input type="checkbox" name="al6" value="6">6<br>
            <input type="checkbox" name="al7" value="7">7<br>
            <input type="checkbox" name="al8" value="8">8<br>
            <input type="checkbox" name="al9" value="9">9<br>
            <input type="checkbox" name="al10" value="10">10<br>
            <input type="checkbox" name="al11" value="11">11<br>
            <input type="checkbox" name="al12" value="12">12<br>
            <input type="checkbox" name="al13" value="13">13<br>
            <input type="checkbox" name="al14" value="14">14<br>
			<input type="submit" name="submit" value="Insert Jidlo">
        </form>
		<?php 
			if(isset($_POST['submit'])){
				$nazev = filter_input(INPUT_POST, "nazev");
          		$popis = filter_input(INPUT_POST, "popis");
				$typ = filter_input(INPUT_POST, "typ");
				$db = dbconnect();
				$sql = "INSERT INTO jidlo (nazev, popis, typ) VALUES (?, ?, ?)";
				if($stat = $db->prepare($sql)){
					$stat->bind_param("sss", $nazev,$popis,$typ);
					$stat->execute();
					$jidlo_id = $stat->insert_id;
					$stat->close();
					echo "Jidlo $jidlo_id úspěšné vloženo - Nazev : $nazev |||| Popis : $popis |||| Typ : $typ<br>";					
				}else{
					echo "Chyba ve vložení jídla do db";
				}	
				for ($i=1; $i <= 14; $i++) { 
					$al = filter_input(INPUT_POST, "al$i");
					if($al != 0){
						$sql_alergen = "INSERT INTO alergeny_v_jidle (alergen, jidlo) VALUES (?, ?)";
						if($alerg = $db->prepare($sql_alergen)){
							$alerg->bind_param("ii", $al, $jidlo_id);
							$alerg->execute();
							$al_id = $alerg->insert_id;
							$alerg->close();
							echo "Spojení $al_id úspěšně vytvořené : $al --- $jidlo_id<br>";
						}else{
							echo "Chyba ve vytvoření spojení alegenu s jídlem";
						}
					}
					$al = 0;
				}
				if (!$_FILES['ob']['size'] == 0 && $_FILES['ob']['error'] == 0){
					$dir = "../pic/$jidlo_id/";
					mkdir("../pic/$jidlo_id");
					$file = $dir . basename($_FILES["ob"]["name"]);
					$filetype = pathinfo($file, PATHINFO_EXTENSION);
					$filename = pathinfo($_FILES['ob']['name'], PATHINFO_FILENAME);
					if (move_uploaded_file($_FILES["ob"]["tmp_name"], $file)) {
						$slq = "UPDATE jidlo SET ob = ? WHERE id = $jidlo_id";
						if($asd = $db->prepare($slq)){
							$ob = $filename.".".$filetype;
							$asd->bind_param("s", $ob);
							$asd->execute();
							$asd->close();
						}
					}
				}
			}
		
		?>
    </main>
	</body>
</html>