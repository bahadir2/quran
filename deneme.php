<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkbox ile Satır Görünürlüğü</title>
    <style>
        .as {
            display: none; /* Varsayılan olarak gizli */
        }
    </style>
</head>
<body>

    <label>
        <input type="checkbox" id="turkishCheckbox"> Türkçe
    </label>
    <label>
        <input type="checkbox" id="swedishCheckbox"> İsveççe
    </label>
    <label>
        <input type="checkbox" id="englishCheckbox"> İngilizce
    </label><?php
// Örnek veriler (gerekirse değiştir)
$turkishText = "Türkçe metin"; // Türkçe metin
$swedishText = "Svensk text"; // İsveççe metin
$englishText = "English text"; // İngilizce metin
$arabicText = "النص العربي"; // Arapça metin

echo "<span class='arabic'>
    <span class='turkish' style='display:none;'>$turkishText</span>
    <span class='tas' style='display:none;'>xxxxxxxxxxxxxxxxxxxxx<br></span>
    <span class='swedish' style='display:none;'>$swedishText</span>
    <span class='sas' style='display:none;'>xxxxxxxxxxxxxxxxxx<br></span>
    <span class='english' style='display:none;'>$englishText</span>
    <span class='eas' style='display:none;'>xxxxxxxxxxxxxxxxx<br></span>
    $arabicText
</span>
<span class='as' style='display:none;'>yyyyyyyyyyyyyyyy<br></span>";
?>

<script>
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