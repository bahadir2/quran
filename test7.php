<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
</head>
<body>
    <canvas id="imageCanvas" style="max-width:100%; height:auto;"></canvas>
    <script>
        const imagePath = 'baslik1.png';
        const canvas = document.getElementById('imageCanvas');
        const ctx = canvas.getContext('2d');
        const text = "بِسْــــــمِ اللّٰهِ الرَّحْمَـنِ الرَّحِيـمِ";

        const image = new Image();
        image.src = imagePath;
        image.onload = () => {
            // Set canvas dimensions to match the image
            canvas.width = image.width;
            canvas.height = image.height;

            // Draw the image on the canvas
            ctx.drawImage(image, 0, 0);

            // Set font properties
            ctx.font = "40px 'Amiri', serif"; // Ensure the Amiri font is available
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";

            // Set shadow for the text
            ctx.fillStyle = "black"; // Shadow color
            ctx.fillText(text, canvas.width / 2 + 2, canvas.height / 2 + 30);

            // Draw the main text
            ctx.fillStyle = "white"; // Text color
            ctx.fillText(text, canvas.width / 2, canvas.height / 2+30);
        };

        image.onerror = () => {
            document.body.innerHTML = "<p>Image not found. Please ensure 'baslik1.png' exists in the script directory.</p>";
        };
    </script>
</body>
</html>
