<?php
function dbconnect(){
	$db = new mysqli("localhost","","","iis_jidelna");
    $db->set_charset("UTF8");
    return $db;
}

function getOpName(int $id){
    $sql = "SELECT jmeno, prijmeni FROM user WHERE id = $id";
    $db = dbconnect();
    $celejmeno = "";
    if($getname = $db->prepare($sql)){
        $getname->execute();
        $getname->bind_result($jmeno, $prijmeni);
        if($getname->fetch())
            $celejmeno = $jmeno." ".$prijmeni;
        $getname->close();
    }
    $db->close();
    return $celejmeno;
}

function getMestaDovozu(int $id){
    $sql = "SELECT mesto FROM mesta_dovozu WHERE jidelna = $id";
    $db = dbconnect();
    $mesta_dovozu = "";
    $prvni = true;
    $load_mesta_dovozu = $db->query($sql);
    if($load_mesta_dovozu->num_rows>0){
        while($row = $load_mesta_dovozu->fetch_assoc()){
            if($prvni){
                $mesta_dovozu = $row['mesto'];
                $prvni = false;
            }else{
                $mesta_dovozu = $mesta_dovozu.", ".$row['mesto'];
            }
        }
    }
    $load_mesta_dovozu->close();
    $db->close();
    return $mesta_dovozu == "" ? "-" : $mesta_dovozu;
}

//YYYY-MM-DD -> DD-MM-YYYY
function dateDTH(string $a){
    $rok = substr($a, 0, 4);
    $mes = substr($a, 5, 2);
    $den = substr($a, 8, 2);
    return "$den-$mes-$rok";
}

//DD-MM-YYYY -> YYYY-MM-DD
function dateHTD(string $a){
    $den = substr($a, 0, 2);
    $mes = substr($a, 3, 2);
    $rok = substr($a, 6, 4);
    return "$rok-$mes-$den";
}




?>
<script>
	function Home(where, what){
        clearTimeout(what);
        window.location = where;
    }
</script>