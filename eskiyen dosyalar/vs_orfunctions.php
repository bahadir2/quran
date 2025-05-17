<?php
// besmele ve cumle sonundaki anlasilamayan tek veya cift noktalar databaseden direk silinmis diger durumlar filtre edileerek duzeltilmeye calisilmistir.
//bu bolumu uce ayirabiliriz 
// 1. Secde ayetlerinin kirmizi yap
// 2.daire icinde arabic rakamlarla ayiraclar 
// 3.harf filtreleme
// 4. kelme filtreleme
// kaynak kod: mb_chr(0x06EA, 'UTF-8') bu kod unikode dan normal harf donusumleri yapar
// kaynak kod: mb_ord($harf, 'UTF-8') bu kod unikode donusumleri yapar


// Tarayıcıdan gelen yazı boyutunu PHP oturumunda saklamak için bir mekanizma
//index.php 594. satirdan gonderilen session

$fontSize = isset($_SESSION['fontSize']) ? $_SESSION['fontSize'] : 32;
switch ($fontSize) {
    case 32:
        $ft = "22";$fs = "22";
        break;
    case 36:
        $ft = "26";$fs = "24";
        break;
    case 40:
        $ft = "30";$fs = "26";
        break;
    case 44:
        $ft = "34";$fs = "28";
        break;
    case 48:
        $ft = "38";$fs = "30";
        break;
    default:
        $ft = "22";$fs = "22"; // Default value if none is set
}



function mb_str_replace($search, $replace, $subject, $encoding = "UTF-8") {
    return mb_convert_encoding(str_replace(
        mb_convert_encoding($search, "UTF-16LE", $encoding),
        mb_convert_encoding($replace, "UTF-16LE", $encoding),
        mb_convert_encoding($subject, "UTF-16LE", $encoding)
    ), $encoding, "UTF-16LE");
}

// Parantezleri ve içindeki Arapça rakamları kaldırmak için düzenli ifade
function latinToArabicNumbers($number) {
    $latin = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    return str_replace($latin, $arabic, $number);
}

function change_red($harf, $harfc, $text, $top, $sol, $desc, $bcol) {
    $y_harf = "<span style='position: relative;'>".
    "<span class='dynamic-font' style='color: $bcol; top: $top; left: $sol;' title='$desc'>$harfc</span>".
    "</span>";
    return str_replace($harf, $y_harf, $text);
}

function change_reds($harf, $harfc, $text, $top, $size, $sol, $desc, $bcol) {
    $y_harf = "<span style='position: relative;'>".
    "<span class='dynamic-font' style='color: $bcol; top: $top; font-size: $size; left: $sol;' title='$desc'>$harfc</span>".
    "</span>";
    return str_replace($harf, $y_harf, $text);
}

function removeArabicParentheses($text,$n,$id) {

// Secde ayetlerinin ID'leri
$secde_ayetleri = [1160, 1722, 1950, 2136, 2308, 2614, 2915, 3184, 3518, 3994, 4255, 4846, 5905, 6125];

if (in_array($id, $secde_ayetleri)) {
        $output = "<span class='secde' title='$n Secde Ayetidir'>".latinToArabicNumbers($n)."</span>";
    }else{
        $output = "<span class='normal' title='$n'>".latinToArabicNumbers($n)."</span>";
    }
//$text="<span class='arabic'>$text</span>".$output; //output ile ayiraclar ekleniyor
$text=$text.$output; //output ile ayiraclar ekleniyor
global $ft, $fs;

//https://en.wikipedia.org/wiki/Arabic_script_in_Unicode
//harf harf filtreleme unicode ile
$text=mb_str_replace(mb_chr(0x06EA, 'UTF-8'), mb_chr(0x0650, 'UTF-8'), $text); //"۪" to "ِ";
//guzel calisiyor


//***************************** normal harflerle isaratleme
//$text = change_reds(mb_chr(0x0653, 'UTF-8'), mb_chr(0x653, 'UTF-8'), $text, '-12px', '52px', '-3px', "5.4 elif uzatilir");
//bu uzatma yaptigimizda uzatma harfi kopuyor onun icin kullanamiyoruz

//* small high seen_6dc harfini tah_637 harfine cevir ve kirmizi yap
$text = change_reds(mb_chr(0x06DC, 'UTF-8'), mb_chr(0x0637, 'UTF-8'), $text, '-18px', '20px', '-6px', "1.Durulması evlâdır, geçilmesi caizdir.", 'blue');
//*SH_rounded_zero_6df harfini ain_639 cevir ve kirmizi yap
$text = change_reds(mb_chr(0x06DF, 'UTF-8'), mb_chr(0x0639, 'UTF-8'), $text, '-28px', '14px', '-18px', "2.Durulması ve namazda ise rükû yapılabilir.", 'red');
//* SH sad with alef maksura_6d6 harfini sad_635 harfine cevir ve kirmizi yap
$text = change_reds(mb_chr(0x06D6, 'UTF-8'), mb_chr(0x0635, 'UTF-8'), $text, '-28px', '18px', '-8px', "3.Geçilmesi evlâdır, durulması caizdir", 'green');
//* SH_mem_initial_6D8 harfini zain_632 harfine cevir ve kirmizi yap
$text = change_reds(mb_chr(0x06D8, 'UTF-8'), mb_chr(0x0632, 'UTF-8'), $text, '-18px', '20px', '-10px', "4.Geçilmesi evlâdır, durulması caizdir", 'green');
//*SH_qaf_with_alef_6d7 harfi qaf_642 harfi ve kirmizi yap (ornek 14.sayfa)
$text = change_reds(mb_chr(0x06D7, 'UTF-8'), mb_chr(0x0642, 'UTF-8'), $text, '-18px', '22px', '-8px', "5.Durulabilir de, geçilebilir de.", 'blue');



//*HS with filled centre_6ec isaretini qaf_642, sad_635 ve reh_631 cevir ve kirmizi yap
$rep=mb_chr(0x0642, 'UTF-8').mb_chr(0x0635).mb_chr(0x0631);
//$ffx='20px';

$text = change_reds(mb_chr(0x06ec, 'UTF-8'), $rep, $text, $ft.'px', ($fs-4).'px', '-12px', "6.Uzatmadan kısa oku demektir.", 'red');
//*empty_centre_high_stop_6eb isaretini meem_645 ve dal_62f cevir ve kirmizi yap (8.sayfada var)
$rep=mb_chr(0x0645, 'UTF-8').mb_chr(0x062f);
$text = change_reds(mb_chr(0x06eb, 'UTF-8'), $rep, $text, $ft.'px', $fs.'px', '-12px', "7.Kısaltmadan uzun oku demektir.", 'green');

//full stop 6d4 harfini sekte_633_643_62a_647 cevir ve kirmizi yap (yasin 442,sayfada var)
$rep=mb_chr(0x0633, 'UTF-8').mb_chr(0x0643).mb_chr(0x062A).mb_chr(0x0647);
$text = change_reds(mb_chr(0x06D4, 'UTF-8'), $rep, $text, $ft.'px', $fs.'px', '-6px', "8.Nefes almadan dur ve oku demektir. Kuran’da 4 yerde vardır.", 'blue');

//SH_upright_6e0 harfini qaf_642, feh_641 cevir ve kirmizi yap (300,598. sayfada var)
$rep=mb_chr(0x0642, 'UTF-8').mb_chr(0x0641);
$text = change_reds(mb_chr(0x06e0, 'UTF-8'), $rep, $text, $ft.'px', $fs.'px', '-6px', "9.Durulması evlâdır, geçilmesi caizdir.", 'red');

//* SH_noon_6e8 harfini noon_646 harfine cevir ve asagiya alarak kirmizi yap (577. sayfada var)
$rep=mb_chr(0x0646, 'UTF-8').mb_chr(0x0650);
$text = change_reds(mb_chr(0x06e8, 'UTF-8'), $rep, $text, $ft.'px', $fs.'px', '-12px', "10.Sonu tenvinli kelimelerden bir sonraki kelimeye geçişi sağlar.", 'red');//40px

//***************************** kucuk harflerle isaratleme

//SH lamalef_6d9 harfi kirmizi yap
$text = change_red(mb_chr(0x06D9, 'UTF-8'), mb_chr(0x06D9, 'UTF-8'), $text, '-38px', '-10px', "11.Durulmaz. Durulursa geriden alınarak geçilir.", 'red');

//* SL_seen_6e3 harfini kirmizi yap (245)
$text = change_red(mb_chr(0x06e3, 'UTF-8'), mb_chr(0x06e3, 'UTF-8'), $text, '4px', '0px', "12.Sad harfi seen gibi ince okunur.", 'red');

$text = preg_replace('/\x{06DA}\x{06DB}/u', mb_chr(0x06EA, 'UTF-8'), $text);    //cim noktaliyi once bir isaret yaptik (6ea)
//simdi kalan cimleri kirmizi yaptik
$text = change_red(mb_chr(0x06DA, 'UTF-8'), mb_chr(0x06DA, 'UTF-8'), $text, '-42px', '-5px', "13.Durulması evlâdır, geçilmesi caizdir.", 'red');   //*sola kaydirdim

//simdi 6ea isaretini noktali cime ve kirmiziya cevirelim
$rep=mb_chr(0x06DA, 'UTF-8').mb_chr(0x06DB);
$text = change_red(mb_chr(0x06EA, 'UTF-8'), $rep, $text, '-34px', '0px', "14.Birbirine yakin iki yerde bulunur. Birinde durulunca ötekinde geçilir.", 'red');

//SH_meem_isolated_6e2 harfi kirmizi (591)
$text = change_red(mb_chr(0x06e2, 'UTF-8'), mb_chr(0x06e2, 'UTF-8'), $text, '-38px', '-5px', "15.Durulması gerekir,  geçilmesi uygun değildir.", 'red');

return $text; 

}

//not:594. ve 592 sayfada lamelif ile sad duraklari ustuste, 577 ayn ve ti duraklari ustuste
?>