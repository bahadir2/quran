<?php

session_start();
require_once "conn.php"; // Veritabanı bağlantısı dosyanız

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

//veriler GET olarak gonderilseydi
//logout.php?turkishChecked=0&swedishChecked=0&englishChecked=1&fontSize=44&searchTerm=
//logout.php?turkishChecked=1&swedishChecked=0&englishChecked=1&fontSize=40&searchTerm=rahman&topicSelect=1

// JavaScript'ten gelen verileri al
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$_SESSION = array();
session_destroy();
    //echo 'hello';exit;
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Gelen verileri değişkenlere ata
    $turkish = isset($data['turkishChecked']) ? (int)$data['turkishChecked'] : 0;
    $swedish = isset($data['swedishChecked']) ? (int)$data['swedishChecked'] : 0;
    $english = isset($data['englishChecked']) ? (int)$data['englishChecked'] : 0;
    $font_size = isset($data['fontSize']) ? (int)$data['fontSize'] : 32;
    $search_term = isset($data['searchTerm']) ? $data['searchTerm'] : null;
    $search_results = isset($data['searchTerm']) ? $data['searchResults'] : null;
    $topic_select = isset($data['topicSelect']) ? (int)$data['topicSelect'] : 0;

    // Test için JSON formatında çıktı gönder
    header('Content-Type: application/json');
    echo json_encode([
        'turkish' => $turkish,
        'swedish' => $swedish,
        'english' => $english,
        'font_size' => $font_size,
        'search_term' => $search_term,
        'search_results' => $search_results,
        'topic_select' => $topic_select
    ]);
    ob_end_flush(); // Flush the output buffer
    exit; // Stop further execution to ensure the output is sent to the client

    // Kullanıcı oturum bilgilerini kontrol et
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Veritabanını güncelle
        $stmt = $db->prepare("UPDATE uyem 
                              SET turkishChecked = :turkish, 
                                  swedishChecked = :swedish, 
                                  englishChecked = :english, 
                                  fontSize = :font_size, 
                                  searchTerm = :search_term,
                                  searchResults = :search_results,
                                  topicSelect = :topic_select
                              WHERE id = :user_id");
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
<!--
<p id="mesaj"></p>
<script>
document.getElementById("mesaj").innerText = "<?php echo $font_size; ?>";
</script>
-->
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

