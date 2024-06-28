<?php

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hoje = date('Y-m-d');
    echo $hoje; // Para depuração, pode ser removido posteriormente

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $funcionario_id = (int)$_POST['funcionario_id'];
    $funcionario_cpf = (int)$_POST['funcionario_cpf'];
    $atual = $_POST['atual'];

    function haversine($lat1, $lon1, $lat2, $lon2) {
        $R = 6371000; // Raio da Terra em metros
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $delta_phi = deg2rad($lat2 - $lat1);
        $delta_lambda = deg2rad($lon2 - $lon1);
    
        $a = sin($delta_phi / 2) * sin($delta_phi / 2) +
             cos($phi1) * cos($phi2) *
             sin($delta_lambda / 2) * sin($delta_lambda / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $R * $c;
        return $distance;
    }

    $gts_lat = -5.042492;
    $gts_lon = -42.7475373;

    $current_lat = (float)$latitude;
    $current_lon = (float)$longitude;

    $radius = 170;

    $distancia = haversine($gts_lat, $gts_lon, $current_lat, $current_lon);
    
    $sql_verifica = "INSERT INTO tbteste (teste) VALUES (?)";
    $stmt = $conn->prepare($sql_verifica);
    $stmt->bind_param("s", $teste);
    $stmt->execute();
    
    $teste = $atual;

    $cord = $latitude . " " . $longitude; // Corrigindo a concatenação de string

        if ($atual == 'entrando') {
            $sql_verifica = "SELECT f.id, p.hora_entrada
                             FROM funcionarios f 
                             LEFT JOIN pontos p ON f.id = p.funcionario_id 
                             WHERE f.cpf = ? AND data = ?";

            $stmt = $conn->prepare($sql_verifica);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                exit();
            }
            $stmt->bind_param("is", $funcionario_cpf, $hoje);
            $stmt->execute();
            $result3 = $stmt->get_result();
            $row3 = $result3->fetch_assoc();

            if (is_null($row3)) {

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
        } elseif ($atual == 'saindo') {
            
            $sql_verifica = "SELECT * FROM pontos WHERE funcionario_id = ? AND data = ? AND hora_saida IS NULL";
            $stmt = $conn->prepare($sql_verifica);
            $stmt->bind_param("is", $funcionario_id, $hoje);
            $stmt->execute();
            $result = $stmt->get_result();

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
            } else {
                $trabalhando = "fim";
            }
        }

    #$sql_verifica = "INSERT INTO  tbteste (teste) VALUES (?)";

    if ($atual == "saindo") {
        $sql_verifica = "UPDATE pontos SET cord_saida = ? WHERE funcionario_id = ? AND data = ?";
    } else{
        $sql_verifica = "UPDATE pontos SET cord_entrada = ? WHERE funcionario_id = ? AND data = ?";
    }

    $stmt = $conn->prepare($sql_verifica);
    if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sis", $cord, $funcionario_id, $hoje);
    $stmt->execute();


    if ($distance <= $radius) {
        $local = 'gts';
        if ($atual == "saindo") {
            $sql_verifica = "UPDATE pontos SET local_saida = ? WHERE funcionario_id = ? AND data = ?";
        } else{         
            $sql_verifica = "UPDATE pontos SET local_entrada = ? WHERE funcionario_id = ? AND data = ?";
        }
        $stmt = $conn->prepare($sql_verifica);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            exit();
        }
    
        $stmt->bind_param("sis", $local, $funcionario_id, $hoje);
        $stmt->execute();
    } else {
        $local = 'fora da gts';
        if ($atual == "saindo") {
            $sql_verifica = "UPDATE pontos SET local_saida = ? WHERE funcionario_id = ? AND data = ?";
        } else{         
            $sql_verifica = "UPDATE pontos SET local_entrada = ? WHERE funcionario_id = ? AND data = ?";
        }
        $stmt = $conn->prepare($sql_verifica);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            exit();
        }
    
        $stmt->bind_param("sis", $local, $funcionario_id, $hoje);
        $stmt->execute();
    }

    // Não é necessário obter resultados se é um UPDATE
    echo "Latitude: $latitude, Longitude: $longitude";
} else {
    echo "Nenhuma localização enviada";
}
?>
