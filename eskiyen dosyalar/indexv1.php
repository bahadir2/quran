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
            font-family: Arial, sans-serif;
            background-color: #FFF9C4;
            text-align: center;
            padding: 18px;
        }
        .turkish {
            //display: inline;
            color: red;
            font-size: 18px;
            margin-right: 10px;
        }
        .swedish {
            //display: inline;
            color: #006400;
            font-size: 18px;
            margin-right: 10px;
        }
        .english {
            //display: inline;
            color: black;
            font-size: 18px;
            margin-right: 10px;
        }
        .arabic {
            //display: inline; /* veya inline-block */
            font-family: 'Traditional Arabic';
            //direction: rtl;
            font-size: 32px;
            //display: block;
            letter-spacing: 0.1px; /* Harfler arasındaki mesafeyi aç */
            line-height: 1.8;
            color: #006600;
        }
        .container1 {
            display: inline; /* veya inline-block */
            max-width: 900px;
            margin: auto;
            text-align: justify;
        }
         .container2 {
            display: inline; /* veya inline-block */
            max-width: 900px;
            margin: auto;
            text-align: justify;
        }
        .content {
            display: inline;
        }
        .slider-container {
            margin: 20px;
        }
        
        .navbar {
            background-color: #8B4513;
            padding: 3px;
            text-align: center;
            height: 30px;
        }
        .navbar select, .navbar input {
            padding: 2px 8px;
            font-size: 10px;
            margin: 0 10px;
            border-radius: 4px;
        }
        .navbar label {
            color: white;
            font-size: 8px;
            margin-right: 5px;
        }
        
        .page-navigation {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,0.1);
            padding: 2px 2px;
            border-radius: 4px;
            margin-right: 8px;
        }

        .page-navigation input {
            width: 50px;
            padding: 2px 4px;
            font-size: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 5px;
        }

        .nav-button {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 13px;
            cursor: pointer;
            margin: 0 2px;
        }

        .nav-button:hover {
            background: #f0f0f0;
        }

        .verse-container {
            text-align: left;
            font-size: 10px; /* Yazı boyutunu 20 piksel yapar */
        }

        #juzSelect {
        font-size: 10px; /* Yazı boyutunu küçült */
        height: 20px; /* Yüksekliği ayarla */
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
        border: 3px solid #182D47; /* Kenar rengi ve kalinligi #182D47 */
        transition: all 0.3s; /* Geçiş efekti */
        }

        .secde:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.4);
        }

        .select2-container {
            width: 120px !important;
        }
        .select2-container--default .select2-selection--single {
        font-size: 10px; /* Yazı puntosunu büyüt */
        height: 28px; /* Yüksekliği artır */
    }
    .select2-dropdown {
        font-size: 10px; /* Açılır listedeki yazıları büyüt */
    }

    /* Her label için farklı arka plan rengi */
        
    </style>
    
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

        function changeVerse() {
            var surah = document.getElementById("surahSelect").value;
            var verse = document.getElementById("verseSelect").value;
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


        
    </script>
</head>
<body>

<div class="navbar">
    <div class="page-navigation">
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
        <input type="number" 
               id="pageInput" 
               min="0" 
               max="604" 
               value="<?php echo $page_number; ?>" 
               onchange="goToPage('pageInput')"
               onkeyup="if(event.keyCode===13) goToPage('pageInput')"
               autocomplete="off">
        <button type="button" onclick="changePage('next')" class="nav-button">&gt;</button>
    </div>

    <label for="surahSelect">Sure:</label>
    <select id="surahSelect"  class="combobox">
        <?php
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
</div>
    

    <div class="navbar">
        <div class="page-navigation">
        <label class="size-label" for="sizeRange1"> Arapça Yazı Boyutu:
        <input type="range" id="sizeRange" min="32" max="50" step="4" value="32" oninput="changeFontSize()">
        <span id="sizeValue">32px</span></label>
         </div>
        <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> Türkçe</label>
        <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> İsveççe</label>
        <label><input type="checkbox" id="english" onclick="updateLanguages()"> İngilizce</label>
<a href="info.html" target="_blank" title="Bilgi"
   style="
     display: inline-flex;
     align-items: center;
     gap: 6px;
     background-color: #5C4033; /* koyu kahverengi */
     color: #FCEFD4;             /* açık krem */
     padding: 2px 8px;
     font-size: 13px;
     font-family: Arial, sans-serif;
     border-radius: 6px;
     text-decoration: none;
     box-shadow: 1px 1px 4px rgba(0,0,0,0.2);
     transition: background-color 0.3s ease;
   "
   onmouseover="this.style.backgroundColor='#4b3226'"
   onmouseout="this.style.backgroundColor='#5C4033'">
   <span style="font-size: 16px;">&#8505;</span> 
</a>


<?php
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    ?>
<a href="logout.php" title="Exit"
   style="
     display: inline-flex;
     align-items: center;
     gap: 6px;
     background-color: #5C4033; /* koyu kahverengi */
     color: #FCEFD4;             /* açık krem */
     padding: 2px 8px;
     font-size: 13px;
     font-family: Arial, sans-serif;
     border-radius: 6px;
     text-decoration: none;
     box-shadow: 1px 1px 4px rgba(0,0,0,0.2);
     transition: background-color 0.3s ease;
   "
   onmouseover="this.style.backgroundColor='#4b3226'"
   onmouseout="this.style.backgroundColor='#5C4033'">
   <strong style="font-size: 16px;">e</strong> 
</a>
    <?php
  }else{
?>
    <a href="login.php" title="Kayit"
   style="
     display: inline-flex;
     align-items: center;
     gap: 6px;
     background-color: #5C4033; /* koyu kahverengi */
     color: #FCEFD4;             /* açık krem */
     padding: 2px 8px;
     font-size: 13px;
     font-family: Arial, sans-serif;
     border-radius: 6px;
     text-decoration: none;
     box-shadow: 1px 1px 4px rgba(0,0,0,0.2);
     transition: background-color 0.3s ease;
   "
   onmouseover="this.style.backgroundColor='#4b3226'"
   onmouseout="this.style.backgroundColor='#5C4033'">
   <strong style="font-size: 16px;">r</strong> 
</a>
    <?php
  }
?>
</div>
<p> &nbsp;</p>
<?php  
    $sql = "SELECT * FROM quran WHERE page = $page_number ORDER BY id ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) 
        {
        // Get the verse number (id)
        $verseNumber = $selected_verse; // Assuming 'id' represents the verse number
        //echo $row['id'];
            if ($row['ayno'] == 1) {
                $surah2 = $surahs[$row["sur"]];
                switch ($row["sur"]){
                case 1:
                    // Resmi yükle
                    //$image = "baslik1.png";
                    //include "orbaslik.php";
                    ?>
                    <div style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=<?= $surah2 ?>' style='max-width: 100%; height: auto;'>
                </div><br><br>
                    <?php 
                    break;
                case 9:
                    // Resmi yükle
                    //$image = "baslik1.png";
                    //include "orbaslik.php";
                    ?>
                    <div style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=<?= $surah2 ?>' style='max-width: 100%; height: auto;'>
                </div><br><br>
                    <?php 
                    break;
                default:
                    //$image = "baslik2.png";
                    //include "orbaslik.php";
                    ?>
                    <div style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=2&k=<?= $surah2 ?>' style='max-width: 100%; height: auto;'>
                </div><br><br>
                    <?php
                    
                }

                
            }
            
            // Arapça metin
            $arabicText = $row['or'];
            // Parantezleri kaldırılmış Arapça metin
            $arabicText = removeArabicParentheses($row['or'],$row['ayno'], $row['id']);
            $turkishText = removeTurkishParentheses($row['tr']);
            $swedishText = removeSwedishParentheses($row['sv']);
            if ($row['tr'] or $row['sv'] or $row['en']) {
                echo "
                <span class='turkish' style='display:none;'>{$turkishText}</span>
                <span class='tas' style='display:none;'><br></span>
                <span class='swedish' style='display:none;'>{$swedishText}</span>
                <span class='sas' style='display:none;'><br></span>
                <span class='english' style='display:none;'>{$row['en']}</span>
                <span class='eas' style='display:none;'><br></span>
                <span class='arabic' style='position: relative; '>{$arabicText}</span>
                <span class='as' style='display:none;'><hr></span>";
            }else {
                echo $arabicText;
                
                //echo "{$arabicText}";
            }
            //echo $row['id'];
        }
    } else {
        echo "<p>Veri bulunamadı.</p>";
    }
    
$conn->close();
        ?>

    <div class='verse-container'>
<?php 
        echo $page_number; 


        ?>
        </div>

</body>
</html>