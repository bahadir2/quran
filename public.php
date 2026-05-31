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
//$sveri='';//sure toplam kac sayfa ve simdi sen kacinci sayfadasin
//$psveri='';//ay no
    $sql = "SELECT * FROM quran WHERE page = $page_number ORDER BY id ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) 
        {
        // Get the verse number (id)
        $verseNumber = $selected_verse; // Assuming 'id' represents the verse number
        //echo $row['id'];
//$sveri=$sveri.$row['ayno'];
            if ($row['ayno'] == 1) {
                $surah2 = $surahs[$row["sur"]];
//$sveri=$sveri.$surah2."-";
                switch ($row["sur"]){
                case 1:
                    // Resmi yükle
                    $blokbartr='<br><br><b><font color=red>*** '.$surah2.' suresi ***</b></font><br>';
                    $blokbarsv='<br><br><b><font color=red>*** Sura al-'.$surah2.' ***</b></font><br>';
                    $blokbaren='<br><br><b><font color=red>*** Surah Al-'.$surah2.' ***</b></font><br>';
                    $bar="<span style='visibility: hidden;'>٠</span><div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    break;
                case 9:
                    $blokbartr='<br><br><b><font color=red>*** '.$surah2.' suresi ***</b></font><br>';
                    $blokbarsv='<br><br><b><font color=red>*** Sura al-'.$surah2.' ***</b></font><br>';
                    $blokbaren='<br><br><b><font color=red>*** Surah Al-'.$surah2.' ***</b></font><br>';
                    
                $bar="<span style='visibility: hidden;'>٠</span><div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    break;
                default:

                    $blokbartr='<br><br><b><font color=red>*** '.$surah2.' suresi ***</b></font><br>Rahmân ve rahîm olan Allah\'ın adıyla<br>';
                    $blokbarsv='<br><br><b><font color=red>*** Sura al-'.$surah2.' ***</b></font><br>I Guds, den Barmhärtiges, den Nåderikes namn.<br>';
                    $blokbaren='<br><br><b><font color=red>*** Surah Al-'.$surah2.' ***</b></font><br>In the name of Allah, the Most Gracious, the Most Merciful.<br>';
                    
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

            $sonuc = removeArabicParentheses($row['or']);
            $f_org = $sonuc['ayet'];
            $isaretler = $sonuc['isaretler'];

            $arabicText = $arabicText.$f_org;


            
if (in_array($row['id'], $secde_ayetleri)) {
    //buradaki output(s_turkish ...)  subtitle olarak cikar
    $output = "<span class='secde' style='margin-left: 0.5em;'>".latinToArabicNumbers($row['ayno']).
    "<div>
        <span style='display:block;'>".$row['ayno']." (Secde Ayetidir.) ".$isaretler."</span>
        <span class='s_turkish' style='display:none;'>" . $row['tr'] . "</span>
        <span class='s_swedish' style='display:none;'>" . $swedishText . "</span>
        <span class='s_english' style='display:none;'>" . $row['en'] . "</span>
    </div>
    </span><span style='visibility: hidden;'>٠</span>";

    //buradaki turkish metin olarak cikar ve output olarakta subtitle aciklamasi eklenir
    $subt_std =  $subt_std.$bar."<span class='turkish' style='display:none;'>{$row['tr']} <font color=red><sup>[Secde Ayeti*]</sup></font></span>
                    <span class='tas' style='display:none;'><br></span>
                    <span class='swedish' style='display:none;'>{$swedishText} <font color=red><sup>[Vers för nedkastelse i tillbedjan*]</sup></font></span>
                    <span class='sas' style='display:none;'><br></span>
                    <span class='english' style='display:none;'>{$row['en']} <font color=red><sup>[Prostration Verse*]</sup></font></span>
                    <span class='eas' style='display:none;'><br></span>
                    <span class='arabic' data-verse='{$row['ayno']}' data-top='-10' data-left='5' style='position: relative; margin-left: 0.5em;'>{$f_org}</span>"
                    .$output
                    ."<span class='as' style='display:none;'><hr></span>";

    $outputsade = "<span class='secde' style='margin-left: 0.5em;' title='".$row['ayno']." (Secde Ayetidir.)'>".latinToArabicNumbers($row['ayno'])."</span>";
    $fast =  $fast.$bar."
                    <span class='arabic2' data-verse='{$row['ayno']}' data-top='-10' data-left='5' style='position: relative; margin-left: 0.5em;'>{$f_org}</span>"
                .$outputsade;

    $blokturkish = $blokturkish.$blokbartr.'<b>'.$row['ayno'].'.</b> <font color=red><sup>[Secde Ayeti*]</sup></font> '.$row['tr'].' ';
    $blokswedish = $blokswedish.$blokbarsv.'<b>'.$row['ayno'].'.</b> <font color=red><sup>[Vers för nedkastelse i tillbedjan*]</sup></font> '.$swedishText.' ';
    $blokenglish = $blokenglish.$blokbaren.'<b>'.$row['ayno'].'.</b>  <font color=red><sup>[Prostration Verse*]</sup></font> '.$row['en'].' ';
}else{
    //*buradaki s_turkish subtitle olarak cikar
    $output = "<span class='normal' style='margin-left: 0.5em;'>".latinToArabicNumbers($row['ayno']).
    "<div>
        <span style='display:block;'>".$row['ayno']." ".$isaretler."</span>
        <span class='s_turkish' style='display:none;'>".$row['tr']."</span>
        <span class='s_swedish' style='display:none;'>".$swedishText."</span>
        <span class='s_english' style='display:none;'>".$row['en']."</span>
    </div>
    </span><span style='visibility: hidden;'>٠</span>";
    //*buradaki turkish metin olarak cikar
    $outputsade = "<span class='normal' style='margin-left: 0.5em;' title='".$row['ayno']."'>".latinToArabicNumbers($row['ayno'])."</span>";

    //*buradaki turkish metin olarak cikar
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

    $blokturkish = $blokturkish.$blokbartr.'<b>'.$row['ayno'].'.</b> '.$row['tr'].' ';
    $blokswedish = $blokswedish.$blokbarsv.'<b>'.$row['ayno'].'.</b> '.$swedishText.' ';
    $blokenglish = $blokenglish.$blokbaren.'<b>'.$row['ayno'].'.</b> '.$row['en'].' ';
}
    

    /********************************************** u

    ********************************************** n*/
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
&nbsp;<a href="help1.htm" target="_blank">Tecvid isaretleri</a>
<a href="help2.htm" target="_blank">Karistirilan harfler</a>
<a href="help3.htm" target="_blank">Faydali Linkler</a>
        
        </div>


