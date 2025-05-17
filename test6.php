
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>New Page 1</title>
    <style>
#searchResults {
    margin-top: 20px;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.search-result {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    cursor: pointer;
    background-color: #fff;
    border-radius: 4px;
}

.search-result:hover {
    background-color: #f1f1f1;
}

</style>
</head>
<body>
<label for="searchInput">Ayetlerde Ara:</label>
<input type="text" id="searchInput" placeholder="Kelime veya ifade girin">
<button type="button" onclick="searchVerses()">Ara</button>

<div id="searchResultsContainer" style="position: relative; display: none;">
    <button id="closeButton" onclick="closeSearchResults()" style="position: absolute; top: 5px; right: 5px; background: transparent; color: black; border: none; font-size: 20px; line-height: 20px; text-align: center; cursor: pointer;">&times;</button>
    <div id="searchResults" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
</div>



<script>
function searchVerses() {
    const searchTerm = document.getElementById("searchInput").value.trim();

    if (!searchTerm) {
        document.getElementById("searchResults").innerHTML = "Lütfen bir kelime veya ifade girin.";
        return;
    }

    // Sonuçlar alanını tekrar görünür yap
    const searchResultsContainer = document.getElementById("searchResultsContainer");
    searchResultsContainer.style.display = "block";

    // AJAX isteği gönder
    fetch(`search.php?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById("searchResults");
            if (data.length > 0) {
                // Arama terimini başa ekle
                const resultsHTML = `
                    <div class="search-term">
                        <strong>Aranan Kelime:</strong> ${searchTerm}
                    </div>
                ` + data.map(verse => `
                    <div class="search-result" onclick="submitSurahVerse(${verse.sur}, ${verse.ayno})">
                        ${verse.sur}-${verse.ayno}
                    </div><hr>
                `).join("");

                resultsDiv.innerHTML = resultsHTML;

                // Sonuçları sessionStorage'da sakla
                sessionStorage.setItem("searchResults", resultsHTML);
                sessionStorage.setItem("searchTerm", searchTerm); // Arama terimini de sakla
            } else {
                resultsDiv.innerHTML = "Sonuç bulunamadı.";
                sessionStorage.removeItem("searchResults"); // Önceki sonuçları temizle
                sessionStorage.removeItem("searchTerm");
            }
        })
        .catch(error => {
            console.error("Hata:", error);
            document.getElementById("searchResults").innerHTML = "Bir hata oluştu.";
        });
}

// Sayfa yüklendiğinde sonuçları geri yükle
document.addEventListener("DOMContentLoaded", function () {
    const resultsHTML = sessionStorage.getItem("searchResults");
    const searchTerm = sessionStorage.getItem("searchTerm");
    if (resultsHTML) {
        const resultsDiv = document.getElementById("searchResults");
        resultsDiv.innerHTML = `
            <div class="search-term">
                <strong>Aranan Kelime:</strong> ${searchTerm}
            </div>
        ` + resultsHTML;

        // Sonuçlar alanını görünür yap
        const searchResultsContainer = document.getElementById("searchResultsContainer");
        searchResultsContainer.style.display = "block";
    }
});
</script>

</body>

</html>