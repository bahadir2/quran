<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arapça Karakter Unicode Analiz Aracı</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 40px;
        }
        
        .input-section {
            margin-bottom: 30px;
        }
        
        .input-section label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            font-size: 1.1em;
        }
        
        .input-section textarea {
            width: 100%;
            min-height: 150px;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 24px;
            font-family: 'Traditional Arabic', 'Arabic Typesetting', serif;
            direction: rtl;
            transition: border-color 0.3s;
        }
        
        .input-section textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .analyze-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1em;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .analyze-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .analyze-btn:active {
            transform: translateY(0);
        }
        
        .results {
            margin-top: 30px;
        }
        
        .result-item {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 20px;
            align-items: center;
            transition: transform 0.2s;
        }
        
        .result-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .char-display {
            font-size: 48px;
            font-family: 'Traditional Arabic', 'Arabic Typesetting', serif;
            text-align: center;
            background: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .char-info {
            display: grid;
            gap: 8px;
        }
        
        .info-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #667eea;
            min-width: 120px;
        }
        
        .info-value {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 5px 12px;
            border-radius: 5px;
            color: #333;
        }
        
        .copy-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.2s;
        }
        
        .copy-btn:hover {
            background: #45a049;
        }
        
        .stats {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats h3 {
            margin-bottom: 10px;
        }
        
        .php-code {
            background: #282c34;
            color: #abb2bf;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            overflow-x: auto;
        }
        
        .php-code pre {
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .php-code .keyword { color: #c678dd; }
        .php-code .string { color: #98c379; }
        .php-code .function { color: #61afef; }
        .php-code .number { color: #d19a66; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Arapça Karakter Unicode Analiz Aracı</h1>
            <p>Arapça metindeki her karakterin Unicode kodlarını öğrenin</p>
        </div>
        
        <div class="content">
            <div class="input-section">
                <label for="arabicText">Arapça Metni Yapıştırın:</label>
                <textarea id="arabicText" placeholder="بِسْمِ ٱللَّهِ ٱلرَّحْمَـٰنِ ٱلرَّحِيمِ">بِسْمِ ٱللَّهِ</textarea>
            </div>
            
            <div class="input-section">
                <label for="codeInput">Veya Unicode Kodu Girin (Hex veya Decimal):</label>
                <input type="text" id="codeInput" placeholder="0x06EA veya 1770" style="width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1.1em;">
            </div>
            
            <button class="analyze-btn" onclick="analyzeText()">Analiz Et</button>
            <button class="analyze-btn" onclick="convertCode()" style="margin-left: 10px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">Kodu Çevir</button>
            
            <div id="results" class="results"></div>
        </div>
    </div>

    <script>
        function analyzeText() {
            const text = document.getElementById('arabicText').value;
            const resultsDiv = document.getElementById('results');
            
            if (!text) {
                resultsDiv.innerHTML = '<p style="color: red; text-align: center;">Lütfen metin girin!</p>';
                return;
            }
            
            // Karakterleri analiz et
            const chars = Array.from(text);
            const uniqueChars = new Map();
            
            chars.forEach(char => {
                if (char.trim() === '') return; // Boşlukları atla
                
                const codePoint = char.codePointAt(0);
                
                if (!uniqueChars.has(codePoint)) {
                    uniqueChars.set(codePoint, {
                        char: char,
                        count: 1,
                        hex: '0x' + codePoint.toString(16).toUpperCase().padStart(4, '0'),
                        decimal: codePoint,
                        phpChr: `mb_chr(0x${codePoint.toString(16).toUpperCase()}, 'UTF-8')`,
                        category: getUnicodeCategory(codePoint)
                    });
                } else {
                    uniqueChars.get(codePoint).count++;
                }
            });
            
            // Sonuçları göster
            let html = `
                <div class="stats">
                    <h3>📊 İstatistikler</h3>
                    <p>Toplam Karakter: ${chars.length} | Benzersiz Karakter: ${uniqueChars.size}</p>
                </div>
            `;
            
            // Her benzersiz karakter için sonuç oluştur
            uniqueChars.forEach((data, codePoint) => {
                html += `
                    <div class="result-item">
                        <div class="char-display">${data.char}</div>
                        <div class="char-info">
                            <div class="info-row">
                                <span class="info-label">Hex Code:</span>
                                <span class="info-value">${data.hex}</span>
                                <button class="copy-btn" onclick="copyToClipboard('${data.hex}')">Kopyala</button>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Decimal:</span>
                                <span class="info-value">${data.decimal}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">PHP Kod:</span>
                                <span class="info-value">${data.phpChr}</span>
                                <button class="copy-btn" onclick="copyToClipboard('${data.phpChr}')">Kopyala</button>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kategori:</span>
                                <span class="info-value">${data.category}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kullanım:</span>
                                <span class="info-value">${data.count} kez</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            // PHP örnek kod
            html += `
                <div class="php-code">
                    <h3 style="color: #61afef; margin-bottom: 15px;">📝 PHP Kullanım Örneği:</h3>
                    <pre><code><span class="keyword">function</span> <span class="function">analyzeArabicText</span>($text) {
    $chars = <span class="function">mb_str_split</span>($text, <span class="number">1</span>, <span class="string">'UTF-8'</span>);
    
    <span class="keyword">foreach</span> ($chars <span class="keyword">as</span> $char) {
        <span class="keyword">if</span> (<span class="function">trim</span>($char) === <span class="string">''</span>) <span class="keyword">continue</span>;
        
        // Unicode kod noktasını al
        $codePoint = <span class="function">mb_ord</span>($char, <span class="string">'UTF-8'</span>);
        
        // Hex formatında göster
        $hex = <span class="string">'0x'</span> . <span class="function">strtoupper</span>(<span class="function">dechex</span>($codePoint));
        
        <span class="keyword">echo</span> <span class="string">"Karakter: $char | Hex: $hex | Decimal: $codePoint\\n"</span>;
    }
}

// Kullanım
<span class="function">analyzeArabicText</span>(<span class="string">"بِسْمِ ٱللَّهِ"</span>);</code></pre>
                </div>
            `;
            
            resultsDiv.innerHTML = html;
        }
        
        function getUnicodeCategory(codePoint) {
            if (codePoint >= 0x0600 && codePoint <= 0x06FF) return 'Arabic';
            if (codePoint >= 0x0750 && codePoint <= 0x077F) return 'Arabic Supplement';
            if (codePoint >= 0xFB50 && codePoint <= 0xFDFF) return 'Arabic Presentation Forms-A';
            if (codePoint >= 0xFE70 && codePoint <= 0xFEFF) return 'Arabic Presentation Forms-B';
            if (codePoint >= 0x0020 && codePoint <= 0x007F) return 'Basic Latin';
            return 'Other';
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Başarılı kopyalama geri bildirimi
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✓ Kopyalandı!';
                btn.style.background = '#4CAF50';
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                }, 2000);
            });
        }
        
        function convertCode() {
            const codeInput = document.getElementById('codeInput').value.trim();
            const resultsDiv = document.getElementById('results');
            
            if (!codeInput) {
                resultsDiv.innerHTML = '<p style="color: red; text-align: center;">Lütfen bir kod girin! (Örn: 0x06EA veya 1770)</p>';
                return;
            }
            
            let codePoint;
            
            // Hex mi Decimal mi kontrol et
            if (codeInput.toLowerCase().startsWith('0x')) {
                // Hex format
                codePoint = parseInt(codeInput, 16);
            } else if (codeInput.toLowerCase().startsWith('u+')) {
                // Unicode format (U+06EA)
                codePoint = parseInt(codeInput.substring(2), 16);
            } else {
                // Decimal format
                codePoint = parseInt(codeInput, 10);
            }
            
            if (isNaN(codePoint) || codePoint < 0 || codePoint > 0x10FFFF) {
                resultsDiv.innerHTML = '<p style="color: red; text-align: center;">Geçersiz kod! Lütfen geçerli bir Unicode değeri girin.</p>';
                return;
            }
            
            // Karakteri oluştur
            const char = String.fromCodePoint(codePoint);
            const hex = '0x' + codePoint.toString(16).toUpperCase().padStart(4, '0');
            const phpChr = `mb_chr(0x${codePoint.toString(16).toUpperCase()}, 'UTF-8')`;
            const category = getUnicodeCategory(codePoint);
            
            // Sonucu göster
            let html = `
                <div class="stats">
                    <h3>🔄 Kod Çevirme Sonucu</h3>
                    <p>Girilen Kod: ${codeInput}</p>
                </div>
                
                <div class="result-item">
                    <div class="char-display" style="font-size: 72px;">${char}</div>
                    <div class="char-info">
                        <div class="info-row">
                            <span class="info-label">Karakter:</span>
                            <span class="info-value" style="font-size: 1.5em; font-family: 'Traditional Arabic', 'Arabic Typesetting', serif;">${char}</span>
                            <button class="copy-btn" onclick="copyToClipboard('${char}')">Kopyala</button>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Hex Code:</span>
                            <span class="info-value">${hex}</span>
                            <button class="copy-btn" onclick="copyToClipboard('${hex}')">Kopyala</button>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Decimal:</span>
                            <span class="info-value">${codePoint}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Unicode:</span>
                            <span class="info-value">U+${codePoint.toString(16).toUpperCase().padStart(4, '0')}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">PHP Kod:</span>
                            <span class="info-value">${phpChr}</span>
                            <button class="copy-btn" onclick="copyToClipboard('${phpChr}')">Kopyala</button>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Kategori:</span>
                            <span class="info-value">${category}</span>
                        </div>
                    </div>
                </div>
                
                <div class="php-code">
                    <h3 style="color: #61afef; margin-bottom: 15px;">📝 PHP Kullanım:</h3>
                    <pre><code><span class="keyword">$char</span> = <span class="function">mb_chr</span>(<span class="number">${hex}</span>, <span class="string">'UTF-8'</span>);
<span class="keyword">echo</span> <span class="keyword">$char</span>; <span style="color: #5c6370;">// Çıktı: ${char}</span>

<span style="color: #5c6370;">// Veya direkt string içinde:</span>
<span class="keyword">$text</span> = <span class="string">"Karakter: ${char}"</span>;</code></pre>
                </div>
            `;
            
            resultsDiv.innerHTML = html;
        }
        
        // Enter tuşu ile çevirme
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('codeInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    convertCode();
                }
            });
        });
        
        // Sayfa yüklendiğinde örnek metni analiz et
        window.onload = function() {
            analyzeText();
        };
    </script>
</body>
</html>