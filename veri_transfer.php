
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dort";


// Veritabanı bağlantısını oluştur
$kaynak_db = new PDO("mysql:host=localhost;dbname=dort;charset=utf8", "root", "");
$hedef_db = new PDO("mysql:host=localhost;dbname=dort;charset=utf8", "root", "");

//$query = $kaynak_db->query("SELECT * FROM en_yusufali WHERE id BETWEEN 1 AND 2000");
//$query = $kaynak_db->query("SELECT * FROM en_yusufali WHERE id BETWEEN 2001 AND 4000");
$query = $kaynak_db->query("SELECT * FROM en_yusufali WHERE id BETWEEN 4001 AND 6236");

$veriler = $query->fetchAll(PDO::FETCH_ASSOC);

// 4. Her satırı hedef veritabanına ekle
// 4. Her satırı hedef veritabanına ekle
foreach ($veriler as $satir) {
    //$insert = $hedef_db->prepare("INSERT INTO quran (tr) VALUES (:tr)");
    //$insert = $hedef_db->prepare("INSERT INTO quran (tr)  VALUES (:a)");
    $id = $satir['id']; // Kaynağından gelen ID
    $text = $satir['text']; // Güncellenmesi gereken değer

    $update = $hedef_db->prepare("UPDATE quran SET en = :en WHERE id = :id");
    //SELECT CAST(text AS CHAR(1200)) FROM tr_bulac");
echo $satir['text']."<br>";

    if ($update->execute(['en' => $text, 'id' => $id])) {
    echo "Veri başarıyla aktarıldı.";
    } else {
        print_r($insert->errorInfo()); // Hata mesajını göster
    }
}

echo "Tüm veriler başarıyla aktarıldı!";

echo "Tüm veriler başarıyla aktarıldı!";
?>