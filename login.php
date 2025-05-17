<?php
session_start();

  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ".$_COOKIE["gbaslik"].".php");
    exit;
  }


include("conn.php");
//T1=n.&T2=a&B1=Login
if(isset($_POST["B1"]))
{
	$in1=$_POST["T1"];
	$in2=$_POST["T2"];

	//data çekelim
	$ilkkullanici = $db->query("SELECT * FROM uyem where uemail='$in1'")->fetch();

	if(isset($ilkkullanici["upassword"]))
	{
		if (password_verify($in2, $ilkkullanici["upassword"])) 
		{
			if ($ilkkullanici["maildogru"]=='1') 
			{					
				if ($ilkkullanici["ustsinif"]>=0) 
				{
                    // Kullanıcı ayarlarını yükle
                    $stmt = $db->prepare("SELECT topicSelect, turkishChecked, swedishChecked, englishChecked, fontSize, searchTerm, searchResults FROM uyem WHERE id = :user_id");
                    $stmt->bindValue(':user_id', $ilkkullanici["id"], PDO::PARAM_INT);
                    $stmt->execute();
                    $user_settings = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user_settings) {
						$_SESSION['loggedin'] = true;
						$_SESSION['user_id'] = $ilkkullanici["id"];
						echo '<form id="redirectForm" method="POST" action="index.php">';
						foreach ($user_settings as $key => $value) {
							echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
						}
						echo '</form>';
						echo '<script>document.getElementById("redirectForm").submit();</script>';
						exit;
					}

                    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
                    exit;
								//----------------------------------------------------------------------
				}else{
					echo 'Hesabınız bloke edilmiş! Referansınızla görüşerek bu sorunu çözebilirsiniz.';
				}
			}else{
				echo 'Doğrulama yapılmamış. Doğrulama yapmak için mailinize gelen doğrulama linkini kliklemelisiniz.';		
			}	
		}else{
			echo 'Girmiş olduğunuz bilgilerde yanlışlık var. Tekrar deneyin.';
		}
	}else{
		echo 'Kayıt bulunamadı';
	}
	
}else{
	//burasına gerek yok session kontrolü yapılacaktır
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
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right" height="54">&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left" width="13" height="54">
		&nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="top" align="left" height="54">  
        <a href="uyeform.php">ilk kayıt</a> 
		(yeni kayıt için klikleyin)</td>
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
		password </td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left" width="13">
		:</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left">
		<input type="password" name="T2" size="20"></td>
	</tr>
	<tr>
		<td style="border-style: none; border-width: medium" valign="middle" width="40%" align="right"> 
         
		</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="center" width="13"> 
        &nbsp;</td>
		<td style="border-style: none; border-width: medium" valign="middle" align="left"> 
		<input type="submit" value="Login" name="B1"> (<a href="logag.php">şifremi 
		unuttum</a>)</td>
	</tr>
</table>
</form>
</body>

</html>