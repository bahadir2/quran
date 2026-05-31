<?php
require_once 'conn.php';

$results      = [];
$error        = '';
$searched     = false;
$hex_input    = '';
$char_display = '';
$hex_parts    = [];   // temizlenmiş hex parçaları
$search_str   = '';   // birleşik UTF-8 arama dizisi
$total        = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw       = trim($_POST['hex_code'] ?? '');
    $hex_input = $raw;
    $searched  = true;

    // Virgülle böl, boşlukları temizle
    $parts = array_map('trim', explode(',', $raw));

    $valid    = true;
    $chars    = [];

    foreach ($parts as $part) {
        if ($part === '') continue;

        // 06EC / 0x06EC / U+06EC formatlarını kabul et
        $clean = preg_replace('/^(0x|U\+)/i', '', $part);

        if (!preg_match('/^[0-9A-Fa-f]{1,6}$/', $clean)) {
            $error = 'Geçersiz hex kodu: <strong>' . htmlspecialchars($part) . '</strong> — Örnek: 06EC &nbsp;·&nbsp; 0x06EC &nbsp;·&nbsp; U+06EC';
            $valid = false;
            break;
        }

        $codepoint  = hexdec($clean);
        $char       = mb_chr($codepoint, 'UTF-8');
        $hex_parts[] = strtoupper($clean);
        $chars[]    = $char;
    }

    if ($valid && empty($chars)) {
        $error = 'En az bir hex kodu girmelisiniz.';
        $valid = false;
    }

    if ($valid) {
        $search_str   = implode('', $chars);   // yan yana birleşik dizi
        $char_display = $search_str;

        try {
            $db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_bin");

            $stmt = $db->prepare(
                "SELECT id, page, ayno, `or`
                 FROM quran
                 WHERE `or` LIKE :pattern
                 ORDER BY id ASC"
            );
            $stmt->execute([':pattern' => '%' . $search_str . '%']);
            $results = $stmt->fetchAll();
            $total   = count($results);

        } catch (PDOException $e) {
            error_log("quran_filter query error: " . $e->getMessage());
            $error = 'Sorgu sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kuran Karakter Filtresi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=IBM+Plex+Mono:wght@400;600&family=Tajawal:wght@300;400;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg:        #0d1117;
    --surface:   #161b22;
    --border:    #30363d;
    --accent:    #d4a853;
    --accent2:   #7ee787;
    --text:      #e6edf3;
    --muted:     #8b949e;
    --danger:    #f85149;
    --row-even:  #161b22;
    --row-odd:   #0d1117;
    --row-hover: #1f2937;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Tajawal', sans-serif;
    min-height: 100vh;
    padding: 2rem 1rem 4rem;
  }
  .topbar {
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), transparent);
    margin-bottom: 2.5rem;
  }
  header { text-align: center; margin-bottom: 2.5rem; }
  header .label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: .7rem;
    letter-spacing: .25em;
    color: var(--accent);
    text-transform: uppercase;
    margin-bottom: .5rem;
  }
  header h1 {
    font-family: 'Amiri', serif;
    font-size: clamp(1.6rem, 5vw, 2.6rem);
    font-weight: 700;
    line-height: 1.2;
  }
  header p { color: var(--muted); margin-top: .5rem; font-size: .95rem; }

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 1.6rem 2rem;
    max-width: 680px;
    margin: 0 auto 2rem;
  }
  .form-row { display: flex; gap: .75rem; align-items: stretch; flex-wrap: wrap; }
  .input-wrap { flex: 1; min-width: 180px; position: relative; }
  .input-wrap .prefix {
    position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
    font-family: 'IBM Plex Mono', monospace; font-size: .85rem;
    color: var(--accent); pointer-events: none; user-select: none;
  }
  input[type="text"] {
    width: 100%; padding: .85rem 1rem .85rem 2.8rem;
    background: var(--bg); border: 1px solid var(--border); border-radius: 7px;
    color: var(--text); font-family: 'IBM Plex Mono', monospace;
    font-size: 1.05rem; letter-spacing: .08em; outline: none; transition: border-color .2s;
  }
  input[type="text"]:focus { border-color: var(--accent); }
  input[type="text"]::placeholder { color: var(--muted); }
  button[type="submit"] {
    padding: .85rem 1.8rem; background: var(--accent); color: #0d1117;
    border: none; border-radius: 7px; font-family: 'Tajawal', sans-serif;
    font-weight: 700; font-size: 1rem; cursor: pointer;
    transition: opacity .2s, transform .1s; white-space: nowrap;
  }
  button[type="submit"]:hover  { opacity: .88; }
  button[type="submit"]:active { transform: scale(.97); }
  .hint { margin-top: .8rem; font-size: .8rem; color: var(--muted); font-family: 'IBM Plex Mono', monospace; }
  .hint span { color: var(--accent2); }

  .error-box {
    background: rgba(248,81,73,.1); border: 1px solid var(--danger);
    border-radius: 7px; padding: .9rem 1.2rem; color: var(--danger);
    margin: 1rem auto; max-width: 680px; font-size: .9rem;
  }
  .stats {
    max-width: 960px; margin: 0 auto 1rem;
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
  }
  .stat-pill {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 20px; padding: .35rem 1rem;
    font-size: .8rem; font-family: 'IBM Plex Mono', monospace;
  }
  .stat-pill strong { color: var(--accent2); }
  /* Stats satırındaki her bir karakter kodu */
  .hex-tag {
    display: inline-flex; align-items: center; gap: .4rem;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 20px; padding: .35rem .85rem;
    font-size: .8rem; font-family: 'IBM Plex Mono', monospace;
  }
  .hex-tag .cp  { color: var(--muted); }
  .hex-tag .ch  { font-family: 'Amiri', serif; font-size: 1.4rem; color: var(--accent); line-height: 1; }
  /* Birleşik dizi önizlemesi */
  .char-preview {
    font-family: 'Amiri', serif; font-size: 1.8rem; color: var(--accent);
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 7px; padding: .05rem .7rem; line-height: 1;
    display: inline-flex; align-items: center;
  }

  .table-wrap {
    max-width: 960px; margin: 0 auto; overflow-x: auto;
    border-radius: 10px; border: 1px solid var(--border);
  }
  table { width: 100%; border-collapse: collapse; font-size: .92rem; }
  thead th {
    background: #1c2128; padding: .85rem 1rem; text-align: left;
    font-family: 'IBM Plex Mono', monospace; font-size: .7rem;
    letter-spacing: .15em; color: var(--muted); text-transform: uppercase;
    border-bottom: 1px solid var(--border); white-space: nowrap;
  }
  tbody tr:nth-child(even) { background: var(--row-even); }
  tbody tr:nth-child(odd)  { background: var(--row-odd); }
  tbody tr { transition: background .15s; }
  tbody tr:hover { background: var(--row-hover); }
  td { padding: .75rem 1rem; border-bottom: 1px solid rgba(48,54,61,.5); vertical-align: middle; }
  tbody tr:last-child td { border-bottom: none; }
  .td-num  { font-family: 'IBM Plex Mono', monospace; color: var(--muted); font-size: .8rem; }
  .td-id   { font-family: 'IBM Plex Mono', monospace; color: var(--muted); }
  .td-page { font-family: 'IBM Plex Mono', monospace; color: var(--accent2); font-weight: 600; }
  .td-ayno { font-family: 'IBM Plex Mono', monospace; color: var(--accent);  font-weight: 600; }
  .td-text {
    font-family: 'Amiri', serif; font-size: 1.2rem;
    direction: rtl; text-align: right; line-height: 2; max-width: 500px;
  }
  .hl {
    background: rgba(212,168,83,.3); color: var(--accent);
    border-radius: 3px; outline: 1px solid rgba(212,168,83,.5);
  }
  .empty { text-align: center; padding: 3rem 1rem; color: var(--muted); font-size: 1rem; }
  .empty .icon { font-size: 2.5rem; display: block; margin-bottom: .6rem; }
  footer {
    text-align: center; margin-top: 3rem;
    font-size: .75rem; color: var(--muted); font-family: 'IBM Plex Mono', monospace;
  }
</style>
</head>
<body>

<div class="topbar"></div>

<header>
  <div class="label">Unicode · Karakter Arama</div>
  <h1>قرآن كريم — Kuran Karakter Filtresi</h1>
  <p>Hex kodu girerek Kuran metninde özel karakterleri arayın</p>
</header>

<div class="card">
  <form method="POST" action="">
    <div class="form-row">
      <div class="input-wrap">
        <span class="prefix">U+</span>
        <input
          type="text"
          name="hex_code"
          placeholder="06EC,0653"
          value="<?= htmlspecialchars($hex_input) ?>"
          maxlength="80"
          autocomplete="off"
          spellcheck="false"
        >
      </div>
      <button type="submit">Ara →</button>
    </div>
    <div class="hint">
      Tek karakter: <span>06EC</span> &nbsp;·&nbsp;
      Yan yana iki karakter: <span>06EC,0653</span> &nbsp;·&nbsp;
      Desteklenen formatlar: <span>06EC</span> <span>0x06EC</span> <span>U+06EC</span>
    </div>
  </form>
</div>

<?php if ($error): ?>
  <div class="error-box">⚠ <?= $error ?></div>
<?php endif; ?>

<?php if ($searched && !$error): ?>
  <div class="stats">
    <?php foreach ($hex_parts as $i => $hp):
      $ch = mb_chr(hexdec($hp), 'UTF-8');
    ?>
      <div class="hex-tag">
        <span class="cp">U+<?= $hp ?></span>
        <span class="ch"><?= htmlspecialchars($ch) ?></span>
      </div>
      <?php if ($i < count($hex_parts) - 1): ?>
        <span style="color:var(--muted);font-family:'IBM Plex Mono',monospace;font-size:.85rem;">+</span>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php if (count($hex_parts) > 1): ?>
      <span style="color:var(--muted);font-family:'IBM Plex Mono',monospace;font-size:.75rem;">=</span>
      <div class="char-preview"><?= htmlspecialchars($char_display) ?></div>
    <?php endif; ?>

    <div class="stat-pill">
      Bulunan: <strong><?= $total ?></strong> ayet
    </div>
  </div>

  <?php if ($total === 0): ?>
    <div class="empty">
      <span class="icon">🔍</span>
      Bu karakter dizisini içeren herhangi bir ayet bulunamadı.
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>ID</th>
            <th>Sayfa</th>
            <th>Ayet No</th>
            <th style="text-align:right">Ayet Metni</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Highlight: birleşik diziyi tek <span> ile işaretle
          $search_esc = htmlspecialchars($search_str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          $row_num    = 0;
          foreach ($results as $row):
            $row_num++;
            $verse_html = htmlspecialchars($row['or'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $verse_html = str_replace(
                $search_esc,
                '<span class="hl">' . $search_esc . '</span>',
                $verse_html
            );
          ?>
          <tr>
            <td class="td-num"><?= $row_num ?></td>
            <td class="td-id"><?= (int)$row['id'] ?></td>
            <td class="td-page"><?= htmlspecialchars($row['page']) ?></td>
            <td class="td-ayno"><?= htmlspecialchars($row['ayno']) ?></td>
            <td class="td-text"><?= $verse_html ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php endif; ?>

<footer>quran_filter.php · conn.php · PDO / utf8mb4</footer>

</body>
</html>