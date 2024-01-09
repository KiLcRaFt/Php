<?php
$kasutaja="d123184_martinn";
$servernimi="d123184.mysql.zonevs.eu";
$parool="321Martin123";
$andmebaas="d123184_andmebaas";
$yhendus=new mysqli($servernimi, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset("UTF8");
?>