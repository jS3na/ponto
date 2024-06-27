<?php

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hoje = date('Y-m-d');
    echo $hoje; // Para depuração, pode ser removido posteriormente

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $funcionario_id = $_POST['funcionario_id'];
    $atual = $_POST['atual'];

    $cord = $latitude . " " . $longitude; // Corrigindo a concatenação de strings

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

    $stmt->bind_param("sss", $cord, $funcionario_id, $hoje);
    $stmt->execute();

    // Não é necessário obter resultados se é um UPDATE
    echo "Latitude: $latitude, Longitude: $longitude";
} else {
    echo "Nenhuma localização enviada";
}
?>
