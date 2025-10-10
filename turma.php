<?php
// Array da turma
$turma = [];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    // Recebe os dados do aluno
    $nome = $_POST['nome'] ?? '';
    $nota1 = $_POST['nota1'] ?? '';
    $nota2 = $_POST['nota2'] ?? '';
    $nota3 = $_POST['nota3'] ?? '';
    $frequencia = $_POST['frequencia'] ?? '';

    // Validação simples
    if ($nome && $nota1 !== '' && $nota2 !== '' && $nota3 !== '' && $frequencia !== '') {
        // Cria o array do aluno
        $aluno = [
            'nome' => $nome,
            'notas' => [floatval($nota1), floatval($nota2), floatval($nota3)],
            'frequencia' => floatval($frequencia),
        ];

        // Salva no array da turma usando sessão
        session_start();
        if (!isset($_SESSION['turma'])) {
            $_SESSION['turma'] = [];
        }
        $_SESSION['turma'][] = $aluno;
        $turma = $_SESSION['turma'];

        $mensagem = "Aluno '$nome' adicionado com sucesso!";
    } else {
        $mensagem = 'Preencha todos os campos corretamente!';
    }
} else {
    session_start();
    if (isset($_SESSION['turma'])) {
        $turma = $_SESSION['turma'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h2>Cadastro de Alunos</h2>
    <?php echo '<h2>' . $_GET['escola'] . '</h2>'; ?>
"
    <?php if (!empty($mensagem)) {
        echo "<p style='color:green;'>$mensagem</p>";
    } ?>

    <form method="post">
        <input type="hidden" name="adicionar" value="1">
        <label>Nome do aluno:</label>
        <input type="text" name="nome" required>

        <label>Nota 1:</label>
        <input type="number" step="0.01" name="nota1" required>

        <label>Nota 2:</label>
        <input type="number" step="0.01" name="nota2" required>

        <label>Nota 3:</label>
        <input type="number" step="0.01" name="nota3" required>

        <label>Percentual de frequência:</label>
        <input type="number" step="0.01" name="frequencia" required>

        <input type="submit" value="Adicionar Aluno">
    </form>

    <?php if (!empty($turma)): ?>
        <h3>Lista de Alunos:</h3>
        <table>
            <tr>
                <th>Nome</th>
                <th>Nota 1</th>
                <th>Nota 2</th>
                <th>Nota 3</th>
                <th>Frequência (%)</th>
            </tr>
            <?php foreach ($turma as $aluno): ?>
                <tr>
                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                    <td><?php echo $aluno['notas'][0]; ?></td>
                    <td><?php echo $aluno['notas'][1]; ?></td>
                    <td><?php echo $aluno['notas'][2]; ?></td>
                    <td><?php echo $aluno['frequencia']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
