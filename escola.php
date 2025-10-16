<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nome da Escola</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h2>Informe o nome da Escola</h2>
    <form method="get" action="turma.php">
        <label for="escola">Nome da Escola:</label>
        <input 
            type="text" 
            id="escola" 
            name="escola" 
            required 
            minlength="3"
            placeholder="Ex: Escola Municipal João Silva"
            autofocus
        >

        <input type="submit" value="Continuar">
    </form>
</div>
</body>
</html>