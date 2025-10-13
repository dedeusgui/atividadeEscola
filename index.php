<?php
// Inicia a sessão para poder redirecionar ou guardar informações no futuro
session_start();

// Variável para guardar a mensagem de erro
$mensagem_erro = '';

// Verifica se o formulário foi enviado (se o método é POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Credenciais corretas
    define('USER', 'aluno');
    define('SENHA', '123');

    // Pega os dados do formulário
    $usuario_digitado = $_POST['usuario'] ?? '';
    $senha_digitada = $_POST['senha'] ?? '';

    // Valida se os campos não estão vazios
    if (!empty($usuario_digitado) && !empty($senha_digitada)) {
        // Verifica se o usuário e a senha estão corretos
        if (USER == $usuario_digitado && SENHA == $senha_digitada) {
            // Se estiverem corretos, redireciona para a página da turma
            header('Location: escola.php');
            exit(); // Garante que o script pare de ser executado após o redirecionamento
        } else {
            // Se estiverem incorretos, define a mensagem de erro
            $mensagem_erro = 'Negado. Usuário ou senha inválidos.';
        }
    } else {
        // Se algum campo estiver vazio
        $mensagem_erro = 'Valores faltando nos inputs.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tela de Login</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="login-container" id="form-container">
    <h2>Login</h2>
    <form id="login-form" method="POST" action="index.php">
        <label for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" />

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" />

        <input type="submit" value="Entrar" />
    </form>

    <p id="acesso" style="color:red;">
        <?php echo $mensagem_erro; ?>
    </p>
</div>

<script src="script.js"></script>
</body>
</html>