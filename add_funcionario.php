<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Adicionar Funcionário</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="ie-fixMinHeight">
    <div class="main">
        <div class="wrap animated fadeIn" id="principal">
            <img id="logogts" src="img/logo_gts.png" />

            <form name='login' method="post" action="add_funcionario.php">

<?php

session_start();

// Verificação de administração
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != true) {
    header("Location: index.php");
    exit();
}
//script php do formulário de alteração do cargo e data de admissão do funcionário

function validaCPF($cpf)
{

    // Extrai somente os números
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

include("config.php");

// Recebe os dados do formulário

if(isset($_POST['registrar'])){

    $username = $_POST['username'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $cpf = str_replace(array('(', ')', '-', '.'), '', $cpf);
    $cargo = $_POST['cargo'];
    $turno = $_POST['turno'];
    $data_admissao = $_POST['data_admissao'];

    if (empty($username) || empty($email) || empty($cpf) || empty($cargo) || empty($turno) || empty($data_admissao)) {
        // Se qualquer uma das variáveis estiver vazia, faça algo
        echo'<style>.infort {
            color: red;
            text-align: center;
            margin-bottom: 30px
            }</style>
        
            <p class="infort" >Preencha todos os campos obrigatórios</p>';

    }

    elseif(strlen($cpf) != 11){

        echo'<style>.infort {
            color: red;
            text-align: center;
            margin-bottom: 30px
            }</style>
        
            <p class="infort" >CPF inválido</p>';
    }

    elseif(!validaCPF($cpf)){
        echo'<style>.infort {
            color: red;
            text-align: center;
            margin-bottom: 30px
            }</style>
        
            <p class="infort" >CPF inválido</p>';
    }
    

    else{

        // Preparar e executar a consulta SQL usando prepared statements
        $sql_verifica = "SELECT * FROM funcionarios WHERE nome = ? OR email = ? OR cpf = ?";
        $stmt = $conn->prepare($sql_verifica);
        $stmt->bind_param("sss", $username, $email, $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar se o usuário já existe
        if ($result->num_rows > 0) {

            echo'<style>.infort {
                color: red;
                text-align: center;
                margin-bottom: 30px
                }</style>
            
                <p class="infort" >Usuário existente</p>';

        }

        else{

            // Inserir novo usuário
            $sql = "INSERT INTO funcionarios (nome, email, cpf, cargo, turno, data_admissao) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $username, $email, $cpf, $cargo, $turno, $data_admissao);
        
            if ($stmt->execute()) {

                header("Location: funcionarios.php");
                exit();
            } else {
                echo "Erro: " . $sql . "<br>" . $conn->error;
            }

            
        }
    }

}

?>

                    <label>
                        <img class="ico" src="img/user.svg" alt="#" />
                        <input name="username" type="text" placeholder="Nome *" />
                    </label>

                    <label>
                        <img class="ico" src="img/email.svg" alt="#" />
                        <input name="email" type="email" placeholder="E-Mail *" />
                    </label>

                    <label>
                        <img class="ico" src="img/cpf.svg" alt="#" />
                        <input name="cpf" type="cpf" placeholder="CPF *" onkeypress="return apenasNum(event)" />
                    </label>

                    <label for="cargo">Cargo:</label>
                    <select id="cargo" name="cargo">
                        <option value="Operacional">Operacional</option>
                        <option value="Administrativo">Administrativo</option>
                    </select>

                    <label for="turno">Turno:</label>
                    <select id="turno" name="turno">
                        <option value="manha">Manhã</option>
                        <option value="tarde">Tarde</option>
                        <option value="dia_todo">Dia todo</option>
                    </select>

                    <label for="data_admissao">Data de Admissão:</label>
                    <input type="date" id="data_admissao" name="data_admissao" value="<?php echo date("Y-m-d"); ?>"><br><br>

                    <label for="admin">Admin:</label>
                    <select id="admin" name="admin">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>

                <input type="submit" name="registrar" value="Adicionar">
            </form>

            <p class="info bt">GTS Net</p>
        </div>
    </div>
</div>

</body>
</html>
