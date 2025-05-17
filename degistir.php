<?php
include 'conn.php'; // Veritabanı bağlantısını içeren dosya

try {
    // Veritabanı bağlantısını kontrol et
    if (!$db) {
        throw new Exception("Veritabanı bağlantısı başarısız.");
    }

    // Offset'i URL'den al (varsayılan olarak 0)
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 500; // Her seferde işlenecek satır sayısı

    // quran tablosundan tr sütununu al (limit ve offset ile)
    $query = "SELECT id, tr FROM quran ORDER BY id ASC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Eğer veri yoksa işlem tamamlandı
    if (count($rows) === 0) {
        echo "Tüm veriler başarıyla işlendi.";
        exit;
    }

    foreach ($rows as $row) {
        $id = $row['id'];
        $text = $row['tr'];

        // Harfleri değiştir
        $updatedText = str_replace(
            [
                ".", "?", "Ã‡", "â€œ", "â€", "Ã¢", "Ã®", "Ã¼", "Ä±", 
                "ÄŸ", "ÅŸ", "Ã¶", "Ã–", "Ã§", "Ä°", "Å", "â€™", 
                "Ã»", "Ãœ", "Ã‚"
            ], // Aranacak harfler
            [
                ". ", "? ", "Ç", "“", "”", "â", "î", "ü", "ı", 
                "ğ", "ş", "ö", "Ö", "ç", "İ", "Ş", "'", 
                "û", "Ü", "Â"
            ], // Değiştirilecek harfler
            $text
        );

        // Güncellenmiş veriyi veritabanına kaydet
        $updateQuery = "UPDATE quran SET tr = :updatedText WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':updatedText', $updatedText);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    // İşlenen satırları atlamak için offset'i artır
    $offset += $limit;

    echo "Offset $offset kadar veri işlendi.<br>";
    echo "<a href='degistir.php?offset=$offset'>Devam Et</a>"; // Kullanıcıdan onay almak için bağlantı
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>