<?php
// s_books.php
// boxSelect için kitap listesini veritabanından çeker
// Daha önce statik array vardı, artık dinamik

$selected_book = isset($_GET['book']) ? intval($_GET['book']) : 1;

$kitap_listesi = [];
try {
    $stmt = $db->query("SELECT id, baslik FROM kitaplar WHERE aktif=1 ORDER BY sira, id");
    $kitap_listesi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tablo henüz yoksa statik fallback
    $kitap_listesi = [
        ['id' => 1, 'baslik' => "Kur'an-ı Kerim"],
    ];
}

foreach ($kitap_listesi as $k) {
    $sel = ($selected_book == $k['id']) ? 'selected' : '';
    echo '<option value="' . $k['id'] . '" ' . $sel . '>'
       . $k['id'] . '. ' . htmlspecialchars($k['baslik'])
       . '</option>';
}
?>