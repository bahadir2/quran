<?php
session_start();
include("conn.php");

include 'orfunctions.php';
include 'svfunctions.php'; 

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ── YENİ: Kullanıcı ve topic bilgisi ──
$user_id = $_SESSION['user_id'] ?? 0;
$topic_liste = [];
if ($user_id) {
    try {
        $stmt = $db->prepare("
            SELECT t.id, t.baslik, t.kitap_id, t.sayfa_bas, t.sayfa_son, t.son_tarih
            FROM okuma_topic t
            WHERE t.uye_id = :u AND t.aktif = 1
            ORDER BY t.id DESC
        ");
        $stmt->execute([':u' => $user_id]);
        $topic_liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $topic_liste = [];
    }
}

$page_number = isset($_GET['page_number']) ? intval($_GET['page_number']) : 1;
$selected_surah = isset($_GET['surah']) ? intval($_GET['surah']) : 1;
$selected_juz = isset($_GET['juz']) ? intval($_GET['juz']) : 1;
$selected_verse = isset($_GET['verse']) ? intval($_GET['verse']) : 1;
$changed = isset($_GET['changed']) ? $_GET['changed'] : '';
$kitap_id = isset($_GET['book']) ? intval($_GET['book']) : 1;

$surah_verses = array(
    1 => 7, 2 => 286, 3 => 200, 4 => 176, 5 => 120,
    6 => 165, 7 => 206, 8 => 75, 9 => 129, 10 => 109,
    11 => 123, 12 => 111, 13 => 43, 14 => 52, 15 => 99,
    16 => 128, 17 => 111, 18 => 110, 19 => 98, 20 => 135,
    21 => 112, 22 => 78, 23 => 118, 24 => 64, 25 => 77,
    26 => 227, 27 => 93, 28 => 88, 29 => 69, 30 => 60,
    31 => 34, 32 => 30, 33 => 73, 34 => 54, 35 => 45,
    36 => 83, 37 => 182, 38 => 88, 39 => 75, 40 => 85,
    41 => 54, 42 => 53, 43 => 89, 44 => 59, 45 => 37,
    46 => 35, 47 => 38, 48 => 29, 49 => 18, 50 => 45,
    51 => 60, 52 => 49, 53 => 62, 54 => 55, 55 => 78,
    56 => 96, 57 => 29, 58 => 22, 59 => 24, 60 => 13,
    61 => 14, 62 => 11, 63 => 11, 64 => 18, 65 => 12,
    66 => 12, 67 => 30, 68 => 52, 69 => 52, 70 => 44,
    71 => 28, 72 => 28, 73 => 20, 74 => 56, 75 => 40,
    76 => 31, 77 => 50, 78 => 40, 79 => 46, 80 => 42,
    81 => 29, 82 => 19, 83 => 36, 84 => 25, 85 => 22,
    86 => 17, 87 => 19, 88 => 26, 89 => 30, 90 => 20,
    91 => 15, 92 => 21, 93 => 11, 94 => 8, 95 => 8,
    96 => 19, 97 => 5, 98 => 8, 99 => 8, 100 => 11,
    101 => 11, 102 => 8, 103 => 3, 104 => 9, 105 => 5,
    106 => 4, 107 => 7, 108 => 3, 109 => 6, 110 => 3,
    111 => 5, 112 => 4, 113 => 5, 114 => 6
);

switch ($changed) {
    case 'surahSelect':
        $sql = "SELECT * FROM quran WHERE sur = $selected_surah ORDER BY id ASC LIMIT 1";
        break;
    case 'verseSelect':
        $sql = "SELECT * FROM quran WHERE sur = $selected_surah AND ayno = $selected_verse ORDER BY id ASC LIMIT 1";
        break;
    case 'pageInput':
        $sql = "SELECT * FROM quran WHERE page = $page_number ORDER BY id ASC LIMIT 1";
        break;
    case 'juzSelect':
        $sql = "SELECT * FROM quran WHERE cuz = $selected_juz ORDER BY id ASC LIMIT 1";
        break;
    default:
        $sql = "SELECT * FROM quran WHERE page = $page_number OR sur = $selected_surah ORDER BY id ASC";
        break;
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $page_number = $row["page"];
    $selected_surah = $row["sur"];
    $selected_juz = $row["cuz"];
    $selected_verse = $row["ayno"];
    $id = $row["id"];
    $ayno = $row["ayno"];
    $altbilgi = $row["aciklama"];
    $or = $row["or"];
    $tr = $row["tr"];
    $sv = $row["sv"];
    $en = $row["en"];
    $dipnot = $row["aciklama2"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quran Pages</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@400;500;600;700&family=Lateef:wght@200;300;400;500;600;700;800&family=Mirza:wght@400;500;600;700&family=Katibeh&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- ── YENİ: Sayfa ve kullanıcı bilgisi JS'e aktarılıyor ── -->
    <script>
    window.HATIM_USER_ID = <?= (int)$user_id ?>;
    window.SAYFA_NO      = <?= (int)$page_number ?>;
    window.KITAP_ID      = <?= (int)$kitap_id ?>;
    </script>
</head>
<body>

<div class="navbar">
    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>

    <!-- ── YENİ: topicSelect dinamik doluyor ── -->
    <div class="topic-container">
        <label for="topicSelect">My Topics:</label>
        <select id="topicSelect" name="topik">
            <option value="0">Kapalı</option>
            <?php foreach ($topic_liste as $t): ?>
            <option value="<?= $t['id'] ?>"
                    data-bas="<?= $t['sayfa_bas'] ?>"
                    data-son="<?= $t['sayfa_son'] ?>"
                    data-kitap="<?= $t['kitap_id'] ?>">
                📖 <?= htmlspecialchars($t['baslik']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button id="plusButton" class="plus-button">+</button>
    </div>

    <?php endif; ?>

    <!-- ana menu -->
    <div class="form-group">
        <label for="boxSelect">Library:</label>
        <select id="boxSelect" class="combobox">
            <?php include 's_books.php'; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="juzSelect">Juz:</label>
        <select id="juzSelect" onchange="goToPage('juzSelect')">
            <?php for ($i = 1; $i <= 30; $i++): ?>
                <option value="<?= $i ?>" <?= ($selected_juz == $i) ? 'selected' : '' ?>>
                    <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="pageInput">Page:</label>
        <div class="page-navigation">
            <button type="button" class="nav-button" onclick="changePage('prev')">&laquo;</button>
            <input type="number" id="pageInput" min="0" max="604" value="<?= $page_number ?>"
                onchange="goToPage('pageInput', this.value)"
                onkeyup="if(event.keyCode===13) goToPage('pageInput', this.value)"
                autocomplete="off">
            <button type="button" class="nav-button" onclick="changePage('next')">&raquo;</button>
        </div>
    </div>

    <div class="form-group">
        <label for="surahSelect">Surah:</label>
        <select id="surahSelect" class="combobox">
            <?php include 's_surahs.php'; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="verseSelect">Verse:</label>
        <select id="verseSelect" onchange="changeVerse();">
            <?php
                $verse_count = isset($surah_verses[$selected_surah]) ? $surah_verses[$selected_surah] : 0;
                for ($i = 1; $i <= $verse_count; $i++) {
                    $selected = ($selected_verse == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                }
            ?>
        </select>
    </div>

    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <div class="action-bar">
        <button onclick="toggleSettings()" title="Ayarlar" class="action-button">&#9881;</button>
        <a href="info.html" title="Bilgi" target="_blank" class="action-button">&#8505;</a>
        <form id="logoutForm" action="logout.php" method="POST" style="display: inline;">
            <input type="hidden" name="logout" value="1">
            <input type="hidden" name="topicSelect" id="topicSelectInput">
            <input type="hidden" name="styleValue" id="styleValue">
            <input type="hidden" name="arabicFont" id="arabicFontInput">
            <input type="hidden" name="turkishChecked" id="turkishCheckedInput">
            <input type="hidden" name="swedishChecked" id="swedishCheckedInput">
            <input type="hidden" name="englishChecked" id="englishCheckedInput">
            <input type="hidden" name="fontSize" id="fontSizeInput">
            <input type="hidden" name="searchTerm" id="searchTermInput">
            <input type="hidden" name="searchResults" id="searchResultsInput">
            <button type="submit" class="action-button logout-button" title="Logout">Logout</button>
        </form>
    </div>
    </div><!-- navbar sonu -->

    <!-- Ayar Paneli (login) -->
    <div class="settings-bar" id="settingsBar">
        <div class="language-options">
            <label>Style<br>
                <select id="style" size="1" onchange="updateLanguages();">
                    <option selected>subtitle</option>
                    <option>standard</option>
                    <option>fast</option>
                    <option>single</option>
                </select>
            </label>
            <span class="divider"></span>
            <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> - Tr <br><font color=red size='2'>Suat Yıldırım</font></label>
            <span class="divider"></span>
            <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> - Sv <br><font color=red size='2'>Knut Bernström</font></label>
            <span class="divider"></span>
            <label><input type="checkbox" id="english" onclick="updateLanguages()"> - En <br><font color=red size='2'>Abdullah Yusuf Ali</font></label>
            <span class="divider"></span>
            <label class="size-label highlighted-label">Arabic:
                <select id="arabicFontSelect" onchange="changeArabicFont()">
                    <option value="Traditional Arabic" selected>Traditional Arabic</option>
                    <option value="KFGQPC Uthmanic Script HAFS" disabled>Mushaf (KFGQPC)</option>
                    <option value="Scheherazade New" disabled>Scheherazade New</option>
                    <option value="Lateef" disabled>Lateef</option>
                    <option value="Mirza" disabled>Mirza</option>
                    <option value="Katibeh" disabled>Katibeh</option>
                </select>
            </label>
            <label class="size-label highlighted-label">Size:
                <select id="fontSizeSelect" onchange="changeFontSizeFromSelect()">
                    <option value="24">24px</option>
                    <option value="32">32px</option>
                    <option value="40" selected>40px</option>
                    <option value="48">48px</option>
                    <option value="56">56px</option>
                    <option value="64">64px</option>
                </select>
            </label><br>
            <label>Ayetlerde Ara:
                <input type="text" id="searchInput" placeholder="Kelime veya ifade girin">
                <button type="button" onclick="searchVerses()">Ara</button>
            </label>
        </div>
        <div id="searchResultsContainer" style="position: relative; display: none;">
            <button id="closeButton" onclick="closeSearchResults()" style="position: absolute; top: 5px; right: 5px; background: transparent; color: black; border: none; font-size: 20px; line-height: 20px; text-align: center; cursor: pointer;">&times;</button>
            <div id="searchResults" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
        </div>
    </div>

    <?php else: ?>

    <div class="action-bar">
        <a href="info.html" title="Bilgi" target="_blank" class="action-button">&#8505;</a>
        <a onclick="toggleSettings()" title="Ayarlar" class="action-button">&#9881;</a>
        <button onclick="window.location.href='login.php'" class="action-button logout-button" title="Giriş"><span class="material-icons">login</span></button>
    </div>
    </div><!-- navbar sonu -->

    <!-- Ayar Paneli (misafir) -->
    <div class="settings-bar" id="settingsBar">
        <div class="language-options">
            <label>Style<br>
                <select id="style" size="1" onchange="updateLanguages();">
                    <option selected>subtitle</option>
                    <option>standard</option>
                    <option>fast</option>
                    <option>single</option>
                </select>
            </label>
            <span class="divider"></span>
            <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> - Tr</label>
            <span class="divider"></span>
            <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> - Sv</label>
            <span class="divider"></span>
            <label><input type="checkbox" id="english" onclick="updateLanguages()"> - En</label>
            <span class="divider"></span>
            <label class="size-label highlighted-label">Arabic:
                <select id="arabicFontSelect" onchange="changeArabicFont()">
                    <option value="Traditional Arabic" selected>Traditional Arabic</option>
                    <option value="KFGQPC Uthmanic Script HAFS" disabled>Mushaf (KFGQPC)</option>
                    <option value="Scheherazade New" disabled>Scheherazade New</option>
                    <option value="Lateef" disabled>Lateef</option>
                    <option value="Mirza" disabled>Mirza</option>
                    <option value="Katibeh" disabled>Katibeh</option>
                </select>
            </label>
            <label class="size-label highlighted-label">Size:
                <select id="fontSizeSelect" onchange="changeFontSizeFromSelect()">
                    <option value="24">24px</option>
                    <option value="32">32px</option>
                    <option value="40" selected>40px</option>
                    <option value="48">48px</option>
                    <option value="56">56px</option>
                    <option value="64">64px</option>
                </select>
            </label><br>
            <label>Ayetlerde Ara:
                <input type="text" id="searchInput" placeholder="Kelime veya ifade girin">
                <button type="button" onclick="searchVerses()">Ara</button>
            </label>
        </div>
        <div id="searchResultsContainer" style="position: relative; display: none;">
            <button id="closeButton" onclick="closeSearchResults()" style="position: absolute; top: 5px; right: 5px; background: transparent; color: black; border: none; font-size: 20px; line-height: 20px; text-align: center; cursor: pointer;">&times;</button>
            <div id="searchResults" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
        </div>
    </div>

    <?php endif; ?>

    <!-- ── YENİ: settingsBar2 — topic.php JS ile dolduruyor ── -->
    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <div class="settings-bar2" id="settingsBar2">
        <?php include 'topic.php'; ?>
    </div>
    <?php endif; ?>

<?php
echo "<br><br>";
include 'public.php';
?>

<script>
$(document).ready(function() {
    $("#boxSelect, #surahSelect, #juzSelect, #verseSelect, #topicSelect").select2({
        placeholder: "Seçiniz...",
        allowClear: true,
        language: { noResults: function() { return "Sonuç bulunamadı"; } }
    });

    $("#juzSelect").select2({ placeholder: "Cüz Seçin" });
    $("#verseSelect").select2({ placeholder: "Ayet Seçin" });

    $("#surahSelect").on("change", function() {
        var selectedSurah = $(this).val();
        if (selectedSurah) window.location.href = "?surah=" + selectedSurah + "&changed=surahSelect";
    });

    $("#juzSelect").on("change", function() {
        window.location.replace("?juz=" + $(this).val() + "&changed=juzSelect");
    });

    $("#verseSelect").on("change", function() { changeVerse(); });

    $("#boxSelect").select2({ width: 'resolve', allowClear: true, placeholder: "Seçiniz..." });
});

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const changed = urlParams.get("changed");
    const verse = urlParams.get("verse");

    if (changed !== "verseSelect") {
        document.querySelectorAll(".arabic").forEach(v => v.classList.remove("active"));
    }
    if (changed === "verseSelect" && verse) {
        const sel = document.querySelector(`.arabic[data-verse="${verse}"]`);
        if (sel) sel.classList.add("active");
    }

    document.querySelectorAll(".arabic").forEach(v => {
        v.addEventListener("click", function () {
            this.classList.toggle("active");
        });
    });

    const turkishChecked = sessionStorage.getItem("turkishChecked") === "true" || false;
    const swedishChecked = sessionStorage.getItem("swedishChecked") === "true" || false;
    const englishChecked = sessionStorage.getItem("englishChecked") === "true" || false;
    const styleValue = sessionStorage.getItem("styleValue") || "standard";

    document.getElementById("style").value = styleValue;
    document.getElementById("turkish").checked = turkishChecked;
    document.getElementById("swedish").checked = swedishChecked;
    document.getElementById("english").checked = englishChecked;

    const savedFont = sessionStorage.getItem("arabicFont") || "Traditional Arabic";
    const fontSelect = document.getElementById("arabicFontSelect");
    if (fontSelect) {
        fontSelect.value = savedFont;
        if (savedFont === "Amiri" && !document.getElementById("amiriFont")) {
            const link = document.createElement("link");
            link.id = "amiriFont";
            link.href = "https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap";
            link.rel = "stylesheet";
            document.head.appendChild(link);
        }
        applyArabicFont(savedFont);
    }

    const fontSize = sessionStorage.getItem("fontSize") || "40";
    const fontSizeSelect = document.getElementById("fontSizeSelect");
    if (fontSizeSelect) {
        fontSizeSelect.value = fontSize;
        applyFontSize(fontSize);
    }

    updateLanguages();

    const resultsHTML = sessionStorage.getItem("searchResults");
    const searchTerm = sessionStorage.getItem("searchTerm");
    if (resultsHTML && searchTerm) {
        const resultsDiv = document.getElementById("searchResults");
        if (!resultsDiv.innerHTML.includes(`<strong>Aranan Kelime:</strong> ${searchTerm}`)) {
            resultsDiv.innerHTML = resultsHTML;
            document.getElementById("searchResultsContainer").style.display = "block";
        }
    }
});

function changeArabicFont() {
    const selectedFont = document.getElementById("arabicFontSelect").value;
    if (selectedFont === "Amiri" && !document.getElementById("amiriFont")) {
        const link = document.createElement("link");
        link.id = "amiriFont";
        link.href = "https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap";
        link.rel = "stylesheet";
        document.head.appendChild(link);
        setTimeout(() => applyArabicFont(selectedFont), 500);
    } else {
        applyArabicFont(selectedFont);
    }
    sessionStorage.setItem("arabicFont", selectedFont);
}

function applyArabicFont(font) {
    document.querySelectorAll(".arabic, .arabic2").forEach(el => {
        switch(font) {
            case "KFGQPC Uthmanic Script HAFS": el.style.fontFamily = "'KFGQPC Uthmanic Script HAFS', 'Scheherazade New', serif"; break;
            case "Scheherazade New": el.style.fontFamily = "'Scheherazade New', 'Traditional Arabic', serif"; break;
            case "Lateef": el.style.fontFamily = "'Lateef', 'Traditional Arabic', serif"; break;
            case "Mirza": el.style.fontFamily = "'Mirza', 'Traditional Arabic', serif"; break;
            case "Katibeh": el.style.fontFamily = "'Katibeh', 'Traditional Arabic', serif"; break;
            case "Traditional Arabic": el.style.fontFamily = "'Traditional Arabic', serif"; break;
            default: el.style.fontFamily = `'${font}', 'Traditional Arabic', serif`;
        }
    });
}

function changeFontSizeFromSelect() {
    const size = document.getElementById("fontSizeSelect").value;
    applyFontSize(size);
    sessionStorage.setItem("fontSize", size);
    fetch("updatefontsize.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ fontSize: size })
    });
}

function applyFontSize(size) {
    document.querySelectorAll(".arabic, .arabic2").forEach(el => el.style.fontSize = size + "px");
}

function changeVerse() {
    const verse = document.getElementById("verseSelect").value;
    document.querySelectorAll(".arabic").forEach(v => v.classList.remove("active"));
    const sel = document.querySelector(`.arabic[data-verse="${verse}"]`);
    if (sel) sel.classList.add("active");
    const surah = document.getElementById("surahSelect").value;
    window.location.href = "?surah=" + surah + "&verse=" + verse + "&changed=verseSelect";
}

function changePage(direction) {
    const pageInput = document.getElementById("pageInput");
    let currentPage = parseInt(pageInput.value);
    if (direction === 'prev' && currentPage > 0) pageInput.value = currentPage - 1;
    else if (direction === 'next' && currentPage < 604) pageInput.value = currentPage + 1;
    goToPage('pageInput', pageInput.value);
}

function goToPage(changedElementId, gh) {
    let page_number = gh;
    const surah = document.getElementById("surahSelect").value;
    const verse = document.getElementById("verseSelect").value;
    const juz = document.getElementById("juzSelect").value;
    if (page_number < 0) page_number = 0;
    if (page_number > 604) page_number = 604;
    window.location.replace("?page_number=" + page_number + "&surah=" + surah + "&verse=" + verse + "&juz=" + juz + "&changed=" + changedElementId);
}

function updateLanguages() {
    function toggle(cls, show, block) {
        document.querySelectorAll('.' + cls).forEach(el => el.style.display = show ? (block ? 'block' : 'inline') : 'none');
    }

    const arabicFontSelect = document.getElementById("arabicFontSelect");
    const fontSizeSelect = document.getElementById("fontSizeSelect");
    if (arabicFontSelect) sessionStorage.setItem("arabicFont", arabicFontSelect.value);
    if (fontSizeSelect) sessionStorage.setItem("fontSize", fontSizeSelect.value);

    const styleValue = document.getElementById("style").value;
    const tr = document.getElementById("turkish").checked;
    const sv = document.getElementById("swedish").checked;
    const en = document.getElementById("english").checked;

    const first = document.getElementsByClassName("first")[0];

    switch (styleValue) {
        case 'subtitle':
            if (first) first.style.display = "inline";
            document.getElementsByClassName("blokarabic")[0]?.style && (document.getElementsByClassName("blokarabic")[0].style.display = "none");
            toggle('s_turkish', tr, true); toggle('s_swedish', sv, true); toggle('s_english', en, true);
            ['blokturkish','blokswedish','blokenglish'].forEach(c => { const el = document.getElementsByClassName(c)[0]; if(el) el.style.display='none'; });
            ['turkish','tas','swedish','sas','english','eas','as'].forEach(c => toggle(c, false));
            break;
        case 'standard':
            if (first) first.style.display = "inline";
            document.getElementsByClassName("blokarabic")[0]?.style && (document.getElementsByClassName("blokarabic")[0].style.display = "none");
            toggle('turkish', tr); toggle('tas', tr); toggle('swedish', sv); toggle('sas', sv);
            toggle('english', en); toggle('eas', en); toggle('as', tr || sv || en);
            ['blokturkish','blokswedish','blokenglish'].forEach(c => { const el = document.getElementsByClassName(c)[0]; if(el) el.style.display='none'; });
            break;
        case 'fast':
            if (first) first.style.display = "none";
            const btr = document.getElementsByClassName("blokturkish")[0]; if(btr) btr.style.display = tr ? 'inline' : 'none';
            const bsv = document.getElementsByClassName("blokswedish")[0]; if(bsv) bsv.style.display = sv ? 'inline' : 'none';
            const ben = document.getElementsByClassName("blokenglish")[0]; if(ben) ben.style.display = en ? 'inline' : 'none';
            const bor = document.getElementsByClassName("blokarabic")[0]; if(bor) bor.style.display = 'inline';
            break;
        case 'single':
            if (first) first.style.display = "none";
            const str2 = document.getElementsByClassName("blokturkish")[0]; if(str2) str2.style.display = tr ? 'inline' : 'none';
            const ssv2 = document.getElementsByClassName("blokswedish")[0]; if(ssv2) ssv2.style.display = sv ? 'inline' : 'none';
            const sen2 = document.getElementsByClassName("blokenglish")[0]; if(sen2) sen2.style.display = en ? 'inline' : 'none';
            const sor2 = document.getElementsByClassName("blokarabic")[0]; if(sor2) sor2.style.display = 'none';
            const mesaj = document.getElementById("mesaj");
            if (mesaj) mesaj.innerText = (tr || sv || en) ? '' : 'Ayarlar menusunden style ve dil secimi yapmalisiniz.';
            break;
    }

    sessionStorage.setItem("styleValue", styleValue);
    sessionStorage.setItem("turkishChecked", tr);
    sessionStorage.setItem("swedishChecked", sv);
    sessionStorage.setItem("englishChecked", en);
}

function toggleSettings() {
    const bar = document.getElementById("settingsBar");
    if (bar.style.display === "block") {
        bar.style.display = "none";
        updateLanguages();
        location.reload();
    } else {
        bar.style.display = "block";
    }
}

let hideTimeout;
document.getElementById("settingsBar").addEventListener("mouseenter", function() {
    if (hideTimeout) clearTimeout(hideTimeout);
});
document.getElementById("settingsBar").addEventListener("mouseleave", function() {
    hideTimeout = setTimeout(() => {
        this.style.display = "none";
        updateLanguages();
        location.reload();
    }, 800);
});

function searchVerses() {
    const searchTerm = document.getElementById("searchInput").value.trim();
    if (!searchTerm) { document.getElementById("searchResults").innerHTML = "Lütfen bir kelime veya ifade girin."; return; }
    document.getElementById("searchResultsContainer").style.display = "block";
    fetch(`search.php?q=${encodeURIComponent(searchTerm)}`)
        .then(r => r.json())
        .then(data => {
            const resultsDiv = document.getElementById("searchResults");
            resultsDiv.innerHTML = "";
            if (data.length > 0) {
                const html = `<div class="search-term"><strong>Aranan Kelime:</strong> ${searchTerm}</div>` +
                    data.map(v => `<div class="search-result" onclick="submitSurahVerse(${v.sur}, ${v.ayno})">${v.sur}-${v.ayno}</div>`).join("");
                resultsDiv.innerHTML = html;
                sessionStorage.setItem("searchResults", html);
                sessionStorage.setItem("searchTerm", searchTerm);
            } else {
                resultsDiv.innerHTML = "Sonuç bulunamadı.";
                sessionStorage.removeItem("searchResults");
                sessionStorage.removeItem("searchTerm");
            }
        }).catch(e => { document.getElementById("searchResults").innerHTML = "Bir hata oluştu."; });
}

function closeSearchResults() {
    document.getElementById("searchResultsContainer").style.display = "none";
    sessionStorage.removeItem("searchResults");
    sessionStorage.removeItem("searchTerm");
}

function submitSurahVerse(surah, verse) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set("surah", surah); urlParams.set("verse", verse); urlParams.set("changed", "verseSelect");
    window.location.href = "?" + urlParams.toString();
}

<?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
document.getElementById("logoutForm").addEventListener("submit", function() {
    const topicVal = document.getElementById("topicSelect").value;
    document.getElementById("topicSelectInput").value = topicVal || "0";
    document.getElementById("turkishCheckedInput").value = sessionStorage.getItem("turkishChecked") === "true" ? "1" : "0";
    document.getElementById("swedishCheckedInput").value = sessionStorage.getItem("swedishChecked") === "true" ? "1" : "0";
    document.getElementById("englishCheckedInput").value = sessionStorage.getItem("englishChecked") === "true" ? "1" : "0";
    document.getElementById("fontSizeInput").value = sessionStorage.getItem("fontSize") || "40";
    document.getElementById("arabicFontInput").value = sessionStorage.getItem("arabicFont") || "Traditional Arabic";
    document.getElementById("searchTermInput").value = sessionStorage.getItem("searchTerm") || "";
    document.getElementById("searchResultsInput").value = sessionStorage.getItem("searchResults") || "";
    document.getElementById("styleValue").value = sessionStorage.getItem("styleValue") || "";
    sessionStorage.setItem("topicSelect", topicVal);
});
<?php endif; ?>

(function() {
    let lastScrollY = window.scrollY;
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        const currentScrollY = window.scrollY;
        navbar.style.top = (currentScrollY > lastScrollY && currentScrollY > 60) ? '-200px' : '0';
        lastScrollY = currentScrollY;
    });
})();

document.querySelectorAll('.normal, .secde').forEach(function(el) {
    el.addEventListener('mouseenter', function(e) {
        const div = this.querySelector('div');
        if (!div) return;
        const mouseX = e.clientX;
        const sw = window.innerWidth;
        if (mouseX < sw / 3) { div.style.left='0'; div.style.right='auto'; div.style.textAlign='left'; }
        else if (mouseX > sw / 3 * 2) { div.style.left='auto'; div.style.right='0'; div.style.textAlign='right'; }
        else { div.style.left='0'; div.style.right='0'; div.style.textAlign='center'; }
    });
});
</script>

<p id='mesaj'><?php
$dipnot = trim($dipnot ?? '', ',');
$dizi = array_unique(array_filter(array_map('trim', explode(',', $dipnot))));
echo $id . ':<br> ' . $altbilgi . '<br>ayetno: ' . $ayno;

function getArabicSign($signNumber) {
    switch($signNumber) {
        case '1': return mb_chr(0x0615,'UTF-8').mb_chr(0x25Cc,'UTF-8');
        case '2': return '<table border="1" style="display:inline-block;border-width:0px;vertical-align:middle;margin:0 5px;"><tr><td style="border-style:none;text-align:center;vertical-align:middle;padding:2px 5px;"><div style="line-height:0.6;"><b><font size="4">&#1593;</font></b><br><font size="7">&#9676;</font></div></td></tr></table>';
        case '3': return mb_chr(0x0635,'UTF-8');
        case '4': return mb_chr(0x0632,'UTF-8');
        case '5': return mb_chr(0x25Cc,'UTF-8').mb_chr(0x0653,'UTF-8');
        case '6': return mb_chr(0x06EC,'UTF-8');
        case '7': return mb_chr(0x06EB,'UTF-8');
        case '8': return mb_chr(0x06D4,'UTF-8');
        case '9': return mb_chr(0x06E0,'UTF-8');
        case '10': return mb_chr(0x06E8,'UTF-8');
        case '11': return '&#1753;'.mb_chr(0x25Cc,'UTF-8');
        case '12': return mb_chr(0x0633,'UTF-8');
        case '13': return mb_chr(0x06DA,'UTF-8');
        case '14': return mb_chr(0x065A,'UTF-8');
        case '15': return mb_chr(0x06E2,'UTF-8');
        default: return '?';
    }
}

function dipnotAsTable($dipnot) {
    if (empty($dipnot)) return "";
    $dizi = array_unique(array_filter(array_map('trim', explode(',', $dipnot))));
    $output = "<div style='line-height:1.8;font-size:0.95em;'>";
    $items = [];
    foreach ($dizi as $numara) {
        $items[] = htmlspecialchars($numara) . " <font face='Traditional Arabic' size='7' color='red'>" . getArabicSign($numara) . "</font>";
    }
    $output .= implode(" &nbsp;|&nbsp; ", $items) . "</div>";
    return $output;
}
?>
</body>
</html>