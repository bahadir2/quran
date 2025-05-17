<?php
include("conn.php");
//link satırı kaçtane
$c2=$_GET["link"];
$stmt = $db->prepare("SELECT count(*) FROM uyem WHERE maildogru = ?");
$stmt->execute([$c2]);
$count = $stmt->fetchColumn();
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Language" content="tr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>STEP 3</title>
</head>

<body>
<?php
switch ($count) {
	case 0:
		echo "Aktif olmayan link girdiniz. Yapmak istediğiniz işlemle ilgili olarak teknik personel ile iletişime geçebilirsiniz.";
		echo '<br><br>Hesabınıza, <a href="login.php">buradan</a> giriş yapabilirsiniz.';
		break;
	case 1:
		// dogrulama yapabiliriz ...
		$kullanici = $db->query("SELECT id FROM uyem where maildogru='$c2'")->fetch();
		$nn=$kullanici["id"];
		$sql = "UPDATE uyem SET maildogru=? WHERE id=?";
		$db->prepare($sql)->execute(["1", $nn]);

		echo "Doğrulama işlemi başarıyla tamamlandı. Artık bir quran.jappsvenska.se üyesi oldunuz.";
		echo '<br><br>Hesabınıza, <a href="login.php">buradan</a> giriş yapabilirsiniz. <br>Başarılar!';
		break;
	default:
		echo "İstenmeyen bir sorun oluştu. (hata: birden fazla doğrulama kodu!)";
		echo "<br>Hesabınızla ilgili doğrulamayı manuel olarak gerçekleştirmek için teknik personelden destek isteyebilirsiniz..";
		echo '<br><br>Hesabınıza, <a href="login.php">buradan</a> giriş yapabilirsiniz.';
		break;
}

?>
</body>

</html>