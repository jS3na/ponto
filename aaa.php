<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="ie-fixMinHeight">
        <div class="main">
            <div class="wrap animated fadeIn" id="principal">
                <form name="login" method="post" action="login.php">
                    <img id="logogts" src="img/logo_gts.png"/>

                    <video id="video" width="640" height="480" autoplay></video>
                    <button id="capture">Capturar Foto</button>
                    <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
                    <form id="photoForm" method="post" enctype="multipart/form-data" action="upload.php">
                        <input type="hidden" name="photo" id="photo">
                        <button type="submit">Enviar Foto</button>
                    </form>

                </form>
                <p class="info bt">GTS Net</p>

            </div>
        </div>
    </div>

        <script>
        // Acessa a câmera
        navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            document.getElementById('video').srcObject = stream;
        });

        // Captura a foto
        document.getElementById('capture').addEventListener('click', function() {
            var canvas = document.getElementById('canvas');
            var context = canvas.getContext('2d');
            context.drawImage(document.getElementById('video'), 0, 0, canvas.width, canvas.height);
            
            var dataUrl = canvas.toDataURL('image/png');
            document.getElementById('photo').value = dataUrl;
        });
    </script>

</body>

</html>
