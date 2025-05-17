<?php

function removeSwedishParentheses($text) {
    //$text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-9');
    $text=str_replace("Ã¤","ä", $text);
    $text=str_replace("Ã¥","å", $text);
    $text=str_replace("Ã„","Ä", $text);
    $text=str_replace("Ã…","Å", $text);
    $text=str_replace("Ã¶","ö", $text);
    $text=str_replace("Ã–","Ö", $text);

    
    return $text;       
}
?>