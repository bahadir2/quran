<?php
if (true){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dort";
}else{
    $servername = "mysql4.unoeuro.com";
    $username = "jappsvenska_se";
    $password = "gAGdHEybhcwe45za63pt";
    $dbname = "jappsvenska_se_db";
}

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Hata yakalama
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>