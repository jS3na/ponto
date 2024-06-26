<?php
include("config.php"); // Inclua seu arquivo de configuração do banco de dados

// Verifica se o ID do funcionário foi passado na URL
if (isset($_GET['id'])) {
    $funcionario_id = $_GET['id'];

    // Prepara uma consulta para obter os dados do funcionário pelo ID
    $sql = "SELECT id, nome, email, cargo, data_admissao FROM funcionarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se encontrou o funcionário
    if ($result->num_rows > 0) {
        $funcionario = $result->fetch_assoc();

        // Exibe o formulário de edição
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Editar Funcionário</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="ie-fixMinHeight">
    <div class="main">
        <div class="wrap animated fadeIn" id="principal">
            <img id="logogts" src="img/logo_gts.png" />

            <form name='login' method="post" action="atualizar_funcionario.php">
                <input type="hidden" name="funcionario_id" value="<?php echo $funcionario['id']; ?>">
                
                <label for="cargo">Cargo:</label>
                <br>
                <input type="text" id="cargo" name="cargo" value="<?php echo $funcionario['cargo']; ?>"><br><br>

                <label for="data_admissao">Data de Admissão:</label>
                <input type="date" id="data_admissao" name="data_admissao" value="<?php echo $funcionario['data_admissao']; ?>"><br><br>

                <input class="desativar" type="submit" id="desativar" name="desativar" value="Desativar Funcionário"><br><br>

                <input type="submit" name="atualizar" value="Atualizar">
            </form>

            <p class="info bt">GTS Net</p>
        </div>
    </div>
</div>

<script src="/md5.js"></script>

</body>
</html>
<?php
    } else {
        // Caso não encontre o funcionário, redireciona de volta para a página inicial
        header("Location: funcionarios.php");
        exit();
    }

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();
}
?>
