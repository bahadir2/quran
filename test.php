<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dort";



$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sorgu: "cuz" 1 ve "ayno" 1 olan ayetleri seç
//$sql = "SELECT * FROM quran WHERE id BETWEEN 1 AND 1000 ";
$r = mb_chr(0x06e3, 'UTF-8');
$sql = "SELECT * FROM quran WHERE `or` LIKE '%$r%' ORDER BY id";


$result = $conn->query($sql);

// Sonuçları kontrol et ve yazdır
if ($result->num_rows > 0) {
    // Her bir ayeti yazdır
    while ($row = $result->fetch_assoc()) {
        
        echo "<hr>".$row['page'] . "<br>";
        echo $row['or'];

        // Besmele kontrolü ve çıkarma
        //$besmele = " ﷽";
        $r = mb_chr(0x0633, 'UTF-8');
    }
} else {
    echo "Sonuç bulunamadı.";
}

// Bağlantıyı kapat
$conn->close();
?>