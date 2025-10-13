<?php
// Inicializa o array da turma (será populado pela session)
$turma = [];
$mensagem = ''; // Inicializa a mensagem

// ----------------------------------------------------
// Função para Calcular a Média e a Situação do Aluno
// ----------------------------------------------------
/**
 * Calcula a média e determina a situação (Aprovado/Reprovado) de um aluno.
 * * @param array $aluno O array contendo 'notas' (array de floats) e 'frequencia' (float).
 * @return array Um array contendo 'media' (float) e 'situacao' (string).
 */
function situacaoAluno(array $aluno): array
{
    // A. Regras de Aprovação (Podes ajustar estes valores)
    $mediaMinima = 7.0;
    $frequenciaMinima = 75.0;

    // B. Cálculo da Média
    // array_sum(): Soma todos os valores no array 'notas'.
    // count(): Conta quantos elementos existem no array 'notas'.
    $soma = array_sum($aluno['notas']);
    $quantidade = count($aluno['notas']);

    // Evita divisão por zero caso não haja notas
    $media = $quantidade > 0 ? $soma / $quantidade : 0;

    // C. Determinação da Situação
    $situacao = 'Reprovado'; // Assume Reprovado por padrão

    // Aprovado se a Média for igual ou maior que a mínima E a Frequência for igual ou maior que a mínima
    if ($media >= $mediaMinima && $aluno['frequencia'] >= $frequenciaMinima) {
        $situacao = 'Aprovado';
    } elseif ($aluno['frequencia'] < $frequenciaMinima) {
        // Se a frequência for muito baixa, é reprovado por falta, independentemente da nota.
        $situacao = 'Reprovado por Falta';
    } else {
        $situacao = 'Reprovado por Nota';
    }

    // Retorna a média e a situação
    return [
        'media' => $media,
        'situacao' => $situacao,
    ];
}

// ----------------------------------------------------
// Lógica de Processamento e Sessão (Sem Alterações)
// ----------------------------------------------------
// ... [O restante do seu código PHP, que não mudou, está aqui] ...

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
    <?php // Certifique-se de que 'escola' está definida antes de usá-la.

if (isset($_GET['escola'])) {
        echo '<h2>' . htmlspecialchars($_GET['escola']) . '</h2>';
    } ?>

    <?php if (!empty($mensagem)) {
        echo "<p style='color:green;'>$mensagem</p>";
    } ?>

    <form method="post">
        <input type="hidden" name="adicionar" value="1">
        <label>Nome do aluno:</label>
        <input type="text" name="nome" required>

        <label>Nota 1:</label>
        <input type="number" step="0.1" name="nota1" required>

        <label>Nota 2:</label>
        <input type="number" step="0.1" name="nota2" required>

        <label>Nota 3:</label>
        <input type="number" step="0.1" name="nota3" required>

        <label>Percentual de frequência (ex: 85):</label>
        <input type="number" step="0.1" name="frequencia" required>

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
                <th>Média</th> 
                <th>Frequência (%)</th>
                <th>Situação</th>
            </tr>
            <?php foreach ($turma as $aluno):

                // Chamada da nova função
                $resultado = situacaoAluno($aluno);
                $mediaFormatada = number_format($resultado['media'], 2);
                $situacao = $resultado['situacao'];

                // Determina a classe CSS para a cor da situação
                $classeSituacao = '';
                if ($situacao === 'Aprovado') {
                    $classeSituacao = 'aprovado';
                } elseif ($situacao === 'Reprovado por Falta') {
                    $classeSituacao = 'reprovado-falta';
                } else {
                    $classeSituacao = 'reprovado-nota';
                }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                    <td><?php echo $aluno['notas'][0]; ?></td>
                    <td><?php echo $aluno['notas'][1]; ?></td>
                    <td><?php echo $aluno['notas'][2]; ?></td>
                    <td><?php echo $mediaFormatada; ?></td> <td><?php echo $aluno[
    'frequencia'
]; ?></td>
                    <td class="<?php echo $classeSituacao; ?>"><?php echo $situacao; ?></td> </tr>
            <?php
            endforeach; ?>
        </table>
        
        <form method="post" style="margin-top: 15px;">
            <button type="submit" name="limpar_turma">Limpar Turma</button>
        </form>
    <?php endif; ?>
</body>
</html>

<?php // Lógica para limpar a sessão e remover a turma

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['limpar_turma'])) {
    session_start();
    unset($_SESSION['turma']);
    header('Location: ' . $_SERVER['PHP_SELF']); // Redireciona para atualizar a página
    exit();
}
?>
