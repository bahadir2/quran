<?php
require_once 'conn.php';

$rows  = [];
$error = '';

try {
    $db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_bin");

    $stmt = $db->query("
        SELECT
            q.page,
            q.id   AS first_id,
            q.ayno AS first_ayno
        FROM quran q
        INNER JOIN (
            SELECT page, MIN(id) AS min_id
            FROM quran
            GROUP BY page
        ) sub ON q.id = sub.min_id
        ORDER BY CAST(q.page AS UNSIGNED) ASC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("page_control query error: " . $e->getMessage());
    $error = 'Sorgu sırasında bir hata oluştu.';
}

$total_pages = count($rows);
?>
<!DOCTYPE html>
<html lang="tr" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kuran — Sayfa İlk Ayet Kontrolü</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=IBM+Plex+Mono:wght@400;600&family=Tajawal:wght@300;400;700&display=swap" rel="stylesheet">
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

  header { text-align: center; margin-bottom: 2rem; }
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
    font-size: clamp(1.5rem, 4vw, 2.4rem);
    font-weight: 700;
    line-height: 1.2;
  }
  header p { color: var(--muted); margin-top: .4rem; font-size: .9rem; }

  .toolbar {
    max-width: 680px;
    margin: 0 auto 1.2rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
  }
  .stat-pill {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: .35rem 1rem;
    font-size: .8rem;
    font-family: 'IBM Plex Mono', monospace;
    white-space: nowrap;
  }
  .stat-pill strong { color: var(--accent2); }

  .search-wrap { position: relative; flex: 1; min-width: 140px; max-width: 220px; }
  .search-wrap input {
    width: 100%;
    padding: .52rem 1rem .52rem 2.2rem;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 7px;
    color: var(--text);
    font-family: 'IBM Plex Mono', monospace;
    font-size: .85rem;
    outline: none;
    transition: border-color .2s;
  }
  .search-wrap input:focus { border-color: var(--accent); }
  .search-wrap input::placeholder { color: var(--muted); }
  .search-icon {
    position: absolute; left: .7rem; top: 50%; transform: translateY(-50%);
    color: var(--muted); font-size: .85rem; pointer-events: none;
  }

  .jump-wrap { display: flex; align-items: center; gap: .5rem; }
  .jump-wrap label { font-size: .8rem; color: var(--muted); font-family: 'IBM Plex Mono', monospace; white-space: nowrap; }
  .jump-wrap input[type="number"] {
    width: 70px;
    padding: .5rem .6rem;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 7px;
    color: var(--text);
    font-family: 'IBM Plex Mono', monospace;
    font-size: .85rem;
    outline: none;
    text-align: center;
    transition: border-color .2s;
    -moz-appearance: textfield;
  }
  .jump-wrap input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; }
  .jump-wrap input[type="number"]:focus { border-color: var(--accent); }
  .jump-btn {
    padding: .5rem .9rem;
    background: var(--accent);
    color: #0d1117;
    border: none;
    border-radius: 7px;
    font-family: 'Tajawal', sans-serif;
    font-weight: 700;
    font-size: .85rem;
    cursor: pointer;
    transition: opacity .2s;
  }
  .jump-btn:hover { opacity: .85; }

  .clear-btn {
    padding: .5rem .9rem;
    background: transparent;
    color: var(--muted);
    border: 1px solid var(--border);
    border-radius: 7px;
    font-family: 'Tajawal', sans-serif;
    font-size: .82rem;
    cursor: pointer;
    transition: color .2s, border-color .2s;
    white-space: nowrap;
  }
  .clear-btn:hover { color: var(--danger); border-color: var(--danger); }

  .table-wrap {
    max-width: 680px;
    margin: 0 auto;
    border-radius: 10px;
    border: 1px solid var(--border);
    overflow: hidden;
  }

  table { width: 100%; border-collapse: collapse; font-size: .9rem; }

  thead th {
    background: #1c2128;
    padding: .8rem 1rem;
    text-align: left;
    font-family: 'IBM Plex Mono', monospace;
    font-size: .68rem;
    letter-spacing: .15em;
    color: var(--muted);
    text-transform: uppercase;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 2;
  }
  thead th.th-check { width: 44px; text-align: center; }

  tbody tr:nth-child(even) { background: var(--row-even); }
  tbody tr:nth-child(odd)  { background: var(--row-odd); }
  tbody tr { transition: background .12s; }
  tbody tr:hover { background: var(--row-hover); }

  td {
    padding: .65rem 1rem;
    border-bottom: 1px solid rgba(48,54,61,.45);
    vertical-align: middle;
  }
  tbody tr:last-child td { border-bottom: none; }

  .td-check { width: 44px; text-align: center; }
  input[type="checkbox"] {
    width: 15px; height: 15px;
    accent-color: var(--accent);
    cursor: pointer;
    vertical-align: middle;
  }

  .td-num  { font-family: 'IBM Plex Mono', monospace; color: var(--muted); font-size: .75rem; width: 48px; }
  .td-page { font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: .95rem; color: var(--accent2); width: 80px; }
  .td-id   { font-family: 'IBM Plex Mono', monospace; color: var(--muted); font-size: .8rem; width: 80px; }
  .td-ayno { font-family: 'IBM Plex Mono', monospace; font-weight: 600; color: var(--accent); }

  tr.checked-row td { opacity: .35; }
  tr.checked-row .td-page { text-decoration: line-through; }

  tr.hidden-row { display: none; }
  tr.match-row  { background: rgba(212,168,83,.07) !important; }
  tr.match-row:hover { background: rgba(212,168,83,.13) !important; }

  @keyframes flash {
    0%   { background: rgba(212,168,83,.4); }
    100% { background: transparent; }
  }
  .flash-row { animation: flash 1s ease-out; }

  .error-box {
    background: rgba(248,81,73,.1);
    border: 1px solid var(--danger);
    border-radius: 7px;
    padding: .9rem 1.2rem;
    color: var(--danger);
    margin: 1rem auto;
    max-width: 680px;
    font-size: .9rem;
  }

  footer {
    text-align: center;
    margin-top: 3rem;
    font-size: .72rem;
    color: var(--muted);
    font-family: 'IBM Plex Mono', monospace;
  }
</style>
</head>
<body>

<div class="topbar"></div>

<header>
  <div class="label">Sayfa · İlk Ayet · Kontrol</div>
  <h1>قرآن كريم — Sayfa İlk Ayet Kontrolü</h1>
  <p>Her sayfanın ilk satırı (min id) ve ayet numarası</p>
</header>

<?php if ($error): ?>
  <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
<?php else: ?>

<div class="toolbar">
  <div class="stat-pill">
    Toplam: <strong><?= $total_pages ?></strong> sayfa
  </div>

  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" id="search-input" placeholder="Sayfa / ayet ara…" autocomplete="off">
  </div>

  <div class="jump-wrap">
    <label for="jump-input">Git:</label>
    <input type="number" id="jump-input" min="1" max="604" placeholder="1–604">
    <button class="jump-btn" onclick="jumpToPage()">↵</button>
  </div>

  <button class="clear-btn" onclick="clearChecked()">Tikleri temizle</button>

  <div class="stat-pill" id="checked-count" style="display:none">
    Tiklenen: <strong id="checked-num">0</strong>
  </div>
</div>

<div class="table-wrap">
  <table id="main-table">
    <thead>
      <tr>
        <th class="th-check">
          <input type="checkbox" id="check-all" title="Tümünü işaretle / kaldır">
        </th>
        <th>Sayfa</th>
        <th>Ayet No</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $i => $row): ?>
      <tr id="page-row-<?= (int)$row['page'] ?>"
          data-page="<?= (int)$row['page'] ?>"
          data-ayno="<?= htmlspecialchars($row['first_ayno']) ?>">
        <td class="td-check">
          <input type="checkbox" class="row-check" data-page="<?= (int)$row['page'] ?>" onchange="onCheck(this)">
        </td>
        <td class="td-page"><?= (int)$row['page'] ?></td>
        <td class="td-ayno"><?= htmlspecialchars($row['first_ayno']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
const STORAGE_KEY  = 'quran_checked_pages';
const allRows      = Array.from(document.querySelectorAll('#main-table tbody tr'));
const searchInput  = document.getElementById('search-input');
const checkAllBox  = document.getElementById('check-all');
const checkedCount = document.getElementById('checked-count');
const checkedNum   = document.getElementById('checked-num');

// ── localStorage yardımcıları ──────────────────────────────
function loadChecked() {
  try { return new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')); }
  catch(e) { return new Set(); }
}
function saveChecked(set) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify([...set]));
}

// ── Sayfa yüklenince tikleri geri yükle ───────────────────
const checkedPages = loadChecked();

document.querySelectorAll('.row-check').forEach(cb => {
  if (checkedPages.has(Number(cb.dataset.page))) {
    cb.checked = true;
    cb.closest('tr').classList.add('checked-row');
  }
});

updateUI();

// ── Tek satır değişince ────────────────────────────────────
function onCheck(cb) {
  const page = Number(cb.dataset.page);
  cb.closest('tr').classList.toggle('checked-row', cb.checked);
  if (cb.checked) checkedPages.add(page);
  else            checkedPages.delete(page);
  saveChecked(checkedPages);
  updateUI();
}

// ── Tümünü seç / kaldır ────────────────────────────────────
checkAllBox.addEventListener('change', function () {
  const visible = allRows.filter(tr => !tr.classList.contains('hidden-row'));
  visible.forEach(tr => {
    const cb = tr.querySelector('.row-check');
    const page = Number(cb.dataset.page);
    cb.checked = this.checked;
    tr.classList.toggle('checked-row', this.checked);
    if (this.checked) checkedPages.add(page);
    else              checkedPages.delete(page);
  });
  saveChecked(checkedPages);
  updateUI();
});

// ── UI güncelle (check-all durumu + sayaç) ─────────────────
function updateUI() {
  const visible  = allRows.filter(tr => !tr.classList.contains('hidden-row'));
  const numChk   = visible.filter(tr => tr.querySelector('.row-check').checked).length;
  checkAllBox.indeterminate = numChk > 0 && numChk < visible.length;
  checkAllBox.checked       = numChk > 0 && numChk === visible.length;

  const total = checkedPages.size;
  checkedCount.style.display = total > 0 ? '' : 'none';
  checkedNum.textContent = total;
}

// ── Tikleri temizle ────────────────────────────────────────
function clearChecked() {
  checkedPages.clear();
  saveChecked(checkedPages);
  document.querySelectorAll('.row-check').forEach(cb => {
    cb.checked = false;
    cb.closest('tr').classList.remove('checked-row');
  });
  checkAllBox.checked = false;
  checkAllBox.indeterminate = false;
  updateUI();
}

// ── Arama filtresi ─────────────────────────────────────────
searchInput.addEventListener('input', function () {
  const q = this.value.trim().toLowerCase();
  allRows.forEach(tr => {
    const match = !q || tr.dataset.page.includes(q) || tr.dataset.ayno.toLowerCase().includes(q);
    tr.classList.toggle('hidden-row', !match);
    tr.classList.toggle('match-row', !!q && match);
  });
  updateUI();
});

// ── Sayfaya git ────────────────────────────────────────────
function jumpToPage() {
  const val = parseInt(document.getElementById('jump-input').value, 10);
  if (!val || val < 1 || val > 604) { alert('1–604 arası bir sayfa girin.'); return; }

  if (searchInput.value) {
    searchInput.value = '';
    allRows.forEach(tr => tr.classList.remove('hidden-row', 'match-row'));
    updateUI();
  }

  const target = document.getElementById('page-row-' + val);
  if (!target) { alert('Sayfa ' + val + ' bulunamadı.'); return; }

  target.scrollIntoView({ behavior: 'smooth', block: 'center' });
  target.classList.remove('flash-row');
  void target.offsetWidth;
  target.classList.add('flash-row');
  target.addEventListener('animationend', () => target.classList.remove('flash-row'), { once: true });
}

document.getElementById('jump-input').addEventListener('keydown', e => {
  if (e.key === 'Enter') jumpToPage();
});
</script>

<?php endif; ?>

<footer>page_control.php · conn.php · PDO / utf8mb4 · <?= $total_pages ?> sayfa</footer>

</body>
</html>