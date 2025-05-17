/* ayetlerin renklenmesi calismasi */

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>New Page 3</title>
</head>
<style>

    .arabic {
            //display: inline; /* veya inline-block */
            font-family: 'Traditional Arabic';
            //direction: rtl;
            font-size: 32px;
            //display: block;
            letter-spacing: 0.1px; /* Harfler arasındaki mesafeyi aç */
            line-height: 1.8;
            color: #006600;
        }

  .arabic:hover {
    background-color: #eef; /* Üzerine gelince mavi-gri */
    cursor: pointer;
  }

  .arabic.active {
    background-color: #cce; /* Tıklanınca kalıcı renk */
  }
</style>

<body>
<span class='arabic' style='position: relative;'>...ayet metni...</span>
<span class='arabic' style='position: relative;'>...ayet metni...</span>
<span class='turkish' style='display:none;'>Elif, Lam, Mim,</span>
                <span class='tas' style='display:none;'><br></span>
                <span class='swedish' style='display:none;'>Alif lam meem.</span>
                <span class='sas' style='display:none;'><br></span>
                <span class='english' style='display:none;'>A. L. M.</span>
                <span class='eas' style='display:none;'><br></span>
                <span class='arabic' style='position: relative; '>الٓمٓ<span style='position: relative;'><span style='padding: 5px; position: absolute;  font-size: 38px; top: -38px; left: 0px; color: red;' title='Durulması evlâdır, geçilmesi caizdir.'>ۚ</span></span><span class='normal'>١</span></span>
                <span class='as' style='display:none;'><hr></span>
                <span class='turkish' style='display:none;'>Bu, kendisinde şüphe olmayan, muttakiler için yol gösterici olan bir Kitap'tır.</span>
                <span class='tas' style='display:none;'><br></span>
                <span class='swedish' style='display:none;'>DENNA Skrift - här råder inget tvivel - är en vägledning för dem som fruktar Gud och ständigt har Honom för ögonen,</span>
                <span class='sas' style='display:none;'><br></span>
                <span class='english' style='display:none;'>This is the Book; in it is guidance sure, without doubt, to those who fear Allah;</span>
                <span class='eas' style='display:none;'><br></span>
                <span class='arabic' style='position: relative; '>ذٰلِكَ الْكِتَابُ لَا رَيْبَ<span style='position: relative;'><span style='padding: 5px; position: absolute;  font-size: 38px; top: -34px; left: 0px; color: red;' title='Birbirine yakin iki yerde bulunur. Birinde durulunca ötekinde geçilir.'>ۚۛ</span></span> فِيهِ<span style='position: relative;'><span style='padding: 5px; position: absolute;  font-size: 38px; top: -34px; left: 0px; color: red;' title='Birbirine yakin iki yerde bulunur. Birinde durulunca ötekinde geçilir.'>ۚۛ</span></span> هُدًى لِلْمُتَّقِينَ<span style='position: relative;'><span style='padding: 5px; position: absolute;  font-size: 38px; top: -38px; left: 0px; color: red;' title='Durulmaz. Durulursa geriden alınarak geçilir.'>ۙ</span></span> <span class='normal'>٢</span></span>
                <span class='as' style='display:none;'><hr></span>
                <span class='turkish' style='display:none;'>Onlar, gaybe inanırlar, namazı dosdoğru kılarlar ve kendilerine rızık olarak verdiklerimizden infak ederler.</span>
                <span class='tas' style='display:none;'><br></span>
                <span class='swedish' style='display:none;'>dem som tror på [existensen av] det som är dolt för människor, dem som förrättar bönen och som ger åt andra av vad Vi har skänkt dem för deras försörjning</span>
                <span class='sas' style='display:none;'><br></span>
                <span class='english' style='display:none;'>Who believe in the Unseen, are steadfast in prayer, and spend out of what We have provided for them;</span>
                <span class='eas' style='display:none;'><br></span>
                <span class='arabic' style='position: relative; '>اَلَّذِينَ يُؤْمِنُونَ بِالْغَيْبِ وَيُقِيمُونَ الصَّلٰوةَ وَمِمَّا رَزَقْنَاهُمْ يُنْفِقُونَ<span style='position: relative;'><span style='padding: 5px; position: absolute;  font-size: 38px; top: -38px; left: 0px; color: red;' title='Durulmaz. Durulursa geriden alınarak geçilir.'>ۙ</span></span> <span class='normal'>٣</span></span>
                <span class='as' style='display:none;'><hr></span>
                <span class='turkish' style='display:none;'>Ve onlar, sana indirilene, senden önce indirilenlere iman ederler ve ahirete de kesin bir bilgiyle inanırlar.</span>
                <span class='tas' style='display:none;'><br></span>
<script>
  // Tüm ayetleri seç
  const verses = document.querySelectorAll(".arabic");

  verses.forEach(verse => {
    verse.addEventListener("click", function () {
      // Önce tüm ayetlerden active sınıfını kaldıralım
      verses.forEach(v => v.classList.remove("active"));
      // Sonra tıklanan ayete active sınıfı ekleyelim
      this.classList.add("active");
    });
  });
</script>
    
		

</body>

</html>
