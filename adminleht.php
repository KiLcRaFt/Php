<?php
require_once ('conf.php');

session_start();

// punktid nulliks
if(isset($_REQUEST["punktid0"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid=0 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["punktid0"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}

// peitmine
if(isset($_REQUEST["peitmine"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET avalik=0 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["peitmine"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
// näitimine
if(isset($_REQUEST["naitmine"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET avalik=1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["naitmine"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}

if(isset($_REQUEST["kustutaminenimi"]) && !empty($_REQUEST["kustutaminenimi"])){
    global $yhendus;
    $kask=$yhendus->prepare("delete from tantsud where id=?");
    $kask->bind_param("i", $_REQUEST["kustutaminenimi"]);
    $kask->execute();
}
?>
<!doctype html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tansud tätedega</title>
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <link rel="stylesheet" type="text/css" href="style/modalLogin.css">
</head>
<body>
<h1>Tantsud tähtedega</h1>
<header>
    <?php
    if(isset($_SESSION['kasutaja'])){
        ?>
        <h1>Tere, <?="$_SESSION[kasutaja]"?></h1>
        <div>
        <a href="logout.php">Logi välja</a>
        </div>
        <?php
    } else {
        ?>
            <div>
        <a href="login.php">Logi sisse</a>
            </div>
                <?php
    }
    ?>
</header>
<nav>
    <ul class="navigation">
    <li class="navi"><h2><a href=""> Administreerimis Leht </a></h2></li>
    <li class="navi"><h2><a href="kasutajaleht.php"> Kasutaja Leht </a></h2></li>
    </ul>
</nav>
<table>
    <tr>
        <th>Tantsupaari nimi</th>
        <th>Punktid</th>
        <th>Kuupaev</th>
        <th>Komentaarid</th>
        <th>Avalik</th>
    </tr>
<?php
    global $yhendus;
    $kask=$yhendus->prepare("Select id, tantsupaar, punktid, ava_paev, kommentaarid, avalik from tantsud");
    $kask->bind_result($id, $tantsupaar, $punktid, $paev, $komment, $avalik);
    $kask->execute();
    while($kask->fetch()){
        $tekst="Näita";
        $seisund="naitmine";
        $tekst2="Kasutaja ei näe";
        if($avalik==1){
            $tekst="Peida";
            $seisund="peitmine";
            $tekst2="Kasutaja näeb";
        }

        echo "<tr>";
        $tantsupaar=htmlspecialchars($tantsupaar);
        echo "<td>".$tantsupaar."</td>";
        echo "<td>".$punktid."</td>";
        echo "<td>".$paev."</td>";
        echo "<td>".$komment."</td>";
        echo "<td>".$avalik." / ".$tekst2."</td>";
        echo "<td><a href='?punktid0=$id'>Punktid nulliks</a></td>";
        echo "<td><a href='?kustutaminenimi=$id'>Kustuta</a></td>";
        echo "<td><a href='?$seisund=$id'>$tekst</a></td>";
        echo "</tr>";
    }
?>

</table>

</body>
</html>

