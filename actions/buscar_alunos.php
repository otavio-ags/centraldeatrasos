<?php

include_once('../config/conexao.php');

header('Content-Type: application/json');

if (!isset($_GET['id_turma']) || !is_numeric($_GET['id_turma'])) {
    echo json_encode([]); 
    exit;
}

$idTurma = $_GET['id_turma'];

try {
    $sqlAlunos = "SELECT id_aluno, nome FROM alunos WHERE id_turma = :id_turma ORDER BY nome ";
    $stmtAlunos = $conn -> prepare($sqlAlunos);
    $stmtAlunos -> bindParam(':id_turma', $idTurma, PDO::PARAM_INT);
    $stmtAlunos -> execute();

    $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($alunos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro de banco de dados']);
}