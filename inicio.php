<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Bater ponto</title>
    <link rel="stylesheet" href="css\style.css">
</head>

<body>

    <div class="ie-fixMinHeight">
        <div class="main">
            <div class="wrap animated fadeIn" id="principal">
                <form name="login" method="post" action="inicio.php?id=<?php echo htmlspecialchars($_GET['id']); ?>">
                    <img id="logogts" src="img/logo_gts.png"/>

<?php

session_start();

if (!$_SESSION['logado']) {
    //echo 'ssassas';
    header("Location: login.php");
    exit();
}

$trabalhando = '';
$funcionario_id = 'aaaa';

$hoje_TOTAL = date('Y-m-d');

include("config.php");

if (isset($_GET['id'])) {

    $funcionario_cpf = $_GET['id'];

    $sql_verifica = "SELECT f.id, f.nome, f.data_admissao, p.funcionario_id, p.data, p.hora_entrada, p.hora_saida 
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
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if(!is_null($row)){
        $hora_saida = $row['hora_saida'];
    }

    $sql_verifica = "SELECT id, nome FROM funcionarios WHERE cpf = ?";
    $stmt = $conn->prepare($sql_verifica);
    if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        exit();
    }
    $stmt->bind_param("s", $funcionario_cpf);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $row2 = $result2->fetch_assoc();

    $funcionario_id = $row2['id'];
    $nome = $row2['nome'];

    if ($result->num_rows == 0) {
        $trabalhando = "inicio";

    }

    elseif(!isset($hora_saida)){
        $trabalhando = "trabalhando";
    }

    else{
        $trabalhando = "fim";
    }

    $hora_atual = date('H:i:s');

    if ($hora_atual < '12:00:00') {
        echo '<p class="bemvindo">Bom dia, ' . htmlspecialchars($nome) . '!</p>';
    } else {
        echo '<p class="bemvindo">Boa tarde, ' . htmlspecialchars($nome) . '!</p>';
    }

    echo '<br>';

    // Recebe os dados do formulário

    //echo $funcionario_cpf;

    if(isset($_POST['entrar'])){

        header("Location: foto.php?id=" . $funcionario_cpf . "&id2=" . $funcionario_id . "&atual=entrando");
        exit();

    }

    if(isset($_POST['sair'])){

        header("Location: foto.php?id=" . $funcionario_cpf . "&id2=" . $funcionario_id . "&atual=saindo");
        exit();

        //echo "SAIUUUUUUUU";
}

}else {

    if($trabalhando == 'fim'){
        // Caso não encontre o funcionário, redireciona de volta para a página inicial
        header("Location: login.php");
        exit();
    }

}


?>
                        <?php if ($trabalhando == 'inicio'): ?>
                        <input class="entrar" name="entrar" type="submit" value="Iniciar expediente"/>
                        <?php endif; ?>

                        <?php if ($trabalhando == 'trabalhando'): ?>
                        <input class="sair" name="sair" type="submit" value="Finalizar expediente" />
                        <?php endif; ?>

                        <?php if ($trabalhando == 'fim'): ?>
                            <p class="bemvindo">Você já trabalhou hoje</p>
                        <?php endif; ?>

                </form>
                <p class="info bt">GTS Net</p>

            </div>
        </div>
    </div>

</body>

</html>
