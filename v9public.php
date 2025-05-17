<br>
<?php  
$blokturkish = '';
$blokswedish = '';
$blokenglish = '';
$blokarabic = '';
$blokbartr='';
$blokbarsv='';
$blokbaren='';
$subtext='';
$arabicText ='';
$arabicText2 ='';
$med='';
$med2='';
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
                    $blokbartr='<br><br><b>*** '.$surah2.' suresi ***</><br>';
                    $blokbarsv='<br><br><b>*** Sura al-'.$surah2.' ***</b><br>';
                    $blokbaren='<br><br><b>*** Surah Al-'.$surah2.' ***</><br>';
                    $med="<div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    $med2="<div class='baslik2'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    break;
                case 9:
                    $blokbartr='<br><br><b>*** '.$surah2.' suresi ***</><br>';
                    $blokbarsv='<br><br><b>*** Sura al-'.$surah2.' ***</b><br>';
                    $blokbaren='<br><br><b>*** Surah Al-'.$surah2.' ***</><br>';
                    
                $med="<div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                $med2="<div class='baslik2'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=1&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                    break;
                default:

                    $blokbartr='<br><br><b>*** '.$surah2.' suresi ***</b><br>Rahmân ve rahîm olan Allah\'ın adıyla<br>';
                    $blokbarsv='<br><br><b>*** Sura al-'.$surah2.' ***</b><br>I Guds, den Barmhärtiges, den Nåderikes namn.<br>';
                    $blokbaren='<br><br><b>*** Surah Al-'.$surah2.' ***</b><br>In the name of Allah, the Most Gracious, the Most Merciful.<br>';
                    
                $med="<div class='baslik'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=2&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                 $med2="<div class='baslik2'; style='text-align: center; margin-top: 20px;'>
                <img src='orbaslik.php?m=2&k=".$surah2."' style='max-width: 100%; height: auto;'>
                <br><br>
                </div>";
                }

            }else {
                $blokbartr='';
                $blokbarsv='';
                $blokbaren='';
                $med='';
                $med2='';
            }
            
            // Arapça metin
            //$arabicText = $row['or'];
            $verseNumber2 = $row['ayno'];
            // Parantezleri kaldırılmış Arapça metin
            $turkishText = $row['tr'];
            $swedishText = removeSwedishParentheses($row['sv']);
            $es=removeArabicParentheses($row['or'],$row['ayno'], $row['id'],'1',$turkishText,$swedishText,$row['en'],$med);
            $arabicText = $arabicText.$es;
            $arabicText2 = $arabicText2.removeArabicParentheses($row['or'],$row['ayno'], $row['id'],'2',$turkishText,$swedishText,$row['en'],$med2);
            $blokturkish = $blokturkish.$blokbartr.'<b>'.$row['ayno'].'.</b> '.$row['tr'].' ';
            $blokswedish = $blokswedish.$blokbarsv.'<b>'.$row['ayno'].'.</b> '.$swedishText.' ';
            $blokenglish = $blokenglish.$blokbaren.'<b>'.$row['ayno'].'.</b> '.$row['en'].' ';
            $blokarabic = $blokarabic."<span class='arabic2' data-verse='{$verseNumber2}' data-top='-10' data-left='5' style='position: relative; '>{$es}</span>
            <span class='as2' style='display:none;'><hr></span>";

        }
    } else {
        echo "<p>Veri bulunamadı.</p>";
    }
    
$conn->close();


        ?>
<div><?php echo $arabicText ?><span style="visibility: hidden;">٠</span>
<div class='subtext-arabic' style='display:none;' ><?php echo $arabicText2 ?><span style="visibility: hidden;">٠</span></div>
<div class='blok-turkish' style='display:none;'><?php echo $blokturkish; ?><hr></div>
<div class='blok-swedish' style='display:none;'><?php echo $blokswedish; ?><hr></div>
<div class='blok-english' style='display:none;'><?php echo $blokenglish; ?><hr></div>
<div class='blok-arabic' style='display:none;'><?php echo $blokarabic; ?><hr></div>

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


