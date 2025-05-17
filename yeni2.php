<?php

$kelime ="ضَٓ";
$harfler = preg_split('//u', $kelime, -1, PREG_SPLIT_NO_EMPTY);

foreach ($harfler as $harf) {
    $unicode = mb_ord($harf, 'UTF-8');
    echo "Karakter: " . $harf . " - Unicode: " . dechex($unicode) . "<br>";
}
////$unicode = mb_ord($harf, 'UTF-8');

?>
<br><br><br>
<?php
echo "\u{0642}<br>";
echo "<br>";
echo "\u{0641}<br>";
echo "<br>";
echo "\u{0628}\u{06d8}";//tam ustune gelmesi icin 
echo "<br>";
echo "\u{0628}\u{fbc0}";
echo "<br>";
echo "\u{fbc0}\u{0628}";
echo "<br>";
?>
<br><br><br><br><br><br><br><br><br>