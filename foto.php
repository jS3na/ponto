<!DOCTYPE html>
<html lang="en">
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

        // Define o caminho e o nome do arquivo
        $filePath = 'uploads/photo_' . $funcionario_cpf . date('Y-m-d') . $atual . '.png';

        // Salva a imagem no servidor
        file_put_contents($filePath, $data);

        if($atual == 'entrando'){

            $sql_verifica = "SELECT f.id, p.hora_entrada
                        FROM funcionarios f 
                        LEFT JOIN pontos p ON f.id = p.funcionario_id 
                        WHERE f.cpf = ? AND data = ?";

            $stmt = $conn->prepare($sql_verifica);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                exit();
            }
            $stmt->bind_param("ss", $funcionario_cpf, $hoje_TOTAL);
            $stmt->execute();
            $result3 = $stmt->get_result();
            $row3 = $result3->fetch_assoc();

            if(is_null($row3)){

                $trabalhando = "trabalhando";

                $hoje_entrar = date('Y-m-d');
                $horario = date('H:i');

                // Inserir novo usuário
                $sql = "INSERT INTO pontos (funcionario_id, data, hora_entrada) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $funcionario_id, $hoje_entrar, $horario);

                if (!$stmt->execute()) {

                    echo "Erro: " . $sql . "<br>" . $conn->error;

                }

            }
        }

        elseif($atual == 'saindo'){

                $sql_verifica = "SELECT * FROM pontos WHERE funcionario_id = ? AND data = ? AND hora_saida IS NULL";
                $stmt = $conn->prepare($sql_verifica);
                $stmt->bind_param("is", $funcionario_id, $hoje_TOTAL);
                $stmt->execute();
                $result = $stmt->get_result();

                //echo $result->num_rows;

                if ($result->num_rows != 0) {

                $horario = date('H:i');
                $hoje = date('Y-m-d');

                $sql = "UPDATE pontos SET hora_saida = ? WHERE funcionario_id = ? AND data = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sis", $horario, $funcionario_id, $hoje);
                
                if (!$stmt->execute()) {

                    echo "Erro: " . $sql . "<br>" . $conn->error;

                }

                $stmt->close();
                $conn->close();

            }

            else{
                $trabalhando = "fim";
                $stmt->close();
                $conn->close();
            }

        }

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
                <video id="video" width="325" height="430" autoplay></video>
                <input type="submit" id="capture" value="Capturar Foto"></input>
                <canvas id="canvas" width="325" height="490" style="display:none;"></canvas>
                <form id="photoForm" method="post" enctype="multipart/form-data" action="foto.php?id=<?php echo htmlspecialchars($_GET['id']); ?>&id2=<?php echo htmlspecialchars($_GET['id2']); ?>&atual=<?php echo htmlspecialchars($_GET['atual']); ?>">
                    <input type="hidden" name="photo" id="photo">
                    <img id="photoPreview" src="" alt="Sua foto" style="display:none; width:325px; height:480px;"/>
                    <input type="submit" style="display:none;" id="sendPhotoButton" value="Enviar Foto"></input>
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

            // Mostra a pré-visualização da foto
            var photoPreview = document.getElementById('photoPreview');
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';

            // Mostra o botão de enviar
            document.getElementById('sendPhotoButton').style.display = 'inline';
        });
    </script>
</body>
</html>
