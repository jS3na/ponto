<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Tabela de usuários</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<?php
session_start();

// Atualiza $_SESSION['hoje'] com a data atual se não estiver definido
if (!isset($_SESSION['hoje'])) {
    $_SESSION['hoje'] = date('Y-m-d');
}

include("config.php");

// Verificação de administração
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != true) {
    header("Location: inicio.php");
    exit();
}

// Verificação do filtro por data
if (isset($_POST['filtro'])) {
    // Atualiza $_SESSION['hoje'] com a data selecionada no filtro
    $_SESSION['hoje'] = $_POST['data'];
}

// Preparar e executar a consulta SQL usando prepared statements
$sql_verifica = "SELECT f.id, f.nome, f.email, f.cargo, f.data_admissao, f.status, p.funcionario_id, p.hora_entrada, p.hora_saida 
                FROM funcionarios f 
                LEFT JOIN pontos p ON f.id = p.funcionario_id 
                WHERE p.data = ?";

$stmt = $conn->prepare($sql_verifica);
$stmt->bind_param("s", $_SESSION['hoje']);
$stmt->execute();
$result = $stmt->get_result();

?>
<body>

<div class="ie-fixMinHeight">
    <div class="main">
        <div id="">
        <img id="logogts" src="img/logo_gts.png" />
        <div id="tabelauser">

            <!-- Formulário de filtro por data -->
            <form class="menu" method="post" action="funcionarios.php">
                <br>
                <label for="data">Filtrar por data:</label>
                <input type="date" id="data" name="data" value="<?php echo $_SESSION['hoje']; ?>"><br><br>
                <input type="submit" name="filtro" id="filtro" value="Filtrar"/>
            </form>

            <!-- Tabela de usuários -->

            <table>
                <tr>
                    <th>Nome do Funcionário</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Data de admissão</th>
                    <th>Hora de entrada</th>
                    <th>Hora de saída</th>
                    <th>Ações</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php if($row['status'] == 'ativo'): ?>
                    <tr>
                        <td class="txtTabela"><?php echo $row['nome']; ?></td>
                        <td class="txtTabela"><?php echo $row['email']; ?></td>
                        <td class="txtTabela"><?php echo $row['cargo']; ?></td>
                        <td class="txtTabela"><?php echo $row['data_admissao']; ?></td>
                        <td class="txtTabela"><?php echo $row['hora_entrada']; ?></td>
                        <td class="txtTabela"><?php echo $row['hora_saida']; ?></td>
                        <td type="submit" name="editar" class="'editartd">
                            <form method="post" action="editar_funcionario.php">
                                <input type="hidden" name="funcionario_id" value="<?php echo $row['funcionario_id']; ?>">
                                <input class="editar" type="submit" name="editar" value="Editar">
                            </form>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if($row['status'] != 'ativo'): ?>
                    <tr class="desativado">
                        <td class="txtTabela"><?php echo $row['nome']; ?></td>
                        <td class="txtTabela"><?php echo $row['email']; ?></td>
                        <td class="txtTabela"><?php echo $row['cargo']; ?></td>
                        <td class="txtTabela"><?php echo $row['data_admissao']; ?></td>
                        <td class="txtTabela"><?php echo $row['hora_entrada']; ?></td>
                        <td class="txtTabela"><?php echo $row['hora_saida']; ?></td>
                        <td type="submit" name="editar" class="'editartd">
                            <form method="post" action="editar_funcionario.php">
                                <input type="hidden" name="funcionario_id" value="<?php echo $row['funcionario_id']; ?>">
                                <input class="editar" type="submit" name="editar" value="Editar">
                            </form>
                        </td>
                    </tr>
                    <?php endif; ?>

                <?php endwhile; ?>
            </table>
            </div>

            <p class="info bt">GTS Net</p>
        </div>
    </div>
</div>

<script src="/md5.js"></script>

</body>
</html>

<?php
// Fechar statement e conexão
$stmt->close();
$conn->close();
?>