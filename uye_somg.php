<?php
use PHPMailer\PHPMailer\PHPMailer;
//----------------------------------------------------------------doğrulama işlemleri_1(kod uretme);
$harfler="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$dognumtekmi=0;

for ($attempt=0; $attempt < 20; $attempt++) {
    $dognum = '';
    for ($i=0; $i < 8; $i++) {
        $dognum .= mb_substr($harfler, random_int(0, mb_strlen($harfler)-1), 1);
    }
	$stmt = $db->prepare("SELECT count(*) FROM uyem WHERE maildogru = ?");
					$stmt->execute([$dognum]);
					$count = $stmt->fetchColumn();
					//mutfak ve sonuç
					if ($count==0) {$dognumtekmi=1;break;}
}
//----------------------------------------------------------------doğrulama işlemleri_1;

//uyeform.php?Tref=xr&T1=a&T2=b&T3=c&T3e=c&B2=KAYIT
//-------------------------------------------insert alanı

$c1=date('Ymd');//date('Y-m-d');
$c2='';//$_POST["Tref"];
$c3 = htmlspecialchars(substr($_POST["T1"], 0,3), ENT_QUOTES, 'UTF-8') 
    . htmlspecialchars(substr($_POST["T11"], 0,3), ENT_QUOTES, 'UTF-8') 
    . intval($_POST["D1"]);
//eklemeler var
$c31=$_POST["D2"].'_1';
$c4 = filter_var($_POST["T2"], FILTER_VALIDATE_EMAIL);
if (!$c4) die("Geçersiz email!");



$c7='';//$_POST["D3"];
$c8='';//$_POST["D3"].'.';
if ($dognumtekmi==1) $c10=$dognum; else $c10=0;

//--------------------------------kripto
$c5=password_hash($_POST["T3"], PASSWORD_DEFAULT); // Created a password
//----------------------------------kripto son
if(isset($_POST["C1"])){$c6=1;}else{$c6=0;}

$sql = "INSERT INTO uyem (maildogru,ldate, ustrefnum, adsoyadyas, cinsiyet, uemail, upassword, rehizni,g_dildes) VALUES (?,?,?,?,?,?,?,?,?)";

if ($db->prepare($sql)->execute([$c10,$c1,$c2,$c3,$c31,$c4,$c5,$c6,$c8])) {
	$yenitim_id = $db->lastInsertId();


	

//----------------------------------------------------------------doğrulama işlemleri_2;


	//bu kodun kullanılmıyor olduğundan emin olmak gerekir eğer kullanılıyorsa değiştirelim
if ($dognumtekmi==1) {
	$icerik = "

Sayın ".$c3." (". $c4 .");

Değerli kullanıcımız, https://quran.learnmate.se/uyedogrulama.php?link=" . urlencode($dognum)." linkini klikleyerek doğrulamanızı tamamlayabilirsiniz.

not: Bu mesajın konusu www.learnmate.se adresine üyelik başvurusudur. Başvuranın bize bildirdiği elektronik posta adresine, adresin kendisine ait olup olmadığını doğrulamak için gönderilmiş rutin bir işlemdir. Eğer bu konu hakkında bilginiz yoksa veya konunun sizinle ilgisi yoksa mesajı lütfen dikkate almayın. Teşekkürler.";
// .env dosyasını oku
$env = parse_ini_file('/var/www/learnmate.se/.env');

if ($env === false) {
    error_log('Yapılandırma dosyası okunamadı');
    die('Sistem hatası. Lütfen tekrar deneyiniz.');
}

require_once('vendor/autoload.php');
$mail = new PHPMailer(true);

//$mail->setFrom($env['FROM_EMAIL'], $env['FROM_NAME']);
$mail->setFrom($env['FROM_EMAIL'], 'quran.learnmate.se');
$mail->addAddress($c4, 'yeni kullanıcı');
$mail->Subject = 'dogrulama linki';
$mail->Body = $icerik;
$mail->isSMTP();
$mail->Host      = $env['SMTP_HOST'];
$mail->SMTPAuth  = true;
$mail->Username  = $env['SMTP_USERNAME'];
$mail->Password  = $env['SMTP_PASSWORD'];
$mail->SMTPSecure = $env['SMTP_SECURE'];
$mail->Port      = (int) $env['SMTP_PORT'];
$result = (bool) $mail->send();

echo ($result ? '' : 'Doğrulama linki gönderilemedi. Lütfen teknik personel ile iletişime geçin!');
//---------------------------------------SMTP
} else {echo 'Doğrulama kodu oluşturulamadı. Lütfen teknik personel ile iletişime geçin!';}  


//----------------------------------------------------------------doğrulama işlemleri_2;
echo '<html>

	<head>
	<title>Sign in</title>
	<meta http-equiv="Content-Language" content="tr">
<meta charset="UTF-8">
	</head>
	<body>';
echo 'STEP 3<br>';
echo 'Kayıt işlemi başarılı ve email adresinize doğrulama linki
gönderildi. Linki klikleyerek doğrulamanızı tamamlayabilirsiniz. 

Böylece <a href="login.php">buradan</a> giriş yapabilirsiniz.';
/*
//---------------erisimi bir azalt
$nn=$kullanici["erisim"]-1;
$sql5 = "UPDATE uyem SET erisim=? WHERE astrefnum=?";
$db->prepare($sql5)->execute([$nn, $c2]);
//---------------------------------erişimi bir azalt son
*/
exit;
//header('location: ./index.php');
}else{echo "Kayıt gerçekleştirilemedi. Tekrar deneyiniz.";echo '<font color="#FF0000"><br>';$yon=2;}
?>