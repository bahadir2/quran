<br>
<?php  


// Parantezleri ve içindeki Arapça rakamları kaldırmak için düzenli ifade
function latinToArabicNumbers($number) {
    $latin = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    return str_replace($latin, $arabic, $number);
}

// Secde ayetlerinin ID'leri
$secde_ayetleri = [1160, 1722, 1950, 2136, 2308, 2614, 2915, 3184, 3518, 3994, 4255, 4846, 5905, 6125];

$blokturkish = '';
$blokswedish = '';
$blokenglish = '';
$fast = '';
$blokbartr='';
$blokbarsv='';
$blokbaren='';
$subtext='';
$arabicText ='';
$arabicText2 ='';
$bar='';
$f_org='';
$subt_std ='';
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
                    $blokbartr='<br><br><b>*** '.$surah2.' suresi ***</b><br>';
                    $blokbarsv='<br><br><b>*** Sura al-'.$surah2.' ***</b><br>';
                    $blokbaren='<br><br><b>*** Surah Al-'.$surah2.' ***</b><br>';
                    $bar="<span style='visibility: hidden;'>٠</span><div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    break;
                case 9:
                    $blokbartr='<br><br><b>*** '.$surah2.' suresi ***</b><br>';
                    $blokbarsv='<br><br><b>*** Sura al-'.$surah2.' ***</b><br>';
                    $blokbaren='<br><br><b>*** Surah Al-'.$surah2.' ***</b><br>';
                    
                $bar="<span style='visibility: hidden;'>٠</span><div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    break;
                default:

                    $blokbartr='<br><br><b>*** '.$surah2.' suresi ***</b><br>Rahmân ve rahîm olan Allah\'ın adıyla<br>';
                    $blokbarsv='<br><br><b>*** Sura al-'.$surah2.' ***</b><br>I Guds, den Barmhärtiges, den Nåderikes namn.<br>';
                    $blokbaren='<br><br><b>*** Surah Al-'.$surah2.' ***</b><br>In the name of Allah, the Most Gracious, the Most Merciful.<br>';
                    
                $bar="<span style='visibility: hidden;'>٠</span><div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=2&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                }

            }else {
                $blokbartr='';
                $blokbarsv='';
                $blokbaren='';
                $bar='';
            }

            $swedishText = removeSwedishParentheses($row['sv']);

            $f_org=removeArabicParentheses($row['or']);
            $arabicText = $arabicText.$f_org;
            
if (in_array($id, $secde_ayetleri)) {
    $output = "<span class='secde' style='margin-left: 0.5em;'>".latinToArabicNumbers($row['ayno']).
    "<div>
        <span style='display:block;'>".$row['ayno']." Secde Ayetidir. </span>
        <span class='s_turkish' style='display:none;'>" . $row['tr'] . "</span>
        <span class='s_swedish' style='display:none;'>" . $swedishText . "</span>
        <span class='s_english' style='display:none;'>" . $row['en'] . "</span>
    </div>
</span><span style='visibility: hidden;'>٠</span>";
$outputsade = "<span class='secde' style='margin-left: 0.5em;' title='".$row['ayno']." Secde Ayetidir'>".latinToArabicNumbers($row['ayno'])."</span>";

}else{
    $output = "<span class='normal' style='margin-left: 0.5em;'>".latinToArabicNumbers($row['ayno']).
    "<div>
        <span style='display:block;'>".$row['ayno']."</span>
        <span class='s_turkish' style='display:none;'>".$row['tr']."</span>
        <span class='s_swedish' style='display:none;'>".$swedishText."</span>
        <span class='s_english' style='display:none;'>".$row['en']."</span>
    </div>
</span><span style='visibility: hidden;'>٠</span>";
$outputsade = "<span class='normal' style='margin-left: 0.5em;' title='".$row['ayno']."'>".latinToArabicNumbers($row['ayno'])."</span>";


}
    
//$ayet="<span class='arabic'>$ayet</span>".$output; //output ile ayiraclar ekleniyor
//$ayet = "<span class='arabic' data-verse='{$row['ayno']}' data-top='-10' data-left='5' style='position: relative; margin-left: 0.5em;'>{$ayet}</span>".$output;
$subt_std =  $subt_std.$bar."<span class='turkish' style='display:none;'>{$row['tr']}</span>
                <span class='tas' style='display:none;'><br></span>
                <span class='swedish' style='display:none;'>{$swedishText}</span>
                <span class='sas' style='display:none;'><br></span>
                <span class='english' style='display:none;'>{$row['en']}</span>
                <span class='eas' style='display:none;'><br></span>
                <span class='arabic' data-verse='{$row['ayno']}' data-top='-10' data-left='5' style='position: relative; margin-left: 0.5em;'>{$f_org}</span>"
                .$output
                ."<span class='as' style='display:none;'><hr></span>";

$fast =  $fast.$bar."
                <span class='arabic2' data-verse='{$row['ayno']}' data-top='-10' data-left='5' style='position: relative; margin-left: 0.5em;'>{$f_org}</span>"
                .$outputsade;

    /********************************************** u
    1.removeArabic... istegi public.php de
    2.$ayet iki farkli gorunumu tasir
    3.totalgorunum(class isimleri): baslik(image)
    4.gorunum1(class isimleri): iskelet_3.sik,arabic, secenekler_turkish,tas,swedish,sas,english,eas,
    5.gorunum2(class isimleri): iskelet_3.sik,arabic2, secenekler_turkish2,swedish2,english2,
    ********************************************** n*/


//paket1 baslik turkish swidish english orginal
            
            //$arabicText2 = $arabicText2.removeArabicParentheses($row['or'],$row['ayno'], $row['id'],$turkishText,$swedishText,$row['en'],$med2);
            $blokturkish = $blokturkish.$blokbartr.'<b>'.$row['ayno'].'.</b> '.$row['tr'].' ';
            $blokswedish = $blokswedish.$blokbarsv.'<b>'.$row['ayno'].'.</b> '.$swedishText.' ';
            $blokenglish = $blokenglish.$blokbaren.'<b>'.$row['ayno'].'.</b> '.$row['en'].' ';
        }
    } else {
        echo "<p>Veri bulunamadı.</p>";
    }
    
$conn->close();


        ?>

<div class='first' style='display:none;'><?php echo $subt_std; ?><span style='visibility: hidden;'>٠</span><hr></div>
<div class='blokturkish' style='display:none;'><?php echo $blokturkish; ?><hr></div>
<div class='blokswedish' style='display:none;'><?php echo $blokswedish; ?><hr></div>
<div class='blokenglish' style='display:none;'><?php echo $blokenglish; ?><hr></div>
<div class='blokarabic' ><?php echo $fast; ?><span style='visibility: hidden;'>٠</span><hr></div>

<hr>
    <div class='verse-container'>
<label for="pageInput">Page:</label>
        <button type="button" onclick="changePage('prev')" class="nav-button">&lt;</button>
        <input type="number" id="pageInput" min="0" max="604" value="<?php echo $page_number; ?>" 
    onchange="goToPage('pageInput', this.value )" 
    onkeyup="if(event.keyCode===13) goToPage('pageInput', this.value )" 
    autocomplete="off">
        <button type="button" onclick="changePage('next')" class="nav-button">&gt;</button>

        
        </div>


