<?php
session_start();
include("conn.php");

include 'orfunctions.php'; // Include the functions file
//include 'trfunctions.php'; 
include 'svfunctions.php'; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set default page number, surah, juz, and verse
$page_number = isset($_GET['page_number']) ? intval($_GET['page_number']) : 1;
$selected_surah = isset($_GET['surah']) ? intval($_GET['surah']) : 1;
$selected_juz = isset($_GET['juz']) ? intval($_GET['juz']) : 1;
$selected_verse = isset($_GET['verse']) ? intval($_GET['verse']) : 1;
$changed = isset($_GET['changed']) ? $_GET['changed'] : '';

// Sure-ayet sayısı array'i
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


// SQL sorgularını changed parametresine göre yap
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
    $id = $row["id"];$ayno = $row["ayno"];
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
    
    <!-- Google Fonts - Seçtiğiniz Arabik fontları -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@400;500;600;700&family=Lateef:wght@200;300;400;500;600;700;800&family=Mirza:wght@400;500;600;700&family=Katibeh&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>

<div class="navbar">
        <?php
    //bar icin
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    ?>    
    <div class="topic-container">
    <label for="topicSelect">My Topics:</label>
    <select id="topicSelect" name="topik">
        <option value='0'>Kapalı</option>
        <option value='1'>Genel kendi hatimim</option>
    </select>
    <button id="plusButton" class="plus-button">+</button>
</div>
    <?php
    //bar icin
}
    ?>
    <!-- ana menu -->
<div class="form-group">
        <label for="boxSelect">Library:</label>
        <select id="boxSelect" class="combobox">
            <?php include 's_books.php'; ?>
        </select>
    </div>
  <!-- public bar u -->  
  <div class="form-group">
        <label for="juzSelect">Juz:</label>
        <select id="juzSelect" onchange="goToPage('juzSelect')">
            <?php for ($i = 1; $i <= 30; $i++): ?>
                <option value="<?php echo $i; ?>" <?php if ($selected_juz == $i) echo 'selected'; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

  <div class="form-group">
    <label for="pageInput">Page:</label>
    <div class="page-navigation">
        <button type="button" class="nav-button" onclick="changePage('prev')">&laquo;</button>
        <input type="number" id="pageInput" min="0" max="604" value="<?php echo $page_number; ?>" 
    onchange="goToPage('pageInput', this.value)" 
    onkeyup="if(event.keyCode===13) goToPage('pageInput', this.value )" 
    autocomplete="off"><button type="button" class="nav-button" onclick="changePage('next')">&raquo;</button>
    
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
    <!-- public bar n -->
    <?php
    //bar icin
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    ?>
<!-- Mevcut link ve form yerine -->
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
    <button type="submit" class="action-button logout-button" title="Logout">
        Logout
    </button>
</form>
</div></div>

</div>
<!-- Ayar Paneli -->
<div class="settings-bar" id="settingsBar">
  <div class="language-options">
    <label>
        Style<br>
        <select id="style" size="1" onchange="updateLanguages();">
<option selected>subtitle</option>
<option>standard</option>
<option>fast</option>
<option>single</option>
</select>

        </label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="turkish" onclick="updateLanguages()"> 
        - Tr <br><font color=red size='2'>Suat Yıldırım</font></label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="swedish" onclick="updateLanguages()"> 
        - Sv <br><font color=red size='2'>Knut Bernström</font></label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="english" onclick="updateLanguages()"> 
        - En <br><font color=red size='2'>Abdullah Yusuf Ali</font></label>
  
<span class="divider"></span>

   <!-- YENİ: Font Seçici -->
    <label class="size-label highlighted-label">
    Arabic: 
    <select id="arabicFontSelect" onchange="changeArabicFont()">
        <option value="Traditional Arabic" selected>Traditional Arabic</option>
        <option value="KFGQPC Uthmanic Script HAFS" disabled>Mushaf (KFGQPC)</option>
        <option value="Scheherazade New" disabled>Scheherazade New</option>
        <option value="Lateef" disabled>Lateef</option>
        <option value="Mirza" disabled>Mirza</option>
        <option value="Katibeh" disabled>Katibeh</option>
        
    </select>
</label>
  <!-- Font Boyutu Seçici (Combobox) -->
<label class="size-label highlighted-label">
    Size: 
    <select id="fontSizeSelect" onchange="changeFontSizeFromSelect()">
        <option value="24">24px</option>
        <option value="32">32px</option>
        <option value="40" selected>40px</option>
        <option value="48">48px</option>
        <option value="56">56px</option>
        <option value="64">64px</option>
    </select>
</label><br><label>
 Ayetlerde Ara:
<input type="text" id="searchInput" placeholder="Kelime veya ifade girin">
<button type="button" onclick="searchVerses()">Ara</button></label>
  </div>
<div id="searchResultsContainer" style="position: relative; display: none;">
    <button id="closeButton" onclick="closeSearchResults()" style="position: absolute; top: 5px; right: 5px; background: transparent; color: black; border: none; font-size: 20px; line-height: 20px; text-align: center; cursor: pointer;">&times;</button>
    <div id="searchResults" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
</div>
</div>
<!-- Ayar Paneli -->


    <?php 
    }else{
    ?>
    
    <div class="action-bar">
       <a href="info.html" title="Bilgi" target="_blank" class="action-button">&#8505;</a>
  <a onclick="toggleSettings()" title="Ayarlar" class="action-button">&#9881;</a> 
      <button onclick="window.location.href='login.php'" class="action-button logout-button" title="Giriş" ><span class="material-icons">login</span></button>
  
</div>
   
</div>

<!-- Ayar Paneli -->
<div class="settings-bar" id="settingsBar">
  <div class="language-options">
    <label>
        Style<br>
        <select id="style" size="1" onchange="updateLanguages();">
<option selected>subtitle</option>
<option>standard</option>
<option>fast</option>
<option>single</option>
</select>

        </label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="turkish" onclick="updateLanguages()"> 
        - Tr </label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="swedish" onclick="updateLanguages()"> 
        - Sv </label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="english" onclick="updateLanguages()"> 
        - En </label>
  
<span class="divider"></span>

    <!-- YENİ: Font Seçici -->
    <label class="size-label highlighted-label">
    Arabic: 
    <select id="arabicFontSelect" onchange="changeArabicFont()">
        <option value="Traditional Arabic" selected>Traditional Arabic</option>
        <option value="KFGQPC Uthmanic Script HAFS" disabled>Mushaf (KFGQPC)</option>
        <option value="Scheherazade New" disabled>Scheherazade New</option>
        <option value="Lateef" disabled>Lateef</option>
        <option value="Mirza" disabled>Mirza</option>
        <option value="Katibeh" disabled>Katibeh</option>
        
    </select>
</label>
  <!-- Font Boyutu Seçici (Combobox) -->
<label class="size-label highlighted-label">
    Size: 
    <select id="fontSizeSelect" onchange="changeFontSizeFromSelect()">
        <option value="24">24px</option>
        <option value="32">32px</option>
        <option value="40" selected>40px</option>
        <option value="48">48px</option>
        <option value="56">56px</option>
        <option value="64">64px</option>
    </select>
</label><br><label>
 Ayetlerde Ara:
<input type="text" id="searchInput" placeholder="Kelime veya ifade girin">
<button type="button" onclick="searchVerses()">Ara</button></label>
  </div>
<div id="searchResultsContainer" style="position: relative; display: none;">
    <button id="closeButton" onclick="closeSearchResults()" style="position: absolute; top: 5px; right: 5px; background: transparent; color: black; border: none; font-size: 20px; line-height: 20px; text-align: center; cursor: pointer;">&times;</button>
    <div id="searchResults" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
</div>
</div>
<!-- Ayar Paneli -->

    <?php
     }

//sayfa icin
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    ?>
    <div class="settings-bar2" id="settingsBar2">
<br>

<?php

include'topic.php';
?>
</div>
<?php
  }
  echo "<br><br>";
      include'public.php';

  ?>

<script>

$(document).ready(function() {
    // Tüm combobox'ları Select2 ile başlat
    $("#boxSelect, #surahSelect, #juzSelect, #verseSelect, #topicSelect").select2({
        placeholder: "Seçiniz...",
        allowClear: true,
        language: {
            noResults: function() {
                return "Sonuç bulunamadı";
            }
        }

    });

    // Her bir combobox için özel placeholder ayarla


    $("#juzSelect").select2({
        placeholder: "Cüz Seçin"
    });

    $("#verseSelect").select2({
        placeholder: "Ayet Seçin"
    });


    // Sayfa yönlendirme olaylarını ekle
    $("#surahSelect").on("change", function() {
        var selectedSurah = $(this).val();
        if (selectedSurah) {
            window.location.href = "?surah=" + selectedSurah + "&changed=surahSelect";
        }
    });

    $("#juzSelect").on("change", function() {
    var juz = $(this).val();
    window.location.replace("?juz=" + juz + "&changed=juzSelect");
});

    $("#verseSelect").on("change", function() {
        changeVerse();
    });

$("#boxSelect").select2({
    
    width: 'resolve', // Genişliği sabitleyin
    allowClear: true, // Temizleme butonunu etkinleştirin
    placeholder: "Seçiniz..." // Placeholder ekleyin

});
});




document.addEventListener("DOMContentLoaded", function () {
    
    // URL'den 'changed' ve 'verse' parametrelerini al
    const urlParams = new URLSearchParams(window.location.search);
    const changed = urlParams.get("changed");
    const verse = urlParams.get("verse");

    // Eğer 'changed=verseSelect' değilse, tüm ayetlerden 'active' sınıfını kaldır
    if (changed !== "verseSelect") {
        const verses = document.querySelectorAll(".arabic");
        verses.forEach(verse => verse.classList.remove("active"));
    }

    // Eğer 'changed=verseSelect' ve 'verse' parametresi varsa, ilgili ayeti aktif yap
    if (changed === "verseSelect" && verse) {
        const selectedVerse = document.querySelector(`.arabic[data-verse="${verse}"]`);
        if (selectedVerse) {
            selectedVerse.classList.add("active");
        }
    }

    // Tüm ayetlere tıklama olayını ekle
    const verses = document.querySelectorAll(".arabic");
    verses.forEach(verse => {
        verse.addEventListener("click", function () {
            // Eğer zaten 'active' sınıfı varsa kaldır, yoksa ekle
            if (this.classList.contains("active")) {
                this.classList.remove("active");
            } else {
                this.classList.add("active");
            }
        });
    });

// Sayfa yüklendiğinde sonuçları geri yükle
    // Dil seçimlerini geri yükle
    const turkishChecked = sessionStorage.getItem("turkishChecked") === "true" || false;
    const swedishChecked = sessionStorage.getItem("swedishChecked") === "true" || false;
    const englishChecked = sessionStorage.getItem("englishChecked") === "true" || false;
    const styleValue = sessionStorage.getItem("styleValue") || "standard"; // Varsayılan değer "standard"
    
    
    document.getElementById("style").value = styleValue;
    document.getElementById("turkish").checked = turkishChecked;
    document.getElementById("swedish").checked = swedishChecked;
    document.getElementById("english").checked = englishChecked;

     // Font seçimini geri yükle
    //const savedFont = sessionStorage.getItem("arabicFont") || "KFGQPC Uthmanic Script HAFS";
    const savedFont = sessionStorage.getItem("arabicFont") || "Traditional Arabic";
    const fontSelect = document.getElementById("arabicFontSelect");
    if (fontSelect) {
        fontSelect.value = savedFont;

        // Amiri için Google Fonts'u ÖNCE yükle
        if (savedFont === "Amiri" && !document.getElementById("amiriFont")) {
            const link = document.createElement("link");
            link.id = "amiriFont";
            link.href = "https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap";
            link.rel = "stylesheet";
            document.head.appendChild(link);
        }


        applyArabicFont(savedFont);
    }



   

    // Yazı boyutunu geri yükle
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

        // Eğer zaten "Aranan Kelime" eklenmişse, tekrar ekleme
        if (!resultsDiv.innerHTML.includes(`<strong>Aranan Kelime:</strong> ${searchTerm}`)) {
            
resultsDiv.innerHTML = resultsHTML;
            // Sonuçlar alanını görünür yap
            const searchResultsContainer = document.getElementById("searchResultsContainer");
            searchResultsContainer.style.display = "block";
        }else {
            
            resultsDiv.innerHTML = `
                <div class="search-term">
                    <strong>Aranan Kelime:</strong> ${searchTerm}
                </div>
            ` + resultsHTML;
        }
    }


    

});



// Font değiştirme fonksiyonu
function changeArabicFont() {
    const fontSelect = document.getElementById("arabicFontSelect");
    const selectedFont = fontSelect.value;
    
    // Amiri için Google Fonts yükle
    if (selectedFont === "Amiri" && !document.getElementById("amiriFont")) {
        const link = document.createElement("link");
        link.id = "amiriFont";
        link.href = "https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap";
        link.rel = "stylesheet";
        document.head.appendChild(link);
        
        // Font yüklendikten sonra uygula
        setTimeout(() => applyArabicFont(selectedFont), 500);
    } else {
        applyArabicFont(selectedFont);
    }
    
    // Kaydet
    sessionStorage.setItem("arabicFont", selectedFont);
}

// Tüm Arabik elementlere font uygula
function applyArabicFont(font) {
    console.log("✅ Font uygulanıyor:", font);
    
    const arabicElements = document.querySelectorAll(".arabic, .arabic2");
    
    arabicElements.forEach(element => {
        // Her font için uygun fallback
        switch(font) {
            case "KFGQPC Uthmanic Script HAFS":
                element.style.fontFamily = "'KFGQPC Uthmanic Script HAFS', 'Scheherazade New', serif";
                break;
            case "Scheherazade New":
                element.style.fontFamily = "'Scheherazade New', 'Traditional Arabic', serif";
                break;
            case "Lateef":
                element.style.fontFamily = "'Lateef', 'Traditional Arabic', serif";
                break;
            case "Mirza":
                element.style.fontFamily = "'Mirza', 'Traditional Arabic', serif";
                break;
            case "Katibeh":
                element.style.fontFamily = "'Katibeh', 'Traditional Arabic', serif";
                break;
            case "Traditional Arabic":
                element.style.fontFamily = "'Traditional Arabic', serif";
                break;
            default:
                element.style.fontFamily = `'${font}', 'Traditional Arabic', serif`;
        }
    });
    
    console.log(`✅ ${arabicElements.length} elemente font uygulandı`);
}

// Font boyutu değiştirme (Combobox'tan)
function changeFontSizeFromSelect() {
    const fontSizeSelect = document.getElementById("fontSizeSelect");
    const size = fontSizeSelect.value;
    
    applyFontSize(size);
    
    // Kaydet
    sessionStorage.setItem("fontSize", size);
    
    // Server'a gönder (mevcut fonksiyonunuz)
    fetch("updatefontsize.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ fontSize: size })
    });
}

// Font boyutunu uygula
function applyFontSize(size) {
    const arabicTexts = document.getElementsByClassName("arabic");
    const arabicTexts2 = document.getElementsByClassName("arabic2");

    for (let i = 0; i < arabicTexts.length; i++) {
        arabicTexts[i].style.fontSize = size + "px";
    }
    for (let i = 0; i < arabicTexts2.length; i++) {
        arabicTexts2[i].style.fontSize = size + "px";
    }
}

function changeVerse() {
    var verse = document.getElementById("verseSelect").value;

    // Tüm ayetlerden 'active' sınıfını kaldır
    const verses = document.querySelectorAll(".arabic");
    verses.forEach(v => v.classList.remove("active"));

    // Seçilen ayeti bul ve 'active' sınıfını ekle
    const selectedVerse = document.querySelector(`.arabic[data-verse="${verse}"]`);
    if (selectedVerse) {
        selectedVerse.classList.add("active");
    }

    // URL'yi güncelle
    const surah = document.getElementById("surahSelect").value;
    window.location.href = "?surah=" + surah + "&verse=" + verse + "&changed=verseSelect";
}

    function changePage(direction) {
        var pageInput = document.getElementById("pageInput");
        var currentPage = parseInt(pageInput.value);
        
        if (direction === 'prev' && currentPage > 0) {
            pageInput.value = currentPage - 1;
        } else if (direction === 'next' && currentPage < 604) {
            pageInput.value = currentPage + 1;
        }
        
        // Doğrudan form submit yerine goToPage'i çağıralım
        goToPage('pageInput', pageInput.value);
    }

    function goToPage(changedElementId,gh) {
            
        var page_number = gh;
        var surah = document.getElementById("surahSelect").value;
        var verse = document.getElementById("verseSelect").value;
        var juz = document.getElementById("juzSelect").value;

        // Sayfa numarası kontrolü
        if (page_number < 0) page_number = 0;
        if (page_number > 604) page_number = 604;

        // window.location.href yerine window.location.replace kullanalım
        window.location.replace("?page_number=" + page_number + 
                            "&surah=" + surah + 
                            "&verse=" + verse + 
                            "&juz=" + juz + 
                            "&changed=" + changedElementId);
    }
        
   


function updateLanguages() {

    function turkish(x)
    {
        var turkish = document.getElementsByClassName("turkish");
        if (x==false) {
            for (var i = 0; i < turkish.length; i++) {
                turkish[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < turkish.length; i++) {
                turkish[i].style.display = "inline";
            }
        }
    }
    function tas(x)
    {
        var tas = document.getElementsByClassName("tas");
        if (x==false) {
            for (var i = 0; i < tas.length; i++) {
                tas[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < tas.length; i++) {
                tas[i].style.display = "inline";
            }
        }
        
    }

function s_turkish(x) {
    var s_turkish = document.getElementsByClassName("s_turkish");
    for (var i = 0; i < s_turkish.length; i++) {
        s_turkish[i].style.display = x == false ? "none" : "block"; // inline → block
    }
}

function s_swedish(x) {
    var s_swedish = document.getElementsByClassName("s_swedish");
    for (var i = 0; i < s_swedish.length; i++) {
        s_swedish[i].style.display = x == false ? "none" : "block"; // inline → block
    }
}

function s_english(x) {
    var s_english = document.getElementsByClassName("s_english");
    for (var i = 0; i < s_english.length; i++) {
        s_english[i].style.display = x == false ? "none" : "block"; // inline → block
    }
}

    function swedish(x)
    {
        var swedish = document.getElementsByClassName("swedish");
        if (x==false) {
            for (var i = 0; i < swedish.length; i++) {
                swedish[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < swedish.length; i++) {
                swedish [i].style.display = "inline";
            }
        }
    }


    function sas(x)
    {
        var sas = document.getElementsByClassName("sas");
        if (x==false) {
            for (var i = 0; i < sas.length; i++) {
                sas[i].style.display = "none";
                as(0);
            }
        }else{
            for (var i = 0; i < sas.length; i++) {
                sas[i].style.display = "inline";
                as(1);
            }
        }
    }

    function english(x)
    {
        var english = document.getElementsByClassName("english");
        if (x==false) {
            for (var i = 0; i < english.length; i++) {
                english[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < english.length; i++) {
                english[i].style.display = "inline";
            }
        }
    }


    function eas(x)
    {
        var eas = document.getElementsByClassName("eas");
        if (x==false) {
            for (var i = 0; i < eas.length; i++) {
                eas[i].style.display = "none";
                as(0);
            }
        }else{
            for (var i = 0; i < eas.length; i++) {
                eas[i].style.display = "inline";
                as(1);
            }
        }
    }

    function arabic(x)
    {
        var arabic = document.getElementsByClassName("arabic");

        if (x==false) {
            for (var i = 0; i < arabic.length; i++) {
                arabic[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < arabic.length; i++) {
                arabic[i].style.display = "inline";
            }
        }
    }


    function as(x)
    {
        var as = document.getElementsByClassName("as");

        if (x==false) {
            for (var i = 0; i < as.length; i++) {
                as[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < as.length; i++) {
                as[i].style.display = "inline";
            }
        }
    }

    function fasttr(x)
    {
        let blokturkish = document.getElementsByClassName("blokturkish");

        if (x==false) {
            document.getElementsByClassName("blokturkish")[0].style.display = "none";
        }else{
            document.getElementsByClassName("blokturkish")[0].style.display = "inline";
        }
    }

    function fastsv(x)
    {
        let blokturkish = document.getElementsByClassName("blokswedish");

        if (x==false) {
            document.getElementsByClassName("blokswedish")[0].style.display = "none";
        }else{
            document.getElementsByClassName("blokswedish")[0].style.display = "inline";
        }
    }

    function fasten(x)
    {
        let blokturkish = document.getElementsByClassName("blokenglish");

        if (x==false) {
            document.getElementsByClassName("blokenglish")[0].style.display = "none";
        }else{
            document.getElementsByClassName("blokenglish")[0].style.display = "inline";
        }
    }

 function fastor(x)
    {
        let blokturkish = document.getElementsByClassName("blokarabic");

        if (x==false) {
            document.getElementsByClassName("blokarabic")[0].style.display = "none";
        }else{
            document.getElementsByClassName("blokarabic")[0].style.display = "inline";
        }
    }

    function baslik(x)
    {
        const baslikElements = document.getElementsByClassName("baslik");


        if (x==false) {
            for (var i = 0; i < baslikElements.length; i++) {
                baslikElements[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < baslikElements.length; i++) {
                baslikElements[i].style.display = "inline";
            }
        }
    }

    function normal(x)
    {
        const baslikElements = document.getElementsByClassName("normal");


        if (x==false) {
            for (var i = 0; i < baslikElements.length; i++) {
                baslikElements[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < baslikElements.length; i++) {
                baslikElements[i].style.display = "inline";
            }
        }
    }

    function secde(x)
    {
        const baslikElements = document.getElementsByClassName("secde");


        if (x==false) {
            for (var i = 0; i < baslikElements.length; i++) {
                baslikElements[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < baslikElements.length; i++) {
                baslikElements[i].style.display = "inline";
            }
        }
    }


    // En sonda font ve boyut seçimlerini kaydet
    const arabicFontSelect = document.getElementById("arabicFontSelect");
    const fontSizeSelect = document.getElementById("fontSizeSelect");
    
    if (arabicFontSelect) {
        sessionStorage.setItem("arabicFont", arabicFontSelect.value);
    }
    if (fontSizeSelect) {
        sessionStorage.setItem("fontSize", fontSizeSelect.value);
    }

const styleValue = document.getElementById("style").value;
var turkishChecked = document.getElementById("turkish").checked;  //false, true
const swedishChecked = document.getElementById("swedish").checked;
const englishChecked = document.getElementById("english").checked;

switch (styleValue) {
    case 'subtitle':
        fastor(false);

        document.getElementsByClassName("first")[0].style.display = "inline";
        
        baslik(true);
        //secenekler
        s_turkish(turkishChecked);
        s_swedish(swedishChecked);
        s_english(englishChecked);

        fasttr(false);
        fastsv(false);
        fasten(false);

        turkish(false);tas(false);
        swedish(false);sas(false);
        english(false);eas(false);
        as(false);
        break;

    case 'standard':
        fastor(false);

        document.getElementsByClassName("first")[0].style.display = "inline";
        
        turkish(turkishChecked);tas(turkishChecked);
        swedish(swedishChecked);sas(swedishChecked);
        english(englishChecked);eas(englishChecked);
        as(turkishChecked || swedishChecked || englishChecked);

        fasttr(false);
        fastsv(false);
        fasten(false);

        break;

    case 'fast':
        document.getElementsByClassName("first")[0].style.display = "none";
        fasttr(turkishChecked);
        fastsv(swedishChecked);
        fasten(englishChecked);
        fastor(true);
  
        break;

    case 'single':
        document.getElementsByClassName("first")[0].style.display = "none";
        fasttr(turkishChecked);
        fastsv(swedishChecked);
        fasten(englishChecked);
        fastor(false);

        if ((turkishChecked || swedishChecked || englishChecked) == false) {
            document.getElementById("mesaj").innerText = 'Ayarlar menusunden style ve dil secimi yapmalisiniz.';
        }else{
            document.getElementById("mesaj").innerText = '';
        }
        break;

    default:    //Arabic
        break;



        
}


// Dil seçimlerini sessionStorage'da sakla
        
        sessionStorage.setItem("styleValue", styleValue);
        sessionStorage.setItem("turkishChecked", turkishChecked);
        sessionStorage.setItem("swedishChecked", swedishChecked);
        sessionStorage.setItem("englishChecked", englishChecked);

}

    function toggleSettings() {
        const bar = document.getElementById("settingsBar");
        if (bar.style.display === "block") {
            bar.style.display = "none";
            saveAndRefresh();
        } else {
            bar.style.display = "block";
        }
    }

    function saveAndRefresh() {
        // Değişiklikleri kaydet
        updateLanguages();
        // Sayfayı yenile
        location.reload();
    }

    document.getElementById("settingsBar").addEventListener("mouseenter", function() {
        if (hideTimeout) {
            clearTimeout(hideTimeout);
        }
    });

    document.getElementById("settingsBar").addEventListener("mouseleave", function() {
        hideTimeout = setTimeout(() => {
            this.style.display = "none";
            saveAndRefresh();
        }, 800);
    });

//

function searchVerses() {
    const searchTerm = document.getElementById("searchInput").value.trim();

    if (!searchTerm) {
        document.getElementById("searchResults").innerHTML = "Lütfen bir kelime veya ifade girin.";
        return;
    }

    // Sonuçlar alanını tekrar görünür yap
    const searchResultsContainer = document.getElementById("searchResultsContainer");
    searchResultsContainer.style.display = "block";

    // AJAX isteği gönder
    fetch(`search.php?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById("searchResults");

            // Eğer zaten "Aranan Kelime" eklenmişse, temizle
            resultsDiv.innerHTML = "";

            if (data.length > 0) {
                // Arama terimini başa ekle
                const resultsHTML = `
                    <div class="search-term">
                        <strong>Aranan Kelime:</strong> ${searchTerm}
                    </div>
                ` + data.map(verse => `
                    <div class="search-result" onclick="submitSurahVerse(${verse.sur}, ${verse.ayno})">
                        ${verse.sur}-${verse.ayno}
                    </div>
                `).join("");

                resultsDiv.innerHTML = resultsHTML;

                // Sonuçları sessionStorage'da sakla
                sessionStorage.setItem("searchResults", resultsHTML);
                sessionStorage.setItem("searchTerm", searchTerm); // Arama terimini de sakla
            } else {
                resultsDiv.innerHTML = "Sonuç bulunamadı.";
                sessionStorage.removeItem("searchResults"); // Önceki sonuçları temizle
                sessionStorage.removeItem("searchTerm");
            }
        })
        .catch(error => {
            console.error("Hata:", error);
            document.getElementById("searchResults").innerHTML = "Bir hata oluştu.";
        });
}


/*
function changeFontSize() {
    const size = document.getElementById("sizeRange").value;

    // Yazı boyutunu sessionStorage'da sakla
    sessionStorage.setItem("fontSize", size);

    const arabicTexts = document.getElementsByClassName("arabic");
    const arabicTexts2 = document.getElementsByClassName("arabic2");

    for (let i = 0; i < arabicTexts.length; i++) {
        arabicTexts[i].style.fontSize = size + "px";
        arabicTexts2[i].style.fontSize = size + "px";
    }
    document.getElementById("sizeValue").innerText = size + "px";
    
    const fontSize = sessionStorage.getItem("fontSize") || "40";
    fetch("updatefontsize.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ fontSize: fontSize })
    });
}*/


function closeSearchResults() {
    const searchResultsContainer = document.getElementById("searchResultsContainer");
    searchResultsContainer.style.display = "none"; // Sonuçlar alanını gizle

    // sessionStorage'daki verileri temizle
    sessionStorage.removeItem("searchResults");
    sessionStorage.removeItem("searchTerm");
}

function submitSurahVerse(surah, verse) {
    // URL'yi güncelle ve sayfayı yenile
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set("surah", surah);
    urlParams.set("verse", verse);
    urlParams.set("changed", "verseSelect");
    window.location.href = "?" + urlParams.toString();

    
}

// Form submit olayını dinle
document.getElementById("logoutForm").addEventListener("submit", function(e) {
    // topicSelect değerini doğrudan select elementinden al
    const topicSelectValue = document.getElementById("topicSelect").value;
    document.getElementById("topicSelectInput").value = topicSelectValue || "0";

    // Diğer değerleri sessionStorage'dan al
    document.getElementById("turkishCheckedInput").value = sessionStorage.getItem("turkishChecked") === "true" ? "1" : "0";
    document.getElementById("swedishCheckedInput").value = sessionStorage.getItem("swedishChecked") === "true" ? "1" : "0";
    document.getElementById("englishCheckedInput").value = sessionStorage.getItem("englishChecked") === "true" ? "1" : "0";
    document.getElementById("fontSizeInput").value = sessionStorage.getItem("fontSize") || "40";
    document.getElementById("arabicFontInput").value = sessionStorage.getItem("arabicFont") || "KFGQPC Uthmanic Script HAFS";
    document.getElementById("searchTermInput").value = sessionStorage.getItem("searchTerm") || "";
    document.getElementById("searchResultsInput").value = sessionStorage.getItem("searchResults") || "";
    document.getElementById("styleValue").value = sessionStorage.getItem("styleValue") || "";

    // Form gönderilmeden önce değerleri sessionStorage'a da kaydedelim
    sessionStorage.setItem("topicSelect", topicSelectValue);
});

// Scroll ile navbar gizle/göster
(function() {
    let lastScrollY = window.scrollY;
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', function() {
        const currentScrollY = window.scrollY;

        if (currentScrollY > lastScrollY && currentScrollY > 60) {
            // Aşağı scroll → gizle
            navbar.style.top = '-200px'; // navbar yüksekliğinden fazla
        } else {
            // Yukarı scroll → göster
            navbar.style.top = '0';
        }

        lastScrollY = currentScrollY;
    });
})();



document.querySelectorAll('.normal, .secde').forEach(function(el) {
    el.addEventListener('mouseenter', function(e) {
        const div = this.querySelector('div');
        if (!div) return;
        
        const screenWidth = window.innerWidth;
        const mouseX = e.clientX;
        
        // Ekranı 3 bölgeye böl
        if (mouseX < screenWidth / 3) {
            // Sol bölge - soldan hizala
            div.style.left = '0';
            div.style.right = 'auto';
            div.style.textAlign = 'left';
        } else if (mouseX > (screenWidth / 3) * 2) {
            // Sağ bölge - sağdan hizala
            div.style.left = 'auto';
            div.style.right = '0';
            div.style.textAlign = 'right';
        } else {
            // Orta bölge - ortadan hizala
            div.style.left = '0';
            div.style.right = '0';
            div.style.textAlign = 'center';
        }
    });
});

</script>

<p id='mesaj'><?php 

/*
echo $page_number; ?>/
<?php echo $page_number; ?>/
<?php echo $selected_surah; ?>/
<?php echo $selected_juz; ?>/
<?php echo $selected_verse; ?>/
<?php echo $changed; ?>/id:
<?php echo $id; ?>/
<?php echo $sveri.$psveri; */


// String'in başındaki ve sonundaki virgülleri direkt temizle
$dipnot = trim($dipnot, ',');

// Virgüllere göre parçalayıp diziye çeviriyoruz
$dizi = explode(',', $dipnot);

// Boşlukları temizleyelim
$dizi = array_map('trim', $dizi);

// Boş elemanları filtrele
$dizi = array_filter($dizi);

// Tekrarları silip sırayı koruyoruz
$temiz_dizi = array_unique($dizi);

// Tekrar virgüllü metin haline getir
$sonuc_metin = implode(',', $temiz_dizi);

echo $id.':<br> '.$altbilgi.'<br>ayetno: '.$ayno;


//-------------------------------------------------
/**
 * Tek bir işaret numarasının Arapça karşılığını döndürür
 */
function getArabicSign($signNumber) {
    switch($signNumber) {
        case '1':
            $char1 = mb_chr(0x0615, 'UTF-8');  //
            $char2 = mb_chr(0x25Cc, 'UTF-8');  // ○             
            $combined = $char1 . $char2; // Small high seen
            return $combined;
        case '2':
            $combined = '<table border="1" style="display: inline-block; border-width: 0px; vertical-align: middle; margin: 0 5px;">
	<tr>
		<td style="border-style: none; border-width: medium; text-align: center; vertical-align: middle; padding: 2px 5px;">
			<div style="line-height: 0.6;">
				<b><font size="4">&#1593;</font></b>
				<br>
				<font size="7">&#9676;</font>
			</div>
		</td>
	</tr>
</table>';
            return $combined; // Rounded zero
        case '3':
            return mb_chr(0x0635, 'UTF-8'); // Sad with alef maksura
        case '4':
            return mb_chr(0x0632, 'UTF-8'); // Meem initial
        case '5':
            $char1 = mb_chr(0x0653, 'UTF-8');  //
            $char2 = mb_chr(0x25Cc, 'UTF-8');  // ○             
            $combined = $char2 . $char1;
            return $combined; // Qaf with alef
        case '6':
            return mb_chr(0x06EC, 'UTF-8'); // HS with filled centre
        case '7':
            return mb_chr(0x06EB, 'UTF-8'); // Empty centre high stop
        case '8':
            return mb_chr(0x06D4, 'UTF-8'); // Full stop
        case '9':
            return mb_chr(0x06E0, 'UTF-8'); // SH upright
        case '10':
            return mb_chr(0x06E8, 'UTF-8'); // SH noon
        case '11':
            $char1 = '&#1753;';  //
            $char2 = mb_chr(0x25Cc, 'UTF-8');  // ○             
            $combined = $char1 . $char2;
            return $combined; // Small high yeh
        case '12':
            return mb_chr(0x0633, 'UTF-8'); // SL seen
        case '13':
            return mb_chr(0x06DA, 'UTF-8'); // Dotless cim
        case '14':
            return mb_chr(0x065A, 'UTF-8'); // Dotted cim
        case '15':
            return mb_chr(0x06E2, 'UTF-8'); // SH meem isolated
        default:
            return '?';
    }
}

/***
 * $cee='<div style="line-height: 1.4;"><b><font size="4">&#1593;</font></b>
		<br>
		<font size="7">&#9676;</font></div>';
 */

/**
 * Tek bir işaret numarasının açıklamasını döndürür
 
function getSignExplanation($signNumber) {
    switch($signNumber) {
        case '1':
            return '+ Durulması evlâdır, geçilmesi caizdir.';
        case '2':
            return 'Durulması ve namazda ise rükû yapılabilir.';
        case '3':
            return 'Geçilmesi evlâdır, durulması caizdir.';
        case '4':
            return 'Geçilmesi evlâdır, durulması caizdir.';
        case '5':
            return '+ Dört elif miktari uzatilir.';
        case '6':
            return 'Uzatmadan kısa oku demektir.';
        case '7':
            return 'Kısaltmadan uzun oku demektir.';
        case '8':
            return "Nefes almadan dur ve oku demektir. Kuran'da 4 yerde vardır.";
        case '9':
            return 'Durulması evlâdır, geçilmesi caizdir.';
        case '10':
            return 'Sonu tenvinli kelimelerden bir sonraki kelimeye geçişi sağlar.';
        case '11':
            return 'Uzatma (MED) Dik cizgi: harfi bir elif miktarı uzatır.';
        case '12':
            return 'Sad harfi seen gibi ince okunur.';
        case '13':
            return 'Durulması evlâdır, geçilmesi caizdir.';
        case '14':
            return '+Birbirine yakın iki yerde bulunur. Birinde durulunca ötekinde geçilir.';
        case '15':
            return 'Durulması gerekir, geçilmesi uygun değildir.';
        case '16':
            return 'Ses normal "nâ" gibi çıkar ama dudaklar sessizce bir "u" hareketi yapar.';
        default:
            return 'Açıklama bulunamadı.';
    }
}
*/
/**
 * Virgülle ayrılmış işaret numaralarını kompakt tablo olarak gösterir
 */
function dipnotAsTable($dipnot) {
    if (empty($dipnot)) {
        return "<p>Bu ayette durma işareti bulunmamaktadır.</p>";
    }
    
    // Virgülle ayrılmış numaraları diziye çevir
    $dizi = array_map('trim', explode(',', $dipnot));
    
    // Benzersiz sayıları al (ilk gelme sırasını korur)
    $benzersiz_sayilar = array_unique($dizi);
    

    $output = "<div style='line-height: 1.8; font-size: 0.95em;'>";

$items = [];
foreach ($benzersiz_sayilar as $numara) {
    $arapca_isaret = getArabicSign($numara);
    $aciklama = getSignExplanation($numara);

    $items[] = htmlspecialchars($numara) . " " .
               "<font face='Traditional Arabic' size='7' color='red'>" .
               $arapca_isaret .
               "</font> : " .
               htmlspecialchars($aciklama);
}

$output .= implode(" &nbsp;|&nbsp; ", $items); // virgül yerine | ayırıcı da olabilir

$output .= "</div>";

return $output;
}



//echo dipnotAsTable($sonuc_metin);
//echo "<div style='background:red;color:white;padding:10px;font-size:20px'>SESSION fontSize: " . ($_SESSION['fontSize'] ?? 'YOK') . "</div>";


?>

</body>
</html>
