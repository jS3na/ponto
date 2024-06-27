<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://gtsnet.com.br/wp-content/uploads/sites/98/2020/08/cropped-favicon-32x32.png" sizes="32x32">
    <title>Registro</title>
    <link rel="stylesheet" href="css\style.css">
</head>

<body>

    <div class="ie-fixMinHeight">
        <div class="main">
            <div class="wrap animated fadeIn" id="principal">
                <form name="registro" method="post" action="registro.php">
                    <img id="logogts" src="img/logo_gts.png"/>

<?php

include("config.php");

// Recebe os dados do formulário

if(isset($_POST['registrar'])){

    $username = $_POST['username'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $cpf = str_replace(array('(', ')', '-', '.'), '', $cpf);
    $cargo = $_POST['cargo'];
    $senha = $_POST['password'];

    if (empty($username) || empty($email) || empty($cpf) || empty($senha)) {
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
            $sql = "INSERT INTO funcionarios (nome, email, cpf, cargo, senha) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $username, $email, $cpf, $cargo, $senha);

            if ($stmt->execute()) {

                header("Location: http://192.168.3.4/ponto/login.php");
                exit();
            } else {
                echo "Erro: " . $sql . "<br>" . $conn->error;
            }

            $stmt->close();
            $conn->close();
            
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

                    <label>
                        <img class="ico" src="img/cargo.svg" alt="#" />
                        <input name="cargo" type="cargo" placeholder="Cargo *"/>
                    </label>

                    <label>
                        <img class="ico" src="img/password.svg" alt="#" />
                        <input name="password" type="password" placeholder="Senha *" />
                    </label>

                    <p id="semConta">Já possui conta? <a href="https://10.10.86.80/ponto/login.php" id="registrar">Conecte-se</a>!</p>

                    <input name="registrar" id="conectar" type="submit" value="Registrar"/>
                </form>
                <p class="info bt">GTS Net</p>

            </div>
        </div>
    </div>

    <script src="/md5.js"></script>

</body>

</html>
