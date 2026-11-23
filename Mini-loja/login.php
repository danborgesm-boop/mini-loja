<?php
session_start();
require 'banco.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $func = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($func && password_verify($senha, $func['senha'])) {
        $_SESSION['funcionario_id'] = $func['id'];
        $_SESSION['funcionario_nome'] = $func['nome'];
        header('Location: index.php');
        exit;
    } else {
        $erro = 'E-mail ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login de Funcionário</h2>
    <p><?= $erro ?></p>
    <form method="POST">
        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <button type="submit">Entrar</button>
    </form>
    <br>
    <a href="cadastro_funcionario.php">Cadastrar Funcionário</a>
</body>
</html>