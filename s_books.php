<?php
//<select id="surahSelect" onchange="changeSurah();">
        $books = array(
            1 => "Kur'an-ı Kerim", 
        );
        
        foreach ($books as $key => $value): 
        echo '<option value="' . $key . '" ' . ($selected_book == $key ? 'selected' : '') . '>' . $key . ". " . $value . '</option>';
endforeach; ?>