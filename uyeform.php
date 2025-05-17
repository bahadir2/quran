<?php
include("conn.php");

$category_id='';
/*
//yon 1(ref formu) eller 2(form)
if (isset($_POST['ref'])) {
	$category_id=$_POST['ref'];
	if ($category_id=='2Z90LATHGO') {//arka giriş
		$yon=2;
	}else{
		//database kontrol edelim. bir ref yoksa break yapalım
		$kullanici = $db->query("SELECT id,erisim FROM uye where astrefnum='$category_id'")->fetch();
		//echo $kullanici["id"];exit;
		//echo $_POST['ref'].'ll';

		if ($kullanici["id"]==0)
		{echo "Bu referans numarasına erisime kapanmıştır. Açılması için referansınızla görüşün!";
			echo '<font color="#FF0000"><br>';$yon=1;
		}else{
			if ($kullanici["erisim"]>0)
			{
				$yon=2;
			}else{
				echo $category_id;
				echo "<br>Bu referans numarasının kontejanı kapanmıştır. Açılması için, öğretmeniniz ile görüşebilirsiniz.";echo '<font color="#FF0000"><br>';$yon=1;
			}
		}
	}

}else{$yon=1;}
*/


if (isset($_POST['B2'])) { // Kayıt butonuna basıldı

    // Eğer T2 ve T3 verileri boş değilse
    if (isset($_POST["T2"]) && isset($_POST["T3"])) {

        // Şifre tekrarı ile şifre aynı mı
        if ($_POST["T3"] == $_POST["T3e"]) {
            
            // Email'de @ işareti var mı
            if (strstr($_POST["T2"], '@', true)) {
                
                $category_id = $_POST['T2'];
                $stmt = $db->prepare("SELECT count(*) FROM uyem WHERE uemail = ?");
                $stmt->execute([$category_id]);
                $count = $stmt->fetchColumn();

                // Email daha önce kayıtlı mı
                if ($count == 0) {
                        include("uye_somg.php");

                } else {
                    echo "Bu mail adresine zaten kayıtlı!"; 
                    echo '<font color="#FF0000"><br>';
                    $yon = 2;
                }
            } else {
                echo "Geçerli bir email adresi girmelisiniz!"; 
                echo '<font color="#FF0000"><br>';
                $yon = 2;
            }
        } else {
            echo "Şifreniz, şifre tekrarı ile aynı değil!"; 
            echo '<font color="#FF0000"><br>';
            $yon = 2;
        }
    } else {
        echo "Lütfen kutucukları doldurunuz!"; 
        echo '<font color="#FF0000"><br>';
        $yon = 2;
    }
}

?>
	<html>

	<head>
	<title>Sign in</title>
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

	 STEP 1
	<form method="POST" action="uyeform.php">
	<table border="1" width="1121" style="border-width: 0px">
			<tr>
			<td style="border-style: none; border-width: medium" height="16" width="523" align="right">
			<p>ad:</td>
			<td style="border-style: none; border-width: medium" height="16" width="7">
			</td>
			<td style="border-style: none; border-width: medium" height="16" width="574" align="left" valign="top">
			<input type="text" name="T1" size="25"></td>
		</tr>
			<tr>
			<td style="border-style: none; border-width: medium" height="16" width="523" align="right">
			<p>soyad:</td>
			<td style="border-style: none; border-width: medium" height="16" width="7">
			</td>
			<td style="border-style: none; border-width: medium" height="16" width="574" align="left" valign="top">
			<input type="text" name="T11" size="25"></td>
		</tr>
	<tr>
			<td style="border-style: none; border-width: medium" height="16" width="523" align="right">
			<p>yaş:</td>
			<td style="border-style: none; border-width: medium" height="16" width="7">
			</td>
			<td style="border-style: none; border-width: medium" height="16" width="574" align="left" valign="top">
			<select size="1" name="D1">
				<option selected>35</option>
		<?php
		for ($i=5; $i <65 ; $i++) { 
			echo "<option>".$i."</option>";
		}
		?>

			</select></td>
		</tr>
				<tr>
			<td style="border-style: none; border-width: medium" height="16" width="523" align="right" valign="bottom">
			<p>cinsiyet:</td>
			<td style="border-style: none; border-width: medium" height="16" width="7">
			</td>
			<td style="border-style: none; border-width: medium" height="16" width="574" align="left" valign="top">
			<select size="1" name="D2">
			<option selected>bay</option>
			<option>bayan</option>

			</select></td>
		</tr>

		<tr>
			<td style="border-style: none; border-width: medium" height="16" width="523" align="right"  valign="bottom">
			<p>email adres:</td>
			<td style="border-style: none; border-width: medium" height="16" width="7">
			</td>
			<td style="border-style: none; border-width: medium" height="16" width="574" align="left" valign="top">
			<input type="text" name="T2" size="35"></td>
		</tr>
		<tr>
			<td style="border-style: none; border-width: medium" height="32" width="523" align="right" valign="bottom">
			<p>şifre (yeni bir şifre yazın):</td>
			<td style="border-style: none; border-width: medium" height="32" width="7">
			&nbsp;</td>
			<td style="border-style: none; border-width: medium" height="32" width="574" align="left" valign="top">
			<input type="password" name="T3" size="35"></td>
		</tr>
		<tr>
			<td style="border-style: none; border-width: medium" height="32" width="523" align="right" valign="bottom">
			<p>şifre (aynı şifreyi tekrar edin):</td>
			<td style="border-style: none; border-width: medium" height="32" width="7">
			&nbsp;</td>
			<td style="border-style: none; border-width: medium" height="32" width="574" align="left" valign="top">
			<input type="password" name="T3e" size="35"></td>
		</tr>	
		<tr>
			<td style="border-style: none; border-width: medium" height="22" width="523" align="right">
			</td>
			<td style="border-style: none; border-width: medium" height="22" width="7">
			&nbsp;</td>
			<td style="border-style: none; border-width: medium" height="22" width="574" align="left">
			</td>
		</tr>
		
		<tr>
			<td style="border-style: none; border-width: medium" width="1115" colspan="3">
				<input type="hidden" name="ref" value="<?php echo $category_id;?>">
				<p align="center"><br><input type="submit" value="KAYIT" name="B2"></p>
			
			</td>
		</tr>
	</table>
	</form>
	<br><br><br><br><br>
	<p align="right">|<a href="index.php">anasayfa</a>|&nbsp;&nbsp;&nbsp; </p>
	</body>

	</html>
