<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: index.php');
    exit();
}

// Inicializa o array da turma (será populado pela session)
$turma = [];
$mensagem = '';
$tipoMensagem = 'success'; // 'success' ou 'error'

// ----------------------------------------------------
// Função para Calcular a Média e a Situação do Aluno
// ----------------------------------------------------
function situacaoAluno(array $aluno): array
{
    $mediaMinima = 7.0;
    $frequenciaMinima = 75.0;

    $soma = array_sum($aluno['notas']);
    $quantidade = count($aluno['notas']);
    $media = $quantidade > 0 ? $soma / $quantidade : 0;

    $situacao = 'Reprovado';

    if ($media >= $mediaMinima && $aluno['frequencia'] >= $frequenciaMinima) {
        $situacao = 'Aprovado';
    } elseif ($aluno['frequencia'] < $frequenciaMinima) {
        $situacao = 'Reprovado por Falta';
    } else {
        $situacao = 'Reprovado por Nota';
    }

    return [
        'media' => $media,
        'situacao' => $situacao,
    ];
}

// ----------------------------------------------------
// Lógica para limpar a turma
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['limpar_turma'])) {
    unset($_SESSION['turma']);

    // Remove o arquivo de alunos também
    $nomeArquivo = 'alunos.txt';
    if (file_exists($nomeArquivo)) {
        unlink($nomeArquivo);
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?escola=' . urlencode($_GET['escola'] ?? ''));
    exit();
}

// ----------------------------------------------------
// Lógica de Processamento
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $nota1 = $_POST['nota1'] ?? '';
    $nota2 = $_POST['nota2'] ?? '';
    $nota3 = $_POST['nota3'] ?? '';
    $frequencia = $_POST['frequencia'] ?? '';

    // Validação completa
    if (empty($nome)) {
        $mensagem = 'O nome do aluno é obrigatório!';
        $tipoMensagem = 'error';
    } elseif ($nota1 === '' || $nota2 === '' || $nota3 === '' || $frequencia === '') {
        $mensagem = 'Preencha todos os campos corretamente!';
        $tipoMensagem = 'error';
    } elseif ($nota1 < 0 || $nota1 > 10 || $nota2 < 0 || $nota2 > 10 || $nota3 < 0 || $nota3 > 10) {
        $mensagem = 'As notas devem estar entre 0 e 10!';
        $tipoMensagem = 'error';
    } elseif ($frequencia < 0 || $frequencia > 100) {
        $mensagem = 'A frequência deve estar entre 0 e 100!';
        $tipoMensagem = 'error';
    } else {
        // Cria o array do aluno
        $aluno = [
            'nome' => $nome,
            'notas' => [floatval($nota1), floatval($nota2), floatval($nota3)],
            'frequencia' => floatval($frequencia),
        ];

        // Salva em arquivo TXT
        $nomeArquivo = 'alunos.txt';
        $linhaParaSalvar =
            implode(';', [
                $aluno['nome'],
                $aluno['notas'][0],
                $aluno['notas'][1],
                $aluno['notas'][2],
                $aluno['frequencia'],
            ]) . PHP_EOL;

        $arquivo = fopen($nomeArquivo, 'a');
        if ($arquivo) {
            fwrite($arquivo, $linhaParaSalvar);
            fclose($arquivo);
        }

        // Salva na sessão
        if (!isset($_SESSION['turma'])) {
            $_SESSION['turma'] = [];
        }
        $_SESSION['turma'][] = $aluno;
        $turma = $_SESSION['turma'];

        $mensagem = "Aluno '$nome' adicionado com sucesso!";
        $tipoMensagem = 'success';
    }
} else {
    if (isset($_SESSION['turma'])) {
        $turma = $_SESSION['turma'];
    }
}

// Pega o nome da escola
$nomeEscola = htmlspecialchars($_GET['escola'] ?? 'Escola');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos - <?php echo $nomeEscola; ?></title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="turma-content">
        <h2><?php echo $nomeEscola; ?></h2>

        <?php if (!empty($mensagem)): ?>
            <p style="color:<?php echo $tipoMensagem === 'success' ? 'green' : 'red'; ?>;">
                <?php echo htmlspecialchars($mensagem); ?>
            </p>
        <?php endif; ?>

        <h3>Adicionar Novo Aluno</h3>
        <form method="post">
            <input type="hidden" name="adicionar" value="1">
            
            <label for="nome">Nome do aluno:</label>
            <input type="text" id="nome" name="nome" required minlength="3">

            <div class="form-grid">
                <div>
                    <label for="nota1">Nota 1:</label>
                    <input type="number" id="nota1" step="0.1" name="nota1" min="0" max="10" required>
                </div>

                <div>
                    <label for="nota2">Nota 2:</label>
                    <input type="number" id="nota2" step="0.1" name="nota2" min="0" max="10" required>
                </div>

                <div>
                    <label for="nota3">Nota 3:</label>
                    <input type="number" id="nota3" step="0.1" name="nota3" min="0" max="10" required>
                </div>

                <div>
                    <label for="frequencia">Frequência (%):</label>
                    <input type="number" id="frequencia" step="0.1" name="frequencia" min="0" max="100" required>
                </div>
            </div>

            <input type="submit" value="Adicionar Aluno">
        </form>

        <?php if (!empty($turma)): ?>
            <h3>Lista de Alunos (<?php echo count($turma); ?> aluno<?php echo count($turma) > 1
     ? 's'
     : ''; ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Nota 1</th>
                        <th>Nota 2</th>
                        <th>Nota 3</th>
                        <th>Média</th>
                        <th>Frequência (%)</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turma as $aluno):

                        $resultado = situacaoAluno($aluno);
                        $mediaFormatada = number_format($resultado['media'], 1);
                        $situacao = $resultado['situacao'];

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
                            <td><?php echo number_format($aluno['notas'][0], 1); ?></td>
                            <td><?php echo number_format($aluno['notas'][1], 1); ?></td>
                            <td><?php echo number_format($aluno['notas'][2], 1); ?></td>
                            <td><?php echo $mediaFormatada; ?></td>
                            <td><?php echo number_format($aluno['frequencia'], 1); ?>%</td>
                            <td class="<?php echo $classeSituacao; ?>"><?php echo $situacao; ?></td>
                        </tr>
                    <?php
                    endforeach; ?>
                </tbody>
            </table>
            
            <form method="post">
                <button type="submit" name="limpar_turma" onclick="return confirm('Tem certeza que deseja limpar todos os alunos da turma?');">
                    Limpar Turma
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>