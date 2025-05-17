<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quran Pages</title>
    <style>
        .arabic {
            font-family: 'Traditional Arabic';
            font-size: 32px;
            letter-spacing: 0.1px;
            line-height: 1.8;
            color: #006600;
        }

        .arabic:hover {
            background-color: #eef; /* Üzerine gelince mavi-gri */
            cursor: pointer;
        }

        .arabic.active {
            background-color: #cce; /* Tıklanınca kalıcı renk */
            color: #003300; /* Tıklanınca yazı rengi değişebilir */
            border: 1px solid #006600; /* İsteğe bağlı kenarlık */
        }
    </style>
</head>
<body>
    <?php  

                echo "<span class='arabic' style='position: relative;'>daha</span>";

    ?>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const verses = document.querySelectorAll(".arabic");

        verses.forEach(verse => {
          verse.addEventListener("click", function () {
            verses.forEach(v => v.classList.remove("active"));
            this.classList.add("active");
          });
        });
      });
    </script>
</body>
</html>