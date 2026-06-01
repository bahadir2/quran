<?php
// okuma_api.php
// Okuma kayıt/sil/sorgula + topic yönetimi
session_start();
header('Content-Type: application/json; charset=utf-8');
include("conn.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { echo json_encode(['hata' => 'Giriş gerekli']); exit; }

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? ($_GET['action'] ?? '');

function ok($data) { echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }

// ─── Sayfa oku / geri al ───────────────────────────────────
if ($action === 'sayfa_isle') {
    $kitap_id = (int)($input['kitap_id'] ?? 1);
    $sayfa_no = (int)($input['sayfa_no'] ?? -1);
    $islem    = $input['islem'] ?? 'toggle'; // oku | geri_al | toggle

    if ($sayfa_no < 0) ok(['hata' => 'Geçersiz sayfa']);

    $chk = $db->prepare("SELECT id FROM okuma_kayit WHERE uye_id=:u AND kitap_id=:k AND sayfa_no=:s");
    $chk->execute([':u' => $user_id, ':k' => $kitap_id, ':s' => $sayfa_no]);
    $var = $chk->fetch();

    if ($islem === 'toggle') $islem = $var ? 'geri_al' : 'oku';

    if ($islem === 'oku' && !$var) {
        $db->prepare("INSERT INTO okuma_kayit (uye_id, kitap_id, sayfa_no) VALUES (:u,:k,:s)")
           ->execute([':u' => $user_id, ':k' => $kitap_id, ':s' => $sayfa_no]);
    } elseif ($islem === 'geri_al' && $var) {
        $db->prepare("DELETE FROM okuma_kayit WHERE uye_id=:u AND kitap_id=:k AND sayfa_no=:s")
           ->execute([':u' => $user_id, ':k' => $kitap_id, ':s' => $sayfa_no]);
    }

    ok(['basari' => true, 'durum' => ($islem === 'oku' ? 'okundu' : 'okunmadi'), 'sayfa' => $sayfa_no]);
}

// ─── Topic'in okunan sayfalarını getir ─────────────────────
if ($action === 'okunanlar') {
    $kitap_id  = (int)($input['kitap_id']  ?? ($_GET['kitap_id']  ?? 1));
    $sayfa_bas = (int)($input['sayfa_bas'] ?? ($_GET['sayfa_bas'] ?? 0));
    $sayfa_son = (int)($input['sayfa_son'] ?? ($_GET['sayfa_son'] ?? 604));

    $stmt = $db->prepare("SELECT sayfa_no FROM okuma_kayit
                          WHERE uye_id=:u AND kitap_id=:k
                          AND sayfa_no BETWEEN :bas AND :son
                          ORDER BY sayfa_no");
    $stmt->execute([':u'=>$user_id,':k'=>$kitap_id,':bas'=>$sayfa_bas,':son'=>$sayfa_son]);
    $sayfalar = $stmt->fetchAll(PDO::FETCH_COLUMN);

    ok(['basari' => true, 'okunanlar' => array_map('intval', $sayfalar)]);
}

// ─── Son okunan sayfayı getir ──────────────────────────────
if ($action === 'son_okunan') {
    $kitap_id  = (int)($input['kitap_id']  ?? ($_GET['kitap_id']  ?? 1));
    $sayfa_bas = (int)($input['sayfa_bas'] ?? ($_GET['sayfa_bas'] ?? 0));
    $sayfa_son = (int)($input['sayfa_son'] ?? ($_GET['sayfa_son'] ?? 604));

    $stmt = $db->prepare("SELECT MAX(sayfa_no) AS son FROM okuma_kayit
                          WHERE uye_id=:u AND kitap_id=:k
                          AND sayfa_no BETWEEN :bas AND :son");
    $stmt->execute([':u'=>$user_id,':k'=>$kitap_id,':bas'=>$sayfa_bas,':son'=>$sayfa_son]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    ok(['basari' => true, 'son_sayfa' => $row['son'] ? (int)$row['son'] : null]);
}

// ─── Topic listesi ─────────────────────────────────────────
if ($action === 'topic_liste') {
    $stmt = $db->prepare("
        SELECT t.id, t.baslik, t.kitap_id, t.sayfa_bas, t.sayfa_son, t.son_tarih,
               k.baslik AS kitap_baslik, k.bolum_adi,
               (SELECT COUNT(*) FROM okuma_kayit o
                WHERE o.uye_id=t.uye_id AND o.kitap_id=t.kitap_id
                AND o.sayfa_no BETWEEN t.sayfa_bas AND t.sayfa_son) AS okunan
        FROM okuma_topic t
        JOIN kitaplar k ON k.id = t.kitap_id
        WHERE t.uye_id=:u AND t.aktif=1
        ORDER BY t.id DESC
    ");
    $stmt->execute([':u' => $user_id]);
    $liste = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ok(['basari' => true, 'liste' => $liste]);
}

// ─── Yeni topic oluştur ────────────────────────────────────
if ($action === 'topic_olustur') {
    $baslik    = trim($input['baslik']    ?? 'Kendi hatmim');
    $kitap_id  = (int)($input['kitap_id']  ?? 1);
    $sayfa_bas = (int)($input['sayfa_bas'] ?? 0);
    $sayfa_son = (int)($input['sayfa_son'] ?? 604);
    $son_tarih = $input['son_tarih'] ?? null;

    if ($sayfa_son < $sayfa_bas) ok(['hata' => 'Bitiş sayfası başlangıçtan küçük olamaz']);

    $stmt = $db->prepare("INSERT INTO okuma_topic
                          (uye_id, baslik, kitap_id, sayfa_bas, sayfa_son, son_tarih)
                          VALUES (:u,:b,:k,:bas,:son,:tarih)");
    $stmt->execute([
        ':u'     => $user_id,
        ':b'     => $baslik,
        ':k'     => $kitap_id,
        ':bas'   => $sayfa_bas,
        ':son'   => $sayfa_son,
        ':tarih' => $son_tarih ?: null,
    ]);

    ok(['basari' => true, 'topic_id' => $db->lastInsertId()]);
}

// ─── Topic sil ─────────────────────────────────────────────
if ($action === 'topic_sil') {
    $topic_id = (int)($input['topic_id'] ?? 0);
    $db->prepare("UPDATE okuma_topic SET aktif=0 WHERE id=:id AND uye_id=:u")
       ->execute([':id' => $topic_id, ':u' => $user_id]);
    ok(['basari' => true]);
}

// ─── Kitap listesi (modal için) ────────────────────────────
if ($action === 'kitap_listesi_public') {
    try {
        $stmt = $db->query("SELECT id, baslik, bolum_adi, sayfa_bas, sayfa_son FROM kitaplar WHERE aktif=1 ORDER BY sira, id");
        $kitaplar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $kitaplar = [['id'=>1,'baslik'=>"Kur'an-ı Kerim",'bolum_adi'=>'Cüz','sayfa_bas'=>0,'sayfa_son'=>604]];
    }
    ok(['basari' => true, 'kitaplar' => $kitaplar]);
}

ok(['hata' => 'Bilinmeyen işlem: ' . $action]);