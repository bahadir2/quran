<?php
use PHPMailer\PHPMailer\PHPMailer;
//----------------------------------------------------------------doğrulama işlemleri_1(kod uretme);
$harfler="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$dognum='';$dognumtekmi=0;
for ($i=0; $i < 20 ; $i++) { 
	
	mb_substr($harfler,mt_rand(0,mb_strlen($harfler)-1),1);
	for ($i=1; $i<=8; $i++)
	{
		$dognum=$dognum.mb_substr($harfler,mt_rand(0,mb_strlen($harfler)-1),1);
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
//$c2=$_POST["Tref"];
$c3=substr($_POST["T1"], 0,3).substr($_POST["T11"], 0,3).$_POST["D1"];
//eklemeler var
$c31=$_POST["D2"].'_1';
$c4=$_POST["T2"];
//$c7=$_POST["D3"];
//$c8=$_POST["D3"].'.';
if ($dognumtekmi==1) $c10=$dognum; else $c10=0;

//--------------------------------kripto
$c5=password_hash($_POST["T3"], PASSWORD_DEFAULT); // Created a password
//----------------------------------kripto son
if(isset($_POST["C1"])){$c6=1;}else{$c6=0;}

$sql = "INSERT INTO uyem (maildogru, ldate, adsoyadyas, cinsiyet, uemail, upassword, rehizni) VALUES ('$c10','$c1','$c3','$c31','$c4','$c5','$c6')";

if ($db->exec($sql)) {
	$yenitim_id = $db->lastInsertId();
	//include ("ana/u_tah8.php");

//----------------------------------------------------------------doğrulama işlemleri_2;
	//bu kodun kullanılmıyor olduğundan emin olmak gerekir eğer kullanılıyorsa değiştirelim
if ($dognumtekmi==1) {
	$icerik = "

Sayın ".$c3." (". $c4 .");

Değerli kullanıcımız, quran.jappsvenska.se/uyedogrulama.php?link=".$dognum." linkini klikleyerek doğrulamanızı tamamlayabilirsiniz.

not: Bu mesajın konusu quran.jappsvenska.se adresine üyelik başvurusudur. Başvuranın bize bildirdiği elektronik posta adresine, adresin kendisine ait olup olmadığını doğrulamak için gönderilmiş rutin bir işlemdir. Eğer bu konu hakkında bilginiz yoksa veya konunun sizinle ilgisi yoksa mesajı lütfen dikkate almayın. 
Teşekkürler.";

	require_once('vendor/autoload.php');
$mail = new PHPMailer(true);

$mail->setFrom("info@jappsvenska.se", "Kuran Uyelik");
$mail->addAddress($c4, 'yeni kullanıcı');
$mail->Subject = 'dogrulama linki';
$mail->Body = $icerik;
$mail->isSMTP();
$mail->Host = 'websmtp.simply.com';
$mail->SMTPAuth = true;
$mail->Username = 'info@jappsvenska.se';
$mail->Password = '21Maskeli'; // SMTP password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$result = (bool) $mail->send();?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Language" content="tr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>STEP 3</title>
</head>

<body>
	<?php
echo ($result ? '' : 'Doğrulama linki gönderilemedi. Lütfen teknik personel ile iletişime geçin!');
//---------------------------------------SMTP
} else {echo 'Doğrulama kodu oluşturulamadı. Lütfen teknik personel ile iletişime geçin!';}  


//----------------------------------------------------------------doğrulama işlemleri_2;
echo 'STEP 3<br>';
echo 'Kayıt işlemi başarılı ve email adresinize doğrulama linki
gönderildi. Linki klikleyerek doğrulamanızı tamamlayabilirsiniz. 
Böylece <a href="login.php">buradan</a> giriş yapabilirsiniz.';
exit;
//header('location: ./index.php');
}else{echo "Kayıt gerçekleştirilemedi. Tekrar deneyiniz.";echo '<font color="#FF0000"><br>';$yon=2;}
?>
</body>

</html>