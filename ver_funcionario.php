<?php

session_start();
include("config.php"); // banco de dados

// Verifica se o ID do funcionário foi passado na URL
if (isset($_GET['id'])) {
    $funcionario_id = $_GET['id'];

    // Atualiza $_SESSION['mes'] com a data selecionada no filtro
    if (isset($_POST['filtro'])) {
        $_SESSION['mes'] = $_POST['mes'];
    }

    $sql = "SELECT id, nome, cpf, email, turno, cargo, data_admissao FROM funcionarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se encontrou o funcionário
    if ($result->num_rows > 0) {
        $funcionario = $result->fetch_assoc();

        // Obtém o mês e ano do filtro
        $mes = isset($_SESSION['mes']) ? $_SESSION['mes'] : date('Y-m');

        // Ajusta a consulta para filtrar por mês e ano
        $sql_verifica = "SELECT f.cpf, f.id, f.nome, f.email, f.status, p.funcionario_id, p.hora_entrada, p.hora_saida, p.almoco_entrada, p.almoco_saida, p.data 
                        FROM funcionarios f 
                        LEFT JOIN pontos p ON f.id = p.funcionario_id 
                        WHERE f.id = ? AND DATE_FORMAT(p.data, '%Y-%m') = ? 
                        ORDER BY p.data DESC";

        $stmt = $conn->prepare($sql_verifica);
        $stmt->bind_param("is", $funcionario_id, $mes);
        $stmt->execute();
        $result = $stmt->get_result();

        // Variável para armazenar o total de horas trabalhadas
        $total_horas = 0;
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Ver Funcionário</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="ie-fixMinHeight">
    <div class="main">
        <div id="">
            <img id="logogts" src="img/logo_gts.png" />
            <div id="tabelauser">
                <!-- Formulário de filtro por mês -->
                <form class="menu" method="post" action="ver_funcionario.php?id=<?php echo $funcionario_id; ?>">
                    <br>
                    <label for="mes">Filtrar por mês:</label>
                    <input type="month" id="mes" name="mes" value="<?php echo $_SESSION['mes']; ?>"><br><br>
                    <div id="btt_func">
                        <input type="submit" name="filtro" id="filtro" value="Filtrar"/>
                        <div id="div_credenciais">
                            <p><b>CPF:</b> <?php echo $funcionario['cpf']; ?></p>
                            <p><b>Nome:</b> <?php echo $funcionario['nome']; ?></p>
                            <p><b>E-mail:</b> <?php echo $funcionario['email']; ?></p>
                            <p><b>Cargo:</b> <?php echo $funcionario['cargo']; ?></p>
                            <?php if ($funcionario['turno'] == 'dia_todo'): ?>
                                <p><b>Turno:</b> dia todo</p>
                            <?php else:?>
                                <p><b>Cargo:</b> <?php echo $funcionario['turno']; ?></p>
                            <?php endif; ?>
                            <p><b>Data de admissão:</b> <?php echo date('d/m/Y', strtotime($funcionario['data_admissao'])); ?></p>
                        </div>
                    </div>
                </form>

                <!-- Tabela de usuários -->
                <table>
                    <tr>
                        <th>DATA</th>
                        <th>Hora de entrada</th>
                        <th>Foto</th>
                        <th>Entrada ao almoço</th>
                        <th>Saída do almoço</th>
                        <th>Hora de saída</th>
                        <th>Foto</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $ativoClass = ($row['status'] == 'ativo') ? '' : 'desativado';
                        $hora_entrada = new DateTime($row['hora_entrada']);
                        $hora_saida = new DateTime($row['hora_saida']);
                        $almoco_entrada = new DateTime($row['almoco_entrada']);
                        $almoco_saida = new DateTime($row['almoco_saida']);

                        // Calcula as horas trabalhadas no dia
                        $intervalo_trabalho = $hora_entrada->diff($hora_saida);
                        $intervalo_almoco = $almoco_entrada->diff($almoco_saida);
                        $horas_trabalhadas = $intervalo_trabalho->h + ($intervalo_trabalho->i / 60) - ($intervalo_almoco->h + ($intervalo_almoco->i / 60));
                        $total_horas += $horas_trabalhadas;
                        ?>
                        <tr class="<?php echo $ativoClass; ?>">
                            <td class="txtTabela"><?php echo date('d/m/Y', strtotime($row['data'])); ?></td>
                            <td class="txtTabela"><?php echo $row['hora_entrada']; ?></td>
                            <td class="tdFoto">
                                <img class="fotoPessoa" src="uploads/photo_<?php echo $funcionario['cpf'];?>_<?php echo $_SESSION['hoje'];?>_entrando.png" alt="Foto de <?php echo $funcionario['nome'];?> ao entrar">
                            </td>
                            <td class="txtTabela"><?php echo $row['almoco_entrada']; ?></td>
                            <td class="txtTabela"><?php echo $row['almoco_saida']; ?></td>
                            <td class="txtTabela"><?php echo $row['hora_saida']; ?></td>
                            <td class="tdFoto">
                                <img class="fotoPessoa" src="uploads/photo_<?php echo $funcionario['cpf'];?>_<?php echo $_SESSION['hoje'];?>_saindo.png" alt="Foto de <?php echo $funcionario['nome'];?> ao sair">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <!-- Exibe o total de horas trabalhadas -->
                <br>
                <p id="txtHoras">Total de horas trabalhadas no mês: <?php echo number_format($total_horas, 2); ?> horas</p>
                <p class="info bt">GTS Net</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
    } else {
        // Caso não encontre o funcionário, redireciona de volta para a página inicial
        header("Location: funcionarios.php");
        exit();
    }
?>
