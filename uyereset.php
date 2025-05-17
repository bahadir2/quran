<?php
include("conn.php");


if (isset($_POST['B1'])) 
{ //..kayıt basıldı

if (isset($_POST["T1"]) && isset($_POST["T2"]))
{ //..eğer veriler boş ise 
	if ($_POST["T1"] == $_POST["T2"])
	{ //..şifre tekrarı ile şifre aynı mı

				
					$c2=$_POST["link"];
					$kullanici = $db->query("SELECT id FROM uyem where mailreset='$c2'")->fetch();
					if ($kullanici["id"]>0)
					{ //..update işlemi yapacağız ama yeni şifre oluşturarak
						//--------------------------------kripto
						$c5=password_hash($_POST["T1"], PASSWORD_DEFAULT); // Created a password
						//----------------------------------kripto son
						$nn=$kullanici["id"];
						$sql = "UPDATE uyem SET mailreset=?, upassword=? WHERE id=?";
						$db->prepare($sql)->execute(["1", $c5, $nn]);
						?>
						<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Language" content="tr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Yeni sifre</title>
</head>

<body>
						<?php
						echo 'Şifreniz değiştirildi! Şimdi yeni şifrenizle <a href="login.php">buradan</a> giriş yapabilirsiniz.';
						
						echo '</body></html>';exit;
					}else{
						echo "Beklenmedik bir hata oluştu. (hata kodu: 21) Lütfen teknik personel ile iletişime geçiniz!";

					}
				
	}else{echo "Şifreniz, şifre tekrarı ile aynı değil!";echo '<font color="#FF0000"><br>';}
}else{echo "Lütfen kutucukları doldurunuz!";echo '<font color="#FF0000"><br>';}

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
<?php	
//link satırı kaçtane
$c2=$_GET["link"];
//echo $c2."son aşama başlıyoruz.";exit;
$stmt = $db->prepare("SELECT count(*) FROM uyem WHERE mailreset = ?");
$stmt->execute([$c2]);
$count = $stmt->fetchColumn();

switch ($count) {
	case 0:
		echo 'Güncel olmayan link girdiniz. <br>Lütfen e-postanızı kontrol ederek enson gönderilen linki bulun ve onu klikleyin..';
		echo '<br><br>Şifrenizi hatırladıysanız; hesabınıza <a href="login.php">buradan</a> giriş yapabilirsiniz.';
		break;
	case 1:
		// resetleme yapabiliriz ...
	?>
		<form method="POST" action="/uyereset.php">
<table border="1" width="100%" style="border-width: 0px">
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="40%" height="156">&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="13" height="156">&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" height="156">&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right" height="54">&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left" width="13" height="54">
		&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="top" align="left" height="54"> 
		Şifrenizi tekrar oluşturabilirsiniz.</td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right">
		new&nbsp;password&nbsp;
					
		</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left" width="13">
		:</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left"> 
		<input type="password" name="T1" size="20"></td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right">
		new&nbsp;password (again) </td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left" width="13">
		:</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left">
		<input type="password" name="T2" size="20">
		<input type="hidden" name="link" value="<?php echo $_GET['link'];?>"></td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right"> 
        &nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="13"> 
        &nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left"> 
		<input type="submit" value="Kayıt" name="B1"> (şifrenizi 
		hatırladıysanız veya vazgeçtiyseniz <a href="login.php">burayı</a> klikleyin)</td>
	</tr>
</table>
</form>
<?php 
		break;
	default:
		echo "İstenmeyen bir sorun oluştu. (hata: birden fazla resetleme kodu!)";
		echo "<br>Hesabınızla ilgili resetlemeyi manuel olarak gerçekleştirmek için teknik personelden destek isteyebilirsiniz..";
		echo '<br><br>Şifrenizi hatırladıysanız, hesabınıza, <a href="login.php">buradan</a> giriş yapabilirsiniz.';
		break;
}

?>
</body>

</html>