<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "conn.php"; // Veritabanı bağlantısı dosyanız

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Gelen verileri değişkenlere ata
    $styleValue = isset($_POST['styleValue']) ? $_POST['styleValue'] : null;
    $turkish = isset($_POST['turkishChecked']) ? (int)$_POST['turkishChecked'] : 0;
    $swedish = isset($_POST['swedishChecked']) ? (int)$_POST['swedishChecked'] : 0;
    $english = isset($_POST['englishChecked']) ? (int)$_POST['englishChecked'] : 0;
    $font_size = isset($_POST['fontSize']) ? (int)$_POST['fontSize'] : 32;
    $search_term = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : null;
    $search_results = isset($_POST['searchResults']) ? $_POST['searchResults'] : null;
    $topic_select = isset($_POST['topicSelect']) ? (int)$_POST['topicSelect'] : 0;


    // Kullanıcı oturum bilgilerini kontrol et
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Veritabanını güncelle
        $stmt = $db->prepare("UPDATE uyem 
                              SET turkishChecked = :turkish, 
                                  swedishChecked = :swedish, 
                                  englishChecked = :english, 
                                  fontSize = :font_size, 
                                  styleValue = :styleValue,
                                  searchTerm = :search_term,
                                  searchResults = :search_results,
                                  topicSelect = :topic_select
                              WHERE id = :user_id");
        $stmt->bindValue(':styleValue', $styleValue, PDO::PARAM_STR);
        $stmt->bindValue(':turkish', $turkish, PDO::PARAM_INT);
        $stmt->bindValue(':swedish', $swedish, PDO::PARAM_INT);
        $stmt->bindValue(':english', $english, PDO::PARAM_INT);
        $stmt->bindValue(':font_size', $font_size, PDO::PARAM_INT);
        $stmt->bindValue(':search_term', $search_term, PDO::PARAM_STR);
        $stmt->bindValue(':search_results', $search_results, PDO::PARAM_STR);
        $stmt->bindValue(':topic_select', $topic_select, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>

<p id="mesaj1"></p><p id="mesaj2"></p>
<script>
document.getElementById("mesaj1").innerText = "<?php echo $font_size; ?>";
document.getElementById("mesaj2").innerText = "<?php echo $_POST['fontSize']; ?>";

</script>

<?php
// Oturumu sonlandır
$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
</head>
<body>
    <?php

	header("location: index.php");
    exit;
?>
</body>
</html>

