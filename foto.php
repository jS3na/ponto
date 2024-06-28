<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Foto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
    date_default_timezone_set('America/Sao_Paulo');

    $funcionario_cpf = '';

    include("config.php");

    $hoje_TOTAL = date('Y-m-d');

    if (isset($_GET['id'])) {
        $funcionario_cpf = $_GET['id'];
        $funcionario_id = $_GET['id2'];
        $atual = $_GET['atual'];
    }

    if (isset($_POST['photo'])) {
        $data = $_POST['photo'];

        // Remove o prefixo "data:image/png;base64,"
        $data = str_replace('data:image/png;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $data = base64_decode($data);

        // Define o caminho e o nome do arquivo com a latitude e longitude
        $filePath = 'uploads/photo_' . $funcionario_cpf . '_' . date('Y-m-d') . '_' . $atual . '.png';

        // Salva a imagem no servidor
        file_put_contents($filePath, $data);

        header("Location: inicio.php?id=" . $funcionario_cpf);
        exit();

    } else {
        echo "Nenhuma foto enviada.";
    }


    ?>

    <div class="ie-fixMinHeight">
        <div class="main">
            <div class="wrap animated fadeIn" id="principal">
                <img id="logogts" src="img/logo_gts.png"/>
                <div id="overlay" class="overlay">
                    <div class="boxtxt">
                    <div id="mensagem">Você deve permitir o acesso à câmera e geolocalização para prosseguir.</div>
                    <br>
                    <button onclick="permitirAcesso()">OK</button>
                    <br>
                    </div>
                </div>
                <video id="video" width="325" height="430" muted autoplay playsinline></video>
                <input type="submit" id="capture" value="Capturar Foto" onclick="return getLocation2();">
                <p id="perm_cam"></p>
                <canvas id="canvas" id="photo" width="325" height="490" style="display:none;"></canvas>
                <form id="photoForm" method="post" enctype="multipart/form-data" action="foto.php?id=<?php echo htmlspecialchars($_GET['id']); ?>&id2=<?php echo htmlspecialchars($_GET['id2']); ?>&atual=<?php echo htmlspecialchars($_GET['atual']); ?>">
                    <input type="hidden" name="photo" id="photo">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <p id="location"></p>
                    <img id="photoPreview" src="" alt="Sua foto" style="display:none; width:325px; height:480px;"/>
                    <input type="submit" style="display:none;" id="sendPhotoButton" value="Enviar Foto" onclick="return getLocation();">
                </form>
                <p class="info bt">GTS Net</p>
            </div>
        </div>
    </div>

    <script>

        var quantLoc = 0;

        exibirOverlay("Você deve permitir o acesso à câmera e geolocalização para prosseguir.");
        var permissao = true;

        function exibirOverlay(mensagem) {
                var overlay = document.getElementById('overlay');
                var mensagemElemento = document.getElementById('mensagem');
                mensagemElemento.textContent = mensagem;
                overlay.style.display = 'block';
            }

        function permitirAcesso() {
            document.getElementById('overlay').style.display = 'none';
            permCam();
        }

        // Acessa a câmera
        function permCam() {
        navigator.mediaDevices.getUserMedia({ audio: false, video: true })
        .then(function(stream) {
            document.getElementById('video').srcObject = stream;
        })
        .catch(function(error) {
        document.getElementById("perm_cam").innerHTML = "Você deve aceitar o acesso a câmera para prosseguir com o ponto.";
        permissao = false;
    });}

        // Captura a foto
        document.getElementById('capture').addEventListener('click', function() {
            var canvas = document.getElementById('canvas');
            var context = canvas.getContext('2d');
            context.drawImage(document.getElementById('video'), 0, 0, canvas.width, canvas.height);

            var dataUrl = canvas.toDataURL('image/png');
            document.getElementById('photo').value = dataUrl;

            // Mostra a pré-visualização da foto
            if (permissao){
            var photoPreview = document.getElementById('photoPreview');
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';

            // Mostra o botão de enviar

                document.getElementById('sendPhotoButton').style.display = 'inline';
            }
        });

        function getLocation() {

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            }
        }

        function getLocation2() {

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition();
            }
        }

        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            var funcionario_id = <?php echo json_encode($_GET['id2']); ?>;
            var funcionario_cpf = <?php echo json_encode($_GET['id']); ?>;
            var atual = <?php echo json_encode($_GET['atual']); ?>;

            console.log(atual);

            //document.getElementById("location").innerHTML = "Latitude: " + latitude + "<br>Longitude: " + longitude;

            // Preenche os campos hidden no formulário
            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

            // Enviar a localização para o servidor PHP
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "inserir_ponto.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("latitude=" + latitude + "&longitude=" + longitude + "&funcionario_id=" + funcionario_id + "&atual=" + atual + "&funcionario_cpf=" + funcionario_cpf);
                quantLoc = 0;

        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById("perm_cam").innerHTML = "Você deve aceitar o acesso a localização para prosseguir com o ponto.";
                    document.getElementById('sendPhotoButton').style.display = 'none';
                    document.getElementById('photoPreview').style.display = 'none';
                    permissao = false;
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById("location").innerHTML = "As informações de localização não estão disponíveis.";
                    break;
                case error.TIMEOUT:
                    document.getElementById("location").innerHTML = "A solicitação para obter a localização do usuário expirou.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById("location").innerHTML = "Ocorreu um erro desconhecido.";
                    break;
            }
        }
    </script>
</body>
</html>