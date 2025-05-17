<?php
session_start();
include("conn.php");



include 'orfunctions.php'; // Include the functions file
include 'trfunctions.php'; 
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <style>
       body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f8f4e3;
    margin: 20px;
    text-align: center;
    padding: 18px;
}

.turkish {
    color: red;
    font-size: 18px;
    margin-right: 10px;
}

.swedish {
    color: #006400;
    font-size: 18px;
    margin-right: 10px;
}

.english {
    color: black;
    font-size: 18px;
    margin-right: 10px;
}

.navbar {
    background-color: #5C4033;
    padding: 12px 16px;
    border-radius: 10px;
    color: #fdfaf5;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
}

.navbar select, .navbar input {
    padding: 6px;
    font-size: 14px;
    border-radius: 6px;
    background-color: #fffaf0;
    color: #333;
    border: 1px solid #ccc;
}

label {
    font-weight: bold;
}

select {
    padding: 6px;
    font-size: 14px;
    border-radius: 6px;
    background-color: #fffaf0;
    color: #333;
    border: 1px solid #ccc;
}

input[type="number"] {
    width: 60px;
    padding: 6px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    background-color: #fffaf0;
    color: #333;
}

.nav-button {
    padding: 5px 10px;
    border: none;
    background-color: #8e5a28;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.nav-button:hover {
    background-color: #a86734;
}

a.button-link[title="Ayarlar"] {
    font-size: 16px; /* Yazı boyutunu diğer metinlerle eşitleyin */
    font-family: 'Segoe UI', sans-serif; /* Yazı tipini metinle uyumlu hale getirin */
    padding: 8px 12px; /* İç boşluk */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 18px; /* Yükseklik */
}

a.button-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background-color: #4b3226;
    color: #FCEFD4;
    padding: 8px 16px; /* Daha büyük iç boşluk */
    font-size: 16px; /* Yazı boyutunu artır */
    border-radius: 6px;
    text-decoration: none;
    box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;
    height: 20px; /* Yükseklik */
    line-height: 24px; /* Yazı hizalaması */
    cursor: pointer;
}

a.button-link:hover {
    background-color: #3a261c;
}

a.button-link[title="Ayarlar"] {
    font-size: 20px; /* Ayarlar simgesi için daha büyük yazı boyutu */
    padding: 10px 18px; /* Daha büyük iç boşluk */
}

.settings-bar {
    display: none;
    background-color: #eee0c9;
    padding: 20px;
    margin-top: 10px;
    border-radius: 10px;
    box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
}

.highlighted-label {
    color: #8e4a1d;
    background-color: #fdf2e1;
    padding: 6px 10px;
    border-radius: 6px;
    display: inline-block;
    margin-top: 10px;
    font-weight: bold;
}

.language-options {
    display: flex;
    align-items: center;
    gap: 10px;
}

.language-options label {
    margin: 0;
}

.divider {
    border-left: 1px solid #aaa;
    height: 16px;
}

#juzSelect, #verseSelect, input[type="number"] {
    width: 60px; /* Genişliği ayarlayın */
    padding: 6px;
    border-radius: 6px;
    font-size: 14px;
    background-color: #fffaf0;
    color: #333;
    border: 1px solid #ccc;
}

#surahSelect {
    width: 150px; /* Genişliği artırın */
    padding: 6px; /* İç boşluk */
    font-size: 14px; /* Yazı boyutu */
    border-radius: 6px; /* Kenar yuvarlama */
    background-color: #fffaf0; /* Arka plan rengi */
    color: #333; /* Yazı rengi */
    border: 1px solid #ccc; /* Kenarlık */
}

.arabic {
    font-family: 'Traditional Arabic';
    font-size: 32px;
    letter-spacing: 0.1px;
    line-height: 1.8;
    color: #006600;
}

.arabic:hover {
    background-color: #eef;
    cursor: pointer;
}

.arabic.active {
    background-color: #cce;
    color: #003300;
    border: 1px solid #006600;
}

.normal {
        //display: inline-block;
        width: 15px; /* Dairenin genişliği */
        height: 15px; /* Dairenin yüksekliği */
        line-height: 15px; /* Yükseklikle hizalama */
        text-align: center; /* Metin ortalama */
        border-radius: 35%; /* Daire oluşturma */
        background-color: #182D47; /* Daire arka plan rengi #775F2F*/
        color: white;
        font-size: 12px; /* Rakam boyutu */
        font-weight: bold; /* Rakam kalınlığı */
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Gölgelendirme */
        margin: 5px; /* Daireler arasında boşluk */
        border: 3px solid #182D47; /* Kenar rengi ve kalinligi #182D47 */
        transition: all 0.3s; /* Geçiş efekti */
        }

        .normal:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.4);
        }

         .secde {
        //display: inline-block;
        width: 15px; /* Dairenin genişliği */
        height: 15px; /* Dairenin yüksekliği */
        line-height: 15px; /* Yükseklikle hizalama */
        text-align: center; /* Metin ortalama */
        border-radius: 35%; /* Daire oluşturma */
        background-color: red; /* Daire arka plan rengi #775F2F*/
        color: white;
        font-size: 12px; /* Rakam boyutu */
        font-weight: bold; /* Rakam kalınlığı */
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Gölgelendirme */
        margin: 5px; /* Daireler arasında boşluk */
        border: 3px solid red; /* Kenar rengi ve kalinligi #182D47 */
        transition: all 0.3s; /* Geçiş efekti */
        }

        .secde:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.4);
        }
        #searchResults {
    margin-top: 20px;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.search-result {

    cursor: pointer;
}

.search-result:hover {
    background-color: #f1f1f1;
}

.search-result p {
    margin: 5px 0;
}

.topic-container {
    display: flex;
    align-items: center;
    gap: 10px; /* Elemanlar arasındaki boşluk */
    background-color:rgb(190, 241, 94); /* Arka plan rengi */
    padding: 10px; /* İç boşluk */
    border-radius: 6px; /* Kenar yuvarlama */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Hafif gölge */
    margin-top: 2px; /* Üst boşluk */
}

.topic-container label {
    font-weight: bold;
    font-size: 14px;
    color: #333;
}

.topic-container select {
    padding: 6px;
    font-size: 14px;
    border-radius: 6px;
    background-color: #fff;
    border: 1px solid #ccc;
    color: #333;
}

.plus-button {
    background-color: #4CAF50; /* Yeşil arka plan */
    color: white; /* Beyaz yazı */
    border: none;
    border-radius: 50%; /* Daire şekli */
    width: 30px;
    height: 30px;
    font-size: 20px;
    cursor: pointer;
    text-align: center;
    line-height: 30px; /* Yazıyı ortalamak için */
}

.plus-button:hover {
    background-color: #45a049; /* Hover durumunda daha koyu yeşil */
}
    </style>
    
</head>
<body>
<div class="navbar">
  <label for="juzSelect">Cüz:</label>
    <select id="juzSelect" onchange="goToPage('juzSelect')">
        <?php for ($i = 1; $i <= 30; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if ($selected_juz == $i) echo 'selected'; ?>>
                <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>

  <label for="pageInput">Sayfa:</label>
  <button type="button" onclick="changePage('prev')" class="nav-button">&lt;</button>
  <input type="number" id="pageInput" min="0" max="604" value="<?php echo $page_number; ?>" onchange="goToPage('pageInput')" onkeyup="if(event.keyCode===13) goToPage('pageInput')" autocomplete="off">
  <button type="button" onclick="changePage('next')" class="nav-button">&gt;</button>

  <label for="surahSelect">Sure:</label>
  <select id="surahSelect" class="combobox"><?php
        //<select id="surahSelect" onchange="changeSurah();">
        $surahs = array(
            1 => "Fatiha", 
            2 => "Bakara", 
            3 => "Al-i İmran", 
            4 => "Nisa", 
            5 => "Maide",
            6 => "Enam", 
            7 => "Araf", 
            8 => "Enfal", 
            9 => "Tevbe", 
            10 => "Yunus",
            11 => "Hud",
            12 => "Yusuf",
            13 => "Rad",
            14 => "İbrahim",
            15 => "Hicr",
            16 => "Nahl",
            17 => "İsra",
            18 => "Kehf",
            19 => "Meryem",
            20 => "Taha",
            21 => "Enbiya",
            22 => "Hac",
            23 => "Müminun",
            24 => "Nur",
            25 => "Furkan",
            26 => "Şuara",
            27 => "Neml",
            28 => "Kasas",
            29 => "Ankebut",
            30 => "Rum",
            31 => "Lokman",
            32 => "Secde",
            33 => "Ahzab",
            34 => "Sebe",
            35 => "Fatır",
            36 => "Yasin",
            37 => "Saffat",
            38 => "Sad",
            39 => "Zümer",
            40 => "Mümin",
            41 => "Fussilet",
            42 => "Şura",
            43 => "Zuhruf",
            44 => "Duhan",
            45 => "Casiye",
            46 => "Ahkaf",
            47 => "Muhammed",
            48 => "Fetih",
            49 => "Hucurat",
            50 => "Kaf",
            51 => "Zariyat",
            52 => "Tur",
            53 => "Necm",
            54 => "Kamer",
            55 => "Rahman",
            56 => "Vakıa",
            57 => "Hadid",
            58 => "Mücadele",
            59 => "Haşr",
            60 => "Mümtehine",
            61 => "Saff",
            62 => "Cuma",
            63 => "Münafikun",
            64 => "Tegabun",
            65 => "Talak",
            66 => "Tahrim",
            67 => "Mülk",
            68 => "Kalem",
            69 => "Hakka",
            70 => "Mearic",
            71 => "Nuh",
            72 => "Cin",
            73 => "Müzzemmil",
            74 => "Müddessir",
            75 => "Kıyamet",
            76 => "İnsan",
            77 => "Mürselat",
            78 => "Nebe",
            79 => "Naziat",
            80 => "Abese",
            81 => "Tekvir",
            82 => "İnfitar",
            83 => "Mutaffifin",
            84 => "İnşikak",
            85 => "Buruc",
            86 => "Tarık",
            87 => "Ala",
            88 => "Gaşiye",
            89 => "Fecr",
            90 => "Beled",
            91 => "Şems",
            92 => "Leyl",
            93 => "Duha",
            94 => "İnşirah",
            95 => "Tin",
            96 => "Alak",
            97 => "Kadir",
            98 => "Beyyine",
            99 => "Zilzal",
            100 => "Adiyat",
            101 => "Karia",
            102 => "Tekasur",
            103 => "Asr",
            104 => "Hümeze",
            105 => "Fil",
            106 => "Kureyş",
            107 => "Maun",
            108 => "Kevser",
            109 => "Kafirun",
            110 => "Nasr",
            111 => "Tebbet",
            112 => "İhlas",
            113 => "Felak",
            114 => "Nas"
        );
        
        foreach ($surahs as $key => $value): ?>
            <option value="<?php echo $key; ?>" <?php if ($selected_surah == $key) echo 'selected'; ?>>
                <?php echo $key . ". " . $value; ?>
            </option>
        <?php endforeach; ?>
    </select>
<label for="verseSelect">Ayet:</label>
    <select id="verseSelect" onchange="changeVerse();">
        <?php
            //$selected_surah = intval($_GET['surah']);
            $verse_count = isset($surah_verses[$selected_surah]) ? $surah_verses[$selected_surah] : 0;
            for ($i = 1; $i <= $verse_count; $i++) {
$selected = ($selected_verse == $i) ? 'selected' : '';
                echo "<option value=\"$i\" $selected>$i</option>";
            }
        
        ?>
    </select>
    <?php
    //bar icin
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    ?>
    
<div class="topic-container">
    <label for="topicSelect">My Topic:</label>
    <select id="topicSelect" name="topik">
        <option>Kapalı</option>
        <option>Genel kendi hatimim</option>
    </select>
    <button id="plusButton" class="plus-button">+</button>
</div>

  <a href="logout.php" title="Çıkış" class="button-link">Logout</a>
  <a href="info.html" title="Bilgi" target="_blank" class="button-link">&#8505;</a>

  <a onclick="toggleSettings()" title="Ayarlar" class="button-link">&#9881;</a>
</div>
<!-- Ayar Paneli -->
<div class="settings-bar" id="settingsBar">
  <div class="language-options">
    <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> Türkçe</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> İsveççe</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="english" onclick="updateLanguages()"> İngilizce</label>
  
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
</div>
<!-- Ayar Paneli -->


    <?php 
    }else{
    ?>
      <a href="login.php" title="Çıkış" class="button-link">Login</a>
  <a href="info.html" title="Bilgi" target="_blank" class="button-link">&#8505;</a>
  <a onclick="toggleSettings()" title="Ayarlar" class="button-link">&#9881;</a>

   
</div>
<!-- Ayar Paneli -->
<div class="settings-bar" id="settingsBar">
  <div class="language-options">
    <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> Türkçe</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> İsveççe</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="english" onclick="updateLanguages()"> İngilizce</label>
  
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
        $("#surahSelect").select2({
            placeholder: "Sûre Seçin",
            allowClear: true
        });

        // Sayfa yönlendirme
        $("#surahSelect").on("change", function() {
            var selectedPage = $(this).val();
            if (selectedPage) {
                var surahSelect = document.getElementById("surahSelect");
                var selectedSurah = surahSelect.value;
                window.location.href = "?surah=" + selectedSurah + "&changed=surahSelect";
            }
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
        goToPage('pageInput');
    }

    function goToPage(changedElementId) {
            
        var page_number = document.getElementById("pageInput").value;
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
        
    function changeFontSize() {
        var size = document.getElementById("sizeRange").value;
        var arabicTexts = document.getElementsByClassName("arabic");
        for (var i = 0; i < arabicTexts.length; i++) {
            arabicTexts[i].style.fontSize = size + "px";
        }
        document.getElementById("sizeValue").innerText = size + "px";
    }


    function updateLanguages() {
        var turkishChecked = document.getElementById("turkish").checked;
        var swedishChecked = document.getElementById("swedish").checked;
        var englishChecked = document.getElementById("english").checked;

        // Dil seçimlerini sessionStorage'da sakla
        sessionStorage.setItem("turkishChecked", turkishChecked);
        sessionStorage.setItem("swedishChecked", swedishChecked);
        sessionStorage.setItem("englishChecked", englishChecked);

        var turkishTexts = document.getElementsByClassName("turkish");
        var swedishTexts = document.getElementsByClassName("swedish");
        var englishTexts = document.getElementsByClassName("english");
        
        // Dil metinlerini görünür hale getir
        for (var i = 0; i < turkishTexts.length; i++) {
            turkishTexts[i].style.display = turkishChecked ? "inline" : "none";
        }
        for (var i = 0; i < swedishTexts.length; i++) {
            swedishTexts[i].style.display = swedishChecked ? "inline" : "none";
        }
        for (var i = 0; i < englishTexts.length; i++) {
            englishTexts[i].style.display = englishChecked ? "inline" : "none";
        }

        // Alt satır metinlerini görünür hale getir
        var tas = document.getElementsByClassName("tas");
        var sas = document.getElementsByClassName("sas");
        var eas = document.getElementsByClassName("eas");
        
        for (var i = 0; i < tas.length; i++) {
            tas[i].style.display = turkishChecked ? "inline" : "none";
        }
        for (var i = 0; i < sas.length; i++) {
            sas[i].style.display = swedishChecked ? "inline" : "none";
        }
        for (var i = 0; i < eas.length; i++) {
            eas[i].style.display = englishChecked ? "inline" : "none";
        }

        // Genel görünürlük ayarı
        var as = document.getElementsByClassName("as");
        for (var i = 0; i < as.length; i++) {
            as[i].style.display = (turkishChecked || swedishChecked || englishChecked) ? "inline" : "none";
        }
    }

    // Checkbox durumları değiştiğinde updateLanguages fonksiyonunu çağır
    document.getElementById("turkish").addEventListener('change', updateLanguages);
    document.getElementById("swedish").addEventListener('change', updateLanguages);
    document.getElementById("english").addEventListener('change', updateLanguages);


    function toggleSettings() {
        const bar = document.getElementById("settingsBar");
        bar.style.display = bar.style.display === "block" ? "none" : "block";
    }


    let hideTimeout;

        document.getElementById("settingsBar").addEventListener("mouseleave", function () {
            // 1 saniye sonra gizle
            hideTimeout = setTimeout(() => {
                this.style.display = "none";
            }, 1000);
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

// Sayfa yüklendiğinde sonuçları geri yükle
document.addEventListener("DOMContentLoaded", function () {
    // Dil seçimlerini geri yükle
    const turkishChecked = sessionStorage.getItem("turkishChecked") === "true";
    const swedishChecked = sessionStorage.getItem("swedishChecked") === "true";
    const englishChecked = sessionStorage.getItem("englishChecked") === "true";

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

function changeFontSize() {
    const size = document.getElementById("sizeRange").value;

    // Yazı boyutunu sessionStorage'da sakla
    sessionStorage.setItem("fontSize", size);

    const arabicTexts = document.getElementsByClassName("arabic");
    for (let i = 0; i < arabicTexts.length; i++) {
        arabicTexts[i].style.fontSize = size + "px";
    }
    document.getElementById("sizeValue").innerText = size + "px";
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
</script>
</body>
</html>
