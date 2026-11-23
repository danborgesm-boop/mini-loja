<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: login.php');
    exit;
}

require 'banco.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->execute([':id' => $id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = $_POST['preco'];
    $nomeImagem = $produto['imagem'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $permitidas)) {
            if ($produto['imagem'] && file_exists(__DIR__ . '/uploads/' . $produto['imagem'])) {
                unlink(__DIR__ . '/uploads/' . $produto['imagem']);
            }
            $nomeImagem = uniqid('prod_') . '.' . $ext;
            move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__ . '/uploads/' . $nomeImagem);
        }
    }

    $stmt = $pdo->prepare("UPDATE produtos SET nome = :nome, preco = :preco, imagem = :imagem WHERE id = :id");
    $stmt->execute([
        ':nome' => $nome,
        ':preco' => $preco,
        ':imagem' => $nomeImagem,
        ':id' => $id
    ]);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
</head>
<body>
    <h2>Editar Produto</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required><br><br>

        <label>Preço:</label><br>
        <input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" required><br><br>

        <label>Nova imagem (opcional):</label><br>
        <input type="file" name="imagem" accept="image/*"><br><br>

        <button type="submit">Atualizar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>