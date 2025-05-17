<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tooltip Example</title>
    <style>
        .normal {
            position: relative;
            display: inline-block;
            cursor: pointer;
            margin: 10px 0;
        }

        .normal div {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            top: 120%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .normal:hover div {
            visibility: visible;
            opacity: 1;
        }

        .normal div span {
            font-size: 28px; /* veya istediğiniz büyüklük, örn. 20px */
            display: none;
        }

        .normal div span.visible {
            display: block;
        }
    </style>
    <script>
        function updateSubtitles() {
            const subtitleSpans = document.querySelectorAll('.normal div span');
            subtitleSpans.forEach(span => {
                const lang = span.id.split('-')[1];
                const checkbox = document.getElementById(`checkbox-${lang}`);
                if (checkbox.checked) {
                    span.classList.add('visible');
                } else {
                    span.classList.remove('visible');
                }
            });
        }
    </script>
</head>
<body>
    <p></p>
    <div>
        <label><input type="checkbox" id="checkbox-tr" onclick="updateSubtitles()"> TR</label>
        <label><input type="checkbox" id="checkbox-sw" onclick="updateSubtitles()"> SW</label>
        <label><input type="checkbox" id="checkbox-en" onclick="updateSubtitles()"> EN</label>
    </div>

    <div class="normal">Hover over me1
        <div>
            <span id="subtitle-tr">Tooltip tr1</span>
            <span id="subtitle-sw">Tooltip sw1</span>
            <span id="subtitle-en">Tooltip en1</span>
        </div>
    </div>
    <div class="normal">Hover over me2
        <div>
            <span id="subtitle-tr">Tooltip tr2</span>
            <span id="subtitle-sw">Tooltip sw2</span>
            <span id="subtitle-en">Tooltip en2</span>
        </div>
    </div>
    <div class="normal">Hover over me3
        <div>
            <span id="subtitle-tr">Tooltip tr3</span>
            <span id="subtitle-sw">Tooltip sw3</span>
            <span id="subtitle-en">Tooltip en3</span>
        </div>
    </div>
</body>
</html>
