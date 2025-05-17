<?php

$kelime = "مٍۨ";

// Her karakteri ayır
$harfler = preg_split('//u', $kelime, -1, PREG_SPLIT_NO_EMPTY);

// Karakterleri Unicode değeriyle beraber ekrana yazdır
foreach ($harfler as $harf) {
    $unicode = mb_ord($harf, 'UTF-8');

    if ($unicode == 0x06eb) { // ۙ karakterini kırmızı yap
        echo "<span style='color: red;'>$harf</span>";
    } else {
        echo $harf;
    }
}

    echo "<hr>\u{0646}\u{0650}<font color=red size='5'> \u{06EB}</font>  harfi<br>";
    echo "<hr>\u{0646}\u{0650}<font color=red size='5'> \u{06D9}</font>  lamelif harfi<br>";
    echo "\u{06da}\u{06db}";

?>
