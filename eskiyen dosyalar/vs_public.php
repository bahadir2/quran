<br>



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
            $verseNumber2 = $row['ayno'];
            // Parantezleri kaldırılmış Arapça metin
            $arabicText = removeArabicParentheses($row['or'],$row['ayno'], $row['id']);
            $swedishText = removeSwedishParentheses($row['sv']);
            if ($row['tr'] or $row['sv'] or $row['en']) {
                echo "
                <span class='turkish' style='display:none;'>{$row['tr']}</span>
                <span class='tas' style='display:none;'><br></span>
                <span class='swedish' style='display:none;'>{$swedishText}</span>
                <span class='sas' style='display:none;'><br></span>
                <span class='english' style='display:none;'>{$row['en']}</span>
                <span class='eas' style='display:none;'><br></span>
                <span class='arabic' data-verse='{$verseNumber2}' data-top='-10' data-left='5' style='position: relative; '>{$arabicText}</span>

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


