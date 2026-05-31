<?php
/**
 * Veritabanı Bağlantı Dosyası (conn.php)
 * Config.php'den bilgileri yükler
 */

// Sadece bir kere yüklenmesini garanti edin
if (defined('DB_CONNECTION_ESTABLISHED')) {
    return;
}
define('DB_CONNECTION_ESTABLISHED', true);

// Projenin kök dizinini belirleyin
define('ROOT_PATH', dirname(__DIR__)); 

// Config dosyasını yükleme
$config_file_path = ROOT_PATH . '/config.php';

// Dosya varlık kontrolü
if (!file_exists($config_file_path)) {
    error_log("CRITICAL: Config dosyası bulunamadı: " . $config_file_path);
    http_response_code(503);
    die("Sistem şu anda bakımda. Lütfen daha sonra tekrar deneyin.");
}

$config = require_once $config_file_path;

// Config yapısı doğrulama
if (!is_array($config) || 
    !isset($config['database']) ||
    !is_array($config['database'])) {
    error_log("CRITICAL: Config.php geçersiz formatta");
    http_response_code(503);
    die("Sistem şu anda bakımda. Lütfen daha sonra tekrar deneyin.");
}

// Gerekli alanların varlığını kontrol et
$required_fields = ['host', 'username', 'password', 'dbname'];
foreach ($required_fields as $field) {
    if (!isset($config['database'][$field])) {
        error_log("CRITICAL: Config.php'de '$field' alanı eksik");
        http_response_code(503);
        die("Sistem şu anda bakımda. Lütfen daha sonra tekrar deneyin.");
    }
}

// Değişkenleri ata
$db_config = $config['database'];
$servername = $db_config['host'];
$username = $db_config['username'];
$password = $db_config['password'];
$dbname = $db_config['dbname'];

// PDO seçeneklerini ayarla
$default_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // Gerçek prepared statements
    PDO::ATTR_STRINGIFY_FETCHES => false, // Tip dönüşümlerini koru
];

// Config'den gelen options ile birleştir (config öncelikli)
$options = array_merge($default_options, $db_config['options'] ?? []);

// Veritabanı bağlantısı
try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $db = new PDO($dsn, $username, $password, $options);
    
} catch (PDOException $e) {
    // Gerçek hatayı loglara kaydet
    error_log("DB Connection Error: " . $e->getMessage());
    
    // Kullanıcıya genel bir mesaj göster
    http_response_code(503);
    die("Veritabanı bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.");
}

// Bağlantıyı test et (isteğe bağlı - performans maliyeti var)
try {
    $db->query("SELECT 1");
} catch (PDOException $e) {
    error_log("DB Connection Test Failed: " . $e->getMessage());
    http_response_code(503);
    die("Veritabanı bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.");
}
?>