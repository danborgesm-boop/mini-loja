<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: login.php');
    exit;
}

require 'banco.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto && $produto['imagem'] && file_exists(__DIR__ . '/uploads/' . $produto['imagem'])) {
        unlink(__DIR__ . '/uploads/' . $produto['imagem']);
    }

    $del = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
    $del->execute([':id' => $id]);
}

header('Location: index.php');
exit;