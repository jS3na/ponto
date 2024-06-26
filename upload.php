<?php
if (isset($_POST['photo'])) {
    $data = $_POST['photo'];

    // Remove o prefixo "data:image/png;base64,"
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $data = base64_decode($data);

    // Define o caminho e o nome do arquivo
    $filePath = 'uploads/photo_' . time() . '.png';

    // Salva a imagem no servidor
    file_put_contents($filePath, $data);

    echo "Foto salva com sucesso em: " . $filePath;
} else {
    echo "Nenhuma foto enviada.";
}
?>
