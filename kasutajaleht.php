<?php
require_once ('startup.php');
require_once ('conf2.php');
session_start();
function isAdmin(){
    return $_SESSION['onAdmin'] && isset($_SESSION['onAdmin']);
}

if(isset($_REQUEST["paarinimi"]) && !empty($_REQUEST["paarinimi"]) && !isAdmin()){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO tantsud(tantsupaar, ava_paev) values(?, NOW())");
    $kask->bind_param("s", $_REQUEST["paarinimi"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    //exit();
}

// punktide lisamine
if(isset($_REQUEST["heatants"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid=punktid+1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["heatants"]);
    $kask->execute();
}
if(isset($_REQUEST["pahatants"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid=punktid-1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["pahatants"]);
    $kask->execute();
}

if(isset($_REQUEST["kustutaminenimi"]) && !empty($_REQUEST["kustutaminenimi"])){
    global $yhendus;
    $kask=$yhendus->prepare("delete from tantsud where id=?");
    $kask->bind_param("i", $_REQUEST["kustutaminenimi"]);
    $kask->execute();
}

//kommentaarid lisamine
if(isset($_REQUEST["komment"])) {
    if(isset($_REQUEST["uuskomment"])) {
        global $yhendus;
        $kask = $yhendus->prepare("UPDATE tantsud SET kommentaarid=CONCAT(kommentaarid, ?) WHERE id=?");
        $kommentplus=$_REQUEST["uuskomment"]."\n";
        $kask->bind_param("si",$kommentplus, $_REQUEST["komment"]);
        $kask->execute();
        header("Location: $_SERVER[PHP_SELF]");
        $yhendus->close();
        //exit();
    }
}

if(isset($_REQUEST["kustutakomment"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET kommentaarid=' ' WHERE id=?");
    $kask->bind_param("i", $_REQUEST["kustutakomment"]);
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
<?php
if(!isAdmin()) {
?>
    <div id="modal">
        <div class="modal__window">
            <a class="modal__close" href="#">X</a>
            <?php
            require 'login.php'
            ?>
        </div>
    </div>
<?php
}
?>
<h1>Tantsud tähtedega</h1>
<header>
    <?php
    if(isset($_SESSION['kasutaja'])){
        ?>
        <h1>Tere, <?="$_SESSION[kasutaja]"?></h1>
        <a href="logout.php">Logi välja</a>
        <?php
    } else {
        ?>
        <div class="open">
            <a href="#modal">Logi sisse</a>
        </div>
        <?php
    }
    ?>
</header>
<nav>
    <ul class="navigation">
        <li class="navi"><h2><a href=""> Kasutaja Leht </a></h2></li>
        <?php
        if(isAdmin()) {
            ?>
           <li class="navi" ><h2 ><a href = "adminleht.php" > Administreerimis Leht </a ></h2 ></li >
        <?php
        }
        ?>
    </ul>
</nav>
        <?php
        if(isset($_SESSION["kasutaja"])){
        ?>
<table>
    <tr>
        <th>Tantsupaari nimi</th>
        <th>Punktid</th>
        <th>Kuupaev</th>
        <th>Kommentaarid</th>
    </tr>
<?php
    global $yhendus;
    $kask=$yhendus->prepare("Select id, tantsupaar, punktid, ava_paev, kommentaarid from tantsud where avalik=1");
    $kask->bind_result($id, $tantsupaar, $punktid, $paev, $komment);
    $kask->execute();
    while($kask->fetch()){
        echo "<tr>";
        $tantsupaar=htmlspecialchars($tantsupaar);
        echo "<td>".$tantsupaar."</td>";
        echo "<td>".$punktid."</td>";
        echo "<td>".$paev."</td>";
        echo"<td>".nl2br(htmlspecialchars($komment))."</td>";
        if(isAdmin()) {
            echo "<td><a href='?kustutakomment=$id'>Kustuta kommentaari</a></td>
        ";
        }
        if(!isAdmin()) {
        echo "<td>
        <form action='?'>
        <input type='hidden' value='$id' name='komment'>
        <input type='text' name='uuskomment' id='uuskomment'>
        <input type='submit' value='OK'>
        </form></td>
        ";

            echo "<td><a href='?heatants=$id'>Lisa +1 punkt</a></td>";
            echo "<td><a href='?pahatants=$id'>Lisa -1 punkt</a></td>";
        }
        echo "<td><a href='?kustutaminenimi=$id'>Kustuta</a></td>";
        echo "</tr>";
    }
?>

    <?php
    if(!isAdmin()){
    ?>
    <form action="?">
        <lable for="paarinimi">Lisa uus paar</lable>
        <input type="text" name="paarinimi" id="paarinimi">
        <input type="submit" value="Lisa paar">


    </form>
    <?php
    }
    ?>
</table>
        <?php
        }
        ?>
</body>
</html>

