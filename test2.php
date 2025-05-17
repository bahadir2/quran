

<script>

const juzPages = {
  1: [0, 20], 2: [21, 40], 3: [41, 60], 4: [61, 80], 5: [81, 100],
  // ...
  30: [581, 604]
};

// Sayfayı güncelle
function updatePages(section) {
  const juzSelect = document.getElementById(section === 'start' ? 'startJuz' : 'endJuz');
  const pageSelect = document.getElementById(section === 'start' ? 'startPage' : 'endPage');
  const otherJuz = document.getElementById(section === 'start' ? 'endJuz' : 'startJuz');
  const otherPage = document.getElementById(section === 'start' ? 'endPage' : 'startPage');

  const juz = parseInt(juzSelect.value);
  const [min, max] = juzPages[juz];

  pageSelect.innerHTML = '';
  for (let i = min; i <= max; i++) {
    let option = document.createElement('option');
    option.value = i;
    option.textContent = i;
    pageSelect.appendChild(option);
  }

  // Diğer combobox'ı filtrele
  if (section === 'start') {
    const selectedPage = parseInt(pageSelect.value || min);
    const newJuzOptions = Object.entries(juzPages).filter(([j, [start]]) => start > selectedPage);
    otherJuz.innerHTML = '';
    for (let [j, [s]] of newJuzOptions) {
      let option = document.createElement('option');
      option.value = j;
      option.textContent = j;
      otherJuz.appendChild(option);
    }
  }
}

// Sayfa seçildiğinde formu otomatik gönder
function autoSubmitForm() {
  document.getElementById("planForm").submit();
}

// Sayfa yüklendiğinde cüz listelerini doldur
window.onload = () => {
  const startJuz = document.getElementById("startJuz");
  const endJuz = document.getElementById("endJuz");

  for (let i = 1; i <= 30; i++) {
    const option1 = new Option(i, i);
    const option2 = new Option(i, i);
    startJuz.add(option1.cloneNode(true));
    endJuz.add(option2.cloneNode(true));
  }

  updatePages('start');
  updatePages('end');
};
</script>
<form id="planForm" method="GET">
  <label>Başlangıç Cüz:</label>
  <select id="startJuz" name="startJuz" onchange="updatePages('start')"></select>

  <label>Başlangıç Sayfa:</label>
  <select id="startPage" name="startPage" onchange="autoSubmitForm()"></select>

  <label>Bitiş Cüz:</label>
  <select id="endJuz" name="endJuz" onchange="updatePages('end')"></select>

  <label>Bitiş Sayfa:</label>
  <select id="endPage" name="endPage" onchange="autoSubmitForm()"></select>

  <button type="submit">Planı Kaydet</button>
</form>
