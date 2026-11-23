<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: login.php');
    exit;
}

require 'banco.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = $_POST['preco'];
    $nomeImagem = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if (in_array($ext, $permitidas)) {
            $nomeImagem = uniqid('prod_') . '.' . $ext;
            move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__ . '/uploads/' . $nomeImagem);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, imagem) VALUES (:nome, :preco, :imagem)");
    $stmt->execute([
        ':nome' => $nome,
        ':preco' => $preco,
        ':imagem' => $nomeImagem
    ]);

    header('Location: index.php');
    exit;
}

$produtos = $pdo->query("SELECT * FROM produtos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mini Loja</title>
</head>
<body>
    <p>Olá, <?= htmlspecialchars($_SESSION['funcionario_nome']) ?> | <a href="logout.php">Sair</a></p>
    <h2>Cadastrar Produto</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Nome do produto:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Preço:</label><br>
        <input type="number" step="0.01" name="preco" required><br><br>

        <label>Imagem:</label><br>
        <input type="file" name="imagem" accept="image/*"><br><br>

        <button type="submit">Salvar Produto</button>
    </form>

    <hr>
    <h2>Produtos Cadastrados</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($produtos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td>
                <?php if ($p['imagem']): ?>
                    <img src="uploads/<?= htmlspecialchars($p['imagem']) ?>" width="70">
                <?php else: ?>
                    Sem imagem
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
            <td>
                <a href="editar.php?id=<?= $p['id'] ?>">Editar</a> |
                <a href="excluir.php?id=<?= $p['id'] ?>" onclick="return confirm('Excluir este produto?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>