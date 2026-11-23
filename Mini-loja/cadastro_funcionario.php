<?php
require 'banco.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    try {
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha
        ]);
        $mensagem = 'Funcionário cadastrado com sucesso!';
    } catch (PDOException $e) {
        $mensagem = 'Erro ao cadastrar (e-mail já existe).';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Funcionário</title>
</head>
<body>
    <h2>Cadastro de Funcionário</h2>
    <p><?= $mensagem ?></p>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <button type="submit">Cadastrar</button>
    </form>
    <br>
    <a href="login.php">Ir para o Login</a>
</body>
</html>