<!-- bar calismasi -->
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Navbar + Ayar Paneli</title>
  <style>
    body {
      font-family: 'Lexend', 'Segoe UI', sans-serif;
      background-color: #f8f4e3;
      margin: 20px;
      text-align: center;
      padding: 18px;
    }

    .navbar {
      background-color: #5C4033;
      padding: 12px 16px;
      border-radius: 10px;
      color: #fdfaf5;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 12px;
      //text-align: center;
      //height: 30px;
    }

    label {
      font-weight: bold;
    }

    select, input[type="number"] {
      padding: 6px;
      border-radius: 6px;
      border: none;
      font-size: 14px;
      background-color: #fffaf0;
      color: #333;
    }

    input[type="number"] {
      width: 60px;
    }

    .nav-button {
      padding: 5px 10px;
      border: none;
      background-color: #8e5a28;
      color: #fff;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    .nav-button:hover {
      background-color: #a86734;
    }

    a.button-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background-color: #4b3226;
      color: #FCEFD4;
      padding: 6px 12px;
      font-size: 13px;
      border-radius: 6px;
      text-decoration: none;
      box-shadow: 1px 1px 4px rgba(0,0,0,0.2);
      transition: background-color 0.3s ease;
      cursor: pointer;
    }

    a.button-link:hover {
      background-color: #3a261c;
    }

    .navbar > * {
      margin-bottom: 6px;
    }

    .settings-bar {
      display: none;
      background-color: #eee0c9;
      padding: 20px;
      margin-top: 10px;
      border-radius: 10px;
      box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
    }
    .settings-bar2 {
      display: true;
      background-color: #eee0c9;
      padding: 20px;
      margin-top: 10px;
      border-radius: 10px;
      box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }
    }

    .highlighted-label {
  color: #8e4a1d;        /* Yazı rengi */
  background-color: #fdf2e1;
  padding: 6px 10px;
  border-radius: 6px;
  display: inline-block;
  margin-top: 10px;
  font-weight: bold;
}
.language-options {
  display: flex;
  align-items: center;
  gap: 10px;
}

.language-options label {
  margin: 0;
}

.divider {
  border-left: 1px solid #aaa;
  height: 16px;
}

  </style>
</head>
<body>

<div class="navbar">
  <label for="juzSelect">Cüz:</label>
  <select id="juzSelect" onchange="goToPage('juzSelect')">
    <option value="1" selected>1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
  </select>

  <label for="pageInput">Sayfa:</label>
  <button type="button" onclick="changePage('prev')" class="nav-button">&lt;</button>
  <input type="number" id="pageInput" min="0" max="604" value="1" onchange="goToPage('pageInput')" onkeyup="if(event.keyCode===13) goToPage('pageInput')" autocomplete="off">
  <button type="button" onclick="changePage('next')" class="nav-button">&gt;</button>

  <label for="surahSelect">Sure:</label>
  <select id="surahSelect" class="combobox">
    <option value="1">1. Fatiha</option>
    <option value="2" selected>2. Bakara</option>
  </select>

  <label for="verseSelect">Topic:</label>
  <select name="topik">
    <option>Kapali</option>
    <option>Genel kendi hatimim</option>
  </select>

  <a href="logout.php" title="Çıkış" class="button-link">Logout</a>
  <a href="info.html" title="Bilgi" target="_blank" class="button-link">&#8505;</a>
  <a onclick="toggleSettings()" title="Ayarlar" class="button-link">&#9881;</a>
</div>

<!-- Ayar Paneli -->
<div class="settings-bar" id="settingsBar">
  <div class="language-options">
    <label><input type="checkbox" id="turkish" onclick="updateLanguages()"> Türkçe</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="swedish" onclick="updateLanguages()"> İsveççe</label>
    <span class="divider"></span>
    <label><input type="checkbox" id="english" onclick="updateLanguages()"> İngilizce</label>
  
<span class="divider"></span>
  <label class="size-label highlighted-label" for="sizeRange">Arapça Yazı Boyutu:
    <input type="range" id="sizeRange" min="32" max="50" step="4" value="32" oninput="changeFontSize()">
    <span id="sizeValue">32px</span>
  </label>
  </div>
</div>

<div class="settings-bar2" id="settingsBar2">
  <h3>Ayarlar Paneli</h3>
  <p>Buraya istediğin ayar içeriklerini ekleyebilirsin.</p>
</div>

<script>
  function toggleSettings() {
    const bar = document.getElementById("settingsBar");
    bar.style.display = bar.style.display === "block" ? "none" : "block";
  }

  function changePage(direction) {
    // placeholder fonksiyon
    console.log("Change page: ", direction);
  }

  function goToPage(source) {
    // placeholder fonksiyon
    console.log("Go to page from: ", source);
  }
  




let hideTimeout;

document.getElementById("settingsBar").addEventListener("mouseleave", function () {
  // 1 saniye sonra gizle
  hideTimeout = setTimeout(() => {
    this.style.display = "none";
  }, 1000);
});

// Eğer kullanıcı geri gelirse iptal edelim:
document.getElementById("settingsBar").addEventListener("mouseenter", function () {
  clearTimeout(hideTimeout);
});


</script>

</body>
</html>
