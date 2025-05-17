<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ".$_COOKIE["gbaslik"].".php");
    exit;
  }


include("conn.php");
//T1=n.&T2=a&B1=Login
if(isset($_POST["B2"])){
	$in1=$_POST["T1"];
	// bu mail hesabı varmı kontrol edelim
	$kullanici = $db->query("SELECT id, maildogru,adsoyadyas FROM uyem where uemail='$in1'")->fetch();
	$c3=$kullanici["adsoyadyas"];
	$c4=$in1;

	switch (strlen($kullanici["maildogru"])) {
		case 0:
			echo 'Kayıtlı bir e-mail hesabı bulunamadı. Tekrar deneyin!';
			break;

		case 1:
			if ($kullanici["maildogru"]==1) 
			{
				// işleme başla
				//-------------------------------------------------------------------------------------------------------
				$harfler="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$dognum='';$dognumtekmi=0;
				for ($i=0; $i < 20 ; $i++) 
				{ 
					
					mb_substr($harfler,mt_rand(0,mb_strlen($harfler)-1),1);
					for ($i=1; $i<=6; $i++)
					{
						$dognum=$dognum.mb_substr($harfler,mt_rand(0,mb_strlen($harfler)-1),1);
					}
					$stmt = $db->prepare("SELECT count(*) FROM uyem WHERE maildogru = ?");
									$stmt->execute([$dognum]);
									$count = $stmt->fetchColumn();
									//mutfak ve sonuç
									if ($count==0) {$dognumtekmi=1;break;}
				}

				if ($dognumtekmi==1) 
				{

					$nn=$kullanici["id"];
					$sql = "UPDATE uyem SET mailreset=? WHERE id=?";
					$db->prepare($sql)->execute([$dognum, $nn]);

					$icerik = "

					Sayın ".$c3." (". $c4 .");

					Değerli kullanıcımız, quran.jappsvenska.se/uyereset.php?link=".$dognum." linkini klikleyerek şifrenizi sıfırlayabilirsiniz. Sonrasında şifrenizi tekrar oluşturabileceğiniz form açılacaktır.

					not: Bu mesajın konusu quran.jappsvenska.se adresinde şifre işlemleridir. Eğer bu konu hakkında bilginiz yoksa veya konunun sizinle ilgisi yoksa mesajı lütfen dikkate almayın. 
					Bu tür mesajları bir daha almak istemiyorsanız lüften bizimle iletişime (quran.jappsvenska.se/yeni.php) geçin.
					Teşekkürler.";

					require_once('vendor/autoload.php');
					$mail = new PHPMailer(true);

					$mail->setFrom('info@jappsvenska.se', 'Kuran Sifre Resetleme');
					$mail->addAddress($c4, 'yeni kullanıcı');
					$mail->Subject = 'resetleme linki';
					$mail->Body = $icerik;
					$mail->isSMTP();
					$mail->Host = 'websmtp.simply.com';
					$mail->SMTPAuth = true;
					$mail->Username = 'info@jappsvenska.se';
					$mail->Password = '321maskeli'; // SMTP password
					$mail->SMTPSecure = 'tls';
					$mail->Port = 587;

					$result = (bool) $mail->send();

					echo ($result ? 'Şifre sıfırlama linki e-posta adresinize gönderildi!' : 'Resetleme linki gönderilemedi. Lütfen teknik personel ile iletişime geçin!');

				} else {echo 'Resetleme kodu oluşturulamadı. Lütfen teknik personel ile iletişime geçin!';} 
				//-------------------------------------------------------------------------------------------------------
			}else echo 'İsteğiniz gerçekleştirilemiyor(hata kodu:26). Lütfen teknik personel ile iletişime geçin!'; //mail doğrulama yapılmamış
			break;

		case 8:
			echo 'Hesabınız henüz doğrulanmamış olduğu için yardımcı olamıyoruz. Önce e-postalarınızı kontrol edin ve doğrulama linkini bulun. Sonra bu doğrulama linkini klikleyin. <br>Sorun devam ediyorsa teknik personel ile iletişime geçin!';
			break;

		default:
			echo 'İsteğiniz gerçekleştirilemiyor(hata kodu:39). Lütfen teknik personel ile iletişime geçin!';
			break;
	}

}

?>
<html>
<head>
<title>Hayatı Önde Keşfedin</title>
<meta http-equiv="Content-Language" content="tr">
<meta charset="UTF-8">
	<link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-qdQEsAI45WFCO5QwXBelBe1rR9Nwiss4rGEqiszC+9olH1ScrLrMQr1KmDR964uZ" crossorigin="anonymous">
	<style>
        .wrapper{ 
        	width: 500px; 
        	padding: 20px; 
        }
        .wrapper h2 {text-align: center}
        .wrapper form .form-group span {color: red;}
	</style>
</head>

<body>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
<table border="1" width="100%" style="border-width: 0px">
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="40%" height="190">&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="13" height="190">&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" height="190">&nbsp;</td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right">&nbsp;
			user(email)
					
		</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left" width="13">
		:</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left"> <input type="text" name="T1" size="20"></td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right"> 
		</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="13"> 
        &nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left"> 
		<input type="submit" value="Şifremi sıfırla" name="B2">  (<a href="login.php">giriş sayfası</a>)</td>
	</tr>
</table>
</form>
<p align="center">Şifre sıfırlama linki email adresinize gönderilir.<br>E-postalarınızı kontrol ediniz. Şifre sıfırlama linkine klikleyerek açılan pencereden yeni şifre oluşturabilirsiniz.</p>

</body>

</html>