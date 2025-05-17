<?php
include("conn.php"); // Veritabanı bağlantısı

header('Content-Type: application/json'); // JSON çıktısı için başlık

// Gelen arama terimini al
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Eğer arama terimi boşsa, boş bir sonuç döndür
if (empty($searchTerm)) {
    echo json_encode([]);
    exit;
}

try {
    // Veritabanında arama yap
    $sql = "SELECT sur, ayno, page, `or`, tr, sv FROM quran 
            WHERE `or` LIKE :searchTerm 
               OR tr LIKE :searchTerm 
               OR sv LIKE :searchTerm 
            LIMIT 50";
    $stmt = $db->prepare($sql);
    $stmt->execute(['searchTerm' => "%$searchTerm%"]);

    $verses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON formatında sonuç döndür
    echo json_encode($verses);
} catch (PDOException $e) {
    echo json_encode(["error" => "Veritabanı hatası: " . $e->getMessage()]);
}
?>