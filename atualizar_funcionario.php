<?php
//arquivo php que muda as informações do funcionário pelo admin

include("config.php"); //banco de dados

if (isset($_POST['atualizar'])) { //se a ação do admin for alterar o cargo e a data de admissão do funcionario
    //captura os dados do formulário
    $funcionario_id = $_POST['funcionario_id'];
    $cargo = $_POST['cargo'];
    $data_admissao = $_POST['data_admissao'];

    if (empty($cargo) || empty($data_admissao)) { //verifica se as variáveis estão vazias

        echo'<style>.infort {
            color: red;
            text-align: center;
            margin-bottom: 30px
            }</style>
        
            <p class="infort" >Preencha todos os campos obrigatórios</p>';

            header("refresh:2;url=editar.php?id=" . $funcionario_id);
    
    }

    else{ //se todos os campos forem preenchidos

    //define qual funcionario vai ser modificado
    $sql = "UPDATE funcionarios SET cargo = ?, data_admissao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $cargo, $data_admissao, $funcionario_id);
    
    //executa
    if ($stmt->execute()) {
        header("Location: funcionarios.php");
    } else {
        echo "Erro ao atualizar funcionário: " . $conn->error;
    }

    //fecha o statement e a conexão
    $stmt->close();
    $conn->close();
} }

else if(isset($_POST['desativar'])) { //se o admin escolher desativar o funcionario

    $funcionario_id = $_POST['funcionario_id'];

    $sql = 'UPDATE funcionarios SET status = "desativado" WHERE id = ?'; //define o funcionario
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $funcionario_id);
    
    //executa a consulta
    if ($stmt->execute()) {
        // Redireciona de volta para a página inicial após a atualização
        header("Location: funcionarios.php");
    } else {
        // Em caso de erro, redireciona para uma página de erro ou trata o erro de outra forma
        echo "Erro ao atualizar funcionário: " . $conn->error;
    }

    //fecha o statement e a conexão
    $stmt->close();
    $conn->close();

}



else {
    // Caso o formulário não tenha sido submetido, redireciona para a página inicial
    header("Location: funcionarios.php");
    exit();

}
?>
