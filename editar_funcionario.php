<?php
// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o parâmetro funcionario_id foi enviado
    if (isset($_POST['funcionario_id'])) {
        $funcionario_id = $_POST['funcionario_id'];
        // Faça o processamento necessário com o $funcionario_id
        // Por exemplo, redirecionar para a página de edição com o ID:
        //echo 'AAAAAAAAAAAAAAAAAAAAAAAAAAAA';
        header("Location: editar.php?id=" . $funcionario_id);
        exit();
    } else {
        // Caso não tenha recebido funcionario_id, faça o tratamento adequado
        echo "ID do funcionário não recebido.";
    }
}

?>

