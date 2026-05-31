<?php
/**
 * Kuran metni filtreleme ve işaretleme fonksiyonları
 * Unicode karakterleri ile Arapça tecvid işaretlerinin HTML'e dönüştürülmesi
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function replaceArabicChar($searchChar, $replaceChar, $text, array $options = []) {
    $opts = array_merge([
        'top'          => '0px',
        'size'         => null,
        'left'         => '0px',
        'color'        => 'inherit',
        'note'         => null,
        'enable_color' => true,
        'dnote'        => '',
    ], $options);

    if ($opts['enable_color'] === false) {
        $opts['color'] = 'inherit';
    }

    $parts  = preg_split('/(<[^>]+>)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $result = '';

    foreach ($parts as $part) {
        if (preg_match('/^<[^>]+>$/u', $part)) {
            $result .= $part;
        } else {
            if ($opts['color'] !== 'inherit') {
                $sizeStyle  = $opts['size'] ? "font-size: {$opts['size']};" : '';
                $styledChar = sprintf(
                    "<span style='position: relative;'>" .
                    "<span class='dynamic-font' style='top: %s; %s left: %s; color: %s; direction: ltr;'>%s</span>" .
                    "</span>",
                    $opts['top'],
                    $sizeStyle,
                    $opts['left'],
                    $opts['color'],
                    $replaceChar
                );
                $result .= str_replace($searchChar, $styledChar, $part);
            } else {
                $result .= str_replace($searchChar, $replaceChar, $part);
            }
        }
    }

    return $result;
}

function removeArabicParentheses($ayet) {

    $yakalananIsaretler = '';

    $fontSize = isset($_SESSION['fontSize']) ? intval($_SESSION['fontSize']) : 40;

    $resolve = function($value) use ($fontSize) {
        if (is_array($value))    return $value[$fontSize] ?? reset($value);
        if (is_callable($value)) return $value($fontSize);
        return $value;
    };

    $imale = mb_chr(0x0627, 'UTF-8') . mb_chr(0x0645, 'UTF-8') .
             mb_chr(0x0644, 'UTF-8') . mb_chr(0x0629, 'UTF-8');
    $idgam = mb_chr(0x0627, 'UTF-8') . mb_chr(0x062F, 'UTF-8') .
             mb_chr(0x063A, 'UTF-8') . mb_chr(0x0645, 'UTF-8');
    $ismam = mb_chr(0x0627, 'UTF-8') . mb_chr(0x0634, 'UTF-8') .
             mb_chr(0x0645, 'UTF-8') . mb_chr(0x0627, 'UTF-8') .
             mb_chr(0x0645, 'UTF-8');
    $teshil = mb_chr(0x062A, 'UTF-8') . mb_chr(0x0633, 'UTF-8') .
              mb_chr(0x0647, 'UTF-8') . mb_chr(0x064A, 'UTF-8') .
              mb_chr(0x0644, 'UTF-8');

    // =========================================================
    // GRUP 1: Çift karakter kombinasyonları
    // ÖNEMLI: Tek karakter işlemlerinden ÖNCE yapılmalıdır.
    // =========================================================
    $doubleCharSigns = [
        [
            // 16. İmale — 06ED+06D9
            'search'  => mb_chr(0x06ED, 'UTF-8') . mb_chr(0x06D9, 'UTF-8'),
            'replace' => $imale,
            'color'   => 'red',
            'note'    => '16',
            'top'     => [24 => '20px', 32 => '28px', 40 => '36px', 48 => '44px', 56 => '52px', 64 => '60px'],
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr>:<font style="font-size:20px;">' . $imale . '</font>' .
                        '<br><br> 16.Sözlükte "meylettirmek" demektir. Tecvidde ise fethayı (üstün) kesreye (esre), elifi de "yâ" harfine yaklaştırarak okumaktır. ' .
                        'Türkçedeki "e" sesine benzer bir sesle okunur. Kur\'an-ı Kerim\'de bizim okuduğumuz rivayette (Hafs) sadece bu kelimede vardır.' .
                        '<br>"Mecrâha" (Kalın bir \'a\' sesiyle) degil de "<b>Mecrêhâ</b>" ("râ" harfi inceltilerek okunur)',
        ],
        [
            // 17. İdgam — 06ED+06D7
            'search'  => mb_chr(0x06ED, 'UTF-8') . mb_chr(0x06D7, 'UTF-8'),
            'replace' => $idgam,
            'color'   => 'red',
            'note'    => '17',
            'top'     => [24 => '20px', 32 => '28px', 40 => '36px', 48 => '44px', 56 => '52px', 64 => '60px'],
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr>:<font style="font-size:20px;">' . $idgam . '</font>' .
                        '<br><br> 17.Burada "Be" (ب) harfi cezimli, "Mim" (م) harfi harekeli gelmiştir. "Be" harfi tamamen "Mim" harfine dönüştürülerek ve tutularak (gunneli) okunur.' .
                        '<br>"İrkeb me\'anâ" (B harfi belirgin şekilde durularak) degil de "<b>İrkem-me\'anâ</b>" "B" harfi tamamen "M" harfine dönüştürülür ve iki "M" varmış gibi tutularak (genizden ses verilerek) okunur.',
        ],
        [
            // 18. İşmam — 06ED+06D6
            'search'  => mb_chr(0x06ED, 'UTF-8') . mb_chr(0x06D6, 'UTF-8'),
            'replace' => $ismam,
            'color'   => 'red',
            'note'    => '18',
            'top'     => [24 => '20px', 32 => '28px', 40 => '36px', 48 => '44px', 56 => '52px', 64 => '60px'],
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr>:<font style="font-size:20px;">' . $ismam . '</font>' .
                        '<br><br> 18.Sözlükte "koklatmak" demektir. Kelimenin aslı "te\'menünâ"dır. İki "nun" birleştirilmiştir (idgam). Buradaki birinci nunun aslen ötreli olduğunu belirtmek için, şeddeli nunu okurken dudaklar anlık olarak ötre pozisyonuna (yumulup) getirilir ama ses çıkarılmaz. Yani sadece görüntüde bir ötre işareti yapılır.' .
                        '<br>"Te\'mennâ" (Sadece şeddeli \'n\' sesiyle) degil de "<b>Te\'men-(dudak hareketi)-nâ</b>" Kelime okunurken "n" harfine gelindiğinde, ses çıkarılmadan dudaklar ötre yapar gibi öne uzatılır ve hemen geri çekilir. Bu, kelimenin aslındaki ötreli harfi işaret eder.',
        ],
        [
            // 19. Teshil — 06ED+06D8
            'search'  => mb_chr(0x06ED, 'UTF-8') . mb_chr(0x06D8, 'UTF-8'),
            'replace' => $teshil,
            'color'   => 'red',
            'note'    => '19',
            'top'     => [24 => '20px', 32 => '28px', 40 => '36px', 48 => '44px', 56 => '52px', 64 => '60px'],
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr>:<font style="font-size:20px;">' . $teshil . '</font>' .
                        '<br><br> 19.Sözlükte "kolaylaştırmak" demektir. Yan yana gelen iki hemzeden ikincisini, hemze ile elif arasında yumuşak bir sesle okumaktır. İkinci "e" sesi tam bir hemze gibi keskin değil, daha yumuşak ve akıcı bir şekilde söylenir.' .
                        '<br>"E-a\'cemiyyün" (İki ayrı hemze, yani iki keskin \'e\' sesiyle) degil de "<b>E-ecemiyyün</b>" yani İlk \'e\' sesi net çıkarılırken, ikinci \'e\' sesi tam \'e\' ile \'h\' harfi arasında çok yumuşak bir geçişle, boğazı sıkmadan okunur.',
        ],
        [
            // 22. Üç noktalı lamelif durağı — 06D9+06DB
            'search'  => mb_chr(0x06D9, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'replace' => mb_chr(0x06D9, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'color'   => 'red',
            'note'    => '22',
            'top' => [24 => '-26px', 32 => '-30px', 40 => '-34px', 48 => '-39px', 56 => '-43px', 64 => '-47px'],
'size' => [24 => '30px', 32 => '38px', 40 => '46px', 48 => '55px', 56 => '63px', 64 => '71px'],
'left' => [24 => '-6px', 32 => '-5px', 40 => '-4px', 48 => '-2px', 56 => '-1px', 64 => '0px'],
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x06D9, 'UTF-8') . mb_chr(0x06DB, 'UTF-8') . '</font>' .
                        '<br> 22.Üç noktalı lamelif durağı. Kesinlikle durulmaması gereken yerde üç noktalı durak işaretiyle birlikte gelir.',
        ],
        [
            // 23. Birinde durul ötekinde geç — 06DA+06DB
            'search'  => mb_chr(0x06DA, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'replace' => mb_chr(0x06DA, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'color'   => 'red',
            'note'    => '23',
            'top'     => [24 => '-29px', 32 => '-30px', 40 => '-31px', 48 => '-32px', 56 => '-33px', 64 => '-34px'],
            'size'    => [24 => '25px',  32 => '29px',  40 => '33px',  48 => '37px',  56 => '41px',  64 => '45px'],
            'left'    => [24 => '-2px',  32 => '-1px',  40 => '0px',   48 => '1px',   56 => '2px',   64 => '3px'],
            'dnote'   => '<hr><br><font style="font-size:40px;">' . mb_chr(0x06DA, 'UTF-8') . mb_chr(0x06DB, 'UTF-8') . '</font>' .
                        '<br> 23.Sayet iki işaret de aynı ise sadece istediğin birinde durabilirsin. Sayet farklı işaretler geliyorsa bu duraktan geçmelisin.',
        ],
        [
            // 24. Tâ + üç yıldız — 06DC+06DB
            'search'  => mb_chr(0x06DC, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'replace' => mb_chr(0x0637, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'color'   => 'green',
            'note'    => '24',
            'top'     => '-18px',
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-8px',
            'dnote'   => '<hr>:<font style="font-size:20px;">' . mb_chr(0x0637, 'UTF-8') . mb_chr(0x06DB, 'UTF-8') . '</font>' .
                        '<br> 24.Önceki üç noktalı durak yerine buradaki üç noktalı durakta durmak daha evlâdır. Sayet her iki isarette aynıysa istediğin birinde durabilirsin.',
        ],
        [
            // 25. Kif + üç yıldız — 06E0+06DB
            'search'  => mb_chr(0x06E0, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'replace' => mb_chr(0x0642, 'UTF-8') . mb_chr(0x06DB, 'UTF-8') . mb_chr(0x0641, 'UTF-8'),
            'color'   => 'red',
            'note'    => '25',
            'top'     => [24 => '-14px', 32 => '-15px', 40 => '-16px', 48 => '-16px', 56 => '-17px', 64 => '-18px'],
            'size'    => [24 => '14px',  32 => '17px',  40 => '20px',  48 => '22px',  56 => '25px',  64 => '28px'],
            'left'    => [24 => '-8px',  32 => '-9px',  40 => '-10px', 48 => '-12px', 56 => '-13px', 64 => '-14px'],
            'dnote'   => '<hr>:<font style="font-size:20px;">' . mb_chr(0x0642, 'UTF-8') . mb_chr(0x06DB, 'UTF-8') . mb_chr(0x0641, 'UTF-8') . '</font>' .
                        '<br> 25.Önceki üç noktalı durak yerine buradaki üç noktalı durakta durmak daha evlâdır.',
        ],
        [
            // 26. Üç noktalı gili durağı — 06D7+06DB
            'search'  => mb_chr(0x06D7, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'replace' => mb_chr(0x06D7, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
            'color'   => 'red',
            'note'    => '26',
            'top' => [24 => '-26px', 32 => '-33px', 40 => '-41px', 48 => '-48px', 56 => '-56px', 64 => '-63px'],
'size' => [24 => '26px', 32 => '35px', 40 => '44px', 48 => '53px', 56 => '62px', 64 => '71px'],
'left' => [24 => '-6px', 32 => '-6px', 40 => '-7px', 48 => '-7px', 56 => '-8px', 64 => '-8px'],
            'dnote'   => '<hr><br><font style="font-size:30px;">' . mb_chr(0x06D7, 'UTF-8') . mb_chr(0x06DB, 'UTF-8') . '</font>' .
                        '<br> 26.Üç noktalı gıli durağı. Geçmek daha iyidir.',
        ],
    ];

    // =========================================================
    // GRUP 2: Tek karakter işaretleri
    // =========================================================
    $singleCharSigns = [
        [
            // 3. Tek tâ — 06DC (06DC+06DB ikilisi doubleCharSigns'ta)
            'search'  => 0x06DC,
            'replace' => 0x0637,
            'color'   => 'green',
            'note'    => '3',
            'top'     => '-18px',
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-8px',
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0637, 'UTF-8') . '</font>' .
                        '<br> 3.Durmak gerekir, gecilebilir de. Durmak daha uygundur, güzel olur. Gecilmesi de caizdir.',
        ],
        [
            // 5. Küçük yuvarlak — tek 06DA (06DA+06DB ikilisi doubleCharSigns'ta)
            'search'  => 0x06DA,
            'replace' => 0x06DA,
            'color'   => 'green',
            'note'    => '5',
            'top'     => [24 => '-31px', 32 => '-36px', 40 => '-42px', 48 => '-47px', 56 => '-53px', 64 => '-58px'],
            'size'    => [24 => '27px',  32 => '33px',  40 => '39px',  48 => '45px',  56 => '51px',  64 => '57px'],
            'left'    => [24 => '-5px',  32 => '-5px',  40 => '-5px',  48 => '-6px',  56 => '-6px',  64 => '-6px'],
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x062C, 'UTF-8') . '</font>' .
                        '<br> 5.Durmanın caiz olduğuna işaret eder ki, böyle yerlerde durmak da geçmek de câizdir. Fakat durmak, daha evlâ(iyi)dır.',
        ],
        [
            // 6. Ayn — 06DF
            'search'  => 0x06DF,
            'replace' => 0x0639,
            'color'   => 'green',
            'note'    => '6',
            'top'     => [24 => '-15px', 32 => '-18px', 40 => '-20px', 48 => '-23px', 56 => '-25px', 64 => '-28px'],
            'size'    => [24 => '14px',  32 => '17px',  40 => '21px',  48 => '24px',  56 => '28px',  64 => '31px'],
            'left'    => [24 => '-15px', 32 => '-16px', 40 => '-18px', 48 => '-19px', 56 => '-21px', 64 => '-22px'],
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0639, 'UTF-8') . '</font>' .
                        '<br> 6.Durmanın caiz olduğuna işaret eder ve namazda ise rükû yapılabilir.',
        ],
        [
            // 4. Sad — 06D6
            'search'  => 0x06D6,
            'replace' => 0x0635,
            'color'   => 'green',
            'note'    => '4',
            'top'     => [24 => '-15px', 32 => '-19px', 40 => '-22px', 48 => '-26px', 56 => '-29px', 64 => '-33px'],
            'size'    => [24 => '14px',  32 => '18px',  40 => '22px',  48 => '27px',  56 => '31px',  64 => '35px'],
            'left'    => [24 => '-8px',  32 => '-8px',  40 => '-9px',  48 => '-9px',  56 => '-10px', 64 => '-10px'],
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0635, 'UTF-8') . '</font>' .
                        '<br> 4.Durmanın caiz olduğuna işaret eder ki, böyle yerlerde durmak da geçmek de câizdir. Fakat durmak, daha evlâ(iyi)dır.',
        ],
        [
            // 7. Geçmek iyi — 06D7 tek (06D7+06DB ikilisi doubleCharSigns'ta)
            'search'  => 0x06D7,
            'replace' => 0x06D7,
            'color'   => 'green',
            'note'    => '7',
            'top'     => [24 => '-15px', 32 => '-18px', 40 => '-20px', 48 => '-23px', 56 => '-25px', 64 => '-28px'],
            'size'    => [24 => '14px',  32 => '17px',  40 => '21px',  48 => '24px',  56 => '28px',  64 => '31px'],
            'left'    => [24 => '-8px',  32 => '-8px',  40 => '-9px',  48 => '-9px',  56 => '-10px', 64 => '-10px'],
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x06D7, 'UTF-8') . '</font>' .
                        '<br> 7.Geçmek de durmak da câizdir fakat geçmek daha iyidir.',
        ],
        [
            // 8. Meem — 06D8
            'search'  => 0x06D8,
            'replace' => 0x0632,
            'color'   => 'green',
            'note'    => '8',
            'top'     => '-18px',
            'size'    => '20px',
            'left'    => '-10px',
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0632, 'UTF-8') . '</font>' .
                        '<br> 8.Geçilmesi evlâdır, durulması caizdir.',
        ],
        [
            // 10. Tek kif — 06E0 (06E0+06DB ikilisi doubleCharSigns'ta)
            'search'  => 0x06E0,
            'replace' => mb_chr(0x0642, 'UTF-8') . mb_chr(0x0641, 'UTF-8'),
            'color'   => 'green',
            'note'    => '10',
            'top'     => [24 => '-18px', 32 => '-18px', 40 => '-20px', 48 => '-20px', 56 => '-22px', 64 => '-22px'],
            'size'    => [24 => '15px',  32 => '18px',  40 => '21px',  48 => '24px',  56 => '27px',  64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0642, 'UTF-8') . mb_chr(0x0641, 'UTF-8') . '</font>' .
                        '<br> 10.Durulması evlâdır, geçilmesi caizdir.',
        ],
        [
            // 12. Subscript alef alttan — 06EA
            'search'  => 0x06EA,
            'replace' => 0x0656,
            'color'   => 'green',
            'note'    => '12',
            'top'     => [24 => '0px',  32 => '4px',  40 => '9px',  48 => '13px', 56 => '18px', 64 => '22px'],
            'size'    => [24 => '22px', 32 => '27px', 40 => '32px', 48 => '36px', 56 => '41px', 64 => '46px'],
            'left'    => [24 => '-2px', 32 => '-1px', 40 => '0px',  48 => '2px',  56 => '3px',  64 => '4px'],
            'dnote'   => '<hr>x<font style="font-size:30px;">' . mb_chr(0x0656, 'UTF-8') . '</font>' .
                        '<br> 12.Harfin altına gelen dik çizgi o harfi bir elif miktarı uzatır.',
        ],
        [
            // 13. Kısa oku — 06EC
            'search'  => 0x06EC,
            'replace' => mb_chr(0x0642, 'UTF-8') . mb_chr(0x0635, 'UTF-8') . mb_chr(0x0631, 'UTF-8'),
            'color'   => 'red',
            'note'    => '13',
            'top'     => [24 => '20px', 32 => '28px', 40 => '36px', 48 => '44px', 56 => '52px', 64 => '60px'],
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0642, 'UTF-8') . mb_chr(0x0635, 'UTF-8') . mb_chr(0x0631, 'UTF-8') . '</font>' .
                        '<br> 13.Uzatmadan kısa oku demektir.',
        ],
        [
            // 14. Uzun oku — 06EB
            'search'  => 0x06EB,
            'replace' => mb_chr(0x0645, 'UTF-8') . mb_chr(0x062F, 'UTF-8'),
            'color'   => 'red',
            'note'    => '14',
            'top'     => [24 => '20px', 32 => '28px', 40 => '36px', 48 => '44px', 56 => '52px', 64 => '60px'],
            'size'    => [24 => '15px', 32 => '18px', 40 => '21px', 48 => '24px', 56 => '27px', 64 => '30px'],
            'left'    => '-10px',
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0645, 'UTF-8') . mb_chr(0x062F, 'UTF-8') . '</font>' .
                        '<br> 14.Kısaltmadan uzun oku demektir.',
        ],
        [
            // 21. Tenvin geçiş — 06E8
            'search'  => 0x06E8,
            'replace' => mb_chr(0x0646, 'UTF-8') . mb_chr(0x0650, 'UTF-8'),
            'color'   => 'green',
            'note'    => '21',
            'top'     => [24 => '8px',   32 => '13px',  40 => '17px',  48 => '22px',  56 => '26px',  64 => '31px'],
            'size'    => [24 => '14px',  32 => '19px',  40 => '24px',  48 => '28px',  56 => '33px',  64 => '38px'],
            'left'    => [24 => '-10px', 32 => '-12px', 40 => '-14px', 48 => '-15px', 56 => '-17px', 64 => '-19px'],
            'dnote'   => '<hr>:<font style="font-size:20px;">' . mb_chr(0x0646, 'UTF-8') . '</font>' .
                        '<br> 21.Sonu tenvinli kelimelerden bir sonraki kelimeye geçişi sağlar.',
        ],
        [
            // 9. Sekte — 06D4
            'search'  => 0x06D4,
            'replace' => mb_chr(0x0633, 'UTF-8') . mb_chr(0x0643, 'UTF-8') .
                         mb_chr(0x062A, 'UTF-8') . mb_chr(0x0647, 'UTF-8'),
            'color'   => 'blue',
            'note'    => '9',
            'top'     => [24 => '17px', 32 => '24px', 40 => '31px', 48 => '37px', 56 => '44px', 64 => '51px'],
            'size'    => [24 => '12px', 32 => '14px', 40 => '17px', 48 => '19px', 56 => '22px', 64 => '24px'],
            'left'    => [24 => '-4px', 32 => '-3px', 40 => '-1px', 48 => '0px',  56 => '2px',  64 => '3px'],
            'dnote'   => '<hr><font style="font-size:20px;">' . mb_chr(0x0633, 'UTF-8') . mb_chr(0x0643, 'UTF-8') . mb_chr(0x062A, 'UTF-8') . mb_chr(0x0647, 'UTF-8') . '</font>' .
                        '<br> 9.Nefes almadan dur ve oku demektir. Kuran\'da 4 yerde vardır.',
        ],
    ];

    // =========================================================
    // Tek karakterin ikilisiyle çakışma haritası
    // =========================================================
    $pairGuard = [
        mb_chr(0x06DC, 'UTF-8') => mb_chr(0x06DC, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
        mb_chr(0x06DA, 'UTF-8') => mb_chr(0x06DA, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
        mb_chr(0x06D7, 'UTF-8') => mb_chr(0x06D7, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
        mb_chr(0x06D9, 'UTF-8') => mb_chr(0x06D9, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
        mb_chr(0x06E0, 'UTF-8') => mb_chr(0x06E0, 'UTF-8') . mb_chr(0x06DB, 'UTF-8'),
        mb_chr(0x06ED, 'UTF-8') => mb_chr(0x06ED, 'UTF-8') . mb_chr(0x06D9, 'UTF-8'),
    ];

    // =========================================================
    // GRUP 3: Sadece tespit edilecek, replace yapılmayacak karakterler
    // =========================================================
    $detectOnlyChars = [
        [
            // 1. Lamelif — 06D9 tek
            'search' => 0x06D9,
            'note'   => '1',
            'dnote'  => '<hr><font style="font-size:30px;">' . mb_chr(0x06D9, 'UTF-8') . '</font>' .
                        '<br> 1.Kesinlikle durulmaması gereğini ifade eder. Eğer lamelif harfinin olduğu yerde durulursa mutlaka geriden alınması gerekir. Zira anlam bozulur.',
        ],
        [
            // 2. Lâzım durak — 06E2
            'search' => 0x06E2,
            'note'   => '2',
            'dnote'  => '<hr><font style="font-size:40px;">' . mb_chr(0x06E2, 'UTF-8') . '</font>' .
                        '<br> 2.Durmanın lâzım olduğuna işâret eder ki geçilirse mana bozulur demektir.',
        ],
        [
            // 11. Bir elif üstten — 0670
            'search' => 0x0670,
            'note'   => '11',
            'dnote'  => '<hr>x<font style="font-size:30px;">' . mb_chr(0x0670, 'UTF-8') . '</font>' .
                        '<br> 11.Harfin üzerine gelen dik çizgi o harfi bir elif miktarı uzatır.',
        ],
        [
            // 15. Dört elif — 0653
            'search' => 0x0653,
            'note'   => '15',
            'dnote'  => '<hr><font style="font-size:40px;">' . mb_chr(0x0653, 'UTF-8') . '</font>' .
                        '<br> 15.Harfin üzerine gelen yatay çizgi, üzerinde bulunduğu harfi dört elif miktarı uzatır.',
        ],
        [
            // 20. Sin ile oku — 06E3
            'search' => 0x06E3,
            'note'   => '20',
            'dnote'  => '<hr><font style="font-size:30px;">' . mb_chr(0x06E3, 'UTF-8') . '</font>' .
                        '<br> 20.Mutlaka "Sin" ile okunmalıdır.',
        ],
    ];

    // 1. Çift karakter kombinasyonlarını işle
    foreach ($doubleCharSigns as $sign) {
        $top   = $resolve($sign['top']);
        $size  = $resolve($sign['size']);
        $left  = $resolve($sign['left']);
        $color = $resolve($sign['color'] ?? 'inherit');

        if (mb_strpos($ayet, $sign['search']) !== false) {
            $yakalananIsaretler .= $sign['dnote'] . ',';
        }

        $ayet = replaceArabicChar($sign['search'], $sign['replace'], $ayet, [
            'top'   => $top,
            'size'  => $size,
            'left'  => $left,
            'color' => $color,
            'note'  => $sign['note'],
            'dnote' => $sign['dnote'],
        ]);
    }

    // 2. Tek karakter işaretlerini işle
    foreach ($singleCharSigns as $sign) {
        $searchChar  = is_int($sign['search'])
            ? mb_chr($sign['search'], 'UTF-8')
            : $sign['search'];

        $replaceChar = is_int($sign['replace'])
            ? mb_chr($sign['replace'], 'UTF-8')
            : $sign['replace'];

        if (isset($pairGuard[$searchChar]) && mb_strpos($ayet, $pairGuard[$searchChar]) !== false) {
            continue;
        }

        $top   = $resolve($sign['top']);
        $size  = $resolve($sign['size'] ?? null);
        $left  = $resolve($sign['left']);
        $color = $resolve($sign['color'] ?? 'inherit');

        if (mb_strpos($ayet, $searchChar) !== false) {
            $yakalananIsaretler .= $sign['dnote'] . ',';
        }

        $ayet = replaceArabicChar($searchChar, $replaceChar, $ayet, [
            'top'   => $top,
            'size'  => $size,
            'left'  => $left,
            'color' => $color,
            'note'  => $sign['note'],
            'dnote' => $sign['dnote'],
        ]);
    }

    // 3. Sadece tespit et, replace yapma
    foreach ($detectOnlyChars as $sign) {
        $searchChar = is_int($sign['search'])
            ? mb_chr($sign['search'], 'UTF-8')
            : $sign['search'];

        if (mb_strpos($ayet, $searchChar) !== false) {
            $yakalananIsaretler .= $sign['dnote'] . ',';
        }
    }

    return [
        'ayet'      => $ayet,
        'isaretler' => $yakalananIsaretler,
    ];
}
?>