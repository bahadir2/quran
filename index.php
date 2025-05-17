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
    $id = $row["id"];

    $or = $row["or"];
    $tr = $row["tr"];
    $sv = $row["sv"];
    $en = $row["en"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quran Pages</title>
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
    <!-- public bar u -->
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
 <div class="action-bar">h
    <button onclick="toggleSettings()" title="Ayarlar" class="action-button">&#9881;</button>
    <a href="info.html" title="Bilgi" target="_blank" class="action-button">&#8505;</a>
    <form id="logoutForm" action="logout.php" method="POST" style="display: inline;">
        <input type="hidden" name="logout" value="1">
        <input type="hidden" name="topicSelect" id="topicSelectInput">
        <input type="hidden" name="turkishChecked" id="turkishCheckedInput">
        <input type="hidden" name="swedishChecked" id="swedishCheckedInput">
        <input type="hidden" name="englishChecked" id="englishCheckedInput">
        <input type="hidden" name="fontSize" id="fontSizeInput">
        <input type="hidden" name="searchTerm" id="searchTermInput">
        <input type="hidden" name="searchResults" id="searchResultsInput">
        <button type="submit" class="action-button logout-button" title="Logout">
            Logout <!-- Power/Logout simgesi -->
        </button>
    </form>
</div></div>
<!-- Ayar Paneli -->
<div class="settings-bar" id="settingsBar">
      <div class="language-options">
    <label>
        Style<br>
        <select id="style" size="1" onchange="updateLanguages();">
<option selected>standard</option>
<option>fast</option>
<option>single</option>
</select>
    </label>
  <div class="language-options">
    <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> Türkçe </label>
    <span class="divider"></span>
    <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> Svenska</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="english" onclick="updateLanguages()"> English</label>
  
<span class="divider"></span>
  <label class="size-label highlighted-label" for="sizeRange">Arapça Yazı Boyutu:
    <input type="range" id="sizeRange" min="32" max="50" step="4" value="32" oninput="changeFontSize()">
    <span id="sizeValue">32px</span>
  </label>
<label class="highlighted-label" for="searchInput">Ayetlerde Ara:
<input type="text" id="searchInput" placeholder="Kelime veya ifade girin">
<button type="button" onclick="searchVerses()">Ara</button></label>
  </div>
<div id="searchResultsContainer" style="position: relative; display: none;">
    <button id="closeButton" onclick="closeSearchResults()" style="position: absolute; top: 5px; right: 5px; background: transparent; color: black; border: none; font-size: 20px; line-height: 20px; text-align: center; cursor: pointer;">&times;</button>
    <div id="searchResults" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
</div>
</div></div>
<!-- Ayar Paneli -->


    <?php 
    }else{
    ?>
    
    <div class="action-bar">
       <a href="info.html" title="Bilgi" target="_blank" class="action-button">&#8505;</a>
  <a onclick="toggleSettings()" title="Ayarlar" class="action-button">&#9881;</a> 
      <button onclick="window.location.href='login.php'" class="action-button logout-button" title="Giriş" >Login</button>
  
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
        <br>Türkçe <br><font color=red size='2'>Suat Yıldırım</font></label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="swedish" onclick="updateLanguages()"> 
        <br>Svenska <br><font color=red size='2'>Knut Bernström</font></label>
    <span class="divider"></span>
    <label>
        <input type="checkbox" id="english" onclick="updateLanguages()"> 
        <br>English <br><font color=red size='2'>Abdullah Yusuf Ali</font></label>
  
<span class="divider"></span>
  <label class="size-label highlighted-label" for="sizeRange">Arabic font size:
    <input type="range" id="sizeRange" min="32" max="50" step="4" value="32" oninput="changeFontSize()">
    <span id="sizeValue">32px</span>
  </label>
<label class="highlighted-label" for="searchInput">Ayetlerde Ara:
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
        goToPage(2,'juzSelect');
    });

    $("#verseSelect").on("change", function() {
        changeVerse();
    });

$("#boxSelect").select2({
    
    width: '200px', // Genişliği sabitleyin
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

     



    updateLanguages();

    // Yazı boyutunu geri yükle
    const fontSize = sessionStorage.getItem("fontSize") || "32";
    document.getElementById("sizeRange").value = fontSize;
    changeFontSize();

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

    function s_turkish(x)
    {
        var s_turkish = document.getElementsByClassName("s_turkish");
        if (x==false) {
            for (var i = 0; i < s_turkish.length; i++) {
                s_turkish[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < s_turkish.length; i++) {
                s_turkish[i].style.display = "inline";
            }
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

    function s_swedish(x)
    {
        var s_swedish = document.getElementsByClassName("s_swedish");
        if (x==false) {
            for (var i = 0; i < s_swedish.length; i++) {
                s_swedish[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < s_swedish.length; i++) {
                s_swedish [i].style.display = "inline";
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

    function s_english(x)
    {
        var s_english = document.getElementsByClassName("s_english");
        if (x==false) {
            for (var i = 0; i < s_english.length; i++) {
                s_english[i].style.display = "none";
            }
        }else{
            for (var i = 0; i < s_english.length; i++) {
                s_english[i].style.display = "inline";
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
        bar.style.display = bar.style.display === "block" ? "none" : "block";
    }


    let hideTimeout;

        document.getElementById("settingsBar").addEventListener("mouseleave", function () {
            // 1 saniye sonra gizle
            hideTimeout = setTimeout(() => {
                this.style.display = "none";
                location.reload(); // Sayfayı yenile
            }, 2000);// Süre 2000 ms (2 saniye) olarak ayarlandı
        });

        // Eğer kullanıcı geri gelirse iptal edelim:
        document.getElementById("settingsBar").addEventListener("mouseenter", function () {
        clearTimeout(hideTimeout);
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
    
    const fontSize = sessionStorage.getItem("fontSize") || "32";
    fetch("updatefontsize.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ fontSize: fontSize })
    });
}


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
    document.getElementById("topicSelectInput").value = topicSelectValue  || "0";

    // Diğer değerleri sessionStorage'dan al
    document.getElementById("turkishCheckedInput").value = sessionStorage.getItem("turkishChecked") === "true" ? "1" : "0";
    document.getElementById("swedishCheckedInput").value = sessionStorage.getItem("swedishChecked") === "true" ? "1" : "0";
    document.getElementById("englishCheckedInput").value = sessionStorage.getItem("englishChecked") === "true" ? "1" : "0";
    document.getElementById("fontSizeInput").value = sessionStorage.getItem("fontSize") || "32";
    document.getElementById("searchTermInput").value = sessionStorage.getItem("searchTerm") || "";
    document.getElementById("searchResultsInput").value = sessionStorage.getItem("searchResults") || "";

    // Form gönderilmeden önce topicSelect değerini sessionStorage'a da kaydedelim
    sessionStorage.setItem("topicSelect", topicSelectValue);


    
});


</script>
<p id='mesaj'></p>
</body>
</html>
