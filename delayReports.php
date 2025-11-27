<?php

include_once('includes/auth_check.php');
include_once('config/conexao.php');

$mapaMotivos = [
    'motivo1' => 'Atraso no transporte coletivo',
    'motivo2' => 'Trânsito',
    'motivo3' => 'Problemas familiares',
    'motivo4' => 'Outros'
];

$dataInicio = filter_input(INPUT_GET, 'data_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$dataFim = filter_input(INPUT_GET, 'data_fim', FILTER_SANITIZE_SPECIAL_CHARS);
$alunoNome = filter_input(INPUT_GET, 'aluno_nome', FILTER_SANITIZE_SPECIAL_CHARS);
$idTurma = filter_input(INPUT_GET, 'id_turma', FILTER_VALIDATE_INT);
$motivoAtraso = filter_input(INPUT_GET, 'motivo_atraso', FILTER_SANITIZE_SPECIAL_CHARS);

$resultados = [];
$params = [];
$whereClauses = ["1=1"]; // Cláusula base para facilitar a adição de filtros

$turmas = [];

try {
    $sqlTurmas = "SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma";
    $stmtTurmas = $conn->query($sqlTurmas);
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erro ao carregar turmas: " . $e->getMessage() . "</p>";
}

try {
    if (!empty($dataInicio) && !empty($dataFim)) {
        $whereClauses[] = "r.data_atraso BETWEEN :data_inicio AND :data_fim";
        $params[':data_inicio'] = $dataInicio;
        $params[':data_fim'] = $dataFim;
    }
    
    if (!empty($alunoNome)) {
        $whereClauses[] = "a.nome LIKE :aluno_nome";
        $params[':aluno_nome'] = '%' . $alunoNome . '%';
    }

    if ($idTurma) {
        $whereClauses[] = "t.id_turma = :id_turma";
        $params[':id_turma'] = $idTurma;
    }

    if (!empty($motivoAtraso) && array_key_exists($motivoAtraso, $mapaMotivos)) {
        // Pega o texto completo do motivo que está no banco (ex: 'Atraso no transporte coletivo')
        $motivoParaFiltrar = $mapaMotivos[$motivoAtraso]; 
        
        $whereClauses[] = "r.motivo_atraso = :motivo_atraso";
        $params[':motivo_atraso'] = $motivoParaFiltrar; // USA O TEXTO COMPLETO!
    }

    $sqlRelatorio = "
        SELECT 
            r.data_atraso, 
            r.horario_chegada, 
            r.motivo_atraso, 
            a.nome AS nome_aluno, 
            t.nome_turma 
        FROM 
            registros_atrasos r
        JOIN 
            alunos a ON r.id_aluno = a.id_aluno
        JOIN 
            turmas t ON a.id_turma = t.id_turma
        WHERE 
            " . implode(' AND ', $whereClauses) . "
        ORDER BY 
            r.data_atraso DESC
    ";

    $stmtRelatorio = $conn->prepare($sqlRelatorio);
    $stmtRelatorio->execute($params);
    $resultados = $stmtRelatorio->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $resultados = [];
    echo "<p style='color: red;'>Erro ao gerar relatório: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/delayReports.css">
    <title>Relatórios</title>
</head>
<body>
    <main>
        <div class="container">
            <h2>Relatório de Atrasos</h2>
        
            <form class="filtros" action="delayReports.php" method="GET" autocomplete="off">
        
                <div>
                    <label for="data_inicio">Data Inicial:</label>
                    <input type="date" name="data_inicio" id="data_inicio" value="<?php echo htmlspecialchars($dataInicio ?? ''); ?>">
                </div>
                <div>
                    <label for="data_fim">Data Final:</label>
                    <input type="date" name="data_fim" id="data_fim" value="<?php echo htmlspecialchars($dataFim ?? ''); ?>">
                </div>
        
                <div>
                    <label for="aluno_nome">Nome do Aluno:</label>
                    <input type="text" name="aluno_nome" id="aluno_nome" placeholder="Buscar por nome..." value="<?php echo htmlspecialchars($alunoNome ?? ''); ?>">
                </div>
                <div>
                    <label for="id_turma">Turma:</label>
                    <select name="id_turma" id="id_turma">
                        <option value="">Todas as Turmas</option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?php echo htmlspecialchars($turma['id_turma']); ?>"
                                    <?php echo ($idTurma == $turma['id_turma']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($turma['nome_turma']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="motivo_atraso">Motivo:</label>
                    <select name="motivo_atraso" id="motivo_atraso">
                        <option value="">Todos os Motivos</option>
                        <option value="motivo1" <?php echo ($motivoAtraso === 'motivo1') ? 'selected' : ''; ?>>Atraso no transporte coletivo</option>
                        <option value="motivo2" <?php echo ($motivoAtraso === 'motivo2') ? 'selected' : ''; ?>>Trânsito</option>
                        <option value="motivo3" <?php echo ($motivoAtraso === 'motivo3') ? 'selected' : ''; ?>>Problemas familiares</option>
                        <option value="motivo4" <?php echo ($motivoAtraso === 'motivo4') ? 'selected' : ''; ?>>Outros</option>
                    </select>
                </div>

                <button type="submit">Filtrar</button>
                <a href="delayReports.php" style="text-decoration: none; color: initial;"><button type="button">Limpar Filtros</button></a>
            </form>
            <button class="btn-print" onclick="window.print()">Imprimir Relatório</button>
            <?php if (!empty($resultados)): ?>
                <table>
                    <thead>
                        <p>Total de Registros Encontrados: <?php echo count($resultados); ?></p>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $registro): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($registro['data_atraso'])); ?></td>
                                <td><?php echo htmlspecialchars($registro['horario_chegada']); ?></td>
                                <td><?php echo htmlspecialchars($registro['nome_aluno']); ?></td>
                                <td><?php echo htmlspecialchars($registro['nome_turma']); ?></td>
                                <td><?php echo htmlspecialchars($registro['motivo_atraso']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum registro de atraso encontrado com os filtros selecionados.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>