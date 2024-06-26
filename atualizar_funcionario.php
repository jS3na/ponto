<?php
include("config.php"); // Inclua seu arquivo de configuração do banco de dados

// Verifica se o formulário foi submetido
if (isset($_POST['atualizar'])) {
    // Captura os dados do formulário
    $funcionario_id = $_POST['funcionario_id'];
    $cargo = $_POST['cargo'];
    $data_admissao = $_POST['data_admissao'];

    if (empty($cargo) || empty($data_admissao)) {
        // Se qualquer uma das variáveis estiver vazia, faça algo
        echo'<style>.infort {
            color: red;
            text-align: center;
            margin-bottom: 30px
            }</style>
        
            <p class="infort" >Preencha todos os campos obrigatórios</p>';

            header("refresh:2;url=editar.php?id=" . $funcionario_id);
    
    }

    else{

    // Prepara uma consulta para atualizar os dados do funcionário
    $sql = "UPDATE funcionarios SET cargo = ?, data_admissao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $cargo, $data_admissao, $funcionario_id);
    
    // Executa a consulta
    if ($stmt->execute()) {
        // Redireciona de volta para a página inicial após a atualização
        header("Location: funcionarios.php");
    } else {
        // Em caso de erro, redireciona para uma página de erro ou trata o erro de outra forma
        echo "Erro ao atualizar funcionário: " . $conn->error;
    }

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();
} }

else if(isset($_POST['desativar'])) {

    $funcionario_id = $_POST['funcionario_id'];

    $sql = 'UPDATE funcionarios SET status = "desativado" WHERE id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $funcionario_id);
    
    // Executa a consulta
    if ($stmt->execute()) {
        // Redireciona de volta para a página inicial após a atualização
        header("Location: funcionarios.php");
    } else {
        // Em caso de erro, redireciona para uma página de erro ou trata o erro de outra forma
        echo "Erro ao atualizar funcionário: " . $conn->error;
    }

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();

}



else {
    // Caso o formulário não tenha sido submetido, redireciona para a página inicial
    header("Location: funcionarios.php");
    exit();

}
?>
