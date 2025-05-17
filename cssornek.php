<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toggle Visibility</title>
    <style>
        .mustafa {
            display: block;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="mustafa">Bu bir mesajdır.</div>
    <button onclick="toggleVisibility()">Gizle/Göster</button>
    <script>
        function toggleVisibility() {
            const element = document.querySelector('.mustafa');
            console.log('Current classes:', element.classList.value); // Log the current classes
            element.classList.toggle('hidden');
        }
    </script>
</body>
</html>