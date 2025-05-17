<?php
include 'conn.php'; // Veritabanı bağlantısını içeren dosya

try {
    // Veritabanı bağlantısını kontrol et
    if (!$db) {
        throw new Exception("Veritabanı bağlantısı başarısız.");
    }

    $limit = 1000; // Her seferde işlenecek satır sayısı
    $offset = 0; // Başlangıç noktası

    while (true) {
        // tr_yildirim tablosundan verileri al (limit ve offset ile)
        $query = "SELECT id, trk FROM tr_yildirim ORDER BY id ASC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eğer veri yoksa döngüyü kır
        if (count($rows) === 0) {
            break;
        }

        // Her bir satırı işle
        foreach ($rows as $row) {
            $id = $row['id'];
            $trk = $row['trk'];

            // quran tablosundaki ilgili satırı güncelle
            $updateQuery = "UPDATE quran SET tr = :trk WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':trk', $trk);
            $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $updateStmt->execute();
        }

        // İşlenen satırları atlamak için offset'i artır
        $offset += $limit;

        echo "Offset $offset kadar veri aktarıldı.<br>";
    }

    echo "Tüm veriler başarıyla aktarıldı.";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}

// PDO bağlantısını kapatmaya gerek yok, script bittiğinde otomatik kapanır.
?>