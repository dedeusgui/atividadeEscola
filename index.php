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

    // Pega os dados do formulário com trim para remover espaços
    $usuario_digitado = trim($_POST['usuario'] ?? '');
    $senha_digitada = trim($_POST['senha'] ?? '');

    // Valida se os campos não estão vazios
    if (!empty($usuario_digitado) && !empty($senha_digitada)) {
        // Verifica se o usuário e a senha estão corretos
        if (USER === $usuario_digitado && SENHA === $senha_digitada) {
            // Define variável de sessão para autenticação
            $_SESSION['autenticado'] = true;
            $_SESSION['usuario'] = $usuario_digitado;

            // Se estiverem corretos, redireciona para a página da escola
            header('Location: escola.php');
            exit();
        } else {
            // Se estiverem incorretos, define a mensagem de erro
            $mensagem_erro = 'Negado. Usuário ou senha inválidos.';
        }
    } else {
        // Se algum campo estiver vazio
        $mensagem_erro = 'Por favor, preencha todos os campos.';
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
        <input 
            type="text" 
            id="usuario" 
            name="usuario" 
            autocomplete="username"
            value="<?php echo htmlspecialchars($usuario_digitado ?? ''); ?>"
        />

        <label for="senha">Senha</label>
        <input 
            type="password" 
            id="senha" 
            name="senha" 
            autocomplete="current-password"
        />

        <input type="submit" value="Entrar" />
    </form>

    <?php if (!empty($mensagem_erro)): ?>
        <p id="acesso" style="color:red;">
            <?php echo htmlspecialchars($mensagem_erro); ?>
        </p>
    <?php endif; ?>
</div>

<script src="script.js"></script>
</body>
</html>